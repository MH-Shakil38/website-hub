<?php

namespace App\Http\Controllers;

use App\Http\Middleware\TrackResellerReferral;
use App\Models\ActivityLog;
use App\Models\Plan;
use App\Models\Role;
use App\Models\SaasSection;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Billing\BillingService;
use App\Services\SaasMailService;
use App\Services\TenantDatabaseManager;
use App\Support\ModuleCmsResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SaasOnboardingController extends Controller
{
    public function __construct(
        protected TenantDatabaseManager $tenantDb,
        protected BillingService $billing,
        protected SaasMailService $mailService
    ) {}

    /**
     * AJAX Live Subdomain Availability Check (Step 2 of Theme Install Wizard).
     * GET /install/check-subdomain?subdomain=mysite
     */
    public function checkSubdomain(Request $request)
    {
        $raw = strtolower(trim((string) $request->query('subdomain', '')));
        $clean = preg_replace('/[^a-z0-9\-]/', '', $raw);
        $clean = trim($clean, '-');

        $baseDomain = config('app.central_domains')[0] ?? 'localhost';

        if (strlen($clean) < 3) {
            return response()->json([
                'available' => false,
                'subdomain' => $clean,
                'full_domain' => "{$clean}.{$baseDomain}",
                'message' => 'Subdomain must be at least 3 characters (letters, numbers, hyphens).',
            ]);
        }

        $reserved = ['app', 'www', 'admin', 'api', 'mail', 'ftp', 'cpanel', 'root', 'billing', 'support', 'status', 'cloud'];
        if (in_array($clean, $reserved, true)) {
            return response()->json([
                'available' => false,
                'subdomain' => $clean,
                'full_domain' => "{$clean}.{$baseDomain}",
                'message' => "The subdomain [{$clean}.{$baseDomain}] is reserved by the platform.",
            ]);
        }

        $exists = Tenant::withTrashed()->where('subdomain', $clean)->exists();
        if ($exists) {
            return response()->json([
                'available' => false,
                'subdomain' => $clean,
                'full_domain' => "{$clean}.{$baseDomain}",
                'message' => "Taken! [{$clean}.{$baseDomain}] is already in use. Try [{$clean}-" . rand(10, 99) . "].",
            ]);
        }

        return response()->json([
            'available' => true,
            'subdomain' => $clean,
            'full_domain' => "{$clean}.{$baseDomain}",
            'message' => "Available! Your website will launch at {$clean}.{$baseDomain}",
        ]);
    }

    /**
     * Step 3 Submission: Register Client Account & Create Pending-Verification Tenant Record.
     * IMPORTANT: Does NOT create the physical MySQL database until Email Verification succeeds!
     */
    public function registerPendingInstall(Request $request)
    {
        Plan::ensureDefaultsSeeded();

        $validated = $request->validate([
            'theme_module' => 'required|string',
            'plan_id' => 'nullable|exists:core_plans,id',
            'billing_cycle' => 'nullable|in:monthly,yearly',
            'site_name' => 'required|string|max:160',
            'subdomain' => 'required|string|min:3|max:60|regex:/^[a-z0-9\-]+$/i|unique:core_tenants,subdomain',
            'name' => 'required|string|max:160',
            'email' => 'required|email|max:180',
            'phone' => 'nullable|string|max:40',
            'password' => 'required|string|min:6',
        ]);

        $themeModule = $validated['theme_module'];
        if (!isset(ModuleCmsResolver::SUPPORTED_MODULES[$themeModule])) {
            return back()->withErrors(['theme_module' => 'Invalid theme selected.'])->withInput();
        }

        $subdomain = strtolower(trim($validated['subdomain'], '-'));
        $reserved = ['app', 'www', 'admin', 'api', 'mail', 'ftp', 'cpanel', 'root'];
        if (in_array($subdomain, $reserved, true)) {
            return back()->withErrors(['subdomain' => "The subdomain [{$subdomain}] is reserved."])->withInput();
        }

        $plan = !empty($validated['plan_id'])
            ? Plan::find($validated['plan_id'])
            : Plan::where('audience', 'client')->where('is_active', true)->orderBy('sort_order')->first();

        // Check if user already exists or create a new Client account
        $user = User::where('email', $validated['email'])->first();
        if (!$user) {
            $referredResellerUserId = TrackResellerReferral::resolveReferredResellerUserId($request, $validated['email']);
            $defaultRole = Role::first();

            $user = User::create([
                'role_id' => $defaultRole?->id,
                'type' => 'client',
                'reseller_id' => $referredResellerUserId,
                'referred_via' => $referredResellerUserId ? 'referral_link' : null,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'status' => 'Active',
                'password' => Hash::make($validated['password']),
                'email_verified_at' => null,
            ]);
        }

        $verificationToken = Str::random(40);

        // Create Tenant in 'pending_verification' status (db_name is NULL until email is verified!)
        $tenant = Tenant::create([
            'owner_id' => $user->id,
            'reseller_id' => $user->reseller_id,
            'plan_id' => $plan?->id,
            'billing_cycle' => $validated['billing_cycle'] ?? 'monthly',
            'theme_module' => $themeModule,
            'name' => $validated['site_name'],
            'subdomain' => $subdomain,
            'status' => 'pending_verification',
            'verification_token' => $verificationToken,
            'email_verified_at' => null,
            'storage_limit_mb' => $plan ? $plan->storage_mb : 1024,
            'db_name' => null,
            'is_demo' => false,
        ]);

        ActivityLog::record(
            'Theme Install Initiated (Awaiting Email Verification)',
            "{$tenant->name} ({$tenant->theme_module}) • {$user->email}",
            'bi-envelope-exclamation-fill',
            '#FEF3C7',
            '#D97706'
        );

        // Send Registration & Theme Installation Confirmation Link Email from Super Admin configured email
        $verifyUrl = route('saas.install.verify.confirm', [
            'uuid' => $tenant->uuid,
            'token' => $tenant->verification_token,
        ]);
        $this->mailService->sendRegistrationVerification($tenant, $user, $verifyUrl);

        return redirect()->route('saas.install.verify.notice', ['uuid' => $tenant->uuid])
            ->with('email_sent', "Confirmation link sent to {$user->email}!");
    }

    /**
     * Resend the Registration & Theme Installation Confirmation Link Email.
     */
    public function resendVerifyEmail(string $uuid)
    {
        $tenant = Tenant::where('uuid', $uuid)->with(['owner', 'plan'])->firstOrFail();

        if ($tenant->status !== 'pending_verification' && $tenant->db_name) {
            return redirect()->route('saas.install.success', ['uuid' => $tenant->uuid]);
        }

        if (!$tenant->verification_token) {
            $tenant->update(['verification_token' => Str::random(40)]);
        }

        $verifyUrl = route('saas.install.verify.confirm', [
            'uuid' => $tenant->uuid,
            'token' => $tenant->verification_token,
        ]);

        if ($tenant->owner) {
            $res = $this->mailService->sendRegistrationVerification($tenant, $tenant->owner, $verifyUrl);
            return back()->with('email_sent', $res['sent']
                ? "A fresh confirmation link has been sent to {$tenant->owner->email}!"
                : "Could not send email ({$res['message']}). You can also verify directly below.");
        }

        return back();
    }

    /**
     * Step 4A: Show "Check Your Email to Complete Theme Installation" page.
     */
    public function showVerifyNotice(string $uuid)
    {
        $tenant = Tenant::where('uuid', $uuid)->with(['owner', 'plan'])->firstOrFail();

        if ($tenant->status !== 'pending_verification' && $tenant->db_name) {
            return redirect()->route('saas.install.success', ['uuid' => $tenant->uuid]);
        }

        $verifyUrl = route('saas.install.verify.confirm', [
            'uuid' => $tenant->uuid,
            'token' => $tenant->verification_token,
        ]);

        return view('frontend.install-verify', compact('tenant', 'verifyUrl'));
    }

    /**
     * Step 4B: Email Verification Confirmed -> Provision Isolated Tenant Database, Run Theme Migrations, Seed Demo Data, Start Free Trial & Auto-Login!
     */
    public function verifyAndProvision(Request $request, string $uuid, string $token)
    {
        $tenant = Tenant::where('uuid', $uuid)->with(['owner', 'plan'])->firstOrFail();

        // If already verified & provisioned (e.g. user clicked link twice or already verified from notice page), redirect smoothly to success
        if ($tenant->status !== 'pending_verification' && $tenant->db_name) {
            return redirect()->route('saas.install.success', ['uuid' => $tenant->uuid]);
        }

        if (!$tenant->verification_token || !hash_equals((string) $tenant->verification_token, $token)) {
            abort(403, 'Invalid or expired installation verification token.');
        }

        $user = $tenant->owner;
        if ($user && !$user->email_verified_at) {
            $user->update(['email_verified_at' => now()]);
        }

        $tenant->update([
            'email_verified_at' => now(),
            'verification_token' => null,
        ]);

        // Provision isolated physical MySQL database + theme tables + demo content
        $tenant = $this->tenantDb->provisionTenant($tenant, false, 'trial');

        // Create 14-Day Free Trial Subscription record for this Tenant & Plan
        $plan = $tenant->plan ?: Plan::where('audience', 'client')->orderBy('sort_order')->first();
        if ($user && $plan) {
            $this->billing->createSubscriptionWithInvoice(
                $user,
                $plan,
                $tenant->billing_cycle ?: 'monthly',
                $tenant,
                true // startAsTrial = true
            );
        }

        if ($user) {
            Auth::login($user);
            session([
                'admin_tenant_id' => $tenant->id,
                'admin_cms_module' => $tenant->theme_module,
            ]);
        }

        ActivityLog::record(
            'Theme Installed & Tenant Database Provisioned',
            "{$tenant->name} ({$tenant->theme_module}) -> DB: {$tenant->db_name}",
            'bi-rocket-takeoff-fill',
            '#DCFCE7',
            '#10B981'
        );

        // Send Welcome & Live Website Provisioned Email to Client
        if ($user) {
            $this->mailService->sendWelcomeWebsiteProvisioned($tenant, $user);
        }

        return redirect()->route('saas.install.success', ['uuid' => $tenant->uuid]);
    }

    /**
     * Step 4C: Installation Success Screen with Live Website Link & Client Dashboard Button.
     */
    public function showInstallSuccess(string $uuid)
    {
        $tenant = Tenant::where('uuid', $uuid)->with(['owner', 'plan', 'domains'])->firstOrFail();
        $baseDomain = config('app.central_domains')[0] ?? 'localhost';
        $primaryDomain = $tenant->domains->firstWhere('is_primary', true)?->domain ?? "{$tenant->subdomain}.{$baseDomain}";

        return view('frontend.install-success', compact('tenant', 'primaryDomain'));
    }

    // =========================================================================
    // ADMIN: SAAS HOMEPAGE BUILDER (/admin/saas-homepage)
    // =========================================================================

    public function editHomepage()
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can manage the SaaS Homepage Builder.');
        }

        $sections = SaasSection::getLandingMap();

        return view('admin.saas-homepage', compact('sections', 'user'));
    }

    public function updateHomepage(Request $request)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();
        if (!$user->isSuperAdmin()) {
            abort(403);
        }

        $defaults = SaasSection::defaultSections();
        $submitted = $request->input('sections', []);

        foreach ($defaults as $key => $cfg) {
            $secInput = $submitted[$key] ?? [];
            $isVisible = !empty($secInput['is_visible']);
            $content = array_replace_recursive($cfg['content'], $secInput['content'] ?? []);

            // Parse FAQ items if submitted
            if ($key === 'faq' && isset($secInput['faq_q']) && is_array($secInput['faq_q'])) {
                $faqItems = [];
                foreach ($secInput['faq_q'] as $idx => $qText) {
                    $qText = trim((string) $qText);
                    $aText = trim((string) ($secInput['faq_a'][$idx] ?? ''));
                    if ($qText !== '' && $aText !== '') {
                        $faqItems[] = ['q' => $qText, 'a' => $aText];
                    }
                }
                if (!empty($faqItems)) {
                    $content['items'] = $faqItems;
                }
            }

            SaasSection::updateOrCreate(
                ['section_key' => $key],
                [
                    'title' => $cfg['title'],
                    'content' => $content,
                    'is_visible' => $isVisible,
                    'sort_order' => (int) ($secInput['sort_order'] ?? $cfg['sort_order']),
                ]
            );
        }

        ActivityLog::record(
            'SaaS Homepage Updated',
            'Updated landing page sections via SaaS Homepage Builder',
            'bi-layout-text-window-reverse',
            '#FFE4EC',
            '#EC134A'
        );

        return back()->with('success', 'SaaS Landing Page sections updated successfully!');
    }
}
