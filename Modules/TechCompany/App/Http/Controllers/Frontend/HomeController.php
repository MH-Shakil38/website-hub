<?php

namespace Modules\TechCompany\App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Website;

class HomeController extends Controller
{
    /**
     * Static pages available in the TechCompany frontend (view: techcompany::frontend.pages.{key}).
     */
    public const PAGES = [
        'index', 'about', 'services', 'portfolio', 'pricing', 'faq', 'contact',
        'service-ecommerce', 'service-pos', 'service-real-estate', 'service-interior-design',
        'service-hospital', 'service-hrm', 'service-manufacturing', 'service-accounting',
        'service-education', 'service-custom-development',
    ];

    public function index()
    {
        return $this->render('index');
    }

    /**
     * Generic page handler used by the hub dispatcher (e.g. /about, /software-company/service-pos).
     */
    public function page(string $page)
    {
        abort_unless(in_array($page, self::PAGES, true), 404);

        return $this->render($page);
    }

    public function asset(string $type, string $file)
    {
        $path = module_path('TechCompany', "Public/assets/{$type}/" . basename($file));
        abort_unless(is_file($path), 404);

        $mime = $type === 'css' ? 'text/css' : 'application/javascript';

        return response()->file($path, [
            'Content-Type' => $mime . '; charset=UTF-8',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    protected function render(string $page)
    {
        $base = $this->baseUrl();

        $url = fn (string $key) => $key === 'index'
            ? ($base === '' ? url('/') : url($base))
            : url(ltrim($base . '/' . $key, '/'));

        $active = fn (string $key) => $key === $page ? ' class="active"' : '';

        $asset = fn (string $path) => route('techcompany.asset', [
            'type' => strtok($path, '/'),
            'file' => basename($path),
        ]) . '?v=' . @filemtime(module_path('TechCompany', 'Public/assets/' . $path));

        return view("techcompany::frontend.pages.{$page}", compact('url', 'active', 'asset', 'page'));
    }

    /**
     * '' when TechCompany is the only active module (served at '/'), otherwise '/{slug}'.
     */
    protected function baseUrl(): string
    {
        $active = Website::activeModules();

        if ($active->count() === 1) {
            return '';
        }

        $slug = Website::where('module_name', 'TechCompany')->value('slug') ?: 'software-company';

        return '/' . $slug;
    }
}
