<?php

namespace Modules\Portfolio\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\ModuleManagerService;
use App\Support\ModuleCmsResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Portfolio\App\Models\ModuleSetting;
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

class PortfolioAdminController extends Controller
{
    public function __construct(
        protected PortfolioDataService $dataService,
        protected ModuleManagerService $managerService
    ) {}

    // =========================================================================
    // 1. VISUAL SECTION & THEME SETTINGS (Hero, About, Process, Stats, SEO, Theme Mode)
    // =========================================================================
    public function settings(Request $request)
    {
        $this->dataService->ensureSeeded();
        $t = $this->dataService->getSettingsData();
        $activeTab = $request->query('tab', 'hero');

        return view('portfolio::admin.settings', compact('t', 'activeTab'));
    }

    public function saveSettings(Request $request)
    {
        $raw = ModuleSetting::getValue('theme_sections_json', '');
        $current = !empty($raw) ? (json_decode($raw, true) ?: []) : [];

        $sections = $request->input('sections', []);
        foreach ($sections as $secKey => $secVal) {
            if (is_array($secVal)) {
                $current[$secKey] = isset($current[$secKey]) && is_array($current[$secKey])
                    ? array_replace_recursive($current[$secKey], $secVal)
                    : $secVal;
            }
        }

        // Handle image & CV uploads into Modules/Portfolio/Public/uploads/
        if ($request->hasFile('header_logo')) {
            $current['header']['logo'] = ModuleCmsResolver::storeModuleFile($request->file('header_logo'), 'branding', 'Portfolio');
        }
        if ($request->hasFile('hero_portrait_image')) {
            $current['hero']['portrait_image'] = ModuleCmsResolver::storeModuleFile($request->file('hero_portrait_image'), 'hero', 'Portfolio');
        }
        if ($request->hasFile('hero_cv_file')) {
            $current['hero']['btn_cv_url'] = ModuleCmsResolver::storeModuleFile($request->file('hero_cv_file'), 'cv', 'Portfolio');
        }
        if ($request->hasFile('about_workspace_image')) {
            $current['about']['workspace_image'] = ModuleCmsResolver::storeModuleFile($request->file('about_workspace_image'), 'about', 'Portfolio');
        }

        ModuleSetting::setValue('theme_sections_json', json_encode($current), 'theme');

        ActivityLog::record(
            'Portfolio Sections Updated',
            'Saved in port_settings',
            'bi-code-slash',
            '#DBEAFE',
            '#2563EB'
        );

        return back()->with('success', '[Developer Portfolio] Website sections & settings saved in port_settings!');
    }

    public function resetSettings()
    {
        $this->managerService->resetModuleToDefault('Portfolio');

        return back()->with('success', 'Developer Portfolio module has been reset 100% to its original default template and seeded data!');
    }

    // =========================================================================
    // 2. PORTFOLIO PROJECTS CRUD (port_projects)
    // =========================================================================
    public function projects(Request $request)
    {
        $this->dataService->ensureSeeded();
        $search = trim((string) $request->query('search', ''));
        $category = $request->query('category');

        $projects = PortfolioProject::query()
            ->when($search !== '', fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('category', 'like', "%{$search}%"))
            ->when($category, fn ($q) => $q->where('category', $category))
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $categories = [
            'Real Estate Website',
            'E-commerce Website',
            'Corporate Website',
            'Business Website',
            'Pharmacy Management',
            'POS & ERP Software',
            'News Portal',
            'Custom Web Application',
        ];

        return view('portfolio::admin.projects', compact('projects', 'categories', 'search', 'category'));
    }

    public function storeProject(Request $request)
    {
        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'category'          => 'required|string|max:120',
            'subtitle'          => 'nullable|string|max:150',
            'short_description' => 'nullable|string|max:600',
            'description'       => 'nullable|string',
            'technologies_raw'  => 'nullable|string|max:500',
            'client_name'       => 'nullable|string|max:150',
            'live_url'          => 'nullable|string|max:255',
            'github_url'        => 'nullable|string|max:255',
            'completion_date'   => 'nullable|string|max:80',
            'sort_order'        => 'nullable|integer',
            'thumbnail_file'    => 'nullable|image|max:5120',
            'gallery_files.*'   => 'nullable|image|max:5120',
        ]);

        $thumb = '/module-media/Portfolio/theme/project-1.jpg';
        if ($request->hasFile('thumbnail_file')) {
            $thumb = ModuleCmsResolver::storeModuleFile($request->file('thumbnail_file'), 'projects', 'Portfolio');
        }

        $gallery = [$thumb];
        if ($request->hasFile('gallery_files')) {
            $gallery = [];
            foreach ($request->file('gallery_files') as $gFile) {
                $gallery[] = ModuleCmsResolver::storeModuleFile($gFile, 'projects', 'Portfolio');
            }
        }

        $techArray = array_values(array_filter(array_map('trim', explode(',', $validated['technologies_raw'] ?? 'Laravel, PHP, Tailwind CSS, MySQL'))));

        $project = PortfolioProject::create([
            'title'             => $validated['title'],
            'slug'              => Str::slug($validated['title']) . '-' . Str::lower(Str::random(4)),
            'category'          => $validated['category'],
            'subtitle'          => $validated['subtitle'] ?: $validated['category'],
            'thumbnail'         => $thumb,
            'gallery_images'    => $gallery,
            'short_description' => $validated['short_description'] ?? '',
            'description'       => $validated['description'] ?? $validated['short_description'],
            'technologies'      => $techArray,
            'client_name'       => $validated['client_name'] ?? 'Global Client',
            'live_url'          => $validated['live_url'] ?? '#',
            'github_url'        => $validated['github_url'] ?? '#',
            'completion_date'   => $validated['completion_date'] ?? date('F Y'),
            'is_featured'       => $request->boolean('is_featured', true),
            'is_active'         => $request->boolean('is_active', true),
            'sort_order'        => $validated['sort_order'] ?? (PortfolioProject::max('sort_order') + 1),
        ]);

        ActivityLog::record('Portfolio Project Added', "{$project->title} [port_projects]", 'bi-kanban', '#DBEAFE', '#2563EB');

        return back()->with('success', "Project '{$project->title}' added to port_projects!");
    }

    public function updateProject(Request $request, int $id)
    {
        $project = PortfolioProject::findOrFail($id);

        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'category'          => 'required|string|max:120',
            'subtitle'          => 'nullable|string|max:150',
            'short_description' => 'nullable|string|max:600',
            'description'       => 'nullable|string',
            'technologies_raw'  => 'nullable|string|max:500',
            'client_name'       => 'nullable|string|max:150',
            'live_url'          => 'nullable|string|max:255',
            'github_url'        => 'nullable|string|max:255',
            'completion_date'   => 'nullable|string|max:80',
            'sort_order'        => 'nullable|integer',
            'thumbnail_file'    => 'nullable|image|max:5120',
            'gallery_files.*'   => 'nullable|image|max:5120',
        ]);

        $thumb = $project->thumbnail;
        if ($request->hasFile('thumbnail_file')) {
            $thumb = ModuleCmsResolver::storeModuleFile($request->file('thumbnail_file'), 'projects', 'Portfolio');
        }

        $gallery = $project->gallery_images ?? [$thumb];
        if ($request->hasFile('gallery_files')) {
            $gallery = [];
            foreach ($request->file('gallery_files') as $gFile) {
                $gallery[] = ModuleCmsResolver::storeModuleFile($gFile, 'projects', 'Portfolio');
            }
        }

        $techArray = array_values(array_filter(array_map('trim', explode(',', $validated['technologies_raw'] ?? 'Laravel, PHP'))));

        $project->update([
            'title'             => $validated['title'],
            'category'          => $validated['category'],
            'subtitle'          => $validated['subtitle'] ?: $validated['category'],
            'thumbnail'         => $thumb,
            'gallery_images'    => $gallery,
            'short_description' => $validated['short_description'],
            'description'       => $validated['description'],
            'technologies'      => $techArray,
            'client_name'       => $validated['client_name'],
            'live_url'          => $validated['live_url'],
            'github_url'        => $validated['github_url'],
            'completion_date'   => $validated['completion_date'],
            'is_featured'       => $request->boolean('is_featured'),
            'is_active'         => $request->boolean('is_active'),
            'sort_order'        => $validated['sort_order'] ?? $project->sort_order,
        ]);

        return back()->with('success', "Project '{$project->title}' updated in port_projects!");
    }

    public function deleteProject(int $id)
    {
        PortfolioProject::findOrFail($id)->delete();
        return back()->with('success', 'Project deleted from port_projects.');
    }

    // =========================================================================
    // 3. SKILLS & TECHNOLOGIES CRUD (port_skills)
    // =========================================================================
    public function skills(Request $request)
    {
        $this->dataService->ensureSeeded();
        $search = trim((string) $request->query('search', ''));
        $category = $request->query('category');

        $skills = PortfolioSkill::query()
            ->when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('subtitle', 'like', "%{$search}%"))
            ->when($category, fn ($q) => $q->where('category', $category))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        $categories = ['Backend', 'Frontend', 'Database', 'Tools', 'Design'];

        return view('portfolio::admin.skills', compact('skills', 'categories', 'search', 'category'));
    }

    public function storeSkill(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100',
            'subtitle'      => 'nullable|string|max:120',
            'category'      => 'required|string|max:60',
            'icon_class'    => 'nullable|string|max:100',
            'icon_color'    => 'nullable|string|max:30',
            'level_percent' => 'nullable|integer|min:1|max:100',
            'sort_order'    => 'nullable|integer',
        ]);

        PortfolioSkill::create([
            'name'          => $validated['name'],
            'subtitle'      => $validated['subtitle'] ?: $validated['category'],
            'category'      => $validated['category'],
            'icon_class'    => $validated['icon_class'] ?: 'fa-solid fa-code',
            'icon_color'    => $validated['icon_color'] ?: '#078BFF',
            'level_percent' => $validated['level_percent'] ?? 95,
            'sort_order'    => $validated['sort_order'] ?? (PortfolioSkill::max('sort_order') + 1),
            'is_active'     => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Skill '{$validated['name']}' saved in port_skills!");
    }

    public function updateSkill(Request $request, int $id)
    {
        $skill = PortfolioSkill::findOrFail($id);
        $validated = $request->validate([
            'name'          => 'required|string|max:100',
            'subtitle'      => 'nullable|string|max:120',
            'category'      => 'required|string|max:60',
            'icon_class'    => 'nullable|string|max:100',
            'icon_color'    => 'nullable|string|max:30',
            'level_percent' => 'nullable|integer|min:1|max:100',
            'sort_order'    => 'nullable|integer',
        ]);

        $skill->update([
            'name'          => $validated['name'],
            'subtitle'      => $validated['subtitle'],
            'category'      => $validated['category'],
            'icon_class'    => $validated['icon_class'] ?: 'fa-solid fa-code',
            'icon_color'    => $validated['icon_color'] ?: '#078BFF',
            'level_percent' => $validated['level_percent'] ?? 95,
            'sort_order'    => $validated['sort_order'] ?? $skill->sort_order,
            'is_active'     => $request->boolean('is_active'),
        ]);

        return back()->with('success', "Skill '{$skill->name}' updated in port_skills!");
    }

    public function deleteSkill(int $id)
    {
        PortfolioSkill::findOrFail($id)->delete();
        return back()->with('success', 'Skill removed from port_skills.');
    }

    // =========================================================================
    // 4. SERVICES CRUD (port_services)
    // =========================================================================
    public function services(Request $request)
    {
        $this->dataService->ensureSeeded();
        $search = trim((string) $request->query('search', ''));

        $services = PortfolioService::query()
            ->when($search !== '', fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('subtitle', 'like', "%{$search}%"))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(12)
            ->withQueryString();

        return view('portfolio::admin.services', compact('services', 'search'));
    }

    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'title'             => 'required|string|max:150',
            'subtitle'          => 'nullable|string|max:150',
            'short_description' => 'nullable|string|max:500',
            'description'       => 'nullable|string',
            'icon_class'        => 'nullable|string|max:100',
            'features_raw'      => 'nullable|string|max:600',
            'sort_order'        => 'nullable|integer',
        ]);

        $features = array_values(array_filter(array_map('trim', explode(',', $validated['features_raw'] ?? 'Responsive Design, Clean Code, Fast Performance'))));

        PortfolioService::create([
            'title'             => $validated['title'],
            'slug'              => Str::slug($validated['title']) . '-' . Str::lower(Str::random(3)),
            'subtitle'          => $validated['subtitle'] ?: 'Custom Web Solution',
            'short_description' => $validated['short_description'] ?: $validated['subtitle'],
            'description'       => $validated['description'] ?: $validated['short_description'],
            'icon_class'        => $validated['icon_class'] ?: 'fa-solid fa-code',
            'features'          => $features,
            'sort_order'        => $validated['sort_order'] ?? (PortfolioService::max('sort_order') + 1),
            'is_active'     => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Service '{$validated['title']}' saved in port_services!");
    }

    public function updateService(Request $request, int $id)
    {
        $srv = PortfolioService::findOrFail($id);
        $validated = $request->validate([
            'title'             => 'required|string|max:150',
            'subtitle'          => 'nullable|string|max:150',
            'short_description' => 'nullable|string|max:500',
            'description'       => 'nullable|string',
            'icon_class'        => 'nullable|string|max:100',
            'features_raw'      => 'nullable|string|max:600',
            'sort_order'        => 'nullable|integer',
        ]);

        $features = array_values(array_filter(array_map('trim', explode(',', $validated['features_raw'] ?? ''))));

        $srv->update([
            'title'             => $validated['title'],
            'subtitle'          => $validated['subtitle'],
            'short_description' => $validated['short_description'],
            'description'       => $validated['description'],
            'icon_class'        => $validated['icon_class'] ?: 'fa-solid fa-code',
            'features'          => $features,
            'sort_order'        => $validated['sort_order'] ?? $srv->sort_order,
            'is_active'         => $request->boolean('is_active'),
        ]);

        return back()->with('success', "Service '{$srv->title}' updated in port_services!");
    }

    public function deleteService(int $id)
    {
        PortfolioService::findOrFail($id)->delete();
        return back()->with('success', 'Service removed from port_services.');
    }

    // =========================================================================
    // 5. EXPERIENCE TIMELINE & EDUCATION/CERTIFICATIONS CRUD (port_experiences, port_educations)
    // =========================================================================
    public function experience()
    {
        $this->dataService->ensureSeeded();
        $experiences = PortfolioExperience::orderBy('sort_order')->orderBy('id')->get();
        $educations = PortfolioEducation::orderBy('sort_order')->orderBy('id')->get();

        return view('portfolio::admin.experience', compact('experiences', 'educations'));
    }

    public function storeExperience(Request $request)
    {
        $validated = $request->validate([
            'period'          => 'required|string|max:100',
            'job_title'       => 'required|string|max:150',
            'company_name'    => 'nullable|string|max:150',
            'employment_type' => 'nullable|string|max:80',
            'description'     => 'nullable|string|max:600',
            'icon_class'      => 'nullable|string|max:100',
            'sort_order'      => 'nullable|integer',
        ]);

        PortfolioExperience::create([
            'period'          => $validated['period'],
            'job_title'       => $validated['job_title'],
            'company_name'    => $validated['company_name'] ?? 'Tech Agency',
            'employment_type' => $validated['employment_type'] ?? 'Full-Time',
            'description'     => $validated['description'],
            'icon_class'      => $validated['icon_class'] ?: 'fa-solid fa-briefcase',
            'sort_order'      => $validated['sort_order'] ?? (PortfolioExperience::max('sort_order') + 1),
            'is_active'       => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Career timeline item added to port_experiences!');
    }

    public function updateExperience(Request $request, int $id)
    {
        $exp = PortfolioExperience::findOrFail($id);
        $validated = $request->validate([
            'period'          => 'required|string|max:100',
            'job_title'       => 'required|string|max:150',
            'company_name'    => 'nullable|string|max:150',
            'employment_type' => 'nullable|string|max:80',
            'description'     => 'nullable|string|max:600',
            'icon_class'      => 'nullable|string|max:100',
            'sort_order'      => 'nullable|integer',
        ]);

        $exp->update([
            'period'          => $validated['period'],
            'job_title'       => $validated['job_title'],
            'company_name'    => $validated['company_name'],
            'employment_type' => $validated['employment_type'],
            'description'     => $validated['description'],
            'icon_class'      => $validated['icon_class'] ?: 'fa-solid fa-briefcase',
            'sort_order'      => $validated['sort_order'] ?? $exp->sort_order,
            'is_active'       => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Career timeline item updated in port_experiences!');
    }

    public function deleteExperience(int $id)
    {
        PortfolioExperience::findOrFail($id)->delete();
        return back()->with('success', 'Career experience deleted from port_experiences.');
    }

    public function storeEducation(Request $request)
    {
        $validated = $request->validate([
            'type'            => 'required|in:Education,Certification',
            'title'           => 'required|string|max:180',
            'institution'     => 'required|string|max:180',
            'completion_date' => 'nullable|string|max:80',
            'description'     => 'nullable|string|max:500',
            'certificate_url' => 'nullable|string|max:255',
            'sort_order'      => 'nullable|integer',
        ]);

        PortfolioEducation::create([
            'type'            => $validated['type'],
            'title'           => $validated['title'],
            'institution'     => $validated['institution'],
            'completion_date' => $validated['completion_date'],
            'description'     => $validated['description'],
            'certificate_url' => $validated['certificate_url'] ?? '#',
            'sort_order'      => $validated['sort_order'] ?? (PortfolioEducation::max('sort_order') + 1),
            'is_active'       => true,
        ]);

        return back()->with('success', "{$validated['type']} record added to port_educations!");
    }

    public function updateEducation(Request $request, int $id)
    {
        $edu = PortfolioEducation::findOrFail($id);
        $validated = $request->validate([
            'type'            => 'required|in:Education,Certification',
            'title'           => 'required|string|max:180',
            'institution'     => 'required|string|max:180',
            'completion_date' => 'nullable|string|max:80',
            'description'     => 'nullable|string|max:500',
            'certificate_url' => 'nullable|string|max:255',
            'sort_order'      => 'nullable|integer',
        ]);

        $edu->update([
            'type'            => $validated['type'],
            'title'           => $validated['title'],
            'institution'     => $validated['institution'],
            'completion_date' => $validated['completion_date'],
            'description'     => $validated['description'],
            'certificate_url' => $validated['certificate_url'],
            'sort_order'      => $validated['sort_order'] ?? $edu->sort_order,
            'is_active'       => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Education/Certification updated in port_educations!');
    }

    public function deleteEducation(int $id)
    {
        PortfolioEducation::findOrFail($id)->delete();
        return back()->with('success', 'Record removed from port_educations.');
    }

    // =========================================================================
    // 6. TESTIMONIALS CRUD (port_testimonials)
    // =========================================================================
    public function testimonials()
    {
        $this->dataService->ensureSeeded();
        $testimonials = PortfolioTestimonial::orderBy('sort_order')->latest('id')->get();

        return view('portfolio::admin.testimonials', compact('testimonials'));
    }

    public function storeTestimonial(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:120',
            'designation' => 'nullable|string|max:120',
            'company'     => 'nullable|string|max:120',
            'review_text' => 'required|string|max:1000',
            'rating'      => 'required|integer|min:1|max:5',
            'avatar_file' => 'nullable|image|max:3072',
            'sort_order'  => 'nullable|integer',
        ]);

        $avatar = '/module-media/Portfolio/theme/avatar-1.jpg';
        if ($request->hasFile('avatar_file')) {
            $avatar = ModuleCmsResolver::storeModuleFile($request->file('avatar_file'), 'testimonials', 'Portfolio');
        }

        PortfolioTestimonial::create([
            'client_name' => $validated['client_name'],
            'designation' => $validated['designation'],
            'company'     => $validated['company'],
            'review_text' => $validated['review_text'],
            'rating'      => $validated['rating'],
            'avatar'      => $avatar,
            'sort_order'  => $validated['sort_order'] ?? (PortfolioTestimonial::max('sort_order') + 1),
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Testimonial from '{$validated['client_name']}' saved in port_testimonials!");
    }

    public function updateTestimonial(Request $request, int $id)
    {
        $testi = PortfolioTestimonial::findOrFail($id);
        $validated = $request->validate([
            'client_name' => 'required|string|max:120',
            'designation' => 'nullable|string|max:120',
            'company'     => 'nullable|string|max:120',
            'review_text' => 'required|string|max:1000',
            'rating'      => 'required|integer|min:1|max:5',
            'avatar_file' => 'nullable|image|max:3072',
            'sort_order'  => 'nullable|integer',
        ]);

        $avatar = $testi->avatar;
        if ($request->hasFile('avatar_file')) {
            $avatar = ModuleCmsResolver::storeModuleFile($request->file('avatar_file'), 'testimonials', 'Portfolio');
        }

        $testi->update([
            'client_name' => $validated['client_name'],
            'designation' => $validated['designation'],
            'company'     => $validated['company'],
            'review_text' => $validated['review_text'],
            'rating'      => $validated['rating'],
            'avatar'      => $avatar,
            'sort_order'  => $validated['sort_order'] ?? $testi->sort_order,
            'is_active'   => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Testimonial updated in port_testimonials!');
    }

    public function deleteTestimonial(int $id)
    {
        PortfolioTestimonial::findOrFail($id)->delete();
        return back()->with('success', 'Testimonial removed from port_testimonials.');
    }

    // =========================================================================
    // 7. BLOG & ARTICLES CRUD (port_blogs)
    // =========================================================================
    public function blogs(Request $request)
    {
        $this->dataService->ensureSeeded();
        $search = trim((string) $request->query('search', ''));
        $blogs = PortfolioBlog::when($search !== '', fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('category', 'like', "%{$search}%"))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('portfolio::admin.blogs', compact('blogs', 'search'));
    }

    public function storeBlog(Request $request)
    {
        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'category'          => 'required|string|max:100',
            'published_date'    => 'nullable|string|max:60',
            'short_description' => 'nullable|string|max:600',
            'content'           => 'nullable|string',
            'image_file'        => 'nullable|image|max:4096',
        ]);

        $img = '/module-media/Portfolio/theme/blog-1.jpg';
        if ($request->hasFile('image_file')) {
            $img = ModuleCmsResolver::storeModuleFile($request->file('image_file'), 'blog', 'Portfolio');
        }

        PortfolioBlog::create([
            'title'             => $validated['title'],
            'slug'              => Str::slug($validated['title']) . '-' . Str::lower(Str::random(4)),
            'category'          => $validated['category'],
            'published_date'    => $validated['published_date'] ?: date('M d, Y'),
            'short_description' => $validated['short_description'],
            'content'           => $validated['content'] ?: '<p>' . e($validated['short_description']) . '</p>',
            'image'             => $img,
            'views'             => 1,
            'is_featured'       => true,
            'is_active'         => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Article '{$validated['title']}' published in port_blogs!");
    }

    public function updateBlog(Request $request, int $id)
    {
        $blog = PortfolioBlog::findOrFail($id);
        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'category'          => 'required|string|max:100',
            'published_date'    => 'nullable|string|max:60',
            'short_description' => 'nullable|string|max:600',
            'content'           => 'nullable|string',
            'image_file'        => 'nullable|image|max:4096',
        ]);

        $img = $blog->image;
        if ($request->hasFile('image_file')) {
            $img = ModuleCmsResolver::storeModuleFile($request->file('image_file'), 'blog', 'Portfolio');
        }

        $blog->update([
            'title'             => $validated['title'],
            'category'          => $validated['category'],
            'published_date'    => $validated['published_date'],
            'short_description' => $validated['short_description'],
            'content'           => $validated['content'],
            'image'             => $img,
            'is_active'         => $request->boolean('is_active'),
        ]);

        return back()->with('success', "Article '{$blog->title}' updated in port_blogs!");
    }

    public function deleteBlog(int $id)
    {
        PortfolioBlog::findOrFail($id)->delete();
        return back()->with('success', 'Article deleted from port_blogs.');
    }

    // =========================================================================
    // 8. FAQ ACCORDION CRUD (port_faqs)
    // =========================================================================
    public function faqs()
    {
        $this->dataService->ensureSeeded();
        $faqs = PortfolioFaq::orderBy('sort_order')->orderBy('id')->get();

        return view('portfolio::admin.faqs', compact('faqs'));
    }

    public function storeFaq(Request $request)
    {
        $validated = $request->validate([
            'question'   => 'required|string|max:255',
            'answer'     => 'required|string|max:2000',
            'category'   => 'nullable|string|max:80',
            'sort_order' => 'nullable|integer',
        ]);

        PortfolioFaq::create([
            'question'   => $validated['question'],
            'answer'     => $validated['answer'],
            'category'   => $validated['category'] ?: 'General',
            'sort_order' => $validated['sort_order'] ?? (PortfolioFaq::max('sort_order') + 1),
            'is_active'  => true,
        ]);

        return back()->with('success', 'FAQ item saved in port_faqs!');
    }

    public function updateFaq(Request $request, int $id)
    {
        $faq = PortfolioFaq::findOrFail($id);
        $validated = $request->validate([
            'question'   => 'required|string|max:255',
            'answer'     => 'required|string|max:2000',
            'category'   => 'nullable|string|max:80',
            'sort_order' => 'nullable|integer',
        ]);

        $faq->update([
            'question'   => $validated['question'],
            'answer'     => $validated['answer'],
            'category'   => $validated['category'] ?: 'General',
            'sort_order' => $validated['sort_order'] ?? $faq->sort_order,
            'is_active'  => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'FAQ item updated in port_faqs!');
    }

    public function deleteFaq(int $id)
    {
        PortfolioFaq::findOrFail($id)->delete();
        return back()->with('success', 'FAQ item removed from port_faqs.');
    }

    // =========================================================================
    // 9. CONTACT MESSAGES & NEWSLETTER SUBSCRIBERS (port_messages, port_subscribers)
    // =========================================================================
    public function messages(Request $request)
    {
        $this->dataService->ensureSeeded();
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status');

        $messages = PortfolioMessage::query()
            ->when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")->orWhere('subject', 'like', "%{$search}%"))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $subscribers = PortfolioSubscriber::latest('id')->get();
        $unreadCount = PortfolioMessage::where('status', 'Unread')->count();

        return view('portfolio::admin.messages', compact('messages', 'subscribers', 'search', 'status', 'unreadCount'));
    }

    public function updateMessageStatus(Request $request, int $id)
    {
        $msg = PortfolioMessage::findOrFail($id);
        $validated = $request->validate([
            'status' => 'required|in:Unread,Read,Replied',
        ]);
        $msg->update(['status' => $validated['status']]);

        return back()->with('success', "Message status updated to '{$validated['status']}'.");
    }

    public function deleteMessage(int $id)
    {
        PortfolioMessage::findOrFail($id)->delete();
        return back()->with('success', 'Contact message deleted from port_messages.');
    }

    public function deleteSubscriber(int $id)
    {
        PortfolioSubscriber::findOrFail($id)->delete();
        return back()->with('success', 'Subscriber removed from port_subscribers.');
    }
}
