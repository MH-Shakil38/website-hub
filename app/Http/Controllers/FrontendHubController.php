<?php

namespace App\Http\Controllers;

use App\Models\Website;

class FrontendHubController extends Controller
{
    /**
     * Root URL (http://multi-website-hub.test/)
     * - If ONLY 1 module is active -> runs that single module's website directly at '/'.
     * - If MULTIPLE modules are active -> shows the "All Themes Showcase Landing Page".
     */
    public function index()
    {
        $activeSites = Website::activeModules();

        if ($activeSites->count() === 1) {
            $singleSite = $activeSites->first();

            return $this->dispatchModuleAction($singleSite->module_name, 'index');
        }

        return view('frontend.hub-landing', [
            'activeSites' => $activeSites,
        ]);
    }

    /**
     * Root-level subpages when ONLY 1 module is active (e.g. /portfolio, /properties, /catalog, /contact)
     */
    public function singleModuleSubpage(string $subpage)
    {
        $activeSites = Website::activeModules();

        if ($activeSites->count() !== 1) {
            abort(404);
        }

        $singleSite = $activeSites->first();

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

        return $this->dispatchModuleAction($site->module_name, $subpage ?: 'index');
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
