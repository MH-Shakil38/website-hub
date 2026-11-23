<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SupportReply;
use App\Models\SupportTicket;
use App\Models\Tenant;
use App\Models\TenantBackup;
use App\Models\User;
use App\Services\TenantBackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class SaasOperationsController extends Controller
{
    public function __construct(
        protected TenantBackupService $backupService
    ) {}

    // =========================================================================
    // 1. PER-TENANT DATABASE BACKUPS & ONE-CLICK RESTORE (/admin/backups)
    // =========================================================================

    public function backups(Request $request)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();
        $accessibleTenants = $user->accessibleTenantsQuery()->whereNotNull('db_name')->orderByDesc('is_demo')->orderBy('name')->get();
        $tenantIds = $accessibleTenants->pluck('id')->all();

        $selectedTenantId = $request->query('tenant_id');

        $backupsQuery = TenantBackup::with(['tenant', 'creator'])->whereIn('tenant_id', $tenantIds);
        if ($selectedTenantId && in_array((int) $selectedTenantId, $tenantIds, true)) {
            $backupsQuery->where('tenant_id', $selectedTenantId);
        }

        $backups = $backupsQuery->latest('id')->paginate(12)->withQueryString();

        return view('admin.backups', compact('backups', 'accessibleTenants', 'selectedTenantId', 'user'));
    }

    public function createBackup(Request $request)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();

        $validated = $request->validate([
            'tenant_id' => 'required|exists:core_tenants,id',
            'notes' => 'nullable|string|max:255',
        ]);

        $tenant = Tenant::findOrFail($validated['tenant_id']);
        if (!$user->canAccessTenant($tenant)) {
            abort(403);
        }

        $backup = $this->backupService->backupTenant($tenant, $user, $validated['notes'] ?? null);

        return back()->with('success', "Snapshot backup #{$backup->id} created for [{$tenant->name}] (DB: {$tenant->db_name})!");
    }

    public function restoreBackup(TenantBackup $backup)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();

        if (!$backup->tenant || !$user->canAccessTenant($backup->tenant)) {
            abort(403);
        }

        $tableCount = $this->backupService->restoreBackup($backup);

        return back()->with('success', "Database [{$backup->db_name}] restored from Backup #{$backup->id} ({$tableCount} tables restored)!");
    }

    public function downloadBackup(TenantBackup $backup)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();

        if (!$backup->tenant || !$user->canAccessTenant($backup->tenant)) {
            abort(403);
        }

        $fullPath = storage_path('app/' . $backup->file_path);
        if (!File::exists($fullPath)) {
            return back()->withErrors(['backup' => 'Backup file not found on disk.']);
        }

        return response()->download($fullPath);
    }

    public function destroyBackup(TenantBackup $backup)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();

        if (!$backup->tenant || !$user->canAccessTenant($backup->tenant)) {
            abort(403);
        }

        $fullPath = storage_path('app/' . $backup->file_path);
        if (File::exists($fullPath)) {
            @File::delete($fullPath);
        }

        $backup->delete();

        return back()->with('success', 'Backup snapshot deleted.');
    }

    // =========================================================================
    // 2. SUPPORT TICKETS & HELP DESK (/admin/support-tickets)
    // =========================================================================

    public function tickets(Request $request)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();
        $statusFilter = $request->query('status');

        $ticketsQuery = SupportTicket::with(['user', 'tenant', 'replies.user']);

        if ($user->isClient()) {
            $ticketsQuery->where('user_id', $user->id);
        } elseif ($user->isReseller()) {
            $clientIds = $user->clients()->pluck('id')->push($user->id)->all();
            $ticketsQuery->whereIn('user_id', $clientIds);
        }

        if ($statusFilter) {
            $ticketsQuery->where('status', $statusFilter);
        }

        $tickets = $ticketsQuery->latest('updated_at')->paginate(10)->withQueryString();
        $accessibleTenants = $user->accessibleTenantsQuery()->orderBy('name')->get();

        return view('admin.support-tickets', compact('tickets', 'accessibleTenants', 'statusFilter', 'user'));
    }

    public function storeTicket(Request $request)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();

        $validated = $request->validate([
            'subject' => 'required|string|max:190',
            'category' => 'required|in:technical,billing,domain,reseller',
            'priority' => 'required|in:low,normal,high,urgent',
            'tenant_id' => 'nullable|exists:core_tenants,id',
            'message' => 'required|string|max:3000',
        ]);

        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'tenant_id' => $validated['tenant_id'] ?? null,
            'subject' => $validated['subject'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'status' => 'open',
        ]);

        SupportReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $validated['message'],
            'is_staff_reply' => $user->isSuperAdmin(),
        ]);

        ActivityLog::record(
            'Support Ticket Opened',
            "{$ticket->ticket_number}: {$ticket->subject}",
            'bi-life-preserver',
            '#DBEAFE',
            '#2563EB'
        );

        return back()->with('success', "Support Ticket [{$ticket->ticket_number}] created!");
    }

    public function replyTicket(Request $request, SupportTicket $ticket)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();

        if (!$user->isSuperAdmin() && !$user->isReseller() && (int) $ticket->user_id !== (int) $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:3000',
        ]);

        $isStaff = $user->isSuperAdmin() || ($user->isReseller() && (int) $ticket->user_id !== (int) $user->id);

        SupportReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $validated['message'],
            'is_staff_reply' => $isStaff,
        ]);

        $ticket->update([
            'status' => $isStaff ? 'answered' : 'open',
        ]);

        return back()->with('success', "Reply posted to ticket [{$ticket->ticket_number}].");
    }

    public function closeTicket(SupportTicket $ticket)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();

        if (!$user->isSuperAdmin() && (int) $ticket->user_id !== (int) $user->id) {
            abort(403);
        }

        $ticket->update(['status' => 'closed']);

        return back()->with('success', "Ticket [{$ticket->ticket_number}] closed.");
    }
}
