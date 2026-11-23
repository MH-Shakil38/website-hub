<?php

namespace App\Http\Middleware;

use App\Models\Domain;
use App\Models\Tenant;
use App\Services\TenantDatabaseManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    public function __construct(
        protected TenantDatabaseManager $tenantDb
    ) {}

    /**
     * Identify whether the incoming request belongs to a Tenant domain/subdomain,
     * the Central SaaS Hub, or an Admin selected tenant context.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (config('app.mode', 'saas') === 'single') {
            return $next($request);
        }

        try {
            if (!Schema::connection('central')->hasTable('core_tenants')) {
                return $next($request);
            }
        } catch (\Throwable) {
            return $next($request);
        }

        // 1. Admin Panel Context: connect to currently selected tenant in session (or active module's demo tenant)
        if ($request->is('admin') || $request->is('admin/*')) {
            $this->connectAdminTenantContext($request);
            return $next($request);
        }

        $host = strtolower($request->getHost());
        $centralDomains = array_map('strtolower', config('app.central_domains', ['localhost', '127.0.0.1', 'multi-website-hub.test']));
        $dashboardDomain = strtolower((string) config('app.dashboard_domain', 'app.multi-website-hub.test'));

        // 2. If visiting a non-central host (custom domain or tenant subdomain like client1.multi-website-hub.test)
        if (!in_array($host, $centralDomains, true) && $host !== $dashboardDomain) {
            $tenant = $this->resolveTenantByHost($host, $centralDomains);

            if ($tenant) {
                if (!$tenant->isAccessible()) {
                    return response()->view('frontend.tenant-suspended', ['tenant' => $tenant], 403);
                }

                $this->tenantDb->connect($tenant);
            }
        }

        return $next($request);
    }

    protected function resolveTenantByHost(string $host, array $centralDomains): ?Tenant
    {
        // A. Direct match in core_domains
        $domainRecord = Domain::where('domain', $host)->with('tenant')->first();
        if ($domainRecord && $domainRecord->tenant) {
            return $domainRecord->tenant;
        }

        // B. Subdomain match against any central domain (e.g. mysite.multi-website-hub.test or mysite.localhost)
        foreach ($centralDomains as $baseDomain) {
            $suffix = '.' . $baseDomain;
            if (str_ends_with($host, $suffix)) {
                $sub = substr($host, 0, -strlen($suffix));
                if ($sub !== '' && !in_array($sub, ['www', 'app', 'admin', 'api', 'mail'], true)) {
                    return Tenant::where('subdomain', $sub)->first();
                }
            }
        }

        return null;
    }

    protected function connectAdminTenantContext(Request $request): void
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        $requestedTenantId = $request->input('tenant_id');
        if ($requestedTenantId) {
            $tenant = Tenant::find($requestedTenantId);
            if ($tenant && (!$user || $user->canAccessTenant($tenant))) {
                session([
                    'admin_tenant_id' => $tenant->id,
                    'admin_cms_module' => $tenant->theme_module,
                ]);
                $this->tenantDb->connect($tenant);
                return;
            }
        }

        $requestedModule = $request->input('cms_module');
        if ($requestedModule) {
            $query = $user ? $user->accessibleTenantsQuery() : Tenant::query();
            $demoTenant = (clone $query)->where('theme_module', $requestedModule)
                ->orderByDesc('is_demo')
                ->first();
            if ($demoTenant) {
                session([
                    'admin_tenant_id' => $demoTenant->id,
                    'admin_cms_module' => $demoTenant->theme_module,
                ]);
                $this->tenantDb->connect($demoTenant);
                return;
            }
        }

        $sessionTenantId = session('admin_tenant_id');
        if ($sessionTenantId) {
            $tenant = Tenant::find($sessionTenantId);
            if ($tenant && (!$user || $user->canAccessTenant($tenant))) {
                session(['admin_cms_module' => $tenant->theme_module]);
                $this->tenantDb->connect($tenant);
                return;
            }
        }

        // Fallback: connect to the first accessible tenant for the user (or demo tenant for Super Admin)
        $query = $user ? $user->accessibleTenantsQuery() : Tenant::query();
        $currentMod = \App\Support\ModuleCmsResolver::currentModule($request);

        $fallbackTenant = (clone $query)->where('theme_module', $currentMod)
            ->orderByDesc('is_demo')
            ->first() ?? (clone $query)->first();

        if ($fallbackTenant) {
            session([
                'admin_tenant_id' => $fallbackTenant->id,
                'admin_cms_module' => $fallbackTenant->theme_module,
            ]);
            $this->tenantDb->connect($fallbackTenant);
        }
    }
}
