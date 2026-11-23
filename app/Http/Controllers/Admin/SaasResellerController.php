<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Commission;
use App\Models\PayoutRequest;
use App\Models\Reseller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\ResellerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaasResellerController extends Controller
{
    public function __construct(
        protected ResellerService $resellerService
    ) {}

    /**
     * Reseller Hub: Referral Link, Billing Mode (Mode A / Mode B), KYC, Wallet, Commissions & Payouts.
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();

        if ($user->isClient()) {
            return redirect()->route('admin.dashboard');
        }

        // Auto-release any commissions whose 7-day maturation period has passed
        $this->resellerService->releaseMaturedCommissions();

        $resellerProfile = null;
        if ($user->isReseller()) {
            $resellerProfile = $user->ensureResellerProfile();
        } elseif ($request->filled('reseller_id')) {
            $resellerProfile = Reseller::with('user')->find($request->query('reseller_id'));
        }

        $allResellers = $user->isSuperAdmin()
            ? Reseller::with(['user', 'plan'])->latest('id')->get()
            : collect([$resellerProfile]);

        $resellerIds = $user->isSuperAdmin()
            ? ($resellerProfile ? [$resellerProfile->id] : $allResellers->pluck('id')->all())
            : [$resellerProfile?->id];

        $walletTransactions = WalletTransaction::with('reseller.user')
            ->whereIn('reseller_id', $resellerIds)
            ->latest('id')
            ->take(15)
            ->get();

        $commissions = Commission::with(['reseller.user', 'client', 'invoice'])
            ->whereIn('reseller_id', $resellerIds)
            ->latest('id')
            ->take(15)
            ->get();

        $payoutRequests = PayoutRequest::with(['reseller.user', 'processor'])
            ->whereIn('reseller_id', $resellerIds)
            ->latest('id')
            ->take(15)
            ->get();

        return view('admin.reseller-hub', compact(
            'user',
            'resellerProfile',
            'allResellers',
            'walletTransactions',
            'commissions',
            'payoutRequests'
        ));
    }

    /**
     * Update Reseller Profile, Billing Mode (Mode A: reseller_collects vs Mode B: commission) & KYC Details.
     */
    public function updateProfile(Request $request)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();

        $validated = $request->validate([
            'reseller_id' => 'nullable|exists:core_resellers,id',
            'company_name' => 'required|string|max:160',
            'phone' => 'nullable|string|max:40',
            'billing_mode' => 'required|in:reseller_collects,commission',
            'kyc_document_type' => 'required|in:NID,Trade License,Passport',
            'kyc_document_number' => 'nullable|string|max:120',
        ]);

        $reseller = $user->isSuperAdmin() && !empty($validated['reseller_id'])
            ? Reseller::findOrFail($validated['reseller_id'])
            : $user->ensureResellerProfile();

        $reseller->update([
            'company_name' => $validated['company_name'],
            'phone' => $validated['phone'] ?? $reseller->phone,
            'billing_mode' => $validated['billing_mode'],
            'kyc_document_type' => $validated['kyc_document_type'],
            'kyc_document_number' => $validated['kyc_document_number'] ?? $reseller->kyc_document_number,
        ]);

        return back()->with('success', "Reseller settings & billing mode updated to [" . strtoupper($reseller->billing_mode) . "].");
    }

    /**
     * Top-up Reseller Wallet (Online or Manual Credit).
     */
    public function topUpWallet(Request $request)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();

        $validated = $request->validate([
            'reseller_id' => 'nullable|exists:core_resellers,id',
            'amount' => 'required|numeric|min:100|max:500000',
            'method' => 'required|string|max:60',
            'reference' => 'nullable|string|max:100',
        ]);

        $reseller = ($user->isSuperAdmin() && !empty($validated['reseller_id']))
            ? Reseller::findOrFail($validated['reseller_id'])
            : $user->ensureResellerProfile();

        $ref = $validated['reference'] ?: ('TOPUP-' . strtoupper(substr(uniqid(), -6)));

        $this->resellerService->creditWallet(
            $reseller,
            (float) $validated['amount'],
            $ref,
            "Wallet Top-Up via {$validated['method']}"
        );

        ActivityLog::record(
            'Reseller Wallet Top-Up',
            "৳{$validated['amount']} credited to {$reseller->company_name} ({$ref})",
            'bi-wallet2',
            '#DCFCE7',
            '#10B981'
        );

        return back()->with('success', "৳{$validated['amount']} added to wallet! New Balance: ৳" . number_format($reseller->fresh()->wallet_balance, 2));
    }

    /**
     * Submit a Payout Request from Reseller Wallet (bKash / Nagad / Bank).
     */
    public function requestPayout(Request $request)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();
        $reseller = $user->ensureResellerProfile();

        $validated = $request->validate([
            'amount' => 'required|numeric|min:500',
            'method' => 'required|in:bKash,Nagad,Rocket,Bank Transfer',
            'account_info' => 'required|string|max:190',
        ]);

        try {
            $payout = $this->resellerService->requestPayout(
                $reseller,
                (float) $validated['amount'],
                $validated['method'],
                $validated['account_info']
            );
        } catch (\Throwable $e) {
            return back()->withErrors(['amount' => $e->getMessage()]);
        }

        ActivityLog::record(
            'Reseller Payout Requested',
            "৳{$payout->amount} via {$payout->method} ({$payout->account_info})",
            'bi-cash-coin',
            '#FEF3C7',
            '#D97706'
        );

        return back()->with('success', "Payout request for ৳{$payout->amount} submitted! Super Admin will review and disburse.");
    }

    /**
     * Super Admin: Approve or Reject a Reseller Payout Request.
     */
    public function processPayout(Request $request, PayoutRequest $payout)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();
        if (!$user->isSuperAdmin()) {
            abort(403);
        }

        $action = $request->input('action', 'paid');
        if ($action === 'paid') {
            $payout->update([
                'status' => 'paid',
                'transaction_reference' => $request->input('transaction_reference', 'PAID-' . strtoupper(substr(uniqid(), -6))),
                'processed_by' => $user->id,
                'processed_at' => now(),
                'admin_note' => $request->input('admin_note', 'Disbursed by Super Admin'),
            ]);

            return back()->with('success', "Payout request #{$payout->id} (৳{$payout->amount}) marked as PAID.");
        }

        // If rejected, refund the amount back to the reseller's wallet
        if ($payout->status === 'pending' && $payout->reseller) {
            $this->resellerService->creditWallet(
                $payout->reseller,
                (float) $payout->amount,
                'REFUND-PAYOUT-' . $payout->id,
                "Refund for rejected payout request #{$payout->id}"
            );
        }

        $payout->update([
            'status' => 'rejected',
            'processed_by' => $user->id,
            'processed_at' => now(),
            'admin_note' => $request->input('admin_note', 'Rejected & refunded to wallet'),
        ]);

        return back()->with('success', "Payout request #{$payout->id} rejected and ৳{$payout->amount} refunded to reseller wallet.");
    }

    /**
     * Super Admin: Approve a Pending Commission immediately into the Reseller's Wallet.
     */
    public function approveCommission(Commission $commission)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();
        if (!$user->isSuperAdmin()) {
            abort(403);
        }

        $this->resellerService->approveCommission($commission);

        return back()->with('success', "Commission #{$commission->id} (৳{$commission->amount}) approved and credited to reseller wallet!");
    }

    /**
     * Super Admin: Update Reseller KYC Status (approved / rejected) & Quota.
     */
    public function updateKycStatus(Request $request, Reseller $reseller)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();
        if (!$user->isSuperAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'kyc_status' => 'required|in:pending,approved,rejected',
            'site_quota' => 'nullable|integer|min:1',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $reseller->update([
            'kyc_status' => $validated['kyc_status'],
            'site_quota' => $validated['site_quota'] ?? $reseller->site_quota,
            'commission_rate' => $validated['commission_rate'] ?? $reseller->commission_rate,
        ]);

        return back()->with('success', "Reseller [{$reseller->company_name}] KYC status updated to [" . strtoupper($reseller->kyc_status) . "].");
    }

    /**
     * Mode A Reseller Action: Renew a Client's Tenant website using Reseller Wallet.
     */
    public function renewClientTenant(Request $request, Tenant $tenant)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();

        if (!$user->isReseller() && !$user->isSuperAdmin()) {
            abort(403);
        }

        $reseller = $user->isReseller()
            ? $user->ensureResellerProfile()
            : Reseller::where('user_id', $tenant->reseller_id)->firstOrFail();

        $months = max(1, (int) $request->input('months', 1));

        try {
            $this->resellerService->renewClientTenantByReseller($reseller, $tenant, $months);
        } catch (\Throwable $e) {
            return back()->withErrors(['wallet' => $e->getMessage()]);
        }

        return back()->with('success', "Client website [{$tenant->name}] renewed for {$months} month(s) via Reseller Wallet!");
    }
}
