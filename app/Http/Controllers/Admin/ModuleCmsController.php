<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Support\ModuleCmsResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModuleCmsController extends Controller
{
    /**
     * Serve uploaded files directly from Modules/{Module}/Public/uploads/{folder}/{filename}
     */
    public function serveModuleMedia(string $module, string $folder, string $filename)
    {
        if (!isset(ModuleCmsResolver::SUPPORTED_MODULES[$module])) {
            abort(404);
        }

        $safeFolder = basename($folder);
        $safeFile = basename($filename);
        $path = base_path("Modules/{$module}/Public/uploads/{$safeFolder}/{$safeFile}");

        if (!File::exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }

    // =========================================================================
    // 1. MODULE PAGES (int_pages / re_pages / pharma_pages)
    // =========================================================================
    public function pages(Request $request)
    {
        $modKey = ModuleCmsResolver::currentModule($request);
        $modMeta = ModuleCmsResolver::moduleMeta($modKey);
        $pageClass = ModuleCmsResolver::pageModel($modKey);

        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status');

        $pages = $pageClass::query()
            ->when($search !== '', fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('slug', 'like', "%{$search}%"))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $publishedCount = $pageClass::where('status', 'Published')->count();
        $draftOrScheduledCount = $pageClass::whereIn('status', ['Draft', 'Scheduled'])->count();

        return view('admin.pages', compact('pages', 'search', 'status', 'modKey', 'modMeta', 'publishedCount', 'draftOrScheduledCount'));
    }

    public function storePage(Request $request)
    {
        $modKey = ModuleCmsResolver::currentModule($request);
        $modMeta = ModuleCmsResolver::moduleMeta($modKey);
        $pageClass = ModuleCmsResolver::pageModel($modKey);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'status' => 'required|in:Published,Draft,Private,Scheduled',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['views'] = 0;

        $page = $pageClass::create($validated);

        ActivityLog::record(
            "New page created ({$modMeta['label']})",
            "{$page->title} [{$modMeta['prefix']}pages]",
            'bi-file-earmark-plus',
            '#DCFCE7',
            '#10B981'
        );

        return back()->with('success', "[{$modMeta['label']}] Page '{$page->title}' created in {$modMeta['prefix']}pages.");
    }

    public function deletePage(Request $request, int $id)
    {
        $modKey = ModuleCmsResolver::currentModule($request);
        $modMeta = ModuleCmsResolver::moduleMeta($modKey);
        $pageClass = ModuleCmsResolver::pageModel($modKey);

        $page = $pageClass::findOrFail($id);
        $page->delete();

        return back()->with('success', "[{$modMeta['label']}] Page deleted from {$modMeta['prefix']}pages.");
    }

    // =========================================================================
    // 2. MODULE MENU BUILDER (int_menus / re_menus / pharma_menus)
    // =========================================================================
    public function menus(Request $request)
    {
        $modKey = ModuleCmsResolver::currentModule($request);
        $modMeta = ModuleCmsResolver::moduleMeta($modKey);
        $menuClass = ModuleCmsResolver::menuModel($modKey);

        $search = trim((string) $request->query('search', ''));
        $location = $request->query('location');

        $menus = $menuClass::query()
            ->when($search !== '', fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('url', 'like', "%{$search}%"))
            ->when($location, fn ($q) => $q->where('location', $location))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        $headerCount = $menuClass::where('location', 'header')->count();
        $footerCount = $menuClass::where('location', 'footer')->count();

        return view('admin.menus', compact('menus', 'search', 'location', 'modKey', 'modMeta', 'headerCount', 'footerCount'));
    }

    public function storeMenu(Request $request)
    {
        $modKey = ModuleCmsResolver::currentModule($request);
        $modMeta = ModuleCmsResolver::moduleMeta($modKey);
        $menuClass = ModuleCmsResolver::menuModel($modKey);

        $validated = $request->validate([
            'title' => 'required|string|max:120',
            'url' => 'required|string|max:255',
            'location' => 'required|in:header,footer',
            'target' => 'required|in:_self,_blank',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['sort_order'] = $validated['sort_order'] ?? ($menuClass::max('sort_order') + 1);
        $validated['is_active'] = true;

        $menu = $menuClass::create($validated);

        ActivityLog::record(
            "Menu item added ({$modMeta['label']})",
            "{$menu->title} ({$menu->url}) [{$modMeta['prefix']}menus]",
            'bi-layers-fill',
            '#DBEAFE',
            '#3B82F6'
        );

        return back()->with('success', "[{$modMeta['label']}] Menu '{$menu->title}' saved in {$modMeta['prefix']}menus.");
    }

    public function deleteMenu(Request $request, int $id)
    {
        $modKey = ModuleCmsResolver::currentModule($request);
        $modMeta = ModuleCmsResolver::moduleMeta($modKey);
        $menuClass = ModuleCmsResolver::menuModel($modKey);

        $menuClass::findOrFail($id)->delete();

        return back()->with('success', "[{$modMeta['label']}] Menu item removed from {$modMeta['prefix']}menus.");
    }

    // =========================================================================
    // 3. MODULE SLIDER (int_sliders / re_sliders / pharma_sliders)
    // =========================================================================
    public function sliders(Request $request)
    {
        $modKey = ModuleCmsResolver::currentModule($request);
        $modMeta = ModuleCmsResolver::moduleMeta($modKey);
        $sliderClass = ModuleCmsResolver::sliderModel($modKey);

        $search = trim((string) $request->query('search', ''));

        $sliders = $sliderClass::query()
            ->when($search !== '', fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('subtitle', 'like', "%{$search}%"))
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $activeSlidersCount = $sliderClass::where('is_active', true)->count();

        return view('admin.sliders', compact('sliders', 'search', 'modKey', 'modMeta', 'activeSlidersCount'));
    }

    public function storeSlider(Request $request)
    {
        $modKey = ModuleCmsResolver::currentModule($request);
        $modMeta = ModuleCmsResolver::moduleMeta($modKey);
        $sliderClass = ModuleCmsResolver::sliderModel($modKey);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:80',
            'button_url' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'image_file' => 'nullable|image|max:4096',
            'image_url' => 'nullable|url',
        ]);

        $imageUrl = 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=800&q=80';
        if ($request->hasFile('image_file')) {
            $imageUrl = ModuleCmsResolver::storeModuleFile($request->file('image_file'), 'sliders', $modKey);
        } elseif (!empty($validated['image_url'])) {
            $imageUrl = $validated['image_url'];
        }

        $slider = $sliderClass::create([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'button_text' => $validated['button_text'] ?? 'Explore Now',
            'button_url' => $validated['button_url'] ?? '/',
            'image' => $imageUrl,
            'sort_order' => $validated['sort_order'] ?? ($sliderClass::max('sort_order') + 1),
            'is_active' => true,
        ]);

        ActivityLog::record(
            "Slider created ({$modMeta['label']})",
            "{$slider->title} [{$modMeta['prefix']}sliders]",
            'bi-card-image',
            '#FFE4EC',
            '#EC134A'
        );

        return back()->with('success', "[{$modMeta['label']}] Slider '{$slider->title}' saved in {$modMeta['prefix']}sliders & Modules/{$modKey}/Public/uploads/sliders.");
    }

    public function deleteSlider(Request $request, int $id)
    {
        $modKey = ModuleCmsResolver::currentModule($request);
        $modMeta = ModuleCmsResolver::moduleMeta($modKey);
        $sliderClass = ModuleCmsResolver::sliderModel($modKey);

        $sliderClass::findOrFail($id)->delete();

        return back()->with('success', "[{$modMeta['label']}] Slider deleted from {$modMeta['prefix']}sliders.");
    }

    // =========================================================================
    // 4. MODULE MEDIA LIBRARY (int_media / re_media / pharma_media)
    // =========================================================================
    public function media(Request $request)
    {
        $modKey = ModuleCmsResolver::currentModule($request);
        $modMeta = ModuleCmsResolver::moduleMeta($modKey);
        $mediaClass = ModuleCmsResolver::mediaModel($modKey);

        $search = trim((string) $request->query('search', ''));

        $mediaItems = $mediaClass::query()
            ->when($search !== '', fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('alt_text', 'like', "%{$search}%"))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.media', compact('mediaItems', 'search', 'modKey', 'modMeta'));
    }

    public function storeMedia(Request $request)
    {
        $modKey = ModuleCmsResolver::currentModule($request);
        $modMeta = ModuleCmsResolver::moduleMeta($modKey);
        $mediaClass = ModuleCmsResolver::mediaModel($modKey);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:50',
            'alt_text' => 'nullable|string|max:255',
            'media_file' => 'nullable|file|max:5120',
            'media_url' => 'nullable|url',
        ]);

        $filePath = 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=600&q=80';
        $fileType = 'image/jpeg';
        $fileSize = '185 KB';

        if ($request->hasFile('media_file')) {
            $file = $request->file('media_file');
            $fileType = $file->getClientMimeType() ?: 'image/jpeg';
            $fileSize = number_format(max(1, $file->getSize() / 1024), 1) . ' KB';
            $filePath = ModuleCmsResolver::storeModuleFile($file, 'media', $modKey);
        } elseif (!empty($validated['media_url'])) {
            $filePath = $validated['media_url'];
        }

        $media = $mediaClass::create([
            'title' => $validated['title'],
            'category' => $validated['category'] ?? 'living',
            'file_path' => $filePath,
            'file_type' => $fileType,
            'file_size' => $fileSize,
            'alt_text' => $validated['alt_text'] ?? $validated['title'],
        ]);

        $settingClass = ModuleCmsResolver::settingModel($modKey);
        $settingClass::setValue('use_custom_gallery', '1');

        ActivityLog::record(
            "Media uploaded ({$modMeta['label']})",
            "{$media->title} (Modules/{$modKey}/Public/uploads/media)",
            'bi-image',
            '#FFE4EC',
            '#EC134A'
        );

        return back()->with('success', "[{$modMeta['label']}] Photo '{$media->title}' added to Gallery & Modules/{$modKey}/Public/uploads/media.");
    }

    public function deleteMedia(Request $request, int $id)
    {
        $modKey = ModuleCmsResolver::currentModule($request);
        $modMeta = ModuleCmsResolver::moduleMeta($modKey);
        $mediaClass = ModuleCmsResolver::mediaModel($modKey);

        $mediaClass::findOrFail($id)->delete();

        return back()->with('success', "[{$modMeta['label']}] Media deleted from {$modMeta['prefix']}media.");
    }

    // =========================================================================
    // 4B. MODULE VIDEO GALLERY (int_videos / re_videos / pharma_videos)
    // =========================================================================
    public function videos(Request $request)
    {
        $modKey = ModuleCmsResolver::currentModule($request);
        $modMeta = ModuleCmsResolver::moduleMeta($modKey);
        $videoClass = ModuleCmsResolver::videoModel($modKey);

        $search = trim((string) $request->query('search', ''));
        $videos = $videoClass::query()
            ->when($search !== '', fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('category', 'like', "%{$search}%"))
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $defaultVideos = app(\App\Services\ModuleThemeDataService::class)->getDefaults($modKey)['videos']['items'] ?? [];

        return view('admin.videos', compact('videos', 'defaultVideos', 'search', 'modKey', 'modMeta'));
    }

    public function storeVideo(Request $request)
    {
        $modKey = ModuleCmsResolver::currentModule($request);
        $modMeta = ModuleCmsResolver::moduleMeta($modKey);
        $videoClass = ModuleCmsResolver::videoModel($modKey);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'duration' => 'nullable|string|max:20',
            'video_url' => 'nullable|string|max:500',
            'video_file' => 'nullable|file|mimetypes:video/mp4,video/webm,video/ogg|max:51200',
            'thumbnail_file' => 'nullable|image|max:4096',
        ]);

        $finalVideoUrl = 'https://www.youtube.com/embed/ScMzIvxBSi4';
        if ($request->hasFile('video_file')) {
            $finalVideoUrl = ModuleCmsResolver::storeModuleFile($request->file('video_file'), 'videos', $modKey);
        } elseif (!empty($validated['video_url'])) {
            $finalVideoUrl = app(\App\Services\ModuleThemeDataService::class)->normalizeVideoUrl($validated['video_url']);
        }

        $thumbUrl = 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=800&q=80';
        if ($request->hasFile('thumbnail_file')) {
            $thumbUrl = ModuleCmsResolver::storeModuleFile($request->file('thumbnail_file'), 'videos', $modKey);
        }

        $video = $videoClass::create([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'duration' => $validated['duration'] ?: '02:45',
            'video_url' => $finalVideoUrl,
            'thumbnail' => $thumbUrl,
            'sort_order' => ($videoClass::max('sort_order') ?? 0) + 1,
            'is_active' => true,
        ]);

        ActivityLog::record(
            "Video added ({$modMeta['label']})",
            "{$video->title} [{$modMeta['prefix']}videos]",
            'bi-play-btn-fill',
            '#FFE4EC',
            '#EC134A'
        );

        return back()->with('success', "[{$modMeta['label']}] Video '{$video->title}' added to Video Gallery ({$modMeta['prefix']}videos).");
    }

    public function deleteVideo(Request $request, int $id)
    {
        $modKey = ModuleCmsResolver::currentModule($request);
        $modMeta = ModuleCmsResolver::moduleMeta($modKey);
        $videoClass = ModuleCmsResolver::videoModel($modKey);

        $videoClass::findOrFail($id)->delete();

        return back()->with('success', "[{$modMeta['label']}] Video removed from {$modMeta['prefix']}videos.");
    }

    // =========================================================================
    // 5. MODULE THEMES (int_themes / re_themes / pharma_themes)
    // =========================================================================
    public function themes(Request $request)
    {
        $modKey = ModuleCmsResolver::currentModule($request);
        $modMeta = ModuleCmsResolver::moduleMeta($modKey);
        $themeClass = ModuleCmsResolver::themeModel($modKey);

        $search = trim((string) $request->query('search', ''));

        $themes = $themeClass::query()
            ->when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('layout_style', 'like', "%{$search}%"))
            ->orderByDesc('is_active')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        $activeTheme = $themeClass::where('is_active', true)->first();

        return view('admin.themes', compact('themes', 'search', 'modKey', 'modMeta', 'activeTheme'));
    }

    public function storeTheme(Request $request)
    {
        $modKey = ModuleCmsResolver::currentModule($request);
        $modMeta = ModuleCmsResolver::moduleMeta($modKey);
        $themeClass = ModuleCmsResolver::themeModel($modKey);

        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'primary_color' => 'required|string|max:30',
            'secondary_color' => 'required|string|max:30',
            'font_family' => 'required|string|max:80',
            'layout_style' => 'required|string|max:80',
        ]);

        $theme = $themeClass::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'primary_color' => $validated['primary_color'],
            'secondary_color' => $validated['secondary_color'],
            'font_family' => $validated['font_family'],
            'layout_style' => $validated['layout_style'],
            'thumbnail' => 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=400&q=80',
            'is_active' => $themeClass::count() === 0,
        ]);

        return back()->with('success', "[{$modMeta['label']}] Theme preset '{$theme->name}' saved in {$modMeta['prefix']}themes.");
    }

    public function activateTheme(Request $request, int $id)
    {
        $modKey = ModuleCmsResolver::currentModule($request);
        $modMeta = ModuleCmsResolver::moduleMeta($modKey);
        $themeClass = ModuleCmsResolver::themeModel($modKey);

        $themeClass::query()->update(['is_active' => false]);
        $theme = $themeClass::findOrFail($id);
        $theme->update(['is_active' => true]);

        ActivityLog::record(
            "Theme activated ({$modMeta['label']})",
            "{$theme->name} [{$modMeta['prefix']}themes]",
            'bi-palette-fill',
            '#F3E8FF',
            '#A855F7'
        );

        return back()->with('success', "[{$modMeta['label']}] Active theme switched to '{$theme->name}'.");
    }

    public function deleteTheme(Request $request, int $id)
    {
        $modKey = ModuleCmsResolver::currentModule($request);
        $modMeta = ModuleCmsResolver::moduleMeta($modKey);
        $themeClass = ModuleCmsResolver::themeModel($modKey);

        $themeClass::findOrFail($id)->delete();

        return back()->with('success', "[{$modMeta['label']}] Theme deleted from {$modMeta['prefix']}themes.");
    }

    // =========================================================================
    // 6. MODULE USERS (int_users / re_users / pharma_users)
    // =========================================================================
    public function users(Request $request)
    {
        $modKey = ModuleCmsResolver::currentModule($request);
        $modMeta = ModuleCmsResolver::moduleMeta($modKey);
        $modUserClass = ModuleCmsResolver::userModel($modKey);

        $search = trim((string) $request->query('search', ''));
        $roleName = $request->query('role_name');

        $moduleUsers = $modUserClass::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })
            ->when($roleName, fn ($q) => $q->where('role_name', $roleName))
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        $roles = Role::with('permissions')->withCount('users')->get();
        $permissions = Permission::orderBy('group')->get();

        return view('admin.users', compact('moduleUsers', 'roles', 'permissions', 'search', 'roleName', 'modKey', 'modMeta'));
    }

    public function storeModuleUser(Request $request)
    {
        $modKey = ModuleCmsResolver::currentModule($request);
        $modMeta = ModuleCmsResolver::moduleMeta($modKey);
        $modUserClass = ModuleCmsResolver::userModel($modKey);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:30',
            'role_name' => 'required|string|max:60',
            'status' => 'required|in:Active,Inactive',
        ]);

        $u = $modUserClass::updateOrCreate(
            ['email' => $validated['email']],
            $validated
        );

        ActivityLog::record(
            "Module user added ({$modMeta['label']})",
            "{$u->email} [{$modMeta['prefix']}users]",
            'bi-person-fill',
            '#FEF3C7',
            '#F59E0B'
        );

        return back()->with('success', "[{$modMeta['label']}] User '{$u->name}' saved in {$modMeta['prefix']}users.");
    }

    public function updateModuleUser(Request $request, int $id)
    {
        $modKey = ModuleCmsResolver::currentModule($request);
        $modMeta = ModuleCmsResolver::moduleMeta($modKey);
        $modUserClass = ModuleCmsResolver::userModel($modKey);

        $u = $modUserClass::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:30',
            'role_name' => 'required|string|max:60',
            'status' => 'required|in:Active,Inactive',
        ]);

        $u->update($validated);

        return back()->with('success', "[{$modMeta['label']}] User '{$u->name}' updated in {$modMeta['prefix']}users.");
    }

    public function deleteModuleUser(Request $request, int $id)
    {
        $modKey = ModuleCmsResolver::currentModule($request);
        $modMeta = ModuleCmsResolver::moduleMeta($modKey);
        $modUserClass = ModuleCmsResolver::userModel($modKey);

        $modUserClass::findOrFail($id)->delete();

        return back()->with('success', "[{$modMeta['label']}] User removed from {$modMeta['prefix']}users.");
    }

    // =========================================================================
    // 7. MODULE SETTINGS (int_settings / re_settings / pharma_settings)
    // =========================================================================
    public function settings(Request $request)
    {
        $modKey = ModuleCmsResolver::currentModule($request);
        $modMeta = ModuleCmsResolver::moduleMeta($modKey);
        $settingClass = ModuleCmsResolver::settingModel($modKey);

        $user = Auth::user() ?? User::with('role')->first();
        $roles = Role::all();
        $globalSettings = Setting::pluck('value', 'key')->toArray();
        $moduleSettings = $settingClass::pluck('value', 'key')->toArray();

        return view('admin.settings', compact('user', 'roles', 'globalSettings', 'moduleSettings', 'modKey', 'modMeta'));
    }

    public function updateModuleSettings(Request $request)
    {
        $modKey = ModuleCmsResolver::currentModule($request);
        $modMeta = ModuleCmsResolver::moduleMeta($modKey);
        $settingClass = ModuleCmsResolver::settingModel($modKey);

        $fields = [
            'site_title',
            'site_tagline',
            'contact_email',
            'contact_phone',
            'contact_address',
            'footer_copyright',
            'facebook_url',
            'instagram_url',
            'hero_heading',
            'hero_subheading',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                $settingClass::setValue($field, $request->input($field));
            }
        }

        if ($request->hasFile('module_logo')) {
            $logoUrl = ModuleCmsResolver::storeModuleFile($request->file('module_logo'), 'branding', $modKey);
            $settingClass::setValue('module_logo', $logoUrl);
        }

        ActivityLog::record(
            "Module settings updated ({$modMeta['label']})",
            "Saved in {$modMeta['prefix']}settings",
            'bi-gear-fill',
            '#F3E8FF',
            '#A855F7'
        );

        return back()->with('success', "[{$modMeta['label']}] Isolated module settings saved in {$modMeta['prefix']}settings!");
    }
}
