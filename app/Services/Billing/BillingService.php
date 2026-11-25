<?php

namespace App\Services\Billing;

use App\Models\ActivityLog;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\User;

class BillingService
{
    /**
     * Resolve a PaymentGatewayInterface driver by key ('sslcommerz' or 'manual').
     */
    public function gateway(string $driver = 'manual'): PaymentGatewayInterface
    {
        return match (strtolower($driver)) {
            'sslcommerz' => new SslCommerzPaymentDriver(),
            default => new ManualPaymentDriver(),
        };
    }

    /**
     * Subscribe a User (and optional Tenant) to a Plan and generate an Invoice if amount > 0.
     * Returns ['subscription' => Subscription, 'invoice' => ?Invoice].
     */
    public function createSubscriptionWithInvoice(
        User $user,
        Plan $plan,
        string $billingCycle = 'monthly',
        ?Tenant $tenant = null,
        bool $startAsTrial = false
    ): array {
        $amount = $billingCycle === 'yearly' ? (float) $plan->price_yearly : (float) $plan->price_monthly;
        $durationDays = $billingCycle === 'yearly' ? 365 : 30;

        if ($startAsTrial && $plan->trial_days > 0) {
            $status = 'trial';
            $endsAt = now()->addDays($plan->trial_days);
        } else {
            $status = $amount > 0 ? 'past_due' : 'active';
            $endsAt = now()->addDays($durationDays);
        }

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'tenant_id' => $tenant?->id,
            'plan_id' => $plan->id,
            'billing_cycle' => $billingCycle,
            'status' => $status,
            'starts_at' => now(),
            'ends_at' => $endsAt,
            'grace_ends_at' => $endsAt->copy()->addDays(3),
            'auto_renew' => true,
        ]);

        if ($tenant) {
            $tenant->update([
                'plan_id' => $plan->id,
                'storage_limit_mb' => $plan->storage_mb,
                'trial_ends_at' => $status === 'trial' ? $endsAt : $tenant->trial_ends_at,
            ]);
        }

        $invoice = null;
        if (!$startAsTrial && $amount > 0) {
            $invoice = Invoice::create([
                'user_id' => $user->id,
                'tenant_id' => $tenant?->id,
                'subscription_id' => $subscription->id,
                'plan_id' => $plan->id,
                'billing_cycle' => $billingCycle,
                'subtotal' => $amount,
                'discount' => 0,
                'total' => $amount,
                'currency' => $plan->currency ?: 'BDT',
                'status' => 'unpaid',
                'due_date' => now()->addDays(3),
                'notes' => "Subscription to {$plan->name} (" . ucfirst($billingCycle) . ")",
            ]);

            try {
                $invoice->load(['user', 'plan', 'tenant']);
                app(\App\Services\SaasMailService::class)->sendInvoiceGenerated($invoice);
            } catch (\Throwable) {
            }
        }

        return [
            'subscription' => $subscription,
            'invoice' => $invoice,
        ];
    }

    /**
     * Mark a Payment as completed, mark its Invoice as paid, and activate/extend the Subscription & Tenant.
     */
    public function completePayment(Payment $payment, ?User $approver = null, ?string $adminNote = null): Payment
    {
        $payment->update([
            'status' => 'completed',
            'approved_by' => $approver?->id,
            'approved_at' => now(),
            'admin_note' => $adminNote ?? $payment->admin_note,
        ]);

        $invoice = $payment->invoice;
        if ($invoice) {
            $invoice->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            $subscription = $invoice->subscription;
            if ($subscription) {
                $days = $invoice->billing_cycle === 'yearly' ? 365 : 30;
                $baseDate = ($subscription->ends_at && $subscription->ends_at->isFuture())
                    ? $subscription->ends_at
                    : now();
                $newEndsAt = $baseDate->copy()->addDays($days);

                $subscription->update([
                    'status' => 'active',
                    'starts_at' => $subscription->starts_at ?? now(),
                    'ends_at' => $newEndsAt,
                    'grace_ends_at' => $newEndsAt->copy()->addDays(3),
                ]);

                if ($subscription->tenant) {
                    $subscription->tenant->update([
                        'status' => 'active',
                        'plan_id' => $invoice->plan_id ?? $subscription->plan_id,
                    ]);
                }
            } elseif ($invoice->tenant) {
                $invoice->tenant->update(['status' => 'active']);
            }

            // Phase 5: If the paying user is a referred Client, record a Mode B Reseller Commission
            try {
                app(\App\Services\ResellerService::class)->recordCommissionForPaidInvoice($invoice);
            } catch (\Throwable) {
            }
        }

        // Also record in legacy core_transactions table for unified dashboard financial reporting
        Transaction::firstOrCreate(
            ['transaction_id' => $payment->gateway_txn_id ?: ('PAY-' . $payment->id)],
            [
                'transacted_at' => now(),
                'wallet_number' => $payment->sender_number ?: ($payment->user?->phone ?: $payment->user?->email ?: 'N/A'),
                'amount' => $payment->amount,
                'coupon_amount' => 0,
                'net_amount' => $payment->amount,
                'cashback_amount' => 0,
                'type' => 'Payment',
                'channel' => strtoupper($payment->gateway),
            ]
        );

        ActivityLog::record(
            'Invoice Paid & Subscription Activated',
            "Invoice {$invoice?->invoice_number} • {$payment->currency} {$payment->amount} via " . strtoupper($payment->gateway),
            'bi-check-circle-fill',
            '#DCFCE7',
            '#10B981'
        );

        try {
            $payment->load(['user', 'invoice.user', 'invoice.plan', 'invoice.tenant']);
            app(\App\Services\SaasMailService::class)->sendPaymentApproved($payment, $payment->invoice);
        } catch (\Throwable) {
        }

        return $payment->fresh();
    }

    /**
     * Check expired trials and past-due subscriptions; auto-suspend tenants whose grace period has ended.
     * Returns ['expired_trials' => int, 'suspended_tenants' => int].
     */
    public function processExpirations(): array
    {
        $expiredTrials = 0;
        $suspendedTenants = 0;

        // 1. Check tenants on trial whose trial_ends_at + 3-day grace period has passed
        $trialTenants = Tenant::where('status', 'trial')
            ->where('is_demo', false)
            ->whereNotNull('trial_ends_at')
            ->get();

        foreach ($trialTenants as $tenant) {
            if ($tenant->trial_ends_at->copy()->addDays(3)->isPast()) {
                $tenant->update(['status' => 'suspended']);
                $expiredTrials++;
                $suspendedTenants++;
            }
        }

        // 2. Check active/trial subscriptions whose grace_ends_at (or ends_at) has passed
        $expiredSubs = Subscription::with('tenant')
            ->whereIn('status', ['trial', 'active'])
            ->whereNotNull('ends_at')
            ->get();

        foreach ($expiredSubs as $sub) {
            $cutoff = $sub->grace_ends_at ?: $sub->ends_at;
            if ($cutoff && $cutoff->isPast()) {
                $sub->update(['status' => 'expired']);
                if ($sub->tenant && !$sub->tenant->is_demo && $sub->tenant->status !== 'suspended') {
                    $sub->tenant->update(['status' => 'suspended']);
                    $suspendedTenants++;
                }
            }
        }

        return [
            'expired_trials' => $expiredTrials,
            'suspended_tenants' => $suspendedTenants,
        ];
    }
}
