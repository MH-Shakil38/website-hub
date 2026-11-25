<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Domain;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PayoutRequest;
use App\Models\Reseller;
use App\Models\Setting;
use App\Models\SupportReply;
use App\Models\SupportTicket;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

class SaasMailService
{
    /**
     * Load all SaaS Email & SMTP configuration values from core_settings (with .env fallback).
     */
    public function getMailConfig(): array
    {
        $stored = [];
        try {
            if (Schema::connection('central')->hasTable('core_settings')) {
                $stored = Setting::pluck('value', 'key')->toArray();
            }
        } catch (\Throwable) {
            $stored = [];
        }

        $siteName = $stored['site_name'] ?? config('app.name', 'SiteBuilder SaaS');
        if ($siteName === 'Laravel') {
            $siteName = 'SiteBuilder SaaS';
        }

        $smtpUsername = trim((string) ($stored['mail_username'] ?? env('MAIL_USERNAME', '')));
        $smtpHost = strtolower(trim((string) ($stored['mail_host'] ?? env('MAIL_HOST', 'smtp.gmail.com'))));
        $rawFromAddress = trim((string) ($stored['mail_from_address'] ?? env('MAIL_FROM_ADDRESS', '')));

        $placeholderEmails = ['', 'hello@example.com', 'noreply@sitebuilder.com', 'noreply@example.com', 'admin@example.com'];
        $strictHosts = ['smtp.hostinger.com', 'smtp.titan.email', 'smtp.gmail.com'];

        // If SMTP username is a valid email and From address is a placeholder (or on Hostinger/Titan/Gmail where From must match Username), auto-use SMTP username
        if (
            filter_var($smtpUsername, FILTER_VALIDATE_EMAIL) &&
            (in_array(strtolower($rawFromAddress), $placeholderEmails, true) || in_array($smtpHost, $strictHosts, true))
        ) {
            $defaultFromAddress = $smtpUsername;
        } else {
            $defaultFromAddress = !in_array(strtolower($rawFromAddress), $placeholderEmails, true)
                ? $rawFromAddress
                : ($smtpUsername ?: 'noreply@sitebuilder.com');
        }

        $rawFromName = trim((string) ($stored['mail_from_name'] ?? env('MAIL_FROM_NAME', '')));
        $defaultFromName = ($rawFromName !== '' && $rawFromName !== 'Laravel') ? $rawFromName : $siteName;

        $rawReplyTo = trim((string) ($stored['mail_reply_to'] ?? ''));
        $replyTo = (!in_array(strtolower($rawReplyTo), $placeholderEmails, true)) ? $rawReplyTo : $defaultFromAddress;

        $rawAdminAlert = trim((string) ($stored['mail_admin_alert_email'] ?? ($stored['support_email'] ?? '')));
        $adminAlert = (!in_array(strtolower($rawAdminAlert), $placeholderEmails, true)) ? $rawAdminAlert : $defaultFromAddress;

        return [
            'mail_mailer' => $stored['mail_mailer'] ?? env('MAIL_MAILER', 'smtp'),
            'mail_host' => $stored['mail_host'] ?? env('MAIL_HOST', 'smtp.gmail.com'),
            'mail_port' => (int) ($stored['mail_port'] ?? env('MAIL_PORT', 587)),
            'mail_encryption' => $stored['mail_encryption'] ?? (env('MAIL_SCHEME') ?: 'tls'),
            'mail_username' => $smtpUsername,
            'mail_password' => $stored['mail_password'] ?? env('MAIL_PASSWORD', ''),
            'mail_from_address' => $defaultFromAddress,
            'mail_from_name' => $defaultFromName,
            'mail_reply_to' => $replyTo,
            'mail_admin_alert_email' => $adminAlert,
            'mail_brand_color' => $stored['mail_brand_color'] ?? '#EC134A',
            'mail_footer_text' => $stored['mail_footer_text'] ?? "Sent automatically by {$siteName} Cloud Platform.",

            // Notification Triggers (Default: enabled = '1')
            'mail_notify_registration_verify' => ($stored['mail_notify_registration_verify'] ?? '1') === '1',
            'mail_notify_welcome_provisioned' => ($stored['mail_notify_welcome_provisioned'] ?? '1') === '1',
            'mail_notify_password_reset' => ($stored['mail_notify_password_reset'] ?? '1') === '1',
            'mail_notify_invoice_payment' => ($stored['mail_notify_invoice_payment'] ?? '1') === '1',
            'mail_notify_support_ticket' => ($stored['mail_notify_support_ticket'] ?? '1') === '1',
            'mail_notify_domain_ssl' => ($stored['mail_notify_domain_ssl'] ?? '1') === '1',
            'mail_notify_reseller_payout' => ($stored['mail_notify_reseller_payout'] ?? '1') === '1',
            'mail_notify_admin_alerts' => ($stored['mail_notify_admin_alerts'] ?? '1') === '1',
        ];
    }

    /**
     * Dynamically apply the Super Admin's saved SMTP / Mail configuration to Laravel's runtime config.
     */
    public function applyMailConfig(?array $cfg = null): array
    {
        $cfg = $cfg ?: $this->getMailConfig();

        $encryption = strtolower((string) ($cfg['mail_encryption'] ?? 'tls'));
        $port = (int) ($cfg['mail_port'] ?? 587);
        $scheme = null;
        if ($encryption === 'ssl' || $port === 465) {
            $scheme = 'smtps';
        } elseif ($encryption === 'tls') {
            $scheme = 'smtp';
        }

        config([
            'mail.default' => $cfg['mail_mailer'] ?: 'smtp',
            'mail.mailers.smtp.transport' => 'smtp',
            'mail.mailers.smtp.host' => $cfg['mail_host'] ?: '127.0.0.1',
            'mail.mailers.smtp.port' => $port,
            'mail.mailers.smtp.scheme' => $scheme,
            'mail.mailers.smtp.encryption' => $encryption === 'none' ? null : $encryption,
            'mail.mailers.smtp.username' => $cfg['mail_username'] ?: null,
            'mail.mailers.smtp.password' => $cfg['mail_password'] ?: null,
            'mail.from.address' => $cfg['mail_from_address'] ?: 'noreply@sitebuilder.com',
            'mail.from.name' => $cfg['mail_from_name'] ?: 'SiteBuilder SaaS',
        ]);

        try {
            Mail::purge('smtp');
            Mail::purge($cfg['mail_mailer'] ?: 'smtp');
        } catch (\Throwable) {
            // Ignore purge exceptions before mail manager boots
        }

        return $cfg;
    }

    /**
     * Sync SMTP settings to the project's .env file when requested by Super Admin.
     */
    public function syncToEnv(array $cfg): void
    {
        $envPath = base_path('.env');
        if (!File::exists($envPath) || !File::isWritable($envPath)) {
            return;
        }

        $envContent = File::get($envPath);

        $scheme = strtolower((string) ($cfg['mail_encryption'] ?? 'tls'));
        $pairs = [
            'MAIL_MAILER' => $cfg['mail_mailer'] ?? 'smtp',
            'MAIL_HOST' => $cfg['mail_host'] ?? 'smtp.gmail.com',
            'MAIL_PORT' => (string) ($cfg['mail_port'] ?? 587),
            'MAIL_USERNAME' => $cfg['mail_username'] ?? '',
            'MAIL_PASSWORD' => $cfg['mail_password'] ?? '',
            'MAIL_SCHEME' => $scheme === 'none' ? 'null' : $scheme,
            'MAIL_FROM_ADDRESS' => $cfg['mail_from_address'] ?? 'noreply@sitebuilder.com',
            'MAIL_FROM_NAME' => $cfg['mail_from_name'] ?? 'SiteBuilder SaaS',
        ];

        foreach ($pairs as $key => $val) {
            $formatted = (str_contains((string) $val, ' ') || str_contains((string) $val, '#'))
                ? '"' . addslashes((string) $val) . '"'
                : (string) $val;

            if (preg_match("/^{$key}=/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$formatted}", $envContent);
            } else {
                $envContent .= "\n{$key}={$formatted}";
            }
        }

        File::put($envPath, $envContent);
    }

    /**
     * Core email dispatcher using the unified SaaS HTML template.
     * Automatically logs delivery status and falls back to 'log' transport during normal user flows if SMTP is unreachable.
     *
     * @return array{sent: bool, status: string, mailer: string, message: string}
     */
    public function send(
        string $toEmail,
        string $subject,
        array $templateData = [],
        string $eventType = 'Notification',
        bool $strictTest = false
    ): array {
        $cfg = $this->applyMailConfig();
        $brandName = Setting::getValue('site_name', 'SiteBuilder SaaS');

        $payload = array_merge([
            'subject' => $subject,
            'brandName' => $brandName,
            'brandColor' => $cfg['mail_brand_color'] ?? '#EC134A',
            'footerText' => $cfg['mail_footer_text'] ?? "Sent automatically by {$brandName}",
            'supportEmail' => $cfg['mail_reply_to'] ?: $cfg['mail_from_address'],
            'recipientName' => 'Valued Client',
            'headline' => $subject,
            'badgeText' => $eventType,
            'introLines' => [],
            'details' => [],
            'actionText' => null,
            'actionUrl' => null,
            'outroLines' => [],
        ], $templateData);

        $htmlBody = View::make('emails.saas-notification', $payload)->render();
        $activeMailer = $cfg['mail_mailer'] ?: 'smtp';

        // Check if SMTP was chosen without a host or username during non-strict flows
        $smtpReady = !empty($cfg['mail_host']) && ($activeMailer !== 'smtp' || !empty($cfg['mail_username']) || in_array($cfg['mail_host'], ['127.0.0.1', 'localhost', 'mailpit', 'mailhog'], true));

        try {
            if (!$strictTest && $activeMailer === 'smtp' && !$smtpReady) {
                // Graceful fallback to log driver until Super Admin enters SMTP credentials
                Mail::mailer('log')->html($htmlBody, function ($message) use ($toEmail, $subject, $cfg) {
                    $message->to($toEmail)
                        ->from($cfg['mail_from_address'], $cfg['mail_from_name'])
                        ->subject($subject);
                    if (!empty($cfg['mail_reply_to'])) {
                        $message->replyTo($cfg['mail_reply_to'], $cfg['mail_from_name']);
                    }
                });

                $this->recordEmailLog($toEmail, $subject, $eventType, 'log (fallback)', 'logged', 'SMTP username not set yet; logged to storage/logs/laravel.log', $payload['actionUrl'] ?? null);

                return [
                    'sent' => true,
                    'status' => 'logged',
                    'mailer' => 'log',
                    'message' => "Email logged to laravel.log (configure SMTP username/password to send live via {$cfg['mail_host']}).",
                ];
            }

            Mail::mailer($activeMailer)->html($htmlBody, function ($message) use ($toEmail, $subject, $cfg) {
                $message->to($toEmail)
                    ->from($cfg['mail_from_address'], $cfg['mail_from_name'])
                    ->subject($subject);

                if (!empty($cfg['mail_reply_to'])) {
                    $message->replyTo($cfg['mail_reply_to'], $cfg['mail_from_name']);
                }
            });

            $statusLabel = $activeMailer === 'log' ? 'logged' : 'sent';
            $this->recordEmailLog($toEmail, $subject, $eventType, $activeMailer, $statusLabel, null, $payload['actionUrl'] ?? null);

            return [
                'sent' => true,
                'status' => $statusLabel,
                'mailer' => $activeMailer,
                'message' => $activeMailer === 'log'
                    ? "Email recorded in log driver for [{$toEmail}]."
                    : "Email sent successfully to [{$toEmail}] via [{$activeMailer}] ({$cfg['mail_from_address']}).",
            ];
        } catch (\Throwable $e) {
            $errorMsg = $e->getMessage();

            // During normal client flows (not strict SMTP test), also write to log mailer so the link is never lost
            if (!$strictTest) {
                try {
                    Mail::mailer('log')->html($htmlBody, function ($message) use ($toEmail, $subject, $cfg) {
                        $message->to($toEmail)
                            ->from($cfg['mail_from_address'], $cfg['mail_from_name'])
                            ->subject($subject);
                    });
                } catch (\Throwable) {
                }
            }

            Log::warning("SaaS Email Delivery Error [{$toEmail} - {$subject}]: {$errorMsg}");
            $this->recordEmailLog($toEmail, $subject, $eventType, $activeMailer, 'failed', $errorMsg, $payload['actionUrl'] ?? null);

            return [
                'sent' => false,
                'status' => 'failed',
                'mailer' => $activeMailer,
                'message' => $errorMsg,
            ];
        }
    }

    /**
     * Store the last 40 email delivery events in core_settings for the Super Admin Email Logs table.
     */
    public function recordEmailLog(
        string $to,
        string $subject,
        string $eventType,
        string $mailer,
        string $status,
        ?string $error = null,
        ?string $actionUrl = null
    ): void {
        try {
            $logs = $this->getRecentEmailLogs();
            array_unshift($logs, [
                'id' => strtoupper(substr(md5(uniqid('', true)), 0, 8)),
                'to' => $to,
                'subject' => $subject,
                'event' => $eventType,
                'mailer' => $mailer,
                'status' => $status,
                'error' => $error ? mb_substr($error, 0, 220) : null,
                'action_url' => $actionUrl,
                'sent_at' => now()->format('Y-m-d H:i:s'),
            ]);

            $logs = array_slice($logs, 0, 40);
            Setting::setValue('saas_email_logs_json', json_encode($logs), 'mail');
        } catch (\Throwable) {
            // Ignore if DB not ready
        }
    }

    /**
     * Retrieve recent email delivery logs for the Super Admin Email Setup dashboard.
     */
    public function getRecentEmailLogs(): array
    {
        try {
            $raw = Setting::getValue('saas_email_logs_json');
            if (!$raw) {
                return [];
            }
            $decoded = json_decode((string) $raw, true);
            return is_array($decoded) ? $decoded : [];
        } catch (\Throwable) {
            return [];
        }
    }

    // =========================================================================
    // 1. REGISTRATION & THEME INSTALLATION CONFIRMATION LINK EMAIL
    // =========================================================================

    public function sendRegistrationVerification(Tenant $tenant, User $user, string $verifyUrl): array
    {
        $cfg = $this->getMailConfig();
        if (!$cfg['mail_notify_registration_verify']) {
            return ['sent' => false, 'status' => 'disabled', 'mailer' => $cfg['mail_mailer'], 'message' => 'Registration verification email notification is disabled.'];
        }

        $baseDomain = config('app.central_domains')[0] ?? 'localhost';
        $fullSubdomain = "{$tenant->subdomain}.{$baseDomain}";
        $brandName = Setting::getValue('site_name', 'SiteBuilder SaaS');

        $res = $this->send(
            $user->email,
            "Confirm Your Email & Launch {$tenant->name} ({$tenant->theme_module})",
            [
                'recipientName' => $user->name,
                'badgeText' => 'Action Required • Email Confirmation',
                'headline' => "Confirm Your Email to Install {$tenant->theme_module}",
                'introLines' => [
                    "Thank you for registering on <strong>{$brandName}</strong>! Your new <strong>{$tenant->theme_module}</strong> website is ready to be provisioned in its own isolated database.",
                    "Please click the confirmation button below to verify your email address and immediately launch your live website and client admin panel:",
                ],
                'details' => [
                    'Website Name' => $tenant->name,
                    'Industry Theme' => $tenant->theme_module,
                    'Reserved Website URL' => $fullSubdomain,
                    'Account Email' => $user->email,
                    'Selected Plan' => $tenant->plan?->name ?? '14-Day Free Trial',
                ],
                'actionText' => 'Verify Email & Install Website Now',
                'actionUrl' => $verifyUrl,
                'outroLines' => [
                    'Once you click the confirmation link above, our system will automatically create your isolated database, load demo content, and sign you into your dashboard.',
                ],
            ],
            'Registration Confirmation'
        );

        // Optional Super Admin Alert
        if ($cfg['mail_notify_admin_alerts'] && !empty($cfg['mail_admin_alert_email']) && $cfg['mail_admin_alert_email'] !== $user->email) {
            $this->send(
                $cfg['mail_admin_alert_email'],
                "[Admin Alert] New Client Registration: {$tenant->name} ({$user->email})",
                [
                    'recipientName' => 'Super Admin',
                    'badgeText' => 'Admin Alert • New Registration',
                    'headline' => "New Client Registration Awaiting Verification",
                    'introLines' => [
                        "A new client has registered for the <strong>{$tenant->theme_module}</strong> theme and was sent an email confirmation link.",
                    ],
                    'details' => [
                        'Client Name' => $user->name,
                        'Client Email' => $user->email,
                        'Client Phone' => $user->phone ?: 'N/A',
                        'Website Name' => $tenant->name,
                        'Subdomain' => $fullSubdomain,
                        'Theme Module' => $tenant->theme_module,
                    ],
                    'actionText' => 'View Tenants in Super Admin',
                    'actionUrl' => route('admin.tenants.index'),
                ],
                'Admin Alert'
            );
        }

        return $res;
    }

    // =========================================================================
    // 2. WELCOME & LIVE WEBSITE PROVISIONED EMAIL
    // =========================================================================

    public function sendWelcomeWebsiteProvisioned(Tenant $tenant, User $user, ?string $plainPassword = null): array
    {
        $cfg = $this->getMailConfig();
        if (!$cfg['mail_notify_welcome_provisioned']) {
            return ['sent' => false, 'status' => 'disabled', 'mailer' => $cfg['mail_mailer'], 'message' => 'Welcome email notification is disabled.'];
        }

        $baseDomain = config('app.central_domains')[0] ?? 'localhost';
        $primaryDomain = $tenant->domains()->where('is_primary', true)->value('domain') ?: "{$tenant->subdomain}.{$baseDomain}";
        $scheme = request()->getScheme() ?: 'http';
        $liveUrl = "{$scheme}://{$primaryDomain}";

        $details = [
            'Website Name' => $tenant->name,
            'Theme Installed' => $tenant->theme_module,
            'Live Website URL' => $liveUrl,
            'Isolated Database' => $tenant->db_name ?: 'Provisioned',
            'Login Email' => $user->email,
            'Subscription Status' => strtoupper($tenant->status),
        ];

        if ($plainPassword) {
            $details['Temporary Password'] = $plainPassword;
        }

        return $this->send(
            $user->email,
            "Your Website {$tenant->name} is Now Live!",
            [
                'recipientName' => $user->name,
                'badgeText' => 'Website Live & Provisioned',
                'headline' => "Congratulations! {$tenant->name} is Live",
                'introLines' => [
                    "Your email has been verified and your dedicated <strong>{$tenant->theme_module}</strong> website database has been provisioned with complete starter content.",
                    "You can now visit your live website or open your Admin Dashboard to customize pages, menus, sliders, domain settings, and branding.",
                ],
                'details' => $details,
                'actionText' => 'Open My Website Admin Dashboard',
                'actionUrl' => route('admin.dashboard'),
                'outroLines' => [
                    "Live Frontend Link: <a href=\"{$liveUrl}\">{$liveUrl}</a>",
                ],
            ],
            'Website Provisioned'
        );
    }

    // =========================================================================
    // 3. ACCOUNT CREATED BY SUPER ADMIN / RESELLER (WITH CREDENTIALS)
    // =========================================================================

    public function sendAccountCreatedCredentials(User $newUser, string $plainPassword, ?User $actor = null): array
    {
        $cfg = $this->getMailConfig();
        if (!$cfg['mail_notify_welcome_provisioned']) {
            return ['sent' => false, 'status' => 'disabled', 'mailer' => $cfg['mail_mailer'], 'message' => 'Welcome email notification is disabled.'];
        }

        $brandName = Setting::getValue('site_name', 'SiteBuilder SaaS');
        $roleLabel = $newUser->isReseller() ? 'Reseller Partner' : 'Client Website Owner';

        return $this->send(
            $newUser->email,
            "Your {$roleLabel} Account on {$brandName} is Ready",
            [
                'recipientName' => $newUser->name,
                'badgeText' => "New {$roleLabel} Account",
                'headline' => "Welcome to {$brandName}!",
                'introLines' => [
                    "An account has been created for you on <strong>{$brandName}</strong>" . ($actor ? " by <strong>{$actor->name}</strong>" : '') . ".",
                    "Use the login credentials below to sign in to your dashboard:",
                ],
                'details' => [
                    'Account Role' => $roleLabel,
                    'Login URL' => route('login'),
                    'Email Address' => $newUser->email,
                    'Password' => $plainPassword,
                ],
                'actionText' => 'Sign In to Dashboard',
                'actionUrl' => route('login'),
                'outroLines' => [
                    'For your security, we recommend changing your password from the Settings page after your first sign-in.',
                ],
            ],
            'Account Credentials'
        );
    }

    // =========================================================================
    // 4. FORGOT PASSWORD RESET LINK & EMAIL VERIFICATION LINK
    // =========================================================================

    public function sendPasswordResetLink(User $user, string $resetUrl): array
    {
        $cfg = $this->getMailConfig();
        if (!$cfg['mail_notify_password_reset']) {
            return ['sent' => false, 'status' => 'disabled', 'mailer' => $cfg['mail_mailer'], 'message' => 'Password reset email notification is disabled.'];
        }

        return $this->send(
            $user->email,
            'Reset Your Account Password',
            [
                'recipientName' => $user->name,
                'badgeText' => 'Security • Password Reset',
                'headline' => 'Password Reset Request',
                'introLines' => [
                    'We received a request to reset the password for your account associated with <strong>' . e($user->email) . '</strong>.',
                    'Click the button below to choose a new password:',
                ],
                'details' => [
                    'Account Email' => $user->email,
                    'Requested At' => now()->format('M d, Y h:i A'),
                ],
                'actionText' => 'Reset My Password',
                'actionUrl' => $resetUrl,
                'outroLines' => [
                    'If you did not request a password reset, no further action is required and your password will remain unchanged.',
                ],
            ],
            'Password Reset'
        );
    }

    public function sendEmailVerificationLink(User $user, string $verifyUrl): array
    {
        return $this->send(
            $user->email,
            'Verify Your Email Address',
            [
                'recipientName' => $user->name,
                'badgeText' => 'Email Verification',
                'headline' => 'Verify Your Email Address',
                'introLines' => [
                    'Please click the button below to confirm that <strong>' . e($user->email) . '</strong> is your active email address.',
                ],
                'actionText' => 'Verify Email Address',
                'actionUrl' => $verifyUrl,
            ],
            'Email Verification'
        );
    }

    // =========================================================================
    // 5. BILLING: INVOICE CREATED, MANUAL PAYMENT, APPROVED & REJECTED
    // =========================================================================

    public function sendInvoiceGenerated(Invoice $invoice): array
    {
        $cfg = $this->getMailConfig();
        if (!$cfg['mail_notify_invoice_payment'] || !$invoice->user) {
            return ['sent' => false, 'status' => 'disabled', 'mailer' => $cfg['mail_mailer'], 'message' => 'Invoice email disabled.'];
        }

        return $this->send(
            $invoice->user->email,
            "Invoice [{$invoice->invoice_number}] Generated - {$invoice->currency} {$invoice->total}",
            [
                'recipientName' => $invoice->user->name,
                'badgeText' => 'Billing • New Invoice',
                'headline' => "New Invoice #{$invoice->invoice_number}",
                'introLines' => [
                    "A new invoice has been generated for your subscription plan <strong>" . e($invoice->plan?->name ?? 'SaaS Plan') . "</strong>.",
                    "You can complete payment online via SSLCommerz or submit a manual bKash / Nagad / Bank transaction from your billing dashboard.",
                ],
                'details' => [
                    'Invoice Number' => $invoice->invoice_number,
                    'Plan / Package' => $invoice->plan?->name ?? 'Subscription',
                    'Website' => $invoice->tenant?->name ?? 'Account Subscription',
                    'Billing Cycle' => ucfirst((string) $invoice->billing_cycle),
                    'Total Due' => "{$invoice->currency} " . number_format((float) $invoice->total, 2),
                    'Due Date' => $invoice->due_date ? $invoice->due_date->format('M d, Y') : now()->addDays(3)->format('M d, Y'),
                ],
                'actionText' => 'View & Pay Invoice',
                'actionUrl' => route('admin.billing.index', ['pay_invoice' => $invoice->id]),
            ],
            'Invoice Generated'
        );
    }

    public function sendManualPaymentSubmitted(Payment $payment, Invoice $invoice): array
    {
        $cfg = $this->getMailConfig();
        $user = $invoice->user ?? $payment->user;
        if (!$cfg['mail_notify_invoice_payment'] || !$user) {
            return ['sent' => false, 'status' => 'disabled', 'mailer' => $cfg['mail_mailer'], 'message' => 'Payment email disabled.'];
        }

        $res = $this->send(
            $user->email,
            "Payment Received (Pending Verification) - Invoice [{$invoice->invoice_number}]",
            [
                'recipientName' => $user->name,
                'badgeText' => 'Payment Under Review',
                'headline' => 'We Received Your Manual Payment Details',
                'introLines' => [
                    "Thank you! Your manual payment submission for Invoice <strong>{$invoice->invoice_number}</strong> has been received and is awaiting Super Admin verification.",
                ],
                'details' => [
                    'Invoice Number' => $invoice->invoice_number,
                    'Payment Method' => $payment->method,
                    'Sender Number' => $payment->sender_number ?: 'N/A',
                    'Transaction ID' => $payment->gateway_txn_id,
                    'Amount' => "{$payment->currency} " . number_format((float) $payment->amount, 2),
                ],
                'actionText' => 'Check Payment Status',
                'actionUrl' => route('admin.billing.index'),
            ],
            'Manual Payment Submitted'
        );

        if ($cfg['mail_notify_admin_alerts'] && !empty($cfg['mail_admin_alert_email'])) {
            $this->send(
                $cfg['mail_admin_alert_email'],
                "[Admin Alert] Pending Manual Payment: {$payment->currency} {$payment->amount} ({$payment->method})",
                [
                    'recipientName' => 'Super Admin',
                    'badgeText' => 'Admin Action Required • Payment Approval',
                    'headline' => "Manual Payment Submitted for {$invoice->invoice_number}",
                    'introLines' => [
                        "Client <strong>" . e($user->name) . " ({$user->email})</strong> submitted a manual payment that requires approval.",
                    ],
                    'details' => [
                        'Client' => "{$user->name} ({$user->email})",
                        'Invoice' => $invoice->invoice_number,
                        'Method' => $payment->method,
                        'Sender Number' => $payment->sender_number ?: 'N/A',
                        'Transaction ID' => $payment->gateway_txn_id,
                        'Amount' => "{$payment->currency} " . number_format((float) $payment->amount, 2),
                    ],
                    'actionText' => 'Review & Approve Payment',
                    'actionUrl' => route('admin.billing.index'),
                ],
                'Admin Alert'
            );
        }

        return $res;
    }

    public function sendPaymentApproved(Payment $payment, ?Invoice $invoice = null): array
    {
        $cfg = $this->getMailConfig();
        $invoice = $invoice ?: $payment->invoice;
        $user = $invoice?->user ?: $payment->user;

        if (!$cfg['mail_notify_invoice_payment'] || !$user) {
            return ['sent' => false, 'status' => 'disabled', 'mailer' => $cfg['mail_mailer'], 'message' => 'Payment email disabled.'];
        }

        return $this->send(
            $user->email,
            "Payment Confirmed & Subscription Activated - Invoice [" . ($invoice?->invoice_number ?? $payment->id) . "]",
            [
                'recipientName' => $user->name,
                'badgeText' => 'Payment Confirmed • Active',
                'headline' => 'Your Payment is Approved & Subscription is Active!',
                'introLines' => [
                    "Great news! Your payment of <strong>{$payment->currency} " . number_format((float) $payment->amount, 2) . "</strong> has been verified and your subscription is now active.",
                ],
                'details' => [
                    'Invoice Number' => $invoice?->invoice_number ?? 'N/A',
                    'Plan / Package' => $invoice?->plan?->name ?? 'SaaS Plan',
                    'Website' => $invoice?->tenant?->name ?? 'Account Subscription',
                    'Transaction ID' => $payment->gateway_txn_id ?: ('PAY-' . $payment->id),
                    'Amount Paid' => "{$payment->currency} " . number_format((float) $payment->amount, 2),
                    'Status' => 'PAID & ACTIVE',
                ],
                'actionText' => $invoice ? 'View Printable Invoice' : 'Open Billing Dashboard',
                'actionUrl' => $invoice ? route('admin.billing.invoices.show', $invoice->id) : route('admin.billing.index'),
            ],
            'Payment Approved'
        );
    }

    public function sendPaymentRejected(Payment $payment, ?Invoice $invoice = null): array
    {
        $cfg = $this->getMailConfig();
        $invoice = $invoice ?: $payment->invoice;
        $user = $invoice?->user ?: $payment->user;

        if (!$cfg['mail_notify_invoice_payment'] || !$user) {
            return ['sent' => false, 'status' => 'disabled', 'mailer' => $cfg['mail_mailer'], 'message' => 'Payment email disabled.'];
        }

        return $this->send(
            $user->email,
            "Payment Verification Update - Invoice [" . ($invoice?->invoice_number ?? $payment->id) . "]",
            [
                'recipientName' => $user->name,
                'badgeText' => 'Payment Rejected',
                'headline' => 'Your Manual Payment Could Not Be Verified',
                'introLines' => [
                    "Unfortunately, we could not verify your manual payment submission (Transaction ID: <strong>" . e($payment->gateway_txn_id) . "</strong>).",
                    "Please review the note below and re-submit the correct transaction ID or contact support.",
                ],
                'details' => [
                    'Invoice Number' => $invoice?->invoice_number ?? 'N/A',
                    'Submitted Method' => $payment->method,
                    'Transaction ID' => $payment->gateway_txn_id,
                    'Admin Note' => $payment->admin_note ?: 'Transaction ID could not be matched.',
                ],
                'actionText' => 'Re-Submit Payment in Dashboard',
                'actionUrl' => route('admin.billing.index'),
            ],
            'Payment Rejected'
        );
    }

    // =========================================================================
    // 6. CUSTOM DOMAIN DNS VERIFIED & TENANT STATUS NOTIFICATIONS
    // =========================================================================

    public function sendCustomDomainVerified(Domain $domain): array
    {
        $cfg = $this->getMailConfig();
        $owner = $domain->tenant?->owner;
        if (!$cfg['mail_notify_domain_ssl'] || !$owner) {
            return ['sent' => false, 'status' => 'disabled', 'mailer' => $cfg['mail_mailer'], 'message' => 'Domain email disabled.'];
        }

        return $this->send(
            $owner->email,
            "Custom Domain Verified & SSL Active: {$domain->domain}",
            [
                'recipientName' => $owner->name,
                'badgeText' => 'Custom Domain • SSL Active',
                'headline' => "Your Domain {$domain->domain} is Verified!",
                'introLines' => [
                    "DNS ownership for <strong>{$domain->domain}</strong> has been verified and On-Demand Let's Encrypt HTTPS/SSL is now active for your website <strong>" . e($domain->tenant->name) . "</strong>.",
                ],
                'details' => [
                    'Custom Domain' => $domain->domain,
                    'Connected Website' => $domain->tenant->name,
                    'SSL Certificate' => strtoupper((string) $domain->ssl_status),
                    'Verified At' => now()->format('M d, Y h:i A'),
                ],
                'actionText' => 'Manage My Domains',
                'actionUrl' => route('admin.domains.index'),
            ],
            'Domain & SSL Verified'
        );
    }

    public function sendTenantStatusChanged(Tenant $tenant, string $newStatus): array
    {
        $cfg = $this->getMailConfig();
        $owner = $tenant->owner;
        if (!$cfg['mail_notify_welcome_provisioned'] || !$owner) {
            return ['sent' => false, 'status' => 'disabled', 'mailer' => $cfg['mail_mailer'], 'message' => 'Status email disabled.'];
        }

        $isSuspended = $newStatus === 'suspended';

        return $this->send(
            $owner->email,
            "Website Status Update: {$tenant->name} is now " . strtoupper($newStatus),
            [
                'recipientName' => $owner->name,
                'badgeText' => 'Website Status • ' . strtoupper($newStatus),
                'headline' => $isSuspended
                    ? "Your Website {$tenant->name} Has Been Suspended"
                    : "Your Website {$tenant->name} Has Been Reactivated!",
                'introLines' => [
                    $isSuspended
                        ? "Your website <strong>" . e($tenant->name) . "</strong> has been temporarily suspended. Please renew your subscription or contact support to restore instant access."
                        : "Your website <strong>" . e($tenant->name) . "</strong> is active and accessible online.",
                ],
                'details' => [
                    'Website Name' => $tenant->name,
                    'Theme Module' => $tenant->theme_module,
                    'Current Status' => strtoupper($newStatus),
                ],
                'actionText' => $isSuspended ? 'Renew Subscription in Billing' : 'Open Website Dashboard',
                'actionUrl' => $isSuspended ? route('admin.billing.index') : route('admin.dashboard'),
            ],
            'Tenant Status Update'
        );
    }

    // =========================================================================
    // 7. SUPPORT TICKETS & HELP DESK NOTIFICATIONS
    // =========================================================================

    public function sendSupportTicketOpened(SupportTicket $ticket, string $initialMessage): array
    {
        $cfg = $this->getMailConfig();
        $user = $ticket->user;
        if (!$cfg['mail_notify_support_ticket'] || !$user) {
            return ['sent' => false, 'status' => 'disabled', 'mailer' => $cfg['mail_mailer'], 'message' => 'Support email disabled.'];
        }

        $res = $this->send(
            $user->email,
            "Support Ticket [{$ticket->ticket_number}] Opened: {$ticket->subject}",
            [
                'recipientName' => $user->name,
                'badgeText' => 'Support Desk • Ticket Opened',
                'headline' => "We Received Your Support Ticket [{$ticket->ticket_number}]",
                'introLines' => [
                    "Your support request has been logged and our technical team will respond shortly.",
                    "<strong>Your Message:</strong><br>" . nl2br(e($initialMessage)),
                ],
                'details' => [
                    'Ticket Number' => $ticket->ticket_number,
                    'Subject' => $ticket->subject,
                    'Category' => ucfirst((string) $ticket->category),
                    'Priority' => strtoupper((string) $ticket->priority),
                ],
                'actionText' => 'View Support Ticket',
                'actionUrl' => route('admin.support.tickets'),
            ],
            'Support Ticket'
        );

        if ($cfg['mail_notify_admin_alerts'] && !empty($cfg['mail_admin_alert_email']) && $cfg['mail_admin_alert_email'] !== $user->email) {
            $this->send(
                $cfg['mail_admin_alert_email'],
                "[Support Alert] New Ticket [{$ticket->ticket_number}] from {$user->name}",
                [
                    'recipientName' => 'Super Admin Support',
                    'badgeText' => 'New Support Ticket • ' . strtoupper((string) $ticket->priority),
                    'headline' => "{$ticket->ticket_number}: {$ticket->subject}",
                    'introLines' => [
                        "<strong>From:</strong> " . e($user->name) . " ({$user->email})",
                        "<strong>Message:</strong><br>" . nl2br(e($initialMessage)),
                    ],
                    'details' => [
                        'Ticket Number' => $ticket->ticket_number,
                        'Category' => ucfirst((string) $ticket->category),
                        'Priority' => strtoupper((string) $ticket->priority),
                    ],
                    'actionText' => 'Reply to Ticket in Dashboard',
                    'actionUrl' => route('admin.support.tickets'),
                ],
                'Admin Support Alert'
            );
        }

        return $res;
    }

    public function sendSupportTicketReplied(SupportTicket $ticket, SupportReply $reply): array
    {
        $cfg = $this->getMailConfig();
        if (!$cfg['mail_notify_support_ticket']) {
            return ['sent' => false, 'status' => 'disabled', 'mailer' => $cfg['mail_mailer'], 'message' => 'Support email disabled.'];
        }

        $ticketOwner = $ticket->user;
        $replier = $reply->user;

        // If staff replied, notify the ticket owner (client/reseller)
        if ($reply->is_staff_reply && $ticketOwner) {
            return $this->send(
                $ticketOwner->email,
                "New Reply on Support Ticket [{$ticket->ticket_number}]: {$ticket->subject}",
                [
                    'recipientName' => $ticketOwner->name,
                    'badgeText' => 'Support Desk • Staff Reply',
                    'headline' => "New Reply on Ticket [{$ticket->ticket_number}]",
                    'introLines' => [
                        "Our support team has replied to your ticket <strong>" . e($ticket->subject) . "</strong>:",
                        "<div style=\"background:#F8FAFC;border-left:4px solid #EC134A;padding:12px 16px;border-radius:8px;\">" . nl2br(e($reply->message)) . "</div>",
                    ],
                    'details' => [
                        'Ticket Number' => $ticket->ticket_number,
                        'Replied By' => $replier?->name ?? 'Support Team',
                        'Status' => strtoupper((string) $ticket->status),
                    ],
                    'actionText' => 'View & Reply in Dashboard',
                    'actionUrl' => route('admin.support.tickets'),
                ],
                'Support Ticket Reply'
            );
        }

        // If client replied, alert Super Admin
        if (!$reply->is_staff_reply && $cfg['mail_notify_admin_alerts'] && !empty($cfg['mail_admin_alert_email'])) {
            return $this->send(
                $cfg['mail_admin_alert_email'],
                "[Support Reply] Client replied to [{$ticket->ticket_number}]",
                [
                    'recipientName' => 'Super Admin',
                    'badgeText' => 'Client Ticket Reply',
                    'headline' => "New Client Reply on [{$ticket->ticket_number}]",
                    'introLines' => [
                        "<strong>" . e($replier?->name ?? 'Client') . "</strong> replied:",
                        nl2br(e($reply->message)),
                    ],
                    'actionText' => 'Open Support Desk',
                    'actionUrl' => route('admin.support.tickets'),
                ],
                'Admin Support Alert'
            );
        }

        return ['sent' => false, 'status' => 'skipped', 'mailer' => $cfg['mail_mailer'], 'message' => 'No target recipient.'];
    }

    // =========================================================================
    // 8. RESELLER PAYOUT & KYC NOTIFICATIONS
    // =========================================================================

    public function sendResellerPayoutProcessed(PayoutRequest $payout): array
    {
        $cfg = $this->getMailConfig();
        $resellerUser = $payout->reseller?->user;
        if (!$cfg['mail_notify_reseller_payout'] || !$resellerUser) {
            return ['sent' => false, 'status' => 'disabled', 'mailer' => $cfg['mail_mailer'], 'message' => 'Reseller email disabled.'];
        }

        $isPaid = $payout->status === 'paid';

        return $this->send(
            $resellerUser->email,
            "Reseller Payout Request #{$payout->id} " . strtoupper($payout->status) . " (৳{$payout->amount})",
            [
                'recipientName' => $resellerUser->name,
                'badgeText' => 'Reseller Payout • ' . strtoupper($payout->status),
                'headline' => $isPaid
                    ? "Your Payout of ৳{$payout->amount} Has Been Disbursed!"
                    : "Update on Your Payout Request #{$payout->id}",
                'introLines' => [
                    $isPaid
                        ? "Your withdrawal request for <strong>৳" . number_format((float) $payout->amount, 2) . "</strong> via <strong>{$payout->method}</strong> has been processed and paid."
                        : "Your withdrawal request for <strong>৳" . number_format((float) $payout->amount, 2) . "</strong> was rejected and the full amount has been refunded to your Reseller Wallet.",
                ],
                'details' => [
                    'Payout ID' => '#' . $payout->id,
                    'Amount' => '৳' . number_format((float) $payout->amount, 2),
                    'Method' => $payout->method,
                    'Account Info' => $payout->account_info,
                    'Transaction Reference' => $payout->transaction_reference ?: 'N/A',
                    'Admin Note' => $payout->admin_note ?: 'Processed by Super Admin',
                ],
                'actionText' => 'Open Reseller Wallet & Payouts',
                'actionUrl' => route('admin.reseller.hub'),
            ],
            'Reseller Payout'
        );
    }

    public function sendResellerKycUpdated(Reseller $reseller): array
    {
        $cfg = $this->getMailConfig();
        $resellerUser = $reseller->user;
        if (!$cfg['mail_notify_reseller_payout'] || !$resellerUser) {
            return ['sent' => false, 'status' => 'disabled', 'mailer' => $cfg['mail_mailer'], 'message' => 'Reseller email disabled.'];
        }

        return $this->send(
            $resellerUser->email,
            "Reseller KYC Verification Status: " . strtoupper($reseller->kyc_status),
            [
                'recipientName' => $resellerUser->name,
                'badgeText' => 'Reseller KYC • ' . strtoupper($reseller->kyc_status),
                'headline' => "Your Reseller KYC Status is Now " . strtoupper($reseller->kyc_status),
                'introLines' => [
                    "Your Reseller Partner profile (<strong>" . e($reseller->company_name) . "</strong>) KYC status has been updated by the Super Admin.",
                ],
                'details' => [
                    'Company Name' => $reseller->company_name,
                    'KYC Status' => strtoupper($reseller->kyc_status),
                    'Site Quota' => $reseller->site_quota . ' Sites',
                    'Commission Rate' => $reseller->commission_rate . '%',
                ],
                'actionText' => 'Open Reseller Hub',
                'actionUrl' => route('admin.reseller.hub'),
            ],
            'Reseller KYC'
        );
    }

    // =========================================================================
    // 9. SUPER ADMIN ONE-CLICK TEST EMAIL
    // =========================================================================

    public function sendTestEmail(string $toEmail, ?string $customNote = null): array
    {
        $cfg = $this->getMailConfig();
        $brandName = Setting::getValue('site_name', 'SiteBuilder SaaS');

        return $this->send(
            $toEmail,
            "[Test Email] {$brandName} SMTP & Notification Configuration Verified",
            [
                'recipientName' => 'Super Admin',
                'badgeText' => 'SMTP Connection Verified',
                'headline' => 'Your SaaS Email & SMTP Configuration is Working!',
                'introLines' => [
                    "This is a live test email sent from your <strong>{$brandName}</strong> Super Admin Dashboard.",
                    "All client notifications — including <strong>Registration Confirmation Links</strong>, <strong>Welcome & Provisioned Website Emails</strong>, <strong>Password Reset Links</strong>, <strong>Invoices & Payment Approvals</strong>, <strong>Custom Domain SSL Alerts</strong>, and <strong>Support Ticket Replies</strong> — will be delivered using this configuration.",
                    $customNote ? ("<strong>Custom Test Note:</strong> " . e($customNote)) : 'Your sender identity and mail transport are properly configured.',
                ],
                'details' => [
                    'Active Mail Driver' => strtoupper((string) $cfg['mail_mailer']),
                    'SMTP Host & Port' => "{$cfg['mail_host']}:{$cfg['mail_port']} (" . strtoupper((string) $cfg['mail_encryption']) . ")",
                    'From Email Address' => $cfg['mail_from_address'],
                    'From Sender Name' => $cfg['mail_from_name'],
                    'Reply-To Email' => $cfg['mail_reply_to'],
                    'Tested At' => now()->format('M d, Y h:i:s A'),
                ],
                'actionText' => 'Return to Super Admin Email Setup',
                'actionUrl' => route('admin.email.settings'),
            ],
            'SMTP Test Email',
            true // strictTest = true so real SMTP errors are surfaced directly to Super Admin
        );
    }
}
