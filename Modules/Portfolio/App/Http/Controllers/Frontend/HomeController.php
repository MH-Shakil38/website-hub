<?php

namespace Modules\Portfolio\App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Modules\Portfolio\App\Models\ModuleMenu;
use Modules\Portfolio\App\Models\PortfolioBlog;
use Modules\Portfolio\App\Models\PortfolioEducation;
use Modules\Portfolio\App\Models\PortfolioExperience;
use Modules\Portfolio\App\Models\PortfolioFaq;
use Modules\Portfolio\App\Models\PortfolioMessage;
use Modules\Portfolio\App\Models\PortfolioProject;
use Modules\Portfolio\App\Models\PortfolioService;
use Modules\Portfolio\App\Models\PortfolioSkill;
use Modules\Portfolio\App\Models\PortfolioSubscriber;
use Modules\Portfolio\App\Models\PortfolioTestimonial;
use Modules\Portfolio\App\Services\PortfolioDataService;

class HomeController extends Controller
{
    public function __construct(
        protected PortfolioDataService $dataService
    ) {}

    protected function sharedData(): array
    {
        $this->dataService->ensureSeeded();
        $t = $this->dataService->getSettingsData();
        $headerMenus = ModuleMenu::where('location', 'header')->where('is_active', true)->orderBy('sort_order')->get();

        return compact('t', 'headerMenus');
    }

    public function index()
    {
        $shared = $this->sharedData();

        $skills = PortfolioSkill::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        $services = PortfolioService::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        $projects = PortfolioProject::where('is_active', true)->orderByDesc('is_featured')->orderBy('sort_order')->orderBy('id')->get();
        $experiences = PortfolioExperience::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        $educations = PortfolioEducation::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        $testimonials = PortfolioTestimonial::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        $blogs = PortfolioBlog::where('is_active', true)->latest('id')->take(6)->get();
        $faqs = PortfolioFaq::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();

        $projectCategories = $projects->pluck('category')->filter()->unique()->values();
        $skillCategories = $skills->pluck('category')->filter()->unique()->values();

        return view('portfolio::frontend.home', array_merge($shared, compact(
            'skills',
            'services',
            'projects',
            'experiences',
            'educations',
            'testimonials',
            'blogs',
            'faqs',
            'projectCategories',
            'skillCategories'
        )));
    }

    public function portfolio(Request $request)
    {
        return $this->projects($request);
    }

    public function projects(Request $request)
    {
        $shared = $this->sharedData();
        $category = $request->query('category');
        $search = trim((string) $request->query('search', ''));

        $projects = PortfolioProject::where('is_active', true)
            ->when($category && $category !== 'All', fn ($q) => $q->where('category', $category))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('title', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%")
                        ->orWhere('short_description', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->paginate(8)
            ->withQueryString();

        $categories = PortfolioProject::where('is_active', true)->pluck('category')->filter()->unique()->values();

        return view('portfolio::frontend.projects', array_merge($shared, compact('projects', 'categories', 'category', 'search')));
    }

    public function projectShow(string $slug)
    {
        $shared = $this->sharedData();
        $project = PortfolioProject::where('slug', $slug)->firstOrFail();
        $relatedProjects = PortfolioProject::where('is_active', true)
            ->where('id', '!=', $project->id)
            ->orderBy('sort_order')
            ->take(3)
            ->get();

        return view('portfolio::frontend.project-details', array_merge($shared, compact('project', 'relatedProjects')));
    }

    public function serviceShow(string $slug)
    {
        $shared = $this->sharedData();
        $service = PortfolioService::where('slug', $slug)->firstOrFail();
        $otherServices = PortfolioService::where('is_active', true)
            ->where('id', '!=', $service->id)
            ->orderBy('sort_order')
            ->take(5)
            ->get();

        return view('portfolio::frontend.service-details', array_merge($shared, compact('service', 'otherServices')));
    }

    public function blog(Request $request)
    {
        $shared = $this->sharedData();
        $category = $request->query('category');
        $search = trim((string) $request->query('search', ''));

        $blogs = PortfolioBlog::where('is_active', true)
            ->when($category && $category !== 'All', fn ($q) => $q->where('category', $category))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('title', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%")
                        ->orWhere('short_description', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate(6)
            ->withQueryString();

        $categories = PortfolioBlog::where('is_active', true)->pluck('category')->filter()->unique()->values();

        return view('portfolio::frontend.blog', array_merge($shared, compact('blogs', 'categories', 'category', 'search')));
    }

    public function blogShow(string $slug)
    {
        $shared = $this->sharedData();
        $blog = PortfolioBlog::where('slug', $slug)->firstOrFail();
        $blog->increment('views');

        $relatedBlogs = PortfolioBlog::where('is_active', true)
            ->where('id', '!=', $blog->id)
            ->latest('id')
            ->take(3)
            ->get();

        return view('portfolio::frontend.blog-details', array_merge($shared, compact('blog', 'relatedBlogs')));
    }

    public function contact()
    {
        return $this->index();
    }

    public function page(string $page)
    {
        if ($page === 'projects' || $page === 'portfolio') {
            return $this->projects(request());
        }
        if ($page === 'blog') {
            return $this->blog(request());
        }
        if ($page === 'sitemap.xml') {
            return $this->sitemap();
        }
        if (str_starts_with($page, 'project-')) {
            return $this->projectShow(substr($page, 8));
        }
        if (str_starts_with($page, 'article-')) {
            return $this->blogShow(substr($page, 8));
        }
        if (str_starts_with($page, 'service-')) {
            return $this->serviceShow(substr($page, 8));
        }

        return $this->index();
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:120',
            'email'   => 'required|email|max:150',
            'phone'   => 'nullable|string|max:40',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string|max:3000',
        ]);

        $msg = PortfolioMessage::create([
            'name'    => $validated['name'],
            'email'   => $validated['email'],
            'phone'   => $validated['phone'] ?? null,
            'subject' => $validated['subject'] ?? 'Portfolio Website Inquiry',
            'message' => $validated['message'],
            'status'  => 'Unread',
        ]);

        ActivityLog::record(
            'New portfolio inquiry received',
            "{$msg->name} ({$msg->email}) [port_messages]",
            'bi-envelope-check-fill',
            '#DBEAFE',
            '#2563EB'
        );

        return back()->with('portfolio_success', 'Thank you, ' . $msg->name . '! Your message has been sent successfully. I will get back to you soon.');
    }

    public function submitNewsletter(Request $request)
    {
        $validated = $request->validate([
            'newsletter_email' => 'required|email|max:150',
        ]);

        PortfolioSubscriber::updateOrCreate(
            ['email' => $validated['newsletter_email']],
            ['status' => 'Subscribed']
        );

        return back()->with('portfolio_success', 'Thank you for subscribing to my developer newsletter!');
    }

    public function sitemap()
    {
        $this->dataService->ensureSeeded();
        $projects = PortfolioProject::where('is_active', true)->get();
        $blogs = PortfolioBlog::where('is_active', true)->get();
        $services = PortfolioService::where('is_active', true)->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        $xml .= '  <url><loc>' . url('/developer-portfolio') . '</loc><priority>1.0</priority></url>' . "\n";
        $xml .= '  <url><loc>' . route('portfolio.projects') . '</loc><priority>0.9</priority></url>' . "\n";
        $xml .= '  <url><loc>' . route('portfolio.blog') . '</loc><priority>0.8</priority></url>' . "\n";

        foreach ($projects as $p) {
            $xml .= '  <url><loc>' . route('portfolio.project.show', $p->slug) . '</loc><priority>0.8</priority></url>' . "\n";
        }
        foreach ($services as $s) {
            $xml .= '  <url><loc>' . route('portfolio.service.show', $s->slug) . '</loc><priority>0.7</priority></url>' . "\n";
        }
        foreach ($blogs as $b) {
            $xml .= '  <url><loc>' . route('portfolio.blog.show', $b->slug) . '</loc><priority>0.7</priority></url>' . "\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function asset(string $type, string $file)
    {
        $path = module_path('Portfolio', "Public/assets/{$type}/" . basename($file));
        abort_unless(is_file($path), 404);

        $mime = $type === 'css' ? 'text/css' : 'application/javascript';

        return response()->file($path, [
            'Content-Type' => $mime . '; charset=UTF-8',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
