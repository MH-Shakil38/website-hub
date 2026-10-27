<?php

namespace Modules\Interior\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\ModuleManagerService;
use App\Services\ModuleStorageService;
use App\Services\ModuleThemeDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Interior\App\Models\ModuleSetting;
use Modules\Interior\App\Models\Project;

class ProjectController extends Controller
{
    public function __construct(
        protected ModuleThemeDataService $themeDataService,
        protected ModuleStorageService $storageService,
        protected ModuleManagerService $managerService
    ) {}

    public function index()
    {
        $projects = Project::latest()->get();
        $t = $this->themeDataService->getFrontendData('Interior');

        return view('interior::admin.projects.index', compact('projects', 't'));
    }

    public function create()
    {
        return view('interior::admin.projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:4096',
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::lower(Str::random(5));

        if ($request->hasFile('image')) {
            $validated['image'] = $this->storageService->store($request->file('image'), 'projects', 'Interior');
        }

        Project::create($validated);

        return redirect()->route('admin.interior.projects.index')->with('success', 'Project added to Interior portfolio successfully.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return back()->with('success', 'Project removed from portfolio.');
    }

    public function packages()
    {
        $t = $this->themeDataService->getFrontendData('Interior');
        return view('interior::admin.theme.settings', ['t' => $t, 'activeTab' => 'services']);
    }

    public function quotes()
    {
        $t = $this->themeDataService->getFrontendData('Interior');
        return view('interior::admin.theme.settings', ['t' => $t, 'activeTab' => 'testimonials']);
    }

    public function themeSettings(Request $request)
    {
        $t = $this->themeDataService->getFrontendData('Interior');
        $activeTab = $request->query('tab', 'hero');

        return view('interior::admin.theme.settings', compact('t', 'activeTab'));
    }

    /**
     * Beginner-friendly Visual Editor Save Handler:
     * Updates any modified text or uploaded image across all 11 sections of the Geotech theme.
     * Any image field left blank automatically retains the current/default image!
     */
    public function saveThemeSettings(Request $request)
    {
        // Start from existing custom overrides in DB (not the huge base64 default config)
        $current = $this->themeDataService->getCustomSections('Interior');
        $sections = $request->input('sections', []);

        // Merge submitted texts into custom overrides
        foreach ($sections as $secKey => $secVal) {
            if (is_array($secVal)) {
                // If saving dynamic services items (Add More / Remove supported), replace the items array cleanly
                if ($secKey === 'services' && isset($secVal['items']) && is_array($secVal['items'])) {
                    $oldItems = $current['services']['items'] ?? [];
                    $newItems = [];
                    foreach ($secVal['items'] as $rawKey => $itemData) {
                        $newIdx = count($newItems);
                        $keptImage = $itemData['existing_image'] ?? ($oldItems[$rawKey]['image'] ?? null);
                        unset($itemData['existing_image']);

                        if ($request->hasFile("service_image_{$rawKey}")) {
                            $itemData['image'] = $this->storageService->store($request->file("service_image_{$rawKey}"), 'services', 'Interior');
                        } elseif (!empty($keptImage) && !str_starts_with($keptImage, 'data:image/')) {
                            $itemData['image'] = $keptImage;
                        }

                        $newItems[$newIdx] = $itemData;
                    }
                    $secVal['items'] = $newItems;
                    $current['services'] = isset($current['services']) && is_array($current['services'])
                        ? array_replace($current['services'], $secVal)
                        : $secVal;
                    continue;
                }

                $current[$secKey] = isset($current[$secKey]) && is_array($current[$secKey])
                    ? array_replace_recursive($current[$secKey], $secVal)
                    : $secVal;
            }
        }

        // Handle optional image uploads (if no image uploaded, keep existing custom or default!)
        if ($request->hasFile('header_logo')) {
            $logoUrl = $this->storageService->store($request->file('header_logo'), 'branding', 'Interior');
            $current['header']['logo'] = $logoUrl;
            $current['footer']['logo'] = $logoUrl;
        }

        if ($request->hasFile('hero_bg_image')) {
            $current['hero']['bg_image'] = $this->storageService->store($request->file('hero_bg_image'), 'hero', 'Interior');
        }

        if ($request->hasFile('founder_photo')) {
            $current['founder']['photo'] = $this->storageService->store($request->file('founder_photo'), 'founder', 'Interior');
        }
        if ($request->hasFile('founder_deco_image')) {
            $current['founder']['deco_image'] = $this->storageService->store($request->file('founder_deco_image'), 'founder', 'Interior');
        }

        for ($i = 0; $i < 5; $i++) {
            if ($request->hasFile("project_image_{$i}")) {
                $current['projects']['items'][$i]['image'] = $this->storageService->store($request->file("project_image_{$i}"), 'projects', 'Interior');
            }
        }

        if ($request->hasFile('cta_image')) {
            $current['cta']['image'] = $this->storageService->store($request->file('cta_image'), 'cta', 'Interior');
        }

        if ($request->hasFile('footer_arch_image')) {
            $current['footer']['arch_image'] = $this->storageService->store($request->file('footer_arch_image'), 'footer', 'Interior');
        }

        // Ensure no base64 default strings are ever written to the database
        $cleanCustom = $this->themeDataService->stripBase64Assets($current);

        ModuleSetting::setValue('theme_sections_json', json_encode($cleanCustom), 'theme');

        ActivityLog::record(
            'Interior Website Sections Updated',
            'Saved via Easy Visual Website Editor (int_settings)',
            'bi-magic',
            '#DCFCE7',
            '#10B981'
        );

        return back()->with('success', 'Website sections saved! Any image you did not upload remains safely at its default.');
    }

    public function resetThemeSettings()
    {
        $this->managerService->resetModuleToDefault('Interior');

        return back()->with('success', 'Interior Design website has been reset 100% to its original Geotech default template!');
    }

    /**
     * Instant onchange AJAX handler for Services Display Mode (Grid vs Owl Carousel) & Carousel Behavior (Auto vs Click).
     */
    public function quickToggleServiceSettings(Request $request)
    {
        $current = $this->themeDataService->getCustomSections('Interior');
        if (!isset($current['services']) || !is_array($current['services'])) {
            $current['services'] = [];
        }

        if ($request->filled('display_mode')) {
            $current['services']['display_mode'] = in_array($request->input('display_mode'), ['grid', 'carousel'], true)
                ? $request->input('display_mode')
                : 'grid';
        }

        if ($request->filled('carousel_mode')) {
            $current['services']['carousel_mode'] = in_array($request->input('carousel_mode'), ['auto', 'click'], true)
                ? $request->input('carousel_mode')
                : 'auto';
        }

        $cleanCustom = $this->themeDataService->stripBase64Assets($current);
        ModuleSetting::setValue('theme_sections_json', json_encode($cleanCustom), 'theme');

        return response()->json([
            'status'        => 'ok',
            'display_mode'  => $current['services']['display_mode'] ?? 'grid',
            'carousel_mode' => $current['services']['carousel_mode'] ?? 'auto',
            'message'       => 'Services display settings updated automatically!',
        ]);
    }
}
