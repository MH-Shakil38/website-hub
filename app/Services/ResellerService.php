<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Commission;
use App\Models\Invoice;
use App\Models\PayoutRequest;
use App\Models\Reseller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WalletTransaction;

class ResellerService
{
    /**
     * Credit a Reseller's wallet and record a transaction.
     */
    public function creditWallet(Reseller $reseller, float $amount, string $reference, string $description): WalletTransaction
    {
        $newBalance = round((float) $reseller->wallet_balance + $amount, 2);
        $reseller->update(['wallet_balance' => $newBalance]);

        return WalletTransaction::create([
            'reseller_id' => $reseller->id,
            'type' => 'credit',
            'amount' => $amount,
            'balance_after' => $newBalance,
            'reference' => $reference,
            'description' => $description,
        ]);
    }

    /**
     * Debit a Reseller's wallet and record a transaction.
     */
    public function debitWallet(Reseller $reseller, float $amount, string $reference, string $description): WalletTransaction
    {
        if ((float) $reseller->wallet_balance < $amount) {
            throw new \RuntimeException("Insufficient reseller wallet balance (Available: ৳{$reseller->wallet_balance}, Required: ৳{$amount}).");
        }

        $newBalance = round((float) $reseller->wallet_balance - $amount, 2);
        $reseller->update(['wallet_balance' => $newBalance]);

        return WalletTransaction::create([
            'reseller_id' => $reseller->id,
            'type' => 'debit',
            'amount' => $amount,
            'balance_after' => $newBalance,
            'reference' => $reference,
            'description' => $description,
        ]);
    }

    /**
     * Consume 1 site slot from a Reseller's package quota, OR deduct the wholesale extra_site_price from their wallet (Mode A).
     * Returns ['method' => 'slot'|'wallet', 'cost' => float].
     */
    public function consumeSlotOrWalletForNewSite(Reseller $reseller, string $siteName): array
    {
        $plan = $reseller->plan ?: $reseller->user?->currentPlan();
        $quota = $plan ? (int) $plan->site_limit : (int) $reseller->site_quota;

        if ($reseller->sites_used < $quota) {
            $reseller->increment('sites_used');
            return [
                'method' => 'slot',
                'cost' => 0.00,
            ];
        }

        // Package slots full -> charge wholesale extra site price from wallet
        $extraCost = $plan && (float) $plan->extra_site_price > 0
            ? (float) $plan->extra_site_price
            : 450.00;

        $this->debitWallet(
            $reseller,
            $extraCost,
            'SITE-' . strtoupper(substr(md5($siteName . time()), 0, 6)),
            "Extra client site provisioning above quota: {$siteName}"
        );

        $reseller->increment('sites_used');

        return [
            'method' => 'wallet',
            'cost' => $extraCost,
        ];
    }

    /**
     * Renew a Client's Tenant website from the Reseller Panel (Mode A: deducts wholesale rate from wallet if needed).
     */
    public function renewClientTenantByReseller(Reseller $reseller, Tenant $tenant, int $months = 1): Tenant
    {
        $plan = $reseller->plan ?: $reseller->user?->currentPlan();
        $monthlyWholesale = $plan && (float) $plan->extra_site_price > 0
            ? (float) $plan->extra_site_price
            : 450.00;

        $totalCost = round($monthlyWholesale * $months, 2);

        $this->debitWallet(
            $reseller,
            $totalCost,
            'RENEW-T' . $tenant->id . '-' . time(),
            "Mode A Client Site Renewal ({$months} mo): {$tenant->name}"
        );

        $baseDate = ($tenant->trial_ends_at && $tenant->trial_ends_at->isFuture())
            ? $tenant->trial_ends_at
            : now();

        $tenant->update([
            'status' => 'active',
            'trial_ends_at' => $baseDate->copy()->addDays(30 * $months),
        ]);

        return $tenant->fresh();
    }

    /**
     * Record a Mode B Referral Commission when a referred Client pays an Invoice on yourhub.com.
     */
    public function recordCommissionForPaidInvoice(Invoice $invoice): ?Commission
    {
        $client = $invoice->user;
        if (!$client || !$client->reseller_id) {
            return null;
        }

        // Prevent duplicate commission for the same invoice
        if (Commission::where('invoice_id', $invoice->id)->exists()) {
            return null;
        }

        $resellerUser = User::find($client->reseller_id);
        if (!$resellerUser || !$resellerUser->isReseller()) {
            return null;
        }

        $reseller = $resellerUser->ensureResellerProfile();

        // Only award commission if the reseller is in Mode B ('commission') or has commission_rate > 0
        $plan = $reseller->plan ?: $resellerUser->currentPlan();
        $rate = $plan && (float) $plan->commission_rate > 0
            ? (float) $plan->commission_rate
            : (float) $reseller->commission_rate;

        if ($rate <= 0) {
            return null;
        }

        $commissionAmount = round(((float) $invoice->total * $rate) / 100, 2);
        if ($commissionAmount <= 0) {
            return null;
        }

        $commission = Commission::create([
            'reseller_id' => $reseller->id,
            'client_id' => $client->id,
            'invoice_id' => $invoice->id,
            'invoice_amount' => $invoice->total,
            'rate' => $rate,
            'amount' => $commissionAmount,
            'status' => 'pending',
            'available_at' => now()->addDays(7), // 7-day refund safety maturation window
        ]);

        ActivityLog::record(
            'Reseller Commission Earned (Pending)',
            "৳{$commissionAmount} ({$rate}%) for {$reseller->company_name} from Invoice {$invoice->invoice_number}",
            'bi-cash-stack',
            '#DBEAFE',
            '#2563EB'
        );

        return $commission;
    }

    /**
     * Approve a pending commission (either after 7-day maturation or manual Super Admin approval) and credit wallet.
     */
    public function approveCommission(Commission $commission): Commission
    {
        if ($commission->status === 'approved') {
            return $commission;
        }

        $commission->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        $reseller = $commission->reseller;
        if ($reseller) {
            $this->creditWallet(
                $reseller,
                (float) $commission->amount,
                'COMM-' . $commission->id,
                "Referral commission ({$commission->rate}%) from Invoice #" . ($commission->invoice?->invoice_number ?? $commission->invoice_id)
            );
        }

        return $commission->fresh();
    }

    /**
     * Process all matured pending commissions (where available_at <= now()).
     */
    public function releaseMaturedCommissions(): int
    {
        $matured = Commission::with(['reseller', 'invoice'])
            ->where('status', 'pending')
            ->whereNotNull('available_at')
            ->where('available_at', '<=', now())
            ->get();

        $count = 0;
        foreach ($matured as $comm) {
            $this->approveCommission($comm);
            $count++;
        }

        return $count;
    }

    /**
     * Create a Payout Request and deduct the requested amount from the Reseller's wallet.
     */
    public function requestPayout(Reseller $reseller, float $amount, string $method, string $accountInfo): PayoutRequest
    {
        if ($amount < 500) {
            throw new \InvalidArgumentException('Minimum payout request amount is ৳500.');
        }

        $this->debitWallet(
            $reseller,
            $amount,
            'PAYOUT-REQ-' . time(),
            "Payout withdrawal request via {$method} ({$accountInfo})"
        );

        return PayoutRequest::create([
            'reseller_id' => $reseller->id,
            'amount' => $amount,
            'method' => $method,
            'account_info' => $accountInfo,
            'status' => 'pending',
        ]);
    }
}
