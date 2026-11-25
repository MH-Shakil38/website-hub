@extends('admin.layouts.master')

@section('title', 'Email & SMTP Setup - ' . ($globalSiteName ?? 'SiteBuilder'))

@section('admin_content')
    @php
        $enabledTriggersCount = collect([
            $mailConfig['mail_notify_registration_verify'] ?? true,
            $mailConfig['mail_notify_welcome_provisioned'] ?? true,
            $mailConfig['mail_notify_password_reset'] ?? true,
            $mailConfig['mail_notify_invoice_payment'] ?? true,
            $mailConfig['mail_notify_support_ticket'] ?? true,
            $mailConfig['mail_notify_domain_ssl'] ?? true,
            $mailConfig['mail_notify_reseller_payout'] ?? true,
            $mailConfig['mail_notify_admin_alerts'] ?? true,
        ])->filter()->count();
    @endphp

    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-pill px-3 py-1" style="background: #FFE4EC; color: #EC134A; font-weight: 700; font-size: 11px;">
                    <i class="bi bi-envelope-at-fill me-1"></i> SUPER ADMIN EMAIL &amp; SMTP GATEWAY
                </span>
            </div>
            <h4 class="fw-bold mb-1" style="color: #0F172A;">SaaS Email Setup &amp; Automated Client Notifications</h4>
            <p class="text-muted small mb-0">
                Configure your central sender email &amp; SMTP server so all client registration confirmation links, welcome emails, password resets, invoices, and support replies are sent from your official email.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.tenants.index') }}" class="btn btn-sm btn-outline-secondary fw-semibold px-3 py-2" style="border-radius: 10px;">
                <i class="bi bi-hdd-network me-1"></i> View Client Websites
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-3 border-0 shadow-sm mb-4">
            <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Please check the following errors:</div>
            <ul class="mb-0 small ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Top 3-Column Summary Strip -->
    <div class="sb-summary-strip mb-4">
        <div class="sb-summary-item">
            <div class="sb-summary-icon">
                <i class="bi bi-envelope-check-fill"></i>
            </div>
            <div>
                <div class="sb-summary-label">Official Sender Email</div>
                <div class="sb-summary-value">{{ $mailConfig['mail_from_address'] }}</div>
                <div style="font-size: 11px; color: #64748B; margin-top: 1px;">
                    From Name: <strong>{{ $mailConfig['mail_from_name'] }}</strong>
                </div>
            </div>
        </div>

        <div class="sb-summary-item">
            <div class="sb-summary-icon">
                <i class="bi bi-hdd-rack-fill"></i>
            </div>
            <div>
                <div class="sb-summary-label">Active Mail Transport</div>
                <div class="sb-summary-value">
                    {{ strtoupper($mailConfig['mail_mailer']) }}
                    @if($mailConfig['mail_mailer'] === 'smtp')
                        <span class="text-muted fw-normal" style="font-size: 13px;">({{ $mailConfig['mail_host'] }}:{{ $mailConfig['mail_port'] }})</span>
                    @endif
                </div>
                <div style="font-size: 11px; color: #64748B; margin-top: 1px;">
                    Encryption: <strong>{{ strtoupper($mailConfig['mail_encryption']) }}</strong> • Auth: <strong>{{ !empty($mailConfig['mail_username']) ? $mailConfig['mail_username'] : 'Not Set' }}</strong>
                </div>
            </div>
        </div>

        <div class="sb-summary-item">
            <div class="sb-summary-icon">
                <i class="bi bi-bell-fill"></i>
            </div>
            <div>
                <div class="sb-summary-label">Automated Event Triggers</div>
                <div class="sb-summary-value">{{ $enabledTriggersCount }} / 8 Active</div>
                <div style="font-size: 11px; color: #64748B; margin-top: 1px;">
                    Admin Alerts: <strong>{{ $mailConfig['mail_admin_alert_email'] }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column: SMTP Server, Sender Identity & Notification Toggles -->
        <div class="col-lg-7">
            <div class="sb-table-card p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 pb-3 border-bottom">
                    <div>
                        <h5 class="fw-bold mb-1" style="color: #0F172A;">
                            <i class="bi bi-sliders2-vertical text-danger me-1"></i> SMTP Server &amp; Sender Email Configuration
                        </h5>
                        <p class="text-muted small mb-0">Choose a quick preset or enter your custom SMTP server credentials below.</p>
                    </div>
                </div>

                <!-- 1-Click Quick SMTP Presets -->
                <div class="mb-4 p-3 rounded-3" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                    <div class="small fw-bold text-secondary text-uppercase mb-2" style="font-size: 11px; letter-spacing: 0.05em;">
                        <i class="bi bi-magic me-1 text-danger"></i> Quick 1-Click Provider Presets
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-outline-dark fw-semibold" onclick="applySmtpPreset('smtp', 'smtp.hostinger.com', 465, 'ssl')">
                            <i class="bi bi-hdd-network-fill me-1" style="color: #673DE6;"></i> Hostinger Webmail (465 SSL)
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-dark fw-semibold" onclick="applySmtpPreset('smtp', 'smtp.titan.email', 465, 'ssl')">
                            <i class="bi bi-envelope-paper-fill me-1" style="color: #673DE6;"></i> Hostinger Titan Mail (465 SSL)
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-dark fw-semibold" onclick="applySmtpPreset('smtp', 'smtp.gmail.com', 587, 'tls')">
                            <i class="bi bi-google me-1 text-danger"></i> Gmail / Google Workspace (587 TLS)
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-dark fw-semibold" onclick="applySmtpPreset('smtp', 'mail.yourdomain.com', 465, 'ssl')">
                            <i class="bi bi-globe2 me-1 text-primary"></i> cPanel / Custom Domain (465 SSL)
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-dark fw-semibold" onclick="applySmtpPreset('smtp', 'smtp-relay.brevo.com', 587, 'tls')">
                            <i class="bi bi-send-check me-1 text-success"></i> Brevo / Sendinblue
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-dark fw-semibold" onclick="applySmtpPreset('smtp', 'sandbox.smtp.mailtrap.io', 2525, 'tls')">
                            <i class="bi bi-bug me-1 text-warning"></i> Mailtrap Testing
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary fw-semibold" onclick="applySmtpPreset('log', '127.0.0.1', 2525, 'none')">
                            <i class="bi bi-journal-code me-1"></i> Local Log Mode
                        </button>
                    </div>
                </div>

                <form action="{{ route('admin.email.settings.update') }}" method="POST">
                    @csrf

                    <!-- Section 1: Transport & SMTP Connection -->
                    <h6 class="fw-bold text-uppercase small text-secondary mb-3" style="letter-spacing: 0.04em;">
                        1. Outgoing Mail Server (SMTP)
                    </h6>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-dark">Mail Driver / Transport</label>
                            <select name="mail_mailer" id="input_mail_mailer" class="form-select">
                                <option value="smtp" @selected($mailConfig['mail_mailer'] === 'smtp')>SMTP Server (Recommended)</option>
                                <option value="sendmail" @selected($mailConfig['mail_mailer'] === 'sendmail')>Sendmail (Linux Server)</option>
                                <option value="log" @selected($mailConfig['mail_mailer'] === 'log')>Log File (Local Testing)</option>
                                <option value="ses" @selected($mailConfig['mail_mailer'] === 'ses')>Amazon SES</option>
                                <option value="postmark" @selected($mailConfig['mail_mailer'] === 'postmark')>Postmark</option>
                                <option value="resend" @selected($mailConfig['mail_mailer'] === 'resend')>Resend</option>
                            </select>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label fw-semibold small text-dark">SMTP Host</label>
                            <input type="text" name="mail_host" id="input_mail_host" value="{{ old('mail_host', $mailConfig['mail_host']) }}" placeholder="e.g. smtp.gmail.com" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-dark">SMTP Port</label>
                            <input type="number" name="mail_port" id="input_mail_port" value="{{ old('mail_port', $mailConfig['mail_port']) }}" placeholder="587" class="form-control">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-dark">Encryption</label>
                            <select name="mail_encryption" id="input_mail_encryption" class="form-select">
                                <option value="tls" @selected($mailConfig['mail_encryption'] === 'tls')>TLS (Port 587 / 2525)</option>
                                <option value="ssl" @selected($mailConfig['mail_encryption'] === 'ssl')>SSL (Port 465)</option>
                                <option value="none" @selected($mailConfig['mail_encryption'] === 'none')>None</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-dark">SMTP Username / Email</label>
                            <input type="text" name="mail_username" id="input_mail_username" value="{{ old('mail_username', $mailConfig['mail_username']) }}" placeholder="yourname@gmail.com" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-dark d-flex justify-content-between">
                                <span>SMTP / App Password</span>
                                @if(!empty($mailConfig['mail_password']))
                                    <span class="badge bg-success-subtle text-success" style="font-size: 10px;">Saved</span>
                                @endif
                            </label>
                            <div class="input-group">
                                <input type="password" name="mail_password" id="input_mail_password" value="{{ old('mail_password', $mailConfig['mail_password']) }}" placeholder="{{ !empty($mailConfig['mail_password']) ? '••••••••••••' : 'Enter SMTP / App Password' }}" class="form-control">
                                <button class="btn btn-outline-secondary" type="button" onclick="toggleSmtpPassword()" style="border-radius: 0 10px 10px 0;">
                                    <i class="bi bi-eye" id="smtp_pass_icon"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Sender Identity & Admin Alert Emails -->
                    <h6 class="fw-bold text-uppercase small text-secondary mb-3 pt-2 border-top" style="letter-spacing: 0.04em;">
                        2. Sender Identity ("From" Email) &amp; Admin Alert Address
                    </h6>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark">Sender "From" Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="mail_from_address" id="input_mail_from_address" value="{{ old('mail_from_address', $mailConfig['mail_from_address']) }}" placeholder="support@yourdomain.com" class="form-control" required>
                            <div class="form-text" style="font-size: 11px;">Must match your <strong>SMTP Username</strong> on Hostinger / Gmail.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark">Sender "From" Name <span class="text-danger">*</span></label>
                            <input type="text" name="mail_from_name" id="input_mail_from_name" value="{{ old('mail_from_name', $mailConfig['mail_from_name']) }}" placeholder="SiteBuilder Cloud" class="form-control" required>
                            <div class="form-text" style="font-size: 11px;">Displayed as the sender name in your clients' inbox.</div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark">Reply-To Support Email</label>
                            <input type="email" name="mail_reply_to" id="input_mail_reply_to" value="{{ old('mail_reply_to', $mailConfig['mail_reply_to']) }}" placeholder="support@yourdomain.com" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark">Super Admin Alert Receiver Email</label>
                            <input type="email" name="mail_admin_alert_email" id="input_mail_admin_alert_email" value="{{ old('mail_admin_alert_email', $mailConfig['mail_admin_alert_email']) }}" placeholder="admin@yourdomain.com" class="form-control">
                            <div class="form-text" style="font-size: 11px;">Receives admin alerts when a new client registers, submits manual payment, or opens a ticket.</div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-dark">Email Button Color</label>
                            <input type="color" name="mail_brand_color" value="{{ old('mail_brand_color', $mailConfig['mail_brand_color'] ?? '#EC134A') }}" class="form-control form-control-color w-100" style="height: 46px; border-radius: 10px; background: #F8FAFC;">
                        </div>
                        <div class="col-md-9">
                            <label class="form-label fw-semibold small text-dark">Email Footer Signature Text</label>
                            <input type="text" name="mail_footer_text" value="{{ old('mail_footer_text', $mailConfig['mail_footer_text']) }}" class="form-control">
                        </div>
                    </div>

                    <!-- Section 3: Automated Notification Triggers -->
                    <h6 class="fw-bold text-uppercase small text-secondary mb-3 pt-2 border-top" style="letter-spacing: 0.04em;">
                        3. Automated Client &amp; Platform Email Notifications
                    </h6>

                    <div class="row g-2 mb-4">
                        @php
                            $triggerList = [
                                [
                                    'key' => 'mail_notify_registration_verify',
                                    'icon' => 'bi-envelope-check-fill text-danger',
                                    'title' => 'Registration & Theme Installation Confirmation Link',
                                    'desc' => 'Sends instant email confirmation link when a new client registers a theme.',
                                ],
                                [
                                    'key' => 'mail_notify_welcome_provisioned',
                                    'icon' => 'bi-rocket-takeoff-fill text-success',
                                    'title' => 'Welcome & Live Website Provisioned Details',
                                    'desc' => 'Sends live website URL, database confirmation & login details upon verification.',
                                ],
                                [
                                    'key' => 'mail_notify_password_reset',
                                    'icon' => 'bi-shield-lock-fill text-primary',
                                    'title' => 'Forgot Password Reset Link & Security Verification',
                                    'desc' => 'Sends password recovery link and email verification link.',
                                ],
                                [
                                    'key' => 'mail_notify_invoice_payment',
                                    'icon' => 'bi-receipt-cutoff text-warning',
                                    'title' => 'Invoices, Manual Payment & Subscription Activation',
                                    'desc' => 'Sends new invoice notification, payment approval receipt, or rejection note.',
                                ],
                                [
                                    'key' => 'mail_notify_support_ticket',
                                    'icon' => 'bi-life-preserver text-info',
                                    'title' => 'Support Tickets & Help Desk Staff Replies',
                                    'desc' => 'Notifies clients immediately when support staff replies to their ticket.',
                                ],
                                [
                                    'key' => 'mail_notify_domain_ssl',
                                    'icon' => 'bi-patch-check-fill text-success',
                                    'title' => 'Custom Domain DNS Verified & SSL Active',
                                    'desc' => 'Notifies website owner when their custom domain DNS & HTTPS SSL are activated.',
                                ],
                                [
                                    'key' => 'mail_notify_reseller_payout',
                                    'icon' => 'bi-wallet2 text-primary',
                                    'title' => 'Reseller KYC Verification & Payout Disbursements',
                                    'desc' => 'Notifies reseller partners when their KYC status or payout request is processed.',
                                ],
                                [
                                    'key' => 'mail_notify_admin_alerts',
                                    'icon' => 'bi-bell-fill text-danger',
                                    'title' => 'Super Admin Instant Copy / Alerts',
                                    'desc' => 'Sends an alert to Super Admin on new client registrations, manual payments & tickets.',
                                ],
                            ];
                        @endphp

                        @foreach($triggerList as $trig)
                            <div class="col-md-6">
                                <label class="d-flex align-items-start gap-2 p-3 rounded-3 h-100" style="background: #F8FAFC; border: 1px solid #E2E8F0; cursor: pointer;">
                                    <input type="checkbox" name="{{ $trig['key'] }}" value="1" @checked($mailConfig[$trig['key']] ?? true) class="mt-1" style="accent-color: #EC134A; width: 17px; height: 17px;">
                                    <div>
                                        <div class="fw-bold small text-dark">
                                            <i class="bi {{ $trig['icon'] }} me-1"></i> {{ $trig['title'] }}
                                        </div>
                                        <div class="text-muted" style="font-size: 11.5px; line-height: 1.4; margin-top: 2px;">
                                            {{ $trig['desc'] }}
                                        </div>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <div class="mb-3 p-3 rounded-3 d-flex align-items-center justify-content-between" style="background: #FFF1F5; border: 1px solid #FFE4EC;">
                        <label class="d-flex align-items-center gap-2 mb-0" style="cursor: pointer;">
                            <input type="checkbox" name="sync_to_env" value="1" checked style="accent-color: #EC134A; width: 17px; height: 17px;">
                            <span class="small fw-bold text-dark">Also sync SMTP settings to <code>.env</code> file (Recommended for background queues &amp; CLI)</span>
                        </label>
                    </div>

                    <button type="submit" class="btn text-white fw-bold w-100" style="height: var(--sb-control-h); background: #EC134A; border-radius: 10px; box-shadow: 0 6px 16px rgba(236, 19, 74, 0.25);">
                        <i class="bi bi-check2-circle me-1"></i> Save Email &amp; Notification Settings
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Column: Live Test Email Sender + Pending Client Verifications + Delivery Logs -->
        <div class="col-lg-5">
            <!-- Card 1: Send Live Test Email -->
            <div class="sb-table-card p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div>
                        <h5 class="fw-bold mb-1" style="color: #0F172A;">
                            <i class="bi bi-send-check-fill text-danger me-1"></i> Send a Live Test Email
                        </h5>
                        <p class="text-muted small mb-0">Verify that your SMTP server &amp; sender email are delivering messages properly.</p>
                    </div>
                    <span class="badge bg-dark">{{ strtoupper($mailConfig['mail_mailer']) }}</span>
                </div>

                <form action="{{ route('admin.email.settings.test') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Recipient Test Email Address</label>
                        <input type="email" name="test_email" value="{{ old('test_email', $user->email) }}" placeholder="Enter email to receive test message" class="form-control" style="height: 44px; border-radius: 10px; background: #F8FAFC;" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Optional Custom Note</label>
                        <input type="text" name="test_note" placeholder="e.g. Testing production SMTP configuration" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-dark fw-semibold w-100" style="height: var(--sb-control-h); border-radius: 10px;">
                        <i class="bi bi-paperplane-fill me-1"></i> Send Test Email Now
                    </button>
                </form>

                <div class="mt-3 p-2.5 rounded-3 small text-muted" style="background: #F8FAFC; border: 1px dashed #CBD5E1; font-size: 11.5px;">
                    <div class="mb-1">
                        <strong><i class="bi bi-hdd-network-fill me-1" style="color: #673DE6;"></i> Hostinger Webmail Tip:</strong> Host: <code>smtp.hostinger.com</code> (or <code>smtp.titan.email</code>), Port: <code>465</code>, Encryption: <code>SSL</code> (or Port <code>587</code> with <code>TLS</code>). Ensure <strong>SMTP Username</strong> &amp; <strong>Sender "From" Email</strong> match your exact Hostinger email address.
                    </div>
                    <div>
                        <strong><i class="bi bi-info-circle-fill text-primary me-1"></i> Gmail Tip:</strong> If using Gmail (<code>smtp.gmail.com</code>), enable 2-Step Verification and generate a 16-character <strong>App Password</strong>.
                    </div>
                </div>
            </div>

            <!-- Card 2: Pending Client Registrations Awaiting Email Confirmation -->
            @if($pendingVerifyTenants->isNotEmpty())
                <div class="sb-table-card p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div>
                            <h6 class="fw-bold mb-0" style="color: #0F172A;">
                                <i class="bi bi-hourglass-split text-warning me-1"></i> Pending Client Email Confirmations
                            </h6>
                            <p class="text-muted mb-0" style="font-size: 11.5px;">Resend confirmation link or manually verify &amp; provision.</p>
                        </div>
                        <span class="badge bg-warning text-dark">{{ $pendingVerifyTenants->count() }} Pending</span>
                    </div>

                    <div class="d-flex flex-column gap-2">
                        @foreach($pendingVerifyTenants as $pTenant)
                            <div class="p-3 rounded-3 border bg-light d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div>
                                    <div class="fw-bold small text-dark">{{ $pTenant->name }} <span class="badge bg-danger">{{ $pTenant->theme_module }}</span></div>
                                    <div class="text-muted" style="font-size: 11.5px;">
                                        {{ $pTenant->owner?->email }} • <code>{{ $pTenant->subdomain }}</code>
                                    </div>
                                </div>
                                <div class="d-flex gap-1">
                                    <form action="{{ route('saas.install.verify.resend', ['uuid' => $pTenant->uuid]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary fw-semibold" title="Resend Confirmation Link Email">
                                            <i class="bi bi-envelope-arrow-up"></i> Resend
                                        </button>
                                    </form>
                                    @if($pTenant->verification_token)
                                        <form action="{{ route('saas.install.verify.confirm', ['uuid' => $pTenant->uuid, 'token' => $pTenant->verification_token]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success fw-semibold" title="Verify & Provision Database Now">
                                                <i class="bi bi-check2-circle"></i> Verify Now
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Card 3: Recent Email Delivery Logs -->
            <div class="sb-table-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div>
                        <h5 class="fw-bold mb-1" style="color: #0F172A;">
                            <i class="bi bi-journal-check text-success me-1"></i> Recent Outgoing Email Logs
                        </h5>
                        <p class="text-muted small mb-0">Live log of confirmation links &amp; notifications sent by the platform.</p>
                    </div>
                    @if(!empty($emailLogs))
                        <form action="{{ route('admin.email.settings.clear-logs') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary" style="font-size: 11px;">
                                Clear Logs
                            </button>
                        </form>
                    @endif
                </div>

                @if(empty($emailLogs))
                    <div class="text-center py-4 text-muted small">
                        <i class="bi bi-inbox fs-3 d-block mb-1 text-secondary"></i>
                        No outgoing emails logged yet. Send a test email above or register a client site to see live delivery logs.
                    </div>
                @else
                    <div class="d-flex flex-column gap-2" style="max-height: 440px; overflow-y: auto;">
                        @foreach($emailLogs as $log)
                            @php
                                $badgeClass = match($log['status'] ?? 'sent') {
                                    'sent' => 'bg-success-subtle text-success',
                                    'logged' => 'bg-info-subtle text-info',
                                    default => 'bg-danger-subtle text-danger',
                                };
                            @endphp
                            <div class="p-2.5 rounded-3 border" style="background: #F8FAFC; padding: 10px 12px;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge {{ $badgeClass }} text-uppercase" style="font-size: 10px; font-weight: 700;">
                                        {{ strtoupper($log['status'] ?? 'SENT') }} • {{ strtoupper($log['mailer'] ?? 'SMTP') }}
                                    </span>
                                    <span class="text-muted" style="font-size: 11px;">{{ $log['sent_at'] ?? '' }}</span>
                                </div>
                                <div class="fw-bold small text-dark" style="font-size: 12.5px;">{{ $log['subject'] ?? 'Notification' }}</div>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <span class="text-muted" style="font-size: 11.5px;">
                                        <i class="bi bi-person-fill me-1"></i>To: <strong>{{ $log['to'] ?? '' }}</strong>
                                        <span class="ms-1 badge bg-secondary-subtle text-secondary" style="font-size: 10px;">{{ $log['event'] ?? '' }}</span>
                                    </span>
                                    @if(!empty($log['action_url']))
                                        <a href="{{ $log['action_url'] }}" target="_blank" class="btn btn-sm btn-link text-danger p-0 fw-semibold text-decoration-none" style="font-size: 11px;">
                                            Open Link <i class="bi bi-box-arrow-up-right"></i>
                                        </a>
                                    @endif
                                </div>
                                @if(!empty($log['error']))
                                    <div class="mt-1 text-danger" style="font-size: 11px; word-break: break-word;">
                                        <i class="bi bi-info-circle me-1"></i>{{ $log['error'] }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function applySmtpPreset(mailer, host, port, encryption) {
            document.getElementById('input_mail_mailer').value = mailer;
            document.getElementById('input_mail_host').value = host;
            document.getElementById('input_mail_port').value = port;
            document.getElementById('input_mail_encryption').value = encryption;
        }

        function toggleSmtpPassword() {
            const input = document.getElementById('input_mail_password');
            const icon = document.getElementById('smtp_pass_icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }

        // Automatically sync Sender "From" Email with SMTP Username when typing an email address
        document.getElementById('input_mail_username')?.addEventListener('input', function () {
            const val = this.value.trim();
            if (val.includes('@')) {
                const fromInput = document.getElementById('input_mail_from_address');
                const replyInput = document.getElementById('input_mail_reply_to');
                const host = (document.getElementById('input_mail_host')?.value || '').toLowerCase();
                const placeholders = ['', 'hello@example.com', 'noreply@sitebuilder.com', 'noreply@example.com'];

                if (fromInput && (placeholders.includes(fromInput.value.trim().toLowerCase()) || host.includes('hostinger') || host.includes('titan') || host.includes('gmail'))) {
                    fromInput.value = val;
                }
                if (replyInput && placeholders.includes(replyInput.value.trim().toLowerCase())) {
                    replyInput.value = val;
                }
            }
        });
    </script>
@endsection
