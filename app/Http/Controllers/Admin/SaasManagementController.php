<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Domain;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Policies\ClientPolicy;
use App\Services\TenantDatabaseManager;
use App\Support\ModuleCmsResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SaasManagementController extends Controller
{
    public function __construct(
        protected TenantDatabaseManager $tenantDb
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
}
