<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Domain;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Policies\ClientPolicy;
use App\Services\DomainVerificationService;
use App\Services\TenantDatabaseManager;
use App\Support\ModuleCmsResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SaasManagementController extends Controller
{
    public function __construct(
        protected TenantDatabaseManager $tenantDb,
        protected DomainVerificationService $domainVerifier
    ) {}

    /**
     * List accessible Tenants (Client Websites & Isolated Databases) scoped by user role.
     */
    public function tenants(Request $request)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();
        $search = trim((string) $request->query('search', ''));
        $themeFilter = $request->query('theme');
        $statusFilter = $request->query('status');

        $query = $user->accessibleTenantsQuery()->with(['owner', 'domains']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('subdomain', 'like', "%{$search}%")
                    ->orWhere('db_name', 'like', "%{$search}%")
                    ->orWhere('theme_module', 'like', "%{$search}%");
            });
        }

        if ($themeFilter) {
            $query->where('theme_module', $themeFilter);
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $tenants = $query->orderByDesc('is_demo')->latest('id')->paginate(12)->withQueryString();
        $clients = $user->isSuperAdmin()
            ? User::whereIn('type', ['client', 'reseller'])->orderBy('name')->get()
            : ($user->isReseller() ? $user->clients()->orderBy('name')->get() : collect([$user]));

        $themes = ModuleCmsResolver::SUPPORTED_MODULES;

        return view('admin.tenants', compact('tenants', 'clients', 'themes', 'search', 'themeFilter', 'statusFilter', 'user'));
    }

    /**
     * Provision a new Client Tenant Website + Isolated MySQL Database from Dashboard.
     */
    public function storeTenant(Request $request)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();

        $validated = $request->validate([
            'name' => 'required|string|max:180',
            'theme_module' => 'required|string',
            'subdomain' => 'required|string|max:80|regex:/^[a-z0-9\-]+$/i|unique:core_tenants,subdomain',
            'owner_id' => 'nullable|exists:users,id',
            'status' => 'nullable|in:trial,active',
        ]);

        $subdomain = strtolower($validated['subdomain']);
        if (in_array($subdomain, ['app', 'www', 'admin', 'api', 'mail', 'ftp', 'cpanel', 'root'], true)) {
            return back()->withErrors(['subdomain' => "The subdomain [{$subdomain}] is reserved by the platform."])->withInput();
        }

        if (!isset(ModuleCmsResolver::SUPPORTED_MODULES[$validated['theme_module']])) {
            return back()->withErrors(['theme_module' => 'Invalid theme module selected.'])->withInput();
        }

        $ownerId = $validated['owner_id'] ?? $user->id;
        $resellerId = $user->isReseller() ? $user->id : null;

        if ($user->isClient()) {
            $ownerId = $user->id;
            $resellerId = $user->reseller_id;
        }

        // Enforce Plan Site Limit & Theme Allowance for non-Super-Admin users
        if ($user->isReseller()) {
            $resellerProfile = $user->ensureResellerProfile();
            if ($resellerProfile->kyc_status === 'rejected') {
                return back()->withErrors(['name' => 'Your reseller KYC verification was rejected. Please update your KYC details.'])->withInput();
            }

            try {
                app(\App\Services\ResellerService::class)->consumeSlotOrWalletForNewSite($resellerProfile, $validated['name']);
            } catch (\Throwable $e) {
                return back()->withErrors(['name' => $e->getMessage()])->withInput();
            }
        } elseif (!$user->isSuperAdmin()) {
            $plan = $user->currentPlan();
            if ($plan) {
                if (!$plan->allowsTheme($validated['theme_module'])) {
                    return back()->withErrors(['theme_module' => "Your current plan [{$plan->name}] does not include the [{$validated['theme_module']}] theme."])->withInput();
                }

                $currentSiteCount = $user->accessibleTenantsQuery()->where('is_demo', false)->count();
                if ($currentSiteCount >= (int) $plan->site_limit) {
                    return back()->withErrors(['name' => "Plan limit reached! Your plan [{$plan->name}] allows up to {$plan->site_limit} website(s). Please upgrade your plan to add more websites."])->withInput();
                }
            }
        }

        $tenant = Tenant::create([
            'owner_id' => $ownerId,
            'reseller_id' => $resellerId,
            'theme_module' => $validated['theme_module'],
            'name' => $validated['name'],
            'subdomain' => $subdomain,
            'status' => 'pending_verification',
            'email_verified_at' => now(),
            'is_demo' => false,
        ]);

        $finalStatus = $validated['status'] ?? 'trial';
        $tenant = $this->tenantDb->provisionTenant($tenant, false, $finalStatus);

        ActivityLog::record(
            'New SaaS Tenant Provisioned',
            "{$tenant->name} ({$tenant->theme_module}) -> DB: {$tenant->db_name}",
            'bi-hdd-network-fill',
            '#DCFCE7',
            '#10B981'
        );

        session([
            'admin_tenant_id' => $tenant->id,
            'admin_cms_module' => $tenant->theme_module,
        ]);

        return back()->with('success', "Website [{$tenant->name}] provisioned in isolated database [{$tenant->db_name}]!");
    }

    /**
     * Switch the active Admin Workspace to a specific Tenant (and connect its database).
     */
    public function switchTenant(Request $request, Tenant $tenant)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();

        if ($user && !$user->canAccessTenant($tenant)) {
            abort(403, 'You do not have access to this website.');
        }

        session([
            'admin_tenant_id' => $tenant->id,
            'admin_cms_module' => $tenant->theme_module,
        ]);

        $this->tenantDb->connect($tenant);

        $redirectTo = $request->input('redirect_to');
        if ($redirectTo && str_starts_with($redirectTo, url('/admin'))) {
            return redirect($redirectTo)->with('success', "Switched active website context to [{$tenant->name}] (DB: {$tenant->db_name}).");
        }

        return redirect()->route('admin.pages')->with('success', "Switched active website context to [{$tenant->name}] (DB: {$tenant->db_name}).");
    }

    /**
     * Suspend or Activate a Tenant Website.
     */
    public function toggleTenantStatus(Tenant $tenant)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();

        if ($user && !$user->isSuperAdmin() && !($user->isReseller() && (int) $tenant->reseller_id === (int) $user->id)) {
            abort(403, 'Only Super Admin or the assigned Reseller can change tenant status.');
        }

        $newStatus = in_array($tenant->status, ['active', 'trial'], true) ? 'suspended' : 'active';
        $tenant->update(['status' => $newStatus]);

        return back()->with('success', "Tenant [{$tenant->name}] status updated to [" . strtoupper($newStatus) . "].");
    }

    /**
     * Delete a Non-Demo Tenant and drop its isolated database.
     */
    public function destroyTenant(Tenant $tenant)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();

        if (!$user || (!$user->isSuperAdmin() && !($user->isReseller() && (int) $tenant->reseller_id === (int) $user->id))) {
            abort(403);
        }

        if ($tenant->is_demo) {
            return back()->withErrors(['tenant' => 'System demo tenants cannot be deleted.']);
        }

        $name = $tenant->name;
        $dbName = $tenant->db_name;

        $this->tenantDb->dropDatabase($tenant);
        Domain::where('tenant_id', $tenant->id)->delete();
        $tenant->forceDelete();

        if ((int) session('admin_tenant_id') === (int) $tenant->id) {
            session()->forget('admin_tenant_id');
        }

        return back()->with('success', "Tenant [{$name}] and database [{$dbName}] removed.");
    }

    /**
     * List Resellers & Clients (Super Admin sees all; Reseller sees own clients).
     */
    public function accounts(Request $request)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();

        if ($user->isClient()) {
            return redirect()->route('admin.tenants.index');
        }

        $search = trim((string) $request->query('search', ''));
        $roleFilter = $request->query('type');

        $query = User::query()->with(['tenants', 'reseller']);

        if ($user->isReseller()) {
            $query->where('reseller_id', $user->id);
        } elseif ($roleFilter) {
            $query->where('type', $roleFilter);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $accounts = $query->latest('id')->paginate(12)->withQueryString();
        $resellers = $user->isSuperAdmin()
            ? User::where('type', 'reseller')->orderBy('name')->get()
            : collect([$user]);

        return view('admin.saas-accounts', compact('accounts', 'resellers', 'search', 'roleFilter', 'user'));
    }

    /**
     * Create a Reseller or Client User Account.
     */
    public function storeAccount(Request $request)
    {
        /** @var User $actor */
        $actor = Auth::user() ?? User::first();

        if ($actor->isClient()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:160',
            'email' => 'required|email|max:180|unique:users,email',
            'phone' => 'nullable|string|max:40',
            'password' => 'required|string|min:6',
            'type' => 'required|in:reseller,client',
            'reseller_id' => 'nullable|exists:users,id',
        ]);

        // Resellers can only create clients under themselves
        $accountType = $actor->isReseller() ? 'client' : $validated['type'];
        $resellerId = $actor->isReseller() ? $actor->id : ($validated['reseller_id'] ?? null);

        $defaultRole = Role::first();

        $newUser = User::create([
            'role_id' => $defaultRole?->id,
            'type' => $accountType,
            'reseller_id' => $accountType === 'client' ? $resellerId : null,
            'referred_via' => 'manual',
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'status' => 'Active',
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        if ($accountType === 'reseller') {
            $newUser->ensureResellerProfile();
        }

        ActivityLog::record(
            'New ' . ucfirst($accountType) . ' Account Created',
            "{$newUser->name} ({$newUser->email})",
            'bi-person-plus-fill',
            '#DBEAFE',
            '#2563EB'
        );

        return back()->with('success', ucfirst($accountType) . " account [{$newUser->name}] created successfully.");
    }

    /**
     * Impersonate a Reseller or Client account (Super Admin -> Reseller/Client, Reseller -> own Client).
     */
    public function impersonate(User $targetUser)
    {
        /** @var User $actor */
        $actor = Auth::user() ?? User::first();

        $policy = new ClientPolicy();
        if (!$policy->impersonate($actor, $targetUser)) {
            abort(403, 'You are not allowed to impersonate this account.');
        }

        // Preserve original impersonator ID
        if (!session()->has('impersonator_id')) {
            session(['impersonator_id' => $actor->id]);
        }

        Auth::login($targetUser);

        // Automatically switch active tenant context to the target user's first accessible tenant
        $firstTenant = $targetUser->accessibleTenantsQuery()->first();
        if ($firstTenant) {
            session([
                'admin_tenant_id' => $firstTenant->id,
                'admin_cms_module' => $firstTenant->theme_module,
            ]);
            $this->tenantDb->connect($firstTenant);
        } else {
            session()->forget('admin_tenant_id');
        }

        return redirect()->route('admin.dashboard')
            ->with('success', "You are now logged in as [{$targetUser->name}] (" . strtoupper($targetUser->panelType()) . " Panel).");
    }

    /**
     * Stop impersonating and return to the original Super Admin / Reseller account.
     */
    public function leaveImpersonation()
    {
        $impersonatorId = session('impersonator_id');
        if (!$impersonatorId) {
            return redirect()->route('admin.dashboard');
        }

        $originalUser = User::find($impersonatorId);
        session()->forget('impersonator_id');

        if ($originalUser) {
            Auth::login($originalUser);
            $firstTenant = $originalUser->accessibleTenantsQuery()->orderByDesc('is_demo')->first();
            if ($firstTenant) {
                session([
                    'admin_tenant_id' => $firstTenant->id,
                    'admin_cms_module' => $firstTenant->theme_module,
                ]);
            }
        }

        return redirect()->route('admin.dashboard')
            ->with('success', 'Returned to your original account.');
    }

    // =========================================================================
    // PHASE 3: DOMAIN MANAGEMENT, DNS VERIFICATION & CADDY ON-DEMAND SSL
    // =========================================================================

    /**
     * List all Domains (Free Subdomains & Custom Domains) for the user's accessible tenants.
     */
    public function domains(Request $request)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();
        $accessibleTenants = $user->accessibleTenantsQuery()->orderByDesc('is_demo')->orderBy('name')->get();
        $tenantIds = $accessibleTenants->pluck('id')->all();

        $selectedTenantId = $request->query('tenant_id');
        $search = trim((string) $request->query('search', ''));

        $domainsQuery = Domain::with('tenant')->whereIn('tenant_id', $tenantIds);

        if ($selectedTenantId && in_array((int) $selectedTenantId, $tenantIds, true)) {
            $domainsQuery->where('tenant_id', $selectedTenantId);
        }

        if ($search !== '') {
            $domainsQuery->where('domain', 'like', "%{$search}%");
        }

        $domains = $domainsQuery->orderByDesc('is_primary')->latest('id')->paginate(12)->withQueryString();

        return view('admin.domains', compact('domains', 'accessibleTenants', 'selectedTenantId', 'search', 'user'));
    }

    /**
     * Add a Custom Domain to an accessible Tenant Website.
     */
    public function storeDomain(Request $request)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();

        $validated = $request->validate([
            'tenant_id' => 'required|exists:core_tenants,id',
            'domain' => 'required|string|max:190',
        ]);

        $tenant = Tenant::findOrFail($validated['tenant_id']);
        if (!$user->canAccessTenant($tenant)) {
            abort(403, 'You do not have permission to add a domain to this website.');
        }

        // Enforce Plan Custom Domain permission
        $tenantPlan = $tenant->plan_id ? \App\Models\Plan::find($tenant->plan_id) : $user->currentPlan();
        if (!$user->isSuperAdmin() && $tenantPlan && !$tenantPlan->custom_domain) {
            return back()->withErrors(['domain' => "Your current plan [{$tenantPlan->name}] only supports free subdomains. Upgrade to a Custom Domain plan to connect [{$validated['domain']}]."])->withInput();
        }

        $cleanDomain = $this->domainVerifier->normalizeHostname($validated['domain']);
        if (!preg_match('/^([a-z0-9\-]+\.)+[a-z]{2,}$/', $cleanDomain)) {
            return back()->withErrors(['domain' => "Invalid domain format [{$cleanDomain}]. Enter a valid domain like example.com or shop.example.com."])->withInput();
        }

        $centralDomains = array_map('strtolower', config('app.central_domains', ['localhost', 'multi-website-hub.test']));
        $dashboardDomain = strtolower((string) config('app.dashboard_domain', 'app.multi-website-hub.test'));

        if (in_array($cleanDomain, $centralDomains, true) || $cleanDomain === $dashboardDomain) {
            return back()->withErrors(['domain' => 'Cannot use a reserved central platform domain as a custom domain.'])->withInput();
        }

        if (Domain::where('domain', $cleanDomain)->exists()) {
            return back()->withErrors(['domain' => "The domain [{$cleanDomain}] is already connected to a website."])->withInput();
        }

        $token = $this->domainVerifier->generateToken($tenant->id, $cleanDomain);

        $domainRecord = Domain::create([
            'tenant_id' => $tenant->id,
            'domain' => $cleanDomain,
            'type' => 'custom',
            'is_primary' => false,
            'verification_token' => $token,
            'verified_at' => null,
            'ssl_status' => 'pending',
        ]);

        ActivityLog::record(
            'Custom Domain Added',
            "{$cleanDomain} -> {$tenant->name} (Pending DNS Verification)",
            'bi-link-45deg',
            '#DBEAFE',
            '#2563EB'
        );

        return back()->with('success', "Custom domain [{$domainRecord->domain}] added! Configure the DNS records shown below and click Verify DNS.");
    }

    /**
     * Trigger DNS Verification (TXT / A / CNAME check) for a Custom Domain.
     */
    public function verifyDomain(Request $request, Domain $domain)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();

        if (!$domain->tenant || !$user->canAccessTenant($domain->tenant)) {
            abort(403);
        }

        $forceLocal = $request->boolean('simulate_local') || $user->isSuperAdmin();
        $result = $this->domainVerifier->verifyDomain($domain, $forceLocal);

        if ($result['verified']) {
            ActivityLog::record(
                'Custom Domain Verified & SSL Activated',
                "{$domain->domain} ({$result['method']})",
                'bi-patch-check-fill',
                '#DCFCE7',
                '#10B981'
            );

            return back()->with('success', $result['message']);
        }

        return back()->withErrors(['domain' => $result['message']]);
    }

    /**
     * Set a verified Domain as the Primary Domain for its Tenant (enables 301 redirect from secondary domains).
     */
    public function setPrimaryDomain(Domain $domain)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();

        if (!$domain->tenant || !$user->canAccessTenant($domain->tenant)) {
            abort(403);
        }

        if ($domain->type === 'custom' && $domain->verified_at === null) {
            return back()->withErrors(['domain' => 'Please verify DNS ownership of this custom domain before setting it as Primary.']);
        }

        Domain::where('tenant_id', $domain->tenant_id)->update(['is_primary' => false]);
        $domain->update(['is_primary' => true]);

        return back()->with('success', "[{$domain->domain}] is now the Primary Domain for [{$domain->tenant->name}]. All other domains will 301 redirect to it.");
    }

    /**
     * Remove a Custom Domain from a Tenant.
     */
    public function destroyDomain(Domain $domain)
    {
        /** @var User $user */
        $user = Auth::user() ?? User::first();

        if (!$domain->tenant || !$user->canAccessTenant($domain->tenant)) {
            abort(403);
        }

        if ($domain->type === 'subdomain') {
            return back()->withErrors(['domain' => 'The default platform subdomain cannot be deleted.']);
        }

        $wasPrimary = $domain->is_primary;
        $tenantId = $domain->tenant_id;
        $host = $domain->domain;
        $domain->delete();

        // If the deleted custom domain was primary, promote the default subdomain back to primary
        if ($wasPrimary) {
            $fallbackSubdomain = Domain::where('tenant_id', $tenantId)->orderBy('id')->first();
            $fallbackSubdomain?->update(['is_primary' => true]);
        }

        return back()->with('success', "Custom domain [{$host}] removed.");
    }

    /**
     * Caddy On-Demand TLS Ask Endpoint: GET /internal/domain-check?domain=example.com
     * Returns HTTP 200 if Caddy is allowed to issue a Let's Encrypt certificate for this domain, or 404 otherwise.
     */
    public function caddyDomainCheck(Request $request)
    {
        $rawDomain = (string) $request->query('domain', '');
        if ($rawDomain === '') {
            return response('Missing domain parameter', 400);
        }

        $host = strtolower(trim($rawDomain));
        $cleanHost = preg_replace('/^www\./i', '', $host);

        $centralDomains = array_map('strtolower', config('app.central_domains', ['localhost', 'multi-website-hub.test']));
        $dashboardDomain = strtolower((string) config('app.dashboard_domain', 'app.multi-website-hub.test'));

        // 1. Central domain or Dashboard domain
        if (in_array($host, $centralDomains, true) || in_array($cleanHost, $centralDomains, true) || $host === $dashboardDomain) {
            return response('Authorized central domain', 200);
        }

        // 2. Verified domain in core_domains attached to an accessible tenant
        $domainRecord = Domain::whereIn('domain', [$host, $cleanHost])
            ->whereNotNull('verified_at')
            ->with('tenant')
            ->first();

        if ($domainRecord && $domainRecord->tenant && $domainRecord->tenant->isAccessible()) {
            return response('Authorized verified tenant domain', 200);
        }

        // 3. Valid active tenant subdomain under any central domain
        foreach ($centralDomains as $baseDomain) {
            $suffix = '.' . $baseDomain;
            if (str_ends_with($host, $suffix)) {
                $sub = substr($host, 0, -strlen($suffix));
                if ($sub !== '' && Tenant::where('subdomain', $sub)->whereIn('status', ['trial', 'active'])->exists()) {
                    return response('Authorized active tenant subdomain', 200);
                }
            }
        }

        return response('Unauthorized domain', 404);
    }
}
