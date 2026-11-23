<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Billing\BillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SaasBillingController extends Controller
{
    public function __construct(
        protected BillingService $billing
    ) {}

    /**
     * Plans & Packages Manager (Super Admin manages all plans; Resellers/Clients view & subscribe).
     */
    public function plans(Request $request)
    {
        Plan::ensureDefaultsSeeded();

        /** @var User $user */
        $user = Auth::user() ?? User::first();
        $audienceFilter = $request->query('audience');

        $query = Plan::query()->orderBy('sort_order')->orderBy('id');

        if (!$user->isSuperAdmin()) {
            $query->where('is_active', true)
                ->where('audience', $user->isReseller() ? 'reseller' : 'client');
        } elseif ($audienceFilter) {
            $query->where('audience', $audienceFilter);
        }

        $plans = $query->get();
        $activeSub = $user->activeSubscription();
        $accessibleTenants = $user->accessibleTenantsQuery()->where('is_demo', false)->get();

        return view('admin.plans', compact('plans', 'activeSub', 'accessibleTenants', 'audienceFilter', 'user'));
    }

    /**
     * Store or Update a SaaS Plan (Super Admin only).
     */
    public function storePlan(Request $request)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can create or edit plans.');
        }

        $validated = $request->validate([
            'audience' => 'required|in:client,reseller',
            'name' => 'required|string|max:160',
            'description' => 'nullable|string|max:500',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'trial_days' => 'nullable|integer|min:0|max:90',
            'site_limit' => 'required|integer|min:1',
            'storage_mb' => 'required|integer|min:100',
            'custom_domain' => 'nullable|boolean',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'extra_site_price' => 'nullable|numeric|min:0',
            'features_text' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
        ]);

        $features = [];
        if (!empty($validated['features_text'])) {
            $features = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $validated['features_text']))));
        }

        $slug = Str::slug($validated['name']);
        if (Plan::where('slug', $slug)->exists()) {
            $slug .= '-' . strtolower(Str::random(4));
        }

        $plan = Plan::create([
            'audience' => $validated['audience'],
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'price_monthly' => $validated['price_monthly'],
            'price_yearly' => $validated['price_yearly'],
            'currency' => $validated['currency'] ?? 'BDT',
            'trial_days' => $validated['trial_days'] ?? 14,
            'site_limit' => $validated['site_limit'],
            'storage_mb' => $validated['storage_mb'],
            'custom_domain' => $request->boolean('custom_domain', true),
            'commission_rate' => $validated['commission_rate'] ?? 0,
            'extra_site_price' => $validated['extra_site_price'] ?? 0,
            'features' => $features,
            'is_featured' => $request->boolean('is_featured', false),
            'is_active' => true,
            'sort_order' => Plan::max('sort_order') + 1,
        ]);

        ActivityLog::record(
            'New SaaS Plan Created',
            "{$plan->name} ({$plan->audience}) • ৳{$plan->price_monthly}/mo",
            'bi-box-seam-fill',
            '#FFE4EC',
            '#EC134A'
        );

        return back()->with('success', "Plan [{$plan->name}] created successfully.");
    }

    /**
     * Delete a SaaS Plan (Super Admin only).
     */
    public function destroyPlan(Plan $plan)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();
        if (!$user->isSuperAdmin()) {
            abort(403);
        }

        $name = $plan->name;
        $plan->delete();

        return back()->with('success', "Plan [{$name}] deleted.");
    }

    /**
     * Subscribe to a Plan and generate an Invoice (or start trial).
     */
    public function subscribe(Request $request, Plan $plan)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();

        $validated = $request->validate([
            'billing_cycle' => 'required|in:monthly,yearly',
            'tenant_id' => 'nullable|exists:core_tenants,id',
            'start_trial' => 'nullable|boolean',
        ]);

        $tenant = null;
        if (!empty($validated['tenant_id'])) {
            $tenant = Tenant::find($validated['tenant_id']);
            if ($tenant && !$user->canAccessTenant($tenant)) {
                abort(403);
            }
        } else {
            $tenant = $user->accessibleTenantsQuery()->where('is_demo', false)->first();
        }

        $startTrial = $request->boolean('start_trial', false);
        $result = $this->billing->createSubscriptionWithInvoice(
            $user,
            $plan,
            $validated['billing_cycle'],
            $tenant,
            $startTrial
        );

        if ($result['invoice']) {
            return redirect()->route('admin.billing.index', ['pay_invoice' => $result['invoice']->id])
                ->with('success', "Invoice [{$result['invoice']->invoice_number}] generated for {$plan->name}. Please complete payment below.");
        }

        return redirect()->route('admin.billing.index')
            ->with('success', "Subscribed to [{$plan->name}] ({$result['subscription']->status})!");
    }

    /**
     * Billing, Subscriptions, Invoices & Manual Payment Approval Hub.
     */
    public function billingIndex(Request $request)
    {
        Plan::ensureDefaultsSeeded();

        /** @var User $user */
        $user = Auth::user() ?? User::first();
        $statusFilter = $request->query('status');

        $invoicesQuery = Invoice::with(['user', 'tenant', 'plan', 'payments']);
        $subscriptionsQuery = Subscription::with(['user', 'tenant', 'plan']);
        $pendingPaymentsQuery = Payment::with(['invoice.plan', 'invoice.tenant', 'user'])->where('status', 'pending');

        if (!$user->isSuperAdmin()) {
            $invoicesQuery->where('user_id', $user->id);
            $subscriptionsQuery->where('user_id', $user->id);
            $pendingPaymentsQuery->where('user_id', $user->id);
        }

        if ($statusFilter) {
            $invoicesQuery->where('status', $statusFilter);
        }

        $invoices = $invoicesQuery->latest('id')->paginate(10)->withQueryString();
        $subscriptions = $subscriptionsQuery->latest('id')->take(10)->get();
        $pendingPayments = $pendingPaymentsQuery->latest('id')->get();

        return view('admin.billing', compact('invoices', 'subscriptions', 'pendingPayments', 'statusFilter', 'user'));
    }

    /**
     * Submit a Manual Payment (bKash / Nagad / Rocket / Bank Transfer Txn ID) for an Invoice.
     */
    public function payManual(Request $request, Invoice $invoice)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();

        if (!$user->isSuperAdmin() && (int) $invoice->user_id !== (int) $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'method' => 'required|in:bKash,Nagad,Rocket,Bank Transfer',
            'sender_number' => 'required|string|max:60',
            'gateway_txn_id' => 'required|string|max:100',
        ]);

        $res = $this->billing->gateway('manual')->initiate($invoice, $validated);

        ActivityLog::record(
            'Manual Payment Submitted',
            "Invoice {$invoice->invoice_number} • {$validated['method']} Txn: {$validated['gateway_txn_id']}",
            'bi-wallet2',
            '#FEF3C7',
            '#D97706'
        );

        return back()->with('success', $res['message']);
    }

    /**
     * Initiate an SSLCommerz Online Payment for an Invoice.
     */
    public function paySslCommerz(Invoice $invoice)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();

        if (!$user->isSuperAdmin() && (int) $invoice->user_id !== (int) $user->id) {
            abort(403);
        }

        $res = $this->billing->gateway('sslcommerz')->initiate($invoice);

        if (!empty($res['redirect_url'])) {
            return redirect()->to($res['redirect_url']);
        }

        return back()->with('success', $res['message']);
    }

    /**
     * SSLCommerz Hosted Checkout / Sandbox Simulator Page.
     */
    public function sslCommerzCheckout(Payment $payment)
    {
        $payment->load(['invoice.plan', 'invoice.tenant', 'user']);
        return view('admin.sslcommerz-checkout', compact('payment'));
    }

    /**
     * SSLCommerz Success / Fail Callback Handler.
     */
    public function sslCommerzCallback(Request $request, Payment $payment)
    {
        $outcome = $request->input('outcome', 'success');

        if ($outcome === 'success') {
            $payment->update([
                'method' => $request->input('channel', 'SSLCommerz - bKash/Card'),
                'gateway_payload' => array_merge($payment->gateway_payload ?? [], [
                    'val_id' => 'VAL-' . strtoupper(Str::random(10)),
                    'bank_tran_id' => 'BANK-' . strtoupper(Str::random(10)),
                    'card_type' => $request->input('channel', 'bKash-SSLCommerz'),
                    'completed_at' => now()->toIso8601String(),
                ]),
            ]);

            $this->billing->completePayment($payment, null, 'Auto-verified via SSLCommerz IPN/Callback');

            return redirect()->route('admin.billing.index')
                ->with('success', "SSLCommerz Payment [{$payment->gateway_txn_id}] successful! Invoice [{$payment->invoice->invoice_number}] is now PAID and subscription is ACTIVE.");
        }

        $payment->update(['status' => 'failed']);

        return redirect()->route('admin.billing.index')
            ->withErrors(['payment' => "SSLCommerz payment [{$payment->gateway_txn_id}] was cancelled or failed."]);
    }

    /**
     * Approve a Pending Manual Payment (Super Admin only).
     */
    public function approvePayment(Request $request, Payment $payment)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();

        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can approve manual payments.');
        }

        $this->billing->completePayment($payment, $user, $request->input('admin_note', 'Approved by Super Admin'));

        return back()->with('success', "Payment [{$payment->gateway_txn_id}] approved! Invoice [{$payment->invoice?->invoice_number}] marked PAID and subscription activated.");
    }

    /**
     * Reject a Pending Manual Payment (Super Admin only).
     */
    public function rejectPayment(Request $request, Payment $payment)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();

        if (!$user->isSuperAdmin()) {
            abort(403);
        }

        $payment->update([
            'status' => 'rejected',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'admin_note' => $request->input('admin_note', 'Rejected by Super Admin'),
        ]);

        if ($payment->invoice && $payment->invoice->status === 'pending_approval') {
            $payment->invoice->update(['status' => 'unpaid']);
        }

        return back()->with('success', "Payment [{$payment->gateway_txn_id}] rejected.");
    }

    /**
     * View Printable HTML/PDF Invoice.
     */
    public function showInvoice(Invoice $invoice)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();

        if (!$user->isSuperAdmin() && (int) $invoice->user_id !== (int) $user->id) {
            abort(403);
        }

        $invoice->load(['user', 'tenant', 'plan', 'subscription', 'payments']);

        return view('admin.invoice-print', compact('invoice'));
    }
}
