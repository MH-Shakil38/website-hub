<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Notification' }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #F1F5F9; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #0F172A; -webkit-font-smoothing: antialiased;">
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color: #F1F5F9; padding: 32px 16px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="max-width: 600px; width: 100%; background-color: #FFFFFF; border-radius: 18px; overflow: hidden; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08); border: 1px solid #E2E8F0;">
                    <!-- Top Brand Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%); padding: 26px 32px; border-bottom: 4px solid {{ $brandColor ?? '#EC134A' }};">
                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td>
                                        <span style="display: inline-block; font-size: 20px; font-weight: 800; color: #FFFFFF; letter-spacing: -0.02em;">
                                            {{ $brandName ?? 'SiteBuilder SaaS' }}
                                        </span>
                                        @if(!empty($badgeText))
                                            <div style="margin-top: 6px;">
                                                <span style="display: inline-block; background-color: {{ $brandColor ?? '#EC134A' }}; color: #FFFFFF; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; padding: 4px 10px; border-radius: 999px;">
                                                    {{ $badgeText }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td align="right" style="color: #94A3B8; font-size: 12px;">
                                        {{ now()->format('M d, Y') }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Main Email Content -->
                    <tr>
                        <td style="padding: 32px;">
                            @if(!empty($headline))
                                <h1 style="margin: 0 0 14px 0; font-size: 21px; font-weight: 800; color: #0F172A; line-height: 1.35;">
                                    {{ $headline }}
                                </h1>
                            @endif

                            <p style="margin: 0 0 16px 0; font-size: 15px; line-height: 1.6; color: #334155;">
                                Hello <strong>{{ $recipientName ?? 'there' }}</strong>,
                            </p>

                            @if(!empty($introLines) && is_array($introLines))
                                @foreach($introLines as $line)
                                    <p style="margin: 0 0 14px 0; font-size: 14.5px; line-height: 1.65; color: #475569;">
                                        {!! $line !!}
                                    </p>
                                @endforeach
                            @endif

                            <!-- Key-Value Summary Box -->
                            @if(!empty($details) && is_array($details))
                                <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 16px 20px; margin: 20px 0;">
                                    @foreach($details as $label => $val)
                                        <tr>
                                            <td style="padding: 6px 0; font-size: 13px; color: #64748B; font-weight: 600; width: 42%; vertical-align: top;">
                                                {{ $label }}
                                            </td>
                                            <td style="padding: 6px 0; font-size: 13.5px; color: #0F172A; font-weight: 700; vertical-align: top;">
                                                {{ $val }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            @endif

                            <!-- Primary CTA Button -->
                            @if(!empty($actionText) && !empty($actionUrl))
                                <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin: 26px 0;">
                                    <tr>
                                        <td align="center">
                                            <a href="{{ $actionUrl }}" target="_blank" style="display: inline-block; background-color: {{ $brandColor ?? '#EC134A' }}; color: #FFFFFF; font-size: 15px; font-weight: 700; text-decoration: none; padding: 14px 28px; border-radius: 12px; box-shadow: 0 6px 18px rgba(236, 19, 74, 0.25);">
                                                {{ $actionText }} &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            @if(!empty($outroLines) && is_array($outroLines))
                                @foreach($outroLines as $line)
                                    <p style="margin: 0 0 12px 0; font-size: 14px; line-height: 1.6; color: #475569;">
                                        {!! $line !!}
                                    </p>
                                @endforeach
                            @endif

                            <!-- Copyable Fallback Link for Verification & Reset URLs -->
                            @if(!empty($actionUrl))
                                <div style="margin-top: 22px; padding-top: 16px; border-top: 1px dashed #E2E8F0; font-size: 12px; color: #64748B; line-height: 1.5; word-break: break-all;">
                                    If you're having trouble clicking the <strong>"{{ $actionText ?? 'Confirm' }}"</strong> button, copy and paste the URL below into your web browser:<br>
                                    <a href="{{ $actionUrl }}" style="color: {{ $brandColor ?? '#EC134A' }}; text-decoration: underline;">{{ $actionUrl }}</a>
                                </div>
                            @endif
                        </td>
                    </tr>

                    <!-- Email Footer -->
                    <tr>
                        <td style="background-color: #F8FAFC; padding: 20px 32px; border-top: 1px solid #E2E8F0; text-align: center;">
                            <p style="margin: 0 0 6px 0; font-size: 12.5px; color: #64748B; font-weight: 600;">
                                {{ $footerText ?? ('Sent automatically by ' . ($brandName ?? 'SiteBuilder SaaS')) }}
                            </p>
                            @if(!empty($supportEmail))
                                <p style="margin: 0 0 6px 0; font-size: 12px; color: #94A3B8;">
                                    Need help? Contact support at <a href="mailto:{{ $supportEmail }}" style="color: #475569; text-decoration: underline;">{{ $supportEmail }}</a>
                                </p>
                            @endif
                            <p style="margin: 0; font-size: 11.5px; color: #94A3B8;">
                                &copy; {{ date('Y') }} {{ $brandName ?? 'SiteBuilder' }}. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
