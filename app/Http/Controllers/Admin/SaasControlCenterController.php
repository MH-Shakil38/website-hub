<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PayoutRequest;
use App\Models\Reseller;
use App\Models\Subscription;
use App\Models\SupportTicket;
use App\Models\Tenant;
use App\Models\User;
use App\Support\ProductionReadiness;
use Illuminate\Support\Carbon;

/**
 * Super Admin "SaaS Control Center": one page with the platform's numbers, everything that is
 * waiting for an admin decision, and shortcuts into the existing management pages.
 */
class SaasControlCenterController extends Controller
{
    public function index()
    {
        $now = now();
        $monthStart = $now->copy()->startOfMonth();

        $completed = Payment::where('status', 'completed');

        $activeSubs = Subscription::with('plan')->where('status', 'active')->get();
        $mrr = $activeSubs->sum(function (Subscription $s) {
            if (! $s->plan) {
                return 0;
            }

            return $s->billing_cycle === 'yearly'
                ? (float) $s->plan->price_yearly / 12
                : (float) $s->plan->price_monthly;
        });

        $tenantCounts = Tenant::selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status');

        $stats = [
            'mrr' => $mrr,
            'revenue_month' => (float) (clone $completed)->where('created_at', '>=', $monthStart)->sum('amount'),
            'revenue_total' => (float) (clone $completed)->sum('amount'),
            'tenants_total' => (int) $tenantCounts->sum(),
            'tenants_active' => (int) ($tenantCounts['active'] ?? 0),
            'tenants_trial' => (int) ($tenantCounts['trial'] ?? 0),
            'tenants_suspended' => (int) (($tenantCounts['suspended'] ?? 0) + ($tenantCounts['expired'] ?? 0)),
            'tenants_pending' => (int) ($tenantCounts['pending_verification'] ?? 0),
            'clients' => User::where('type', 'client')->count(),
            'resellers' => User::where('type', 'reseller')->count(),
            'unpaid_invoices' => Invoice::whereIn('status', ['unpaid', 'pending_approval'])->count(),
            'unpaid_amount' => (float) Invoice::whereIn('status', ['unpaid', 'pending_approval'])->sum('total'),
            'open_tickets' => SupportTicket::where('status', 'open')->count(),
            'past_due' => Subscription::whereIn('status', ['past_due', 'suspended'])->count(),
        ];

        // Revenue for the last 6 months (completed payments).
        $revenueByMonth = collect(range(5, 0))->map(function (int $back) use ($now) {
            $start = $now->copy()->startOfMonth()->subMonths($back);

            return [
                'label' => $start->format('M'),
                'amount' => (float) Payment::where('status', 'completed')
                    ->whereBetween('created_at', [$start, $start->copy()->endOfMonth()])
                    ->sum('amount'),
            ];
        });

        $queues = [
            'payments' => Payment::with(['invoice', 'user'])->where('status', 'pending')->where('gateway', 'manual')->latest('id')->take(8)->get(),
            'domains' => Domain::with('tenant')->whereNull('verified_at')->where('type', 'custom')->latest('id')->take(8)->get(),
            'tickets' => SupportTicket::with(['user', 'tenant'])->where('status', 'open')
                ->orderByRaw("FIELD(priority, 'urgent', 'high', 'normal', 'low')")->latest('id')->take(8)->get(),
            'payouts' => PayoutRequest::with('reseller.user')->where('status', 'pending')->latest('id')->take(8)->get(),
            'kyc' => Reseller::with('user')->where('kyc_status', 'pending')->latest('id')->take(8)->get(),
            'expiring' => Subscription::with(['tenant', 'user', 'plan'])
                ->whereIn('status', ['active', 'trial'])
                ->whereBetween('ends_at', [$now, $now->copy()->addDays(7)])
                ->orderBy('ends_at')->take(8)->get(),
        ];

        $queueCounts = [
            'payments' => Payment::where('status', 'pending')->where('gateway', 'manual')->count(),
            'domains' => Domain::whereNull('verified_at')->where('type', 'custom')->count(),
            'tickets' => $stats['open_tickets'],
            'payouts' => PayoutRequest::where('status', 'pending')->count(),
            'kyc' => Reseller::where('kyc_status', 'pending')->count(),
            'expiring' => Subscription::whereIn('status', ['active', 'trial'])->whereBetween('ends_at', [$now, $now->copy()->addDays(7)])->count(),
        ];

        $recentTenants = Tenant::with(['owner', 'plan'])->latest('id')->take(8)->get();
        $readinessIssues = ProductionReadiness::issues();
        $currency = 'BDT';
        $generatedAt = Carbon::now();

        return view('admin.saas-control-center', compact(
            'stats', 'revenueByMonth', 'queues', 'queueCounts', 'recentTenants', 'readinessIssues', 'currency', 'generatedAt'
        ));
    }
}
