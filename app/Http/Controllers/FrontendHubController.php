<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\SaasSection;
use App\Models\Tenant;
use App\Models\Website;
use App\Services\TenantDatabaseManager;
use Illuminate\Support\Facades\Schema;

class FrontendHubController extends Controller
{
    public function __construct(
        protected TenantDatabaseManager $tenantDb
    ) {}

    /**
     * Root URL (http://multi-website-hub.test/ or tenant domain/subdomain)
     * - If a Tenant was resolved by IdentifyTenant middleware -> runs that tenant's theme module directly at '/'.
     * - If ONLY 1 module is active -> runs that single module's website directly at '/'.
     * - If MULTIPLE modules are active -> shows the "All Themes Showcase Landing Page".
     */
    public function index()
    {
        // 0. SaaS Tenant Domain/Subdomain Mode: Render tenant's theme directly at '/'
        if (app()->bound('tenant')) {
            /** @var Tenant $tenant */
            $tenant = app('tenant');
            return $this->dispatchModuleAction($tenant->theme_module, 'index');
        }

        $activeSites = Website::activeModules();

        // 0b. In SaaS Mode, the central domain root '/' ALWAYS renders the SaaS Hub Landing Page
        if (config('app.mode', 'saas') === 'saas') {
            if ($activeSites->count() <= 1) {
                $activeSites = Website::whereNotNull('module_name')->orderBy('id')->get();
            }
            return view('frontend.hub-landing', $this->buildHubLandingData($activeSites));
        }

        // 1. Legacy Single Active Module Mode: Runs directly on root '/'
        if ($activeSites->count() === 1) {
            $singleSite = $activeSites->first();
            $this->connectDemoTenantForModule($singleSite->module_name);

            return $this->dispatchModuleAction($singleSite->module_name, 'index');
        }

        // 2. Multi-Module Showcase Hub Mode: Shows all active themes landing page
        if ($activeSites->count() > 1) {
            return view('frontend.hub-landing', $this->buildHubLandingData($activeSites));
        }

        // 3. Fallback if no database active modules: check config('app.active_module')
        $activeModuleName = config('app.active_module');
        if ($activeModuleName && $activeModuleName !== 'Hub' && is_dir(base_path('Modules/' . $activeModuleName))) {
            $this->connectDemoTenantForModule($activeModuleName);
            return $this->dispatchModuleAction($activeModuleName, 'index');
        }

        return view('frontend.hub-landing', $this->buildHubLandingData($activeSites));
    }

    protected function buildHubLandingData($activeSites): array
    {
        $saasSections = SaasSection::getLandingMap();
        $clientPlans = collect();

        try {
            if (Schema::connection('central')->hasTable('core_plans')) {
                Plan::ensureDefaultsSeeded();
                $clientPlans = Plan::where('audience', 'client')
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();
            }
        } catch (\Throwable) {
        }

        return [
            'activeSites' => $activeSites,
            'saasSections' => $saasSections,
            'clientPlans' => $clientPlans,
        ];
    }

    /**
     * Root-level subpages when visiting a Tenant domain/subdomain OR when ONLY 1 module is active
     * (e.g. /portfolio, /properties, /catalog, /contact)
     */
    public function singleModuleSubpage(string $subpage)
    {
        if (app()->bound('tenant')) {
            /** @var Tenant $tenant */
            $tenant = app('tenant');
            return $this->dispatchModuleAction($tenant->theme_module, $subpage);
        }

        $activeSites = Website::activeModules();

        if ($activeSites->count() !== 1) {
            abort(404);
        }

        $singleSite = $activeSites->first();
        $this->connectDemoTenantForModule($singleSite->module_name);

        return $this->dispatchModuleAction($singleSite->module_name, $subpage);
    }

    /**
     * Sub-path module routes when MULTIPLE modules are active (e.g. /interior-design, /real-estate, /pharmacy)
     */
    public function moduleRoute(string $slug, ?string $subpage = null)
    {
        $site = Website::where('slug', $slug)->whereNotNull('module_name')->first();

        if (!$site || !$site->is_active) {
            abort(404, 'This website module is currently inactive.');
        }

        $activeSites = Website::activeModules();

        // If only 1 module is active, canonical URL is root '/'
        if ($activeSites->count() === 1 && $activeSites->first()->id === $site->id) {
            return redirect($subpage ? url('/' . $subpage) : url('/'));
        }

        $this->connectDemoTenantForModule($site->module_name);

        return $this->dispatchModuleAction($site->module_name, $subpage ?: 'index');
    }

    /**
     * Connect to the dedicated demo tenant database for a module if available in SaaS mode.
     */
    protected function connectDemoTenantForModule(string $moduleName): void
    {
        if (config('app.mode', 'saas') !== 'saas' || app()->bound('tenant')) {
            return;
        }

        try {
            if (!Schema::connection('central')->hasTable('core_tenants')) {
                return;
            }

            $demoTenant = Tenant::where('theme_module', $moduleName)
                ->where('is_demo', true)
                ->first();

            if ($demoTenant && $demoTenant->db_name) {
                $this->tenantDb->connect($demoTenant);
            }
        } catch (\Throwable) {
            // Safely fall back if demo tenant is not provisioned yet
        }
    }

    /**
     * Invoke the target module's Frontend\HomeController method.
     */
    protected function dispatchModuleAction(string $moduleName, string $action)
    {
        $controllerClass = "Modules\\{$moduleName}\\App\\Http\\Controllers\\Frontend\\HomeController";

        if (!class_exists($controllerClass)) {
            abort(404, "Frontend controller for module [{$moduleName}] not found.");
        }

        $controller = app($controllerClass);

        $methodMap = [
            'index' => 'index',
            'portfolio' => 'portfolio',
            'properties' => 'properties',
            'catalog' => 'catalog',
            'contact' => 'contact',
        ];

        $method = $methodMap[$action] ?? $action;

        if (!method_exists($controller, $method)) {
            // Modules with many static pages (e.g. TechCompany) expose a generic page($page) handler
            if (method_exists($controller, 'page')) {
                return app()->call([$controller, 'page'], ['page' => $action]);
            }

            abort(404);
        }

        return app()->call([$controller, $method]);
    }
}
