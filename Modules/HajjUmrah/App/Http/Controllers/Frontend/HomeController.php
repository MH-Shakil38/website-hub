<?php

namespace Modules\HajjUmrah\App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Modules\HajjUmrah\App\Models\HajjBlog;
use Modules\HajjUmrah\App\Models\HajjInquiry;
use Modules\HajjUmrah\App\Models\HajjPackage;
use Modules\HajjUmrah\App\Models\HajjPartner;
use Modules\HajjUmrah\App\Models\HajjPricingPlan;
use Modules\HajjUmrah\App\Models\HajjService;
use Modules\HajjUmrah\App\Models\HajjSubscriber;
use Modules\HajjUmrah\App\Models\HajjTestimonial;
use Modules\HajjUmrah\App\Models\ModuleMedia;
use Modules\HajjUmrah\App\Models\ModuleMenu;
use Modules\HajjUmrah\App\Models\ModuleVideo;
use Modules\HajjUmrah\App\Services\HajjUmrahDataService;

class HomeController extends Controller
{
    public function __construct(
        protected HajjUmrahDataService $dataService
    ) {}

    protected function resolveLocale(Request $request): string
    {
        if ($request->filled('lang') && in_array($request->query('lang'), ['en', 'bn', 'ar'], true)) {
            $loc = $request->query('lang');
            session(['hajj_locale' => $loc]);
            return $loc;
        }

        $t = $this->dataService->getSettingsData();
        $defaultLang = $t['general']['default_language'] ?? 'en';
        $loc = session('hajj_locale', $defaultLang);

        return in_array($loc, ['en', 'bn', 'ar'], true) ? $loc : 'en';
    }

    public function switchLanguage(string $locale)
    {
        if (in_array($locale, ['en', 'bn', 'ar'], true)) {
            session(['hajj_locale' => $locale]);
        }

        return back();
    }

    protected function sharedData(Request $request): array
    {
        $this->dataService->ensureSeeded();
        $t = $this->dataService->getSettingsData();
        $locale = $this->resolveLocale($request);
        $isRtl = ($locale === 'ar');
        $tr = $t['translations'][$locale] ?? $t['translations']['en'];

        $headerMenus = ModuleMenu::where('location', 'header')->where('is_active', true)->orderBy('sort_order')->get();
        $dataService = $this->dataService;

        return compact('t', 'locale', 'isRtl', 'tr', 'headerMenus', 'dataService');
    }

    public function index(Request $request = null)
    {
        $request = $request ?? request();
        $shared = $this->sharedData($request);

        $packages = HajjPackage::where('is_active', true)->orderByDesc('is_featured')->orderBy('sort_order')->orderBy('id')->get();
        $specialOffers = $packages->where('is_featured', true)->take(4);
        if ($specialOffers->isEmpty()) {
            $specialOffers = $packages->take(4);
        }

        $hajjPackages = $packages->filter(fn ($p) => strtolower($p->package_type) === 'hajj')->values();
        $umrahPackages = $packages->filter(fn ($p) => strtolower($p->package_type) === 'umrah')->values();

        $services = HajjService::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        $pricingPlans = HajjPricingPlan::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        $trustBadges = HajjPartner::where('is_active', true)->whereIn('type', ['review_badge', 'TrustBadge'])->orderBy('sort_order')->get();
        $airlinePartners = HajjPartner::where('is_active', true)->whereIn('type', ['airline', 'hotel', 'Airline', 'Hotel'])->orderBy('sort_order')->get();
        $videos = ModuleVideo::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        $testimonials = HajjTestimonial::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        $galleryItems = ModuleMedia::latest('id')->take(6)->get();
        $blogs = HajjBlog::where('is_active', true)->latest('id')->take(3)->get();
        $sliders = \Modules\HajjUmrah\App\Models\ModuleSlider::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();

        return view('hajjumrah::frontend.home', array_merge($shared, compact(
            'packages',
            'specialOffers',
            'hajjPackages',
            'umrahPackages',
            'services',
            'pricingPlans',
            'trustBadges',
            'airlinePartners',
            'videos',
            'testimonials',
            'galleryItems',
            'blogs',
            'sliders'
        )));
    }

    public function packages(Request $request)
    {
        return $this->index($request);
    }

    public function packageShow(string $slug)
    {
        $shared = $this->sharedData(request());
        $package = HajjPackage::where('slug', $slug)->firstOrFail();
        $relatedPackages = HajjPackage::where('is_active', true)
            ->where('id', '!=', $package->id)
            ->orderBy('sort_order')
            ->take(3)
            ->get();

        return view('hajjumrah::frontend.package-details', array_merge($shared, compact('package', 'relatedPackages')));
    }

    public function blog(Request $request)
    {
        return $this->index($request);
    }

    public function blogShow(string $slug)
    {
        $shared = $this->sharedData(request());
        $blog = HajjBlog::where('slug', $slug)->firstOrFail();
        $blog->increment('views');

        $relatedBlogs = HajjBlog::where('is_active', true)
            ->where('id', '!=', $blog->id)
            ->latest('id')
            ->take(3)
            ->get();

        return view('hajjumrah::frontend.blog-details', array_merge($shared, compact('blog', 'relatedBlogs')));
    }

    public function page(string $page)
    {
        if (str_starts_with($page, 'lang-')) {
            $loc = substr($page, 5, 2);
            return $this->switchLanguage($loc);
        }
        if (str_starts_with($page, 'package-')) {
            return $this->packageShow(substr($page, 8));
        }
        if (str_starts_with($page, 'article-')) {
            return $this->blogShow(substr($page, 8));
        }
        if ($page === 'sitemap.xml') {
            return $this->sitemap();
        }

        return $this->index(request());
    }

    public function submitInquiry(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:120',
            'email'         => 'nullable|email|max:150',
            'phone'         => 'required|string|max:40',
            'package_name'  => 'nullable|string|max:180',
            'pilgrim_count' => 'nullable|integer|min:1|max:50',
            'travel_date'   => 'nullable|string|max:80',
            'message'       => 'nullable|string|max:2000',
        ]);

        $inq = HajjInquiry::create([
            'name'            => $validated['name'],
            'email'           => $validated['email'] ?? 'pilgrim@alhajenra.com',
            'phone'           => $validated['phone'],
            'package_name'    => $validated['package_name'] ?? 'General Hajj & Umrah Package',
            'travelers_count' => ($validated['pilgrim_count'] ?? 1) . ' Persons',
            'preferred_date'  => $validated['travel_date'] ?? 'Upcoming Season',
            'message'         => $validated['message'] ?? null,
            'status'          => 'Pending',
        ]);

        ActivityLog::record(
            'New Hajj/Umrah booking inquiry',
            "{$inq->name} ({$inq->phone}) - {$inq->package_name} [hajj_inquiries]",
            'bi-moon-stars-fill',
            '#DCFCE7',
            '#10B981'
        );

        return back()->with('hajj_success', 'JazakAllah Khair, ' . $inq->name . '! Your Hajj/Umrah package inquiry has been submitted. Our Muallim advisor will contact you shortly.');
    }

    public function submitNewsletter(Request $request)
    {
        $validated = $request->validate([
            'newsletter_email' => 'required|email|max:150',
        ]);

        HajjSubscriber::updateOrCreate(
            ['email' => $validated['newsletter_email']],
            ['status' => 'Subscribed']
        );

        return back()->with('hajj_success', 'Thank you for subscribing to Al Hajenra Hajj & Umrah updates!');
    }

    public function sitemap()
    {
        $this->dataService->ensureSeeded();
        $packages = HajjPackage::where('is_active', true)->get();
        $blogs = HajjBlog::where('is_active', true)->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        $xml .= '  <url><loc>' . url('/hajj-umrah') . '</loc><priority>1.0</priority></url>' . "\n";

        foreach ($packages as $p) {
            $xml .= '  <url><loc>' . route('hajjumrah.package.show', $p->slug) . '</loc><priority>0.9</priority></url>' . "\n";
        }
        foreach ($blogs as $b) {
            $xml .= '  <url><loc>' . route('hajjumrah.blog.show', $b->slug) . '</loc><priority>0.8</priority></url>' . "\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function asset(string $type, string $file)
    {
        $path = module_path('HajjUmrah', "Public/assets/{$type}/" . basename($file));
        abort_unless(is_file($path), 404);

        $mime = $type === 'css' ? 'text/css' : 'application/javascript';

        return response()->file($path, [
            'Content-Type' => $mime . '; charset=UTF-8',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
