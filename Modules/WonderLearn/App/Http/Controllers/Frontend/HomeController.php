<?php

namespace Modules\WonderLearn\App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Modules\WonderLearn\App\Models\ModuleMedia;
use Modules\WonderLearn\App\Models\ModuleMenu;
use Modules\WonderLearn\App\Models\ModulePage;
use Modules\WonderLearn\App\Models\ModuleSlider;
use Modules\WonderLearn\App\Models\ModuleTheme;
use Modules\WonderLearn\App\Models\ModuleVideo;
use Modules\WonderLearn\App\Models\WlAdventure;
use Modules\WonderLearn\App\Models\WlEnrollment;
use Modules\WonderLearn\App\Models\WlGame;
use Modules\WonderLearn\App\Models\WlStory;
use Modules\WonderLearn\App\Models\WlSubscriber;
use Modules\WonderLearn\App\Models\WlTestimonial;
use Modules\WonderLearn\App\Models\WlTopic;
use Modules\WonderLearn\App\Services\WonderLearnDataService;

class HomeController extends Controller
{
    public function __construct(
        protected WonderLearnDataService $dataService
    ) {}

    public function index(?Request $request = null)
    {
        $this->dataService->ensureSeeded();

        $s            = $this->dataService->getSettingsData();
        $headerMenus  = ModuleMenu::where('location', 'header')->where('is_active', true)->orderBy('sort_order')->get();
        $activeTheme  = ModuleTheme::where('is_active', true)->first();
        $topics       = WlTopic::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        $stories      = WlStory::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        $games        = WlGame::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        $adventures   = WlAdventure::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        $testimonials = WlTestimonial::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        $pages        = ModulePage::where('status', 'Published')->orderBy('id')->get();
        $videos       = ModuleVideo::where('is_active', true)->orderBy('sort_order')->get();
        $mediaItems   = ModuleMedia::latest('id')->get();
        $sliders      = ModuleSlider::where('is_active', true)->orderBy('sort_order')->get();

        return view('wonderlearn::frontend.home', compact(
            's',
            'headerMenus',
            'activeTheme',
            'topics',
            'stories',
            'games',
            'adventures',
            'testimonials',
            'pages',
            'videos',
            'mediaItems',
            'sliders'
        ));
    }

    public function submitEnrollment(Request $request)
    {
        $validated = $request->validate([
            'parent_name'    => 'required|string|max:120',
            'child_name'     => 'required|string|max:120',
            'child_age'      => 'required|integer|min:3|max:14',
            'email'          => 'required|email|max:150',
            'phone'          => 'nullable|string|max:40',
            'favorite_topic' => 'nullable|string|max:80',
        ]);

        $enrollment = WlEnrollment::create([
            'parent_name'    => $validated['parent_name'],
            'child_name'     => $validated['child_name'],
            'child_age'      => $validated['child_age'],
            'email'          => $validated['email'],
            'phone'          => $validated['phone'] ?? null,
            'favorite_topic' => $validated['favorite_topic'] ?? 'Math Magic',
            'plan_type'      => 'Wonder Explorer Pass',
            'status'         => 'Active',
        ]);

        ActivityLog::record(
            'New WonderLearn Explorer Joined',
            "{$enrollment->child_name} (Age {$enrollment->child_age}) — Parent: {$enrollment->parent_name} [wl_enrollments]",
            'bi-stars',
            '#FEF3C7',
            '#D97706'
        );

        return back()->with(
            'wl_success',
            "Welcome to WonderLearn, {$enrollment->child_name} & {$enrollment->parent_name}! Your magical learning adventure is now unlocked!"
        );
    }

    public function submitNewsletter(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:150',
        ]);

        WlSubscriber::updateOrCreate(
            ['email' => $validated['email']],
            ['status' => 'Subscribed']
        );

        return back()->with(
            'wl_success',
            'Thank you for subscribing to WonderLearn parent updates, new stories, and learning tips!'
        );
    }

    public function page(string $page)
    {
        if ($page === 'sitemap.xml') {
            return $this->sitemap();
        }

        return $this->index(request());
    }

    public function sitemap()
    {
        $this->dataService->ensureSeeded();
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        $xml .= '  <url><loc>' . url('/wonder-learn') . '</loc><priority>1.0</priority></url>' . "\n";
        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function asset(string $type, string $file)
    {
        $path = module_path('WonderLearn', "Public/assets/{$type}/" . basename($file));
        abort_unless(is_file($path), 404);

        $mime = $type === 'css' ? 'text/css' : 'application/javascript';

        return response()->file($path, [
            'Content-Type'  => $mime . '; charset=UTF-8',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
