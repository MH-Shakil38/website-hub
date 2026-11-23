<?php

namespace Modules\HajjUmrah\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\ModuleManagerService;
use App\Support\ModuleCmsResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\HajjUmrah\App\Models\HajjBlog;
use Modules\HajjUmrah\App\Models\HajjInquiry;
use Modules\HajjUmrah\App\Models\HajjPackage;
use Modules\HajjUmrah\App\Models\HajjPartner;
use Modules\HajjUmrah\App\Models\HajjPricingPlan;
use Modules\HajjUmrah\App\Models\HajjService;
use Modules\HajjUmrah\App\Models\HajjSubscriber;
use Modules\HajjUmrah\App\Models\HajjTestimonial;
use Modules\HajjUmrah\App\Models\ModuleSetting;
use Modules\HajjUmrah\App\Services\HajjUmrahDataService;

class HajjUmrahAdminController extends Controller
{
    public function __construct(
        protected HajjUmrahDataService $dataService,
        protected ModuleManagerService $managerService
    ) {}

    // =========================================================================
    // 1. HOMEPAGE BUILDER, HEADER, HERO, MULTILINGUAL TRANSLATIONS & FOOTER
    // =========================================================================
    public function settings(Request $request)
    {
        $this->dataService->ensureSeeded();
        $t = $this->dataService->getSettingsData();
        $activeTab = $request->query('tab', 'builder');

        return view('hajjumrah::admin.settings', compact('t', 'activeTab'));
    }

    public function saveSettings(Request $request)
    {
        $raw = ModuleSetting::getValue('theme_sections_json', '');
        $current = !empty($raw) ? (json_decode($raw, true) ?: []) : [];

        $sections = $request->input('sections', []);
        foreach ($sections as $secKey => $secVal) {
            if ($secKey === 'section_order' && is_array($secVal)) {
                $current['section_order'] = array_values(array_filter($secVal));
                continue;
            }
            if (is_array($secVal)) {
                $current[$secKey] = isset($current[$secKey]) && is_array($current[$secKey])
                    ? array_replace_recursive($current[$secKey], $secVal)
                    : $secVal;
            }
        }

        // Handle image uploads into Modules/HajjUmrah/Public/uploads/
        if ($request->hasFile('header_logo')) {
            $current['header']['logo'] = ModuleCmsResolver::storeModuleFile($request->file('header_logo'), 'branding', 'HajjUmrah');
        }
        if ($request->hasFile('hero_image_file')) {
            $current['hero']['hero_image'] = ModuleCmsResolver::storeModuleFile($request->file('hero_image_file'), 'hero', 'HajjUmrah');
        }
        if ($request->hasFile('services_pilgrim_file')) {
            $current['services_section']['pilgrim_image'] = ModuleCmsResolver::storeModuleFile($request->file('services_pilgrim_file'), 'services', 'HajjUmrah');
        }
        if ($request->hasFile('footer_kaaba_file')) {
            $current['footer']['kaaba_image'] = ModuleCmsResolver::storeModuleFile($request->file('footer_kaaba_file'), 'footer', 'HajjUmrah');
        }

        ModuleSetting::setValue('theme_sections_json', json_encode($current), 'theme');

        ActivityLog::record(
            'Hajj & Umrah Sections Updated',
            'Saved in hajj_settings',
            'bi-moon-stars-fill',
            '#DCFCE7',
            '#10B981'
        );

        return back()->with('success', '[Hajj & Umrah] Homepage Builder, Multilingual Translations & Theme Settings saved in hajj_settings!');
    }

    public function resetSettings()
    {
        $this->managerService->resetModuleToDefault('HajjUmrah');

        return back()->with('success', 'Hajj & Umrah module has been reset 100% to its original default template and seeded data!');
    }

    // =========================================================================
    // 2. SPECIAL OFFERS & HAJJ/UMRAH PACKAGES CRUD (hajj_packages)
    // =========================================================================
    public function packages(Request $request)
    {
        $this->dataService->ensureSeeded();
        $search = trim((string) $request->query('search', ''));
        $type = $request->query('type');

        $packages = HajjPackage::query()
            ->when($search !== '', fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('title_bn', 'like', "%{$search}%")->orWhere('title_ar', 'like', "%{$search}%"))
            ->when($type, fn ($q) => $q->where('package_type', $type))
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('hajjumrah::admin.packages', compact('packages', 'search', 'type'));
    }

    public function storePackage(Request $request)
    {
        $validated = $request->validate([
            'package_type'       => 'required|in:Hajj,Umrah,Tour',
            'category_label'     => 'nullable|string|max:120',
            'category_label_bn'  => 'nullable|string|max:120',
            'category_label_ar'  => 'nullable|string|max:120',
            'title'              => 'required|string|max:255',
            'title_bn'           => 'nullable|string|max:255',
            'title_ar'           => 'nullable|string|max:255',
            'price_display'      => 'required|string|max:80',
            'price_unit'         => 'nullable|string|max:60',
            'price_unit_bn'      => 'nullable|string|max:60',
            'price_unit_ar'      => 'nullable|string|max:60',
            'duration'           => 'required|string|max:100',
            'duration_bn'        => 'nullable|string|max:100',
            'duration_ar'        => 'nullable|string|max:100',
            'flight_info'        => 'nullable|string|max:150',
            'makkah_hotel'       => 'nullable|string|max:150',
            'madinah_hotel'      => 'nullable|string|max:150',
            'short_description'  => 'nullable|string',
            'short_description_bn' => 'nullable|string',
            'short_description_ar' => 'nullable|string',
            'description'        => 'nullable|string',
            'description_bn'     => 'nullable|string',
            'description_ar'     => 'nullable|string',
            'features_raw'       => 'nullable|string|max:800',
            'sort_order'         => 'nullable|integer',
            'thumbnail_file'     => 'nullable|image|max:5120',
        ]);

        $thumb = '/module-media/HajjUmrah/theme/offer-passport.jpg';
        if ($request->hasFile('thumbnail_file')) {
            $thumb = ModuleCmsResolver::storeModuleFile($request->file('thumbnail_file'), 'packages', 'HajjUmrah');
        }

        $features = array_values(array_filter(array_map('trim', explode(',', $validated['features_raw'] ?? 'Direct Flight, Visa Processing, Star Hotel, Ziyarah Tour'))));

        $pkg = HajjPackage::create([
            'package_type'         => strtolower($validated['package_type']),
            'category_label'       => $validated['category_label'] ?: 'Category',
            'category_label_bn'    => $validated['category_label_bn'] ?: 'ক্যাটাগরি',
            'category_label_ar'    => $validated['category_label_ar'] ?: 'الفئة',
            'title'                => $validated['title'],
            'title_bn'             => $validated['title_bn'] ?: $validated['title'],
            'title_ar'             => $validated['title_ar'] ?: $validated['title'],
            'slug'                 => Str::slug($validated['title']) . '-' . Str::lower(Str::random(4)),
            'thumbnail'            => $thumb,
            'price'                => $validated['price_display'],
            'price_unit'           => $validated['price_unit'] ?: '/Person',
            'price_unit_bn'        => $validated['price_unit_bn'] ?: '/জনপ্রতি',
            'price_unit_ar'        => $validated['price_unit_ar'] ?: '/للشخص',
            'duration'             => $validated['duration'],
            'duration_bn'          => $validated['duration_bn'] ?: $validated['duration'],
            'duration_ar'          => $validated['duration_ar'] ?: $validated['duration'],
            'airline'              => $validated['flight_info'] ?: 'Direct Saudia / Biman Flight',
            'makkah_hotel'         => $validated['makkah_hotel'] ?: 'Star Hotel Near Haram',
            'madinah_hotel'        => $validated['madinah_hotel'] ?: 'Star Hotel Near Masjid Nabawi',
            'short_description'    => $validated['short_description'],
            'short_description_bn' => $validated['short_description_bn'],
            'short_description_ar' => $validated['short_description_ar'],
            'description'          => $validated['description'] ?: '<p>' . e($validated['short_description']) . '</p>',
            'description_bn'       => $validated['description_bn'],
            'description_ar'       => $validated['description_ar'],
            'features'             => $features,
            'is_featured'          => $request->boolean('is_special_offer', true),
            'is_active'            => $request->boolean('is_active', true),
            'sort_order'           => $validated['sort_order'] ?? (HajjPackage::max('sort_order') + 1),
        ]);

        ActivityLog::record('Hajj/Umrah Package Added', "{$pkg->title} [hajj_packages]", 'bi-airplane-engines', '#DCFCE7', '#10B981');

        return back()->with('success', "Package '{$pkg->title}' saved in hajj_packages!");
    }

    public function updatePackage(Request $request, int $id)
    {
        $pkg = HajjPackage::findOrFail($id);

        $validated = $request->validate([
            'package_type'       => 'required|in:Hajj,Umrah,Tour',
            'category_label'     => 'nullable|string|max:120',
            'category_label_bn'  => 'nullable|string|max:120',
            'category_label_ar'  => 'nullable|string|max:120',
            'title'              => 'required|string|max:255',
            'title_bn'           => 'nullable|string|max:255',
            'title_ar'           => 'nullable|string|max:255',
            'price_display'      => 'required|string|max:80',
            'price_unit'         => 'nullable|string|max:60',
            'price_unit_bn'      => 'nullable|string|max:60',
            'price_unit_ar'      => 'nullable|string|max:60',
            'duration'           => 'required|string|max:100',
            'duration_bn'        => 'nullable|string|max:100',
            'duration_ar'        => 'nullable|string|max:100',
            'flight_info'        => 'nullable|string|max:150',
            'makkah_hotel'       => 'nullable|string|max:150',
            'madinah_hotel'      => 'nullable|string|max:150',
            'short_description'  => 'nullable|string',
            'short_description_bn' => 'nullable|string',
            'short_description_ar' => 'nullable|string',
            'description'        => 'nullable|string',
            'description_bn'     => 'nullable|string',
            'description_ar'     => 'nullable|string',
            'features_raw'       => 'nullable|string|max:800',
            'sort_order'         => 'nullable|integer',
            'thumbnail_file'     => 'nullable|image|max:5120',
        ]);

        $thumb = $pkg->thumbnail;
        if ($request->hasFile('thumbnail_file')) {
            $thumb = ModuleCmsResolver::storeModuleFile($request->file('thumbnail_file'), 'packages', 'HajjUmrah');
        }

        $features = array_values(array_filter(array_map('trim', explode(',', $validated['features_raw'] ?? ''))));

        $pkg->update([
            'package_type'         => strtolower($validated['package_type']),
            'category_label'       => $validated['category_label'] ?: 'Category',
            'category_label_bn'    => $validated['category_label_bn'],
            'category_label_ar'    => $validated['category_label_ar'],
            'title'                => $validated['title'],
            'title_bn'             => $validated['title_bn'],
            'title_ar'             => $validated['title_ar'],
            'thumbnail'            => $thumb,
            'price'                => $validated['price_display'],
            'price_unit'           => $validated['price_unit'] ?: '/Person',
            'price_unit_bn'        => $validated['price_unit_bn'],
            'price_unit_ar'        => $validated['price_unit_ar'],
            'duration'             => $validated['duration'],
            'duration_bn'          => $validated['duration_bn'],
            'duration_ar'          => $validated['duration_ar'],
            'airline'              => $validated['flight_info'],
            'makkah_hotel'         => $validated['makkah_hotel'],
            'madinah_hotel'        => $validated['madinah_hotel'],
            'short_description'    => $validated['short_description'],
            'short_description_bn' => $validated['short_description_bn'],
            'short_description_ar' => $validated['short_description_ar'],
            'description'          => $validated['description'],
            'description_bn'       => $validated['description_bn'],
            'description_ar'       => $validated['description_ar'],
            'features'             => $features,
            'is_featured'          => $request->boolean('is_special_offer'),
            'is_active'            => $request->boolean('is_active'),
            'sort_order'           => $validated['sort_order'] ?? $pkg->sort_order,
        ]);

        return back()->with('success', "Package '{$pkg->title}' updated in hajj_packages!");
    }

    public function deletePackage(int $id)
    {
        HajjPackage::findOrFail($id)->delete();
        return back()->with('success', 'Package deleted from hajj_packages.');
    }

    // =========================================================================
    // 3. ALL-INCLUSIVE PRICING PLANS CRUD (hajj_pricing_plans)
    // =========================================================================
    public function pricing()
    {
        $this->dataService->ensureSeeded();
        $pricingPlans = HajjPricingPlan::orderBy('sort_order')->orderBy('id')->get();

        return view('hajjumrah::admin.pricing', compact('pricingPlans'));
    }

    public function storePricing(Request $request)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:150',
            'name_bn'             => 'nullable|string|max:150',
            'name_ar'             => 'nullable|string|max:150',
            'tagline'             => 'nullable|string',
            'tagline_bn'          => 'nullable|string',
            'tagline_ar'          => 'nullable|string',
            'icon_class'          => 'nullable|string|max:100',
            'currency'            => 'nullable|string|max:30',
            'price'               => 'required|string|max:60',
            'starts_from_label'   => 'nullable|string|max:60',
            'duration'            => 'required|string|max:120',
            'duration_bn'         => 'nullable|string|max:120',
            'duration_ar'         => 'nullable|string|max:120',
            'direct_flight'       => 'required|string|max:150',
            'direct_flight_bn'    => 'nullable|string|max:150',
            'direct_flight_ar'    => 'nullable|string|max:150',
            'hotel_aziziyah'      => 'required|string|max:150',
            'hotel_aziziyah_bn'   => 'nullable|string|max:150',
            'hotel_aziziyah_ar'   => 'nullable|string|max:150',
            'hotel_makkah'        => 'required|string|max:150',
            'hotel_makkah_bn'     => 'nullable|string|max:150',
            'hotel_makkah_ar'     => 'nullable|string|max:150',
            'hotel_madinah'       => 'required|string|max:150',
            'hotel_madinah_bn'    => 'nullable|string|max:150',
            'hotel_madinah_ar'    => 'nullable|string|max:150',
            'food_service'        => 'required|string|max:150',
            'food_service_bn'     => 'nullable|string|max:150',
            'food_service_ar'     => 'nullable|string|max:150',
            'special_services'    => 'required|string|max:255',
            'special_services_bn' => 'nullable|string|max:255',
            'special_services_ar' => 'nullable|string|max:255',
            'button_text'         => 'nullable|string|max:80',
            'button_text_bn'      => 'nullable|string|max:80',
            'button_text_ar'      => 'nullable|string|max:80',
            'terms_note'          => 'nullable|string|max:150',
            'terms_note_bn'       => 'nullable|string|max:150',
            'terms_note_ar'       => 'nullable|string|max:150',
            'sort_order'          => 'nullable|integer',
        ]);

        HajjPricingPlan::create([
            'name'                => $validated['name'],
            'name_bn'             => $validated['name_bn'] ?? $validated['name'],
            'name_ar'             => $validated['name_ar'] ?? $validated['name'],
            'slug'                => Str::slug($validated['name']) . '-' . Str::lower(Str::random(3)),
            'subtitle'            => $validated['tagline'] ?? null,
            'subtitle_bn'         => $validated['tagline_bn'] ?? null,
            'subtitle_ar'         => $validated['tagline_ar'] ?? null,
            'icon_class'          => $validated['icon_class'] ?: 'fa-solid fa-crown',
            'currency'            => $validated['currency'] ?: 'BDT',
            'price'               => $validated['price'],
            'price_suffix'        => $validated['starts_from_label'] ?: 'STARTS FROM',
            'duration'            => $validated['duration'],
            'duration_bn'         => $validated['duration_bn'] ?? null,
            'duration_ar'         => $validated['duration_ar'] ?? null,
            'direct_flight'       => $validated['direct_flight'],
            'direct_flight_bn'    => $validated['direct_flight_bn'] ?? null,
            'direct_flight_ar'    => $validated['direct_flight_ar'] ?? null,
            'hotel_aziziyah'      => $validated['hotel_aziziyah'],
            'hotel_aziziyah_bn'   => $validated['hotel_aziziyah_bn'] ?? null,
            'hotel_aziziyah_ar'   => $validated['hotel_aziziyah_ar'] ?? null,
            'hotel_makkah'        => $validated['hotel_makkah'],
            'hotel_makkah_bn'     => $validated['hotel_makkah_bn'] ?? null,
            'hotel_makkah_ar'     => $validated['hotel_makkah_ar'] ?? null,
            'hotel_madinah'       => $validated['hotel_madinah'],
            'hotel_madinah_bn'    => $validated['hotel_madinah_bn'] ?? null,
            'hotel_madinah_ar'    => $validated['hotel_madinah_ar'] ?? null,
            'food'                => $validated['food_service'],
            'food_bn'             => $validated['food_service_bn'] ?? null,
            'food_ar'             => $validated['food_service_ar'] ?? null,
            'special_services'    => $validated['special_services'],
            'special_services_bn' => $validated['special_services_bn'] ?? null,
            'special_services_ar' => $validated['special_services_ar'] ?? null,
            'button_text'         => $validated['button_text'] ?: 'SELECT PACKAGE',
            'terms_text'          => $validated['terms_note'] ?: '*Terms & Conditions Applicable',
            'is_featured'         => $request->boolean('is_highlighted', false),
            'is_active'           => $request->boolean('is_active', true),
            'sort_order'          => $validated['sort_order'] ?? (HajjPricingPlan::max('sort_order') + 1),
        ]);

        return back()->with('success', "Pricing Plan '{$validated['name']}' saved in hajj_pricing_plans!");
    }

    public function updatePricing(Request $request, int $id)
    {
        $plan = HajjPricingPlan::findOrFail($id);

        $validated = $request->validate([
            'name'                => 'required|string|max:150',
            'name_bn'             => 'nullable|string|max:150',
            'name_ar'             => 'nullable|string|max:150',
            'tagline'             => 'nullable|string',
            'tagline_bn'          => 'nullable|string',
            'tagline_ar'          => 'nullable|string',
            'icon_class'          => 'nullable|string|max:100',
            'currency'            => 'nullable|string|max:30',
            'price'               => 'required|string|max:60',
            'starts_from_label'   => 'nullable|string|max:60',
            'duration'            => 'required|string|max:120',
            'duration_bn'         => 'nullable|string|max:120',
            'duration_ar'         => 'nullable|string|max:120',
            'direct_flight'       => 'required|string|max:150',
            'direct_flight_bn'    => 'nullable|string|max:150',
            'direct_flight_ar'    => 'nullable|string|max:150',
            'hotel_aziziyah'      => 'required|string|max:150',
            'hotel_aziziyah_bn'   => 'nullable|string|max:150',
            'hotel_aziziyah_ar'   => 'nullable|string|max:150',
            'hotel_makkah'        => 'required|string|max:150',
            'hotel_makkah_bn'     => 'nullable|string|max:150',
            'hotel_makkah_ar'     => 'nullable|string|max:150',
            'hotel_madinah'       => 'required|string|max:150',
            'hotel_madinah_bn'    => 'nullable|string|max:150',
            'hotel_madinah_ar'    => 'nullable|string|max:150',
            'food_service'        => 'required|string|max:150',
            'food_service_bn'     => 'nullable|string|max:150',
            'food_service_ar'     => 'nullable|string|max:150',
            'special_services'    => 'required|string|max:255',
            'special_services_bn' => 'nullable|string|max:255',
            'special_services_ar' => 'nullable|string|max:255',
            'button_text'         => 'nullable|string|max:80',
            'button_text_bn'      => 'nullable|string|max:80',
            'button_text_ar'      => 'nullable|string|max:80',
            'terms_note'          => 'nullable|string|max:150',
            'terms_note_bn'       => 'nullable|string|max:150',
            'terms_note_ar'       => 'nullable|string|max:150',
            'sort_order'          => 'nullable|integer',
        ]);

        $plan->update([
            'name'                => $validated['name'],
            'name_bn'             => $validated['name_bn'] ?? $plan->name_bn,
            'name_ar'             => $validated['name_ar'] ?? $plan->name_ar,
            'subtitle'            => $validated['tagline'] ?? $plan->subtitle,
            'subtitle_bn'         => $validated['tagline_bn'] ?? $plan->subtitle_bn,
            'subtitle_ar'         => $validated['tagline_ar'] ?? $plan->subtitle_ar,
            'icon_class'          => $validated['icon_class'] ?: $plan->icon_class,
            'currency'            => $validated['currency'] ?: $plan->currency,
            'price'               => $validated['price'],
            'duration'            => $validated['duration'],
            'duration_bn'         => $validated['duration_bn'] ?? $plan->duration_bn,
            'duration_ar'         => $validated['duration_ar'] ?? $plan->duration_ar,
            'direct_flight'       => $validated['direct_flight'],
            'direct_flight_bn'    => $validated['direct_flight_bn'] ?? $plan->direct_flight_bn,
            'direct_flight_ar'    => $validated['direct_flight_ar'] ?? $plan->direct_flight_ar,
            'hotel_aziziyah'      => $validated['hotel_aziziyah'],
            'hotel_makkah'        => $validated['hotel_makkah'],
            'hotel_madinah'       => $validated['hotel_madinah'],
            'food'                => $validated['food_service'],
            'special_services'    => $validated['special_services'],
            'is_featured'         => $request->boolean('is_highlighted'),
            'is_active'           => $request->boolean('is_active'),
            'sort_order'          => $validated['sort_order'] ?? $plan->sort_order,
        ]);

        return back()->with('success', "Pricing Plan '{$plan->name}' updated in hajj_pricing_plans!");
    }

    public function deletePricing(int $id)
    {
        HajjPricingPlan::findOrFail($id)->delete();
        return back()->with('success', 'Pricing plan removed from hajj_pricing_plans.');
    }

    // =========================================================================
    // 4. WHAT DO WE OFFER SERVICES CRUD (hajj_services)
    // =========================================================================
    public function services(Request $request)
    {
        $this->dataService->ensureSeeded();
        $services = HajjService::orderBy('sort_order')->orderBy('id')->get();

        return view('hajjumrah::admin.services', compact('services'));
    }

    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:150',
            'title_bn'       => 'nullable|string|max:150',
            'title_ar'       => 'nullable|string|max:150',
            'subtitle'       => 'nullable|string|max:180',
            'subtitle_bn'    => 'nullable|string|max:180',
            'subtitle_ar'    => 'nullable|string|max:180',
            'description'    => 'nullable|string',
            'description_bn' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'icon_class'     => 'nullable|string|max:100',
            'sort_order'     => 'nullable|integer',
        ]);

        HajjService::create([
            'title'          => $validated['title'],
            'title_bn'       => $validated['title_bn'] ?: $validated['title'],
            'title_ar'       => $validated['title_ar'] ?: $validated['title'],
            'slug'           => Str::slug($validated['title']) . '-' . Str::lower(Str::random(3)),
            'subtitle'       => $validated['subtitle'],
            'subtitle_bn'    => $validated['subtitle_bn'],
            'subtitle_ar'    => $validated['subtitle_ar'],
            'description'    => $validated['description'],
            'description_bn' => $validated['description_bn'],
            'description_ar' => $validated['description_ar'],
            'icon_class'     => $validated['icon_class'] ?: 'fa-solid fa-plane-up',
            'sort_order'     => $validated['sort_order'] ?? (HajjService::max('sort_order') + 1),
            'is_active'      => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Service '{$validated['title']}' saved in hajj_services!");
    }

    public function updateService(Request $request, int $id)
    {
        $srv = HajjService::findOrFail($id);
        $validated = $request->validate([
            'title'          => 'required|string|max:150',
            'title_bn'       => 'nullable|string|max:150',
            'title_ar'       => 'nullable|string|max:150',
            'subtitle'       => 'nullable|string|max:180',
            'subtitle_bn'    => 'nullable|string|max:180',
            'subtitle_ar'    => 'nullable|string|max:180',
            'description'    => 'nullable|string',
            'description_bn' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'icon_class'     => 'nullable|string|max:100',
            'sort_order'     => 'nullable|integer',
        ]);

        $srv->update([
            'title'          => $validated['title'],
            'title_bn'       => $validated['title_bn'],
            'title_ar'       => $validated['title_ar'],
            'subtitle'       => $validated['subtitle'],
            'subtitle_bn'    => $validated['subtitle_bn'],
            'subtitle_ar'    => $validated['subtitle_ar'],
            'description'    => $validated['description'],
            'description_bn' => $validated['description_bn'],
            'description_ar' => $validated['description_ar'],
            'icon_class'     => $validated['icon_class'] ?: 'fa-solid fa-plane-up',
            'sort_order'     => $validated['sort_order'] ?? $srv->sort_order,
            'is_active'      => $request->boolean('is_active'),
        ]);

        return back()->with('success', "Service '{$srv->title}' updated in hajj_services!");
    }

    public function deleteService(int $id)
    {
        HajjService::findOrFail($id)->delete();
        return back()->with('success', 'Service removed from hajj_services.');
    }

    // =========================================================================
    // 5. AFFILIATED AIRLINES, HOTELS & TRUST BADGES CRUD (hajj_partners)
    // =========================================================================
    public function partners()
    {
        $this->dataService->ensureSeeded();
        $partners = HajjPartner::orderBy('type')->orderBy('sort_order')->get();

        return view('hajjumrah::admin.partners', compact('partners'));
    }

    public function storePartner(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:120',
            'partner_type'  => 'required|in:Airline,Hotel,TrustBadge',
            'icon_class'    => 'nullable|string|max:100',
            'brand_color'   => 'nullable|string|max:30',
            'rating_text'   => 'nullable|string|max:50',
            'reviews_count' => 'nullable|string|max:80',
            'website_url'   => 'nullable|string|max:255',
            'sort_order'    => 'nullable|integer',
            'logo_file'     => 'nullable|image|max:3072',
        ]);

        $logo = null;
        if ($request->hasFile('logo_file')) {
            $logo = ModuleCmsResolver::storeModuleFile($request->file('logo_file'), 'partners', 'HajjUmrah');
        }

        $typeMap = ['TrustBadge' => 'review_badge', 'Hotel' => 'hotel', 'Airline' => 'airline'];

        HajjPartner::create([
            'name'         => $validated['name'],
            'type'         => $typeMap[$validated['partner_type']] ?? 'airline',
            'logo'         => $logo,
            'rating'       => $validated['rating_text'],
            'reviews_text' => $validated['reviews_count'],
            'website_url'  => $validated['website_url'] ?: '#',
            'sort_order'   => $validated['sort_order'] ?? (HajjPartner::max('sort_order') + 1),
            'is_active'    => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Partner '{$validated['name']}' saved in hajj_partners!");
    }

    public function updatePartner(Request $request, int $id)
    {
        $partner = HajjPartner::findOrFail($id);
        $validated = $request->validate([
            'name'          => 'required|string|max:120',
            'partner_type'  => 'required|in:Airline,Hotel,TrustBadge',
            'icon_class'    => 'nullable|string|max:100',
            'brand_color'   => 'nullable|string|max:30',
            'rating_text'   => 'nullable|string|max:50',
            'reviews_count' => 'nullable|string|max:80',
            'website_url'   => 'nullable|string|max:255',
            'sort_order'    => 'nullable|integer',
            'logo_file'     => 'nullable|image|max:3072',
        ]);

        $logo = $partner->logo;
        if ($request->hasFile('logo_file')) {
            $logo = ModuleCmsResolver::storeModuleFile($request->file('logo_file'), 'partners', 'HajjUmrah');
        }

        $typeMap = ['TrustBadge' => 'review_badge', 'Hotel' => 'hotel', 'Airline' => 'airline'];

        $partner->update([
            'name'         => $validated['name'],
            'type'         => $typeMap[$validated['partner_type']] ?? 'airline',
            'logo'         => $logo,
            'rating'       => $validated['rating_text'],
            'reviews_text' => $validated['reviews_count'],
            'website_url'  => $validated['website_url'],
            'sort_order'   => $validated['sort_order'] ?? $partner->sort_order,
            'is_active'    => $request->boolean('is_active'),
        ]);

        return back()->with('success', "Partner '{$partner->name}' updated in hajj_partners!");
    }

    public function deletePartner(int $id)
    {
        HajjPartner::findOrFail($id)->delete();
        return back()->with('success', 'Partner removed from hajj_partners.');
    }

    // =========================================================================
    // 6. PILGRIM TESTIMONIALS CRUD (hajj_testimonials)
    // =========================================================================
    public function testimonials()
    {
        $this->dataService->ensureSeeded();
        $testimonials = HajjTestimonial::orderBy('sort_order')->latest('id')->get();

        return view('hajjumrah::admin.testimonials', compact('testimonials'));
    }

    public function storeTestimonial(Request $request)
    {
        $validated = $request->validate([
            'client_name'    => 'required|string|max:120',
            'client_name_bn' => 'nullable|string|max:120',
            'client_name_ar' => 'nullable|string|max:120',
            'designation'    => 'nullable|string|max:150',
            'designation_bn' => 'nullable|string|max:150',
            'designation_ar' => 'nullable|string|max:150',
            'review_text'    => 'required|string',
            'review_text_bn' => 'nullable|string',
            'review_text_ar' => 'nullable|string',
            'rating'         => 'required|integer|min:1|max:5',
            'avatar_file'    => 'nullable|image|max:3072',
            'sort_order'     => 'nullable|integer',
        ]);

        $avatar = '/module-media/HajjUmrah/theme/services-pilgrim.jpg';
        if ($request->hasFile('avatar_file')) {
            $avatar = ModuleCmsResolver::storeModuleFile($request->file('avatar_file'), 'testimonials', 'HajjUmrah');
        }

        HajjTestimonial::create([
            'client_name'    => $validated['client_name'],
            'client_name_bn' => $validated['client_name_bn'] ?: $validated['client_name'],
            'client_name_ar' => $validated['client_name_ar'] ?: $validated['client_name'],
            'designation'    => $validated['designation'],
            'designation_bn' => $validated['designation_bn'],
            'designation_ar' => $validated['designation_ar'],
            'review_text'    => $validated['review_text'],
            'review_text_bn' => $validated['review_text_bn'],
            'review_text_ar' => $validated['review_text_ar'],
            'rating'         => $validated['rating'],
            'avatar'         => $avatar,
            'sort_order'     => $validated['sort_order'] ?? (HajjTestimonial::max('sort_order') + 1),
            'is_active'      => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Pilgrim testimonial saved in hajj_testimonials!");
    }

    public function updateTestimonial(Request $request, int $id)
    {
        $testi = HajjTestimonial::findOrFail($id);
        $validated = $request->validate([
            'client_name'    => 'required|string|max:120',
            'client_name_bn' => 'nullable|string|max:120',
            'client_name_ar' => 'nullable|string|max:120',
            'designation'    => 'nullable|string|max:150',
            'designation_bn' => 'nullable|string|max:150',
            'designation_ar' => 'nullable|string|max:150',
            'review_text'    => 'required|string',
            'review_text_bn' => 'nullable|string',
            'review_text_ar' => 'nullable|string',
            'rating'         => 'required|integer|min:1|max:5',
            'avatar_file'    => 'nullable|image|max:3072',
            'sort_order'     => 'nullable|integer',
        ]);

        $avatar = $testi->avatar;
        if ($request->hasFile('avatar_file')) {
            $avatar = ModuleCmsResolver::storeModuleFile($request->file('avatar_file'), 'testimonials', 'HajjUmrah');
        }

        $testi->update([
            'client_name'    => $validated['client_name'],
            'client_name_bn' => $validated['client_name_bn'],
            'client_name_ar' => $validated['client_name_ar'],
            'designation'    => $validated['designation'],
            'designation_bn' => $validated['designation_bn'],
            'designation_ar' => $validated['designation_ar'],
            'review_text'    => $validated['review_text'],
            'review_text_bn' => $validated['review_text_bn'],
            'review_text_ar' => $validated['review_text_ar'],
            'rating'         => $validated['rating'],
            'avatar'         => $avatar,
            'sort_order'     => $validated['sort_order'] ?? $testi->sort_order,
            'is_active'      => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Pilgrim testimonial updated in hajj_testimonials!');
    }

    public function deleteTestimonial(int $id)
    {
        HajjTestimonial::findOrFail($id)->delete();
        return back()->with('success', 'Testimonial removed from hajj_testimonials.');
    }

    // =========================================================================
    // 7. BLOG & TRAVEL GUIDES CRUD (hajj_blogs)
    // =========================================================================
    public function blogs(Request $request)
    {
        $this->dataService->ensureSeeded();
        $search = trim((string) $request->query('search', ''));
        $blogs = HajjBlog::when($search !== '', fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('title_bn', 'like', "%{$search}%"))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('hajjumrah::admin.blogs', compact('blogs', 'search'));
    }

    public function storeBlog(Request $request)
    {
        $validated = $request->validate([
            'title'                => 'required|string|max:255',
            'title_bn'             => 'nullable|string|max:255',
            'title_ar'             => 'nullable|string|max:255',
            'category'             => 'required|string|max:100',
            'category_bn'          => 'nullable|string|max:100',
            'category_ar'          => 'nullable|string|max:100',
            'published_date'       => 'nullable|string|max:60',
            'short_description'    => 'required|string',
            'short_description_bn' => 'nullable|string',
            'short_description_ar' => 'nullable|string',
            'content'              => 'nullable|string',
            'content_bn'           => 'nullable|string',
            'content_ar'           => 'nullable|string',
            'image_file'           => 'nullable|image|max:4096',
        ]);

        $img = '/module-media/HajjUmrah/theme/offer-kaaba-door.jpg';
        if ($request->hasFile('image_file')) {
            $img = ModuleCmsResolver::storeModuleFile($request->file('image_file'), 'blog', 'HajjUmrah');
        }

        HajjBlog::create([
            'title'                => $validated['title'],
            'title_bn'             => $validated['title_bn'] ?: $validated['title'],
            'title_ar'             => $validated['title_ar'] ?: $validated['title'],
            'slug'                 => Str::slug($validated['title']) . '-' . Str::lower(Str::random(4)),
            'category'             => $validated['category'],
            'category_bn'          => $validated['category_bn'] ?: $validated['category'],
            'category_ar'          => $validated['category_ar'] ?: $validated['category'],
            'published_date'       => $validated['published_date'] ?: date('M d, Y'),
            'short_description'    => $validated['short_description'],
            'short_description_bn' => $validated['short_description_bn'],
            'short_description_ar' => $validated['short_description_ar'],
            'content'              => $validated['content'] ?: '<p>' . e($validated['short_description']) . '</p>',
            'content_bn'           => $validated['content_bn'],
            'content_ar'           => $validated['content_ar'],
            'image'                => $img,
            'views'                => 1,
            'is_featured'          => true,
            'is_active'            => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Article '{$validated['title']}' published in hajj_blogs!");
    }

    public function updateBlog(Request $request, int $id)
    {
        $blog = HajjBlog::findOrFail($id);
        $validated = $request->validate([
            'title'                => 'required|string|max:255',
            'title_bn'             => 'nullable|string|max:255',
            'title_ar'             => 'nullable|string|max:255',
            'category'             => 'required|string|max:100',
            'category_bn'          => 'nullable|string|max:100',
            'category_ar'          => 'nullable|string|max:100',
            'published_date'       => 'nullable|string|max:60',
            'short_description'    => 'required|string',
            'short_description_bn' => 'nullable|string',
            'short_description_ar' => 'nullable|string',
            'content'              => 'nullable|string',
            'content_bn'           => 'nullable|string',
            'content_ar'           => 'nullable|string',
            'image_file'           => 'nullable|image|max:4096',
        ]);

        $img = $blog->image;
        if ($request->hasFile('image_file')) {
            $img = ModuleCmsResolver::storeModuleFile($request->file('image_file'), 'blog', 'HajjUmrah');
        }

        $blog->update([
            'title'                => $validated['title'],
            'title_bn'             => $validated['title_bn'],
            'title_ar'             => $validated['title_ar'],
            'category'             => $validated['category'],
            'category_bn'          => $validated['category_bn'],
            'category_ar'          => $validated['category_ar'],
            'published_date'       => $validated['published_date'],
            'short_description'    => $validated['short_description'],
            'short_description_bn' => $validated['short_description_bn'],
            'short_description_ar' => $validated['short_description_ar'],
            'content'              => $validated['content'],
            'content_bn'           => $validated['content_bn'],
            'content_ar'           => $validated['content_ar'],
            'image'                => $img,
            'is_active'            => $request->boolean('is_active'),
        ]);

        return back()->with('success', "Article '{$blog->title}' updated in hajj_blogs!");
    }

    public function deleteBlog(int $id)
    {
        HajjBlog::findOrFail($id)->delete();
        return back()->with('success', 'Article deleted from hajj_blogs.');
    }

    // =========================================================================
    // 8. BOOKING INQUIRIES & NEWSLETTER SUBSCRIBERS (hajj_inquiries, hajj_subscribers)
    // =========================================================================
    public function inquiries(Request $request)
    {
        $this->dataService->ensureSeeded();
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status');

        $inquiries = HajjInquiry::query()
            ->when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%")->orWhere('package_name', 'like', "%{$search}%"))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $subscribers = HajjSubscriber::latest('id')->get();
        $pendingCount = HajjInquiry::where('status', 'Pending')->count();

        return view('hajjumrah::admin.inquiries', compact('inquiries', 'subscribers', 'search', 'status', 'pendingCount'));
    }

    public function updateInquiryStatus(Request $request, int $id)
    {
        $inq = HajjInquiry::findOrFail($id);
        $validated = $request->validate([
            'status' => 'required|in:Pending,Contacted,Booked',
        ]);
        $inq->update(['status' => $validated['status']]);

        return back()->with('success', "Booking inquiry status updated to '{$validated['status']}'.");
    }

    public function deleteInquiry(int $id)
    {
        HajjInquiry::findOrFail($id)->delete();
        return back()->with('success', 'Booking inquiry deleted from hajj_inquiries.');
    }

    public function deleteSubscriber(int $id)
    {
        HajjSubscriber::findOrFail($id)->delete();
        return back()->with('success', 'Subscriber removed from hajj_subscribers.');
    }
}
