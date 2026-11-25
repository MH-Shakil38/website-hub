<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use App\Models\SupportReply;
use App\Models\SupportTicket;
use App\Models\Tenant;
use App\Models\TenantBackup;
use App\Models\User;
use App\Services\SaasMailService;
use App\Services\TenantBackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class SaasOperationsController extends Controller
{
    public function __construct(
        protected TenantBackupService $backupService,
        protected SaasMailService $mailService
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

        // Send email notification to client & alert to Super Admin
        $ticket->load('user');
        $this->mailService->sendSupportTicketOpened($ticket, $validated['message']);

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

        $reply = SupportReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $validated['message'],
            'is_staff_reply' => $isStaff,
        ]);

        $ticket->update([
            'status' => $isStaff ? 'answered' : 'open',
        ]);

        // Send email notification on ticket reply
        $ticket->load('user');
        $reply->load('user');
        $this->mailService->sendSupportTicketReplied($ticket, $reply);

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

    // =========================================================================
    // 3. SUPER ADMIN EMAIL & SMTP SETUP + NOTIFICATION TRIGGERS (/admin/email-settings)
    // =========================================================================

    public function emailSettings()
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can configure platform Email & SMTP settings.');
        }

        $mailConfig = $this->mailService->getMailConfig();
        $emailLogs = $this->mailService->getRecentEmailLogs();
        $pendingVerifyTenants = Tenant::with('owner')
            ->where('status', 'pending_verification')
            ->latest('id')
            ->take(10)
            ->get();

        return view('admin.email-settings', compact('mailConfig', 'emailLogs', 'pendingVerifyTenants', 'user'));
    }

    public function updateEmailSettings(Request $request)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();
        if (!$user->isSuperAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'mail_mailer' => 'required|in:smtp,log,sendmail,ses,postmark,resend',
            'mail_host' => 'nullable|string|max:190',
            'mail_port' => 'nullable|integer|min:1|max:65535',
            'mail_encryption' => 'required|in:tls,ssl,none',
            'mail_username' => 'nullable|string|max:190',
            'mail_password' => 'nullable|string|max:255',
            'mail_from_address' => 'required|email|max:190',
            'mail_from_name' => 'required|string|max:160',
            'mail_reply_to' => 'nullable|email|max:190',
            'mail_admin_alert_email' => 'nullable|email|max:190',
            'mail_brand_color' => 'nullable|string|max:20',
            'mail_footer_text' => 'nullable|string|max:255',
        ]);

        // Preserve existing SMTP password if left blank and already saved
        $existingPassword = Setting::getValue('mail_password');
        if (($validated['mail_password'] === null || $validated['mail_password'] === '') && $existingPassword && !$request->boolean('clear_password')) {
            $validated['mail_password'] = $existingPassword;
        }

        $cleanHost = trim((string) ($validated['mail_host'] ?? 'smtp.gmail.com'));
        $cleanUsername = trim((string) ($validated['mail_username'] ?? ''));
        $cleanFromAddress = trim($validated['mail_from_address']);

        $placeholderEmails = ['hello@example.com', 'noreply@sitebuilder.com', 'noreply@example.com', 'admin@example.com'];
        $strictHosts = ['smtp.hostinger.com', 'smtp.titan.email', 'smtp.gmail.com'];

        // Prevent 553 5.7.1 "Sender address rejected: not owned by user" on Hostinger / Titan / Gmail or when placeholder is left in From field
        if (
            filter_var($cleanUsername, FILTER_VALIDATE_EMAIL) &&
            (in_array(strtolower($cleanFromAddress), $placeholderEmails, true) || in_array(strtolower($cleanHost), $strictHosts, true))
        ) {
            $cleanFromAddress = $cleanUsername;
        }

        $cleanReplyTo = trim((string) ($validated['mail_reply_to'] ?: $cleanFromAddress));
        if (in_array(strtolower($cleanReplyTo), $placeholderEmails, true)) {
            $cleanReplyTo = $cleanFromAddress;
        }

        $cleanAdminAlert = trim((string) ($validated['mail_admin_alert_email'] ?: $cleanFromAddress));
        if (in_array(strtolower($cleanAdminAlert), $placeholderEmails, true)) {
            $cleanAdminAlert = $cleanFromAddress;
        }

        $cleanFromName = trim($validated['mail_from_name']);
        if ($cleanFromName === '' || $cleanFromName === 'Laravel') {
            $cleanFromName = Setting::getValue('site_name', 'SiteBuilder SaaS');
        }

        $settingsToSave = [
            'mail_mailer' => $validated['mail_mailer'],
            'mail_host' => $cleanHost,
            'mail_port' => (string) ($validated['mail_port'] ?? 587),
            'mail_encryption' => $validated['mail_encryption'],
            'mail_username' => $cleanUsername,
            'mail_password' => (string) ($validated['mail_password'] ?? ''),
            'mail_from_address' => $cleanFromAddress,
            'mail_from_name' => $cleanFromName,
            'mail_reply_to' => $cleanReplyTo,
            'mail_admin_alert_email' => $cleanAdminAlert,
            'mail_brand_color' => $validated['mail_brand_color'] ?: '#EC134A',
            'mail_footer_text' => $validated['mail_footer_text'] ?: '',

            // Toggle Switches
            'mail_notify_registration_verify' => $request->boolean('mail_notify_registration_verify') ? '1' : '0',
            'mail_notify_welcome_provisioned' => $request->boolean('mail_notify_welcome_provisioned') ? '1' : '0',
            'mail_notify_password_reset' => $request->boolean('mail_notify_password_reset') ? '1' : '0',
            'mail_notify_invoice_payment' => $request->boolean('mail_notify_invoice_payment') ? '1' : '0',
            'mail_notify_support_ticket' => $request->boolean('mail_notify_support_ticket') ? '1' : '0',
            'mail_notify_domain_ssl' => $request->boolean('mail_notify_domain_ssl') ? '1' : '0',
            'mail_notify_reseller_payout' => $request->boolean('mail_notify_reseller_payout') ? '1' : '0',
            'mail_notify_admin_alerts' => $request->boolean('mail_notify_admin_alerts') ? '1' : '0',
        ];

        foreach ($settingsToSave as $key => $val) {
            Setting::setValue($key, $val, 'mail');
        }

        // Also update global support_email to keep branding consistent
        Setting::setValue('support_email', $settingsToSave['mail_reply_to'], 'general');

        if ($request->boolean('sync_to_env')) {
            $this->mailService->syncToEnv($settingsToSave);
        }

        $this->mailService->applyMailConfig();

        ActivityLog::record(
            'SaaS Email & SMTP Configuration Updated',
            "Driver: " . strtoupper($settingsToSave['mail_mailer']) . " • From: {$settingsToSave['mail_from_name']} <{$settingsToSave['mail_from_address']}>",
            'bi-envelope-at-fill',
            '#FFE4EC',
            '#EC134A'
        );

        return back()->with('success', "Email & SMTP settings saved! All client registration confirmation links and notifications will now be sent from [{$settingsToSave['mail_from_name']} <{$settingsToSave['mail_from_address']}>].");
    }

    public function sendTestEmail(Request $request)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();
        if (!$user->isSuperAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'test_email' => 'required|email|max:190',
            'test_note' => 'nullable|string|max:500',
        ]);

        $res = $this->mailService->sendTestEmail($validated['test_email'], $validated['test_note'] ?? null);

        if ($res['sent']) {
            return back()->with('success', $res['message']);
        }

        return back()->withErrors([
            'smtp_test' => "SMTP Test Failed ({$res['mailer']}): {$res['message']}",
        ]);
    }

    public function clearEmailLogs()
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();
        if (!$user->isSuperAdmin()) {
            abort(403);
        }

        Setting::setValue('saas_email_logs_json', json_encode([]), 'mail');

        return back()->with('success', 'Email delivery logs cleared.');
    }
}
