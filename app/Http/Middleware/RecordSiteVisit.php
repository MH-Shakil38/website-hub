<?php

namespace App\Http\Middleware;

use App\Models\SiteVisit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

/**
 * Counts public page views for the dashboard "Website Traffic" chart.
 * Only successful HTML GET requests on public pages are counted; admin,
 * API, assets and bots are skipped. Recording happens after the response is
 * sent and never breaks a page.
 */
class RecordSiteVisit
{
    protected const SKIP_PATHS = [
        'admin', 'admin/*', 'api/*', 'internal/*', 'login', 'logout', 'register', 'password/*',
        'storage/*', 'build/*', 'vendor/*', 'livewire/*', '_debugbar/*', 'up', 'payments/*',
        'impersonate/*', '*.js', '*.css', '*.map', '*.png', '*.jpg', '*.jpeg', '*.gif', '*.svg', '*.ico', '*.webp', '*.xml', '*.txt',
    ];

    protected static ?bool $tableReady = null;

    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        if (! $this->shouldRecord($request, $response)) {
            return;
        }

        try {
            if (static::$tableReady === null) {
                static::$tableReady = Schema::connection('central')->hasTable('core_site_visits');
            }
            if (! static::$tableReady) {
                return;
            }

            SiteVisit::create([
                'host' => substr(strtolower($request->getHost()), 0, 191),
                'path' => substr('/' . ltrim($request->path(), '/'), 0, 255),
                'visitor_hash' => sha1($request->ip() . '|' . $request->userAgent() . '|' . now()->toDateString() . '|' . config('app.key')),
                'tenant_id' => app()->bound('tenant') ? optional(app('tenant'))->id : null,
                'visited_at' => now(),
            ]);
        } catch (\Throwable) {
            // Traffic stats must never break a public page.
        }
    }

    protected function shouldRecord(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET') || $request->ajax() || $request->expectsJson() || $request->prefetch()) {
            return false;
        }

        if ($response->getStatusCode() !== 200) {
            return false;
        }

        $type = (string) $response->headers->get('Content-Type', '');
        if ($type !== '' && ! str_contains($type, 'text/html')) {
            return false;
        }

        if ($request->is(...self::SKIP_PATHS)) {
            return false;
        }

        $agent = strtolower((string) $request->userAgent());
        if ($agent === '' || preg_match('/bot|crawl|spider|slurp|curl|wget|python|headless|lighthouse|monitor|preview/', $agent)) {
            return false;
        }

        // Logged-in staff browsing their own sites should not inflate visitor numbers.
        $user = $request->user();
        if ($user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return false;
        }

        return true;
    }
}
