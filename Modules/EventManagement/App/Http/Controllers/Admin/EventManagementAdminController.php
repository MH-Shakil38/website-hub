<?php

namespace Modules\EventManagement\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\ModuleManagerService;
use App\Support\ModuleCmsResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\EventManagement\App\Models\EmMemory;
use Modules\EventManagement\App\Models\EmPricingPlan;
use Modules\EventManagement\App\Models\EmSchedule;
use Modules\EventManagement\App\Models\EmSpeaker;
use Modules\EventManagement\App\Models\EmSponsor;
use Modules\EventManagement\App\Models\EmTicketBooking;
use Modules\EventManagement\App\Models\ModuleSetting;
use Modules\EventManagement\App\Services\EventManagementDataService;

class EventManagementAdminController extends Controller
{
    public function __construct(
        protected EventManagementDataService $dataService
    ) {}

    public function dashboard()
    {
        $this->dataService->ensureSeeded();

        $speakersCount  = EmSpeaker::count();
        $schedulesCount = EmSchedule::count();
        $pricingCount   = EmPricingPlan::count();
        $sponsorsCount  = EmSponsor::count();
        $memoriesCount  = EmMemory::count();
        $bookingsCount  = EmTicketBooking::count();
        $totalRevenue   = EmTicketBooking::sum('total_amount');

        $recentSpeakers = EmSpeaker::orderBy('sort_order')->take(4)->get();
        $recentBookings = EmTicketBooking::latest('id')->take(6)->get();

        return view('eventmanagement::admin.dashboard', compact(
            'speakersCount',
            'schedulesCount',
            'pricingCount',
            'sponsorsCount',
            'memoriesCount',
            'bookingsCount',
            'totalRevenue',
            'recentSpeakers',
            'recentBookings'
        ));
    }

    // =========================================================================
    // 1. EVENT SPEAKERS CRUD (em_speakers)
    // =========================================================================
    public function speakers()
    {
        $this->dataService->ensureSeeded();
        $speakers = EmSpeaker::orderBy('sort_order')->orderBy('id')->get();

        return view('eventmanagement::admin.speakers', compact('speakers'));
    }

    public function storeSpeaker(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:120',
            'designation' => 'required|string|max:120',
            'company'     => 'nullable|string|max:120',
            'bio'         => 'nullable|string|max:500',
            'sort_order'  => 'nullable|integer',
            'avatar_file' => 'nullable|image|max:4096',
        ]);

        $avatarUrl = '/module-media/EventManagement/theme/speaker-alex-robertson.jpg';
        if ($request->hasFile('avatar_file')) {
            $avatarUrl = ModuleCmsResolver::storeModuleFile($request->file('avatar_file'), 'speakers', 'EventManagement');
        }

        $speaker = EmSpeaker::create([
            'name'        => $validated['name'],
            'slug'        => Str::slug($validated['name']) . '-' . Str::random(3),
            'designation' => $validated['designation'],
            'company'     => $validated['company'] ?? 'Eventify Global',
            'avatar'      => $avatarUrl,
            'bio'         => $validated['bio'] ?? null,
            'sort_order'  => $validated['sort_order'] ?? (EmSpeaker::max('sort_order') + 1),
            'is_active'   => true,
        ]);

        ActivityLog::record('Eventify speaker added', "{$speaker->name} ({$speaker->designation}) [em_speakers]", 'bi-mic-fill', '#ECFCCB', '#4D7C0F');

        return back()->with('success', "Speaker [{$speaker->name}] added successfully.");
    }

    public function updateSpeaker(Request $request, int $id)
    {
        $speaker = EmSpeaker::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'required|string|max:120',
            'designation' => 'required|string|max:120',
            'company'     => 'nullable|string|max:120',
            'bio'         => 'nullable|string|max:500',
            'sort_order'  => 'nullable|integer',
            'avatar_file' => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('avatar_file')) {
            $speaker->avatar = ModuleCmsResolver::storeModuleFile($request->file('avatar_file'), 'speakers', 'EventManagement');
        }

        $speaker->update([
            'name'        => $validated['name'],
            'designation' => $validated['designation'],
            'company'     => $validated['company'] ?? $speaker->company,
            'bio'         => $validated['bio'] ?? $speaker->bio,
            'sort_order'  => $validated['sort_order'] ?? $speaker->sort_order,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Speaker [{$speaker->name}] updated successfully.");
    }

    public function deleteSpeaker(int $id)
    {
        EmSpeaker::findOrFail($id)->delete();

        return back()->with('success', 'Speaker removed from em_speakers.');
    }

    // =========================================================================
    // 2. EVENT SCHEDULE PLAN CRUD (em_schedules)
    // =========================================================================
    public function schedules()
    {
        $this->dataService->ensureSeeded();
        $schedules = EmSchedule::orderBy('sort_order')->orderBy('id')->get();

        return view('eventmanagement::admin.schedules', compact('schedules'));
    }

    public function storeSchedule(Request $request)
    {
        $validated = $request->validate([
            'day_key'        => 'required|in:day1,day2,day3',
            'day_label'      => 'nullable|string|max:60',
            'day_date'       => 'nullable|string|max:60',
            'session_number' => 'required|string|max:10',
            'title'          => 'required|string|max:180',
            'time_range'     => 'required|string|max:80',
            'venue_location' => 'required|string|max:120',
            'speaker1_name'  => 'required|string|max:100',
            'speaker1_role'  => 'nullable|string|max:100',
            'speaker2_name'  => 'nullable|string|max:100',
            'speaker2_role'  => 'nullable|string|max:100',
            'button_text'    => 'nullable|string|max:60',
            'sort_order'     => 'nullable|integer',
            'image_file'     => 'nullable|image|max:4096',
        ]);

        $imageUrl = '/module-media/EventManagement/theme/schedule-01.jpg';
        if ($request->hasFile('image_file')) {
            $imageUrl = ModuleCmsResolver::storeModuleFile($request->file('image_file'), 'schedules', 'EventManagement');
        }

        $dayLabels = ['day1' => 'Day One', 'day2' => 'Day Two', 'day3' => 'Day Three'];

        $sch = EmSchedule::create([
            'day_key'         => $validated['day_key'],
            'day_label'       => $validated['day_label'] ?: ($dayLabels[$validated['day_key']] ?? 'Day One'),
            'day_date'        => $validated['day_date'] ?: 'May 25, 2024',
            'session_number'  => $validated['session_number'],
            'title'           => $validated['title'],
            'time_range'      => $validated['time_range'],
            'venue_location'  => $validated['venue_location'],
            'image'           => $imageUrl,
            'speaker1_name'   => $validated['speaker1_name'],
            'speaker1_role'   => $validated['speaker1_role'] ?: 'UI/UX Designer',
            'speaker1_avatar' => '/module-media/EventManagement/theme/speaker-alex-robertson.jpg',
            'speaker2_name'   => $validated['speaker2_name'] ?: 'Alexys Archer',
            'speaker2_role'   => $validated['speaker2_role'] ?: 'WP Developer',
            'speaker2_avatar' => '/module-media/EventManagement/theme/speaker-alexy-sammo.jpg',
            'button_text'     => $validated['button_text'] ?: 'PURCHASE TICKET NOW',
            'sort_order'      => $validated['sort_order'] ?? (EmSchedule::max('sort_order') + 1),
            'is_active'       => true,
        ]);

        ActivityLog::record('Schedule session added', "{$sch->title} ({$sch->day_label}) [em_schedules]", 'bi-calendar2-week-fill', '#DBEAFE', '#2563EB');

        return back()->with('success', "Schedule session [{$sch->title}] created successfully.");
    }

    public function updateSchedule(Request $request, int $id)
    {
        $sch = EmSchedule::findOrFail($id);

        $validated = $request->validate([
            'day_key'        => 'required|in:day1,day2,day3',
            'session_number' => 'required|string|max:10',
            'title'          => 'required|string|max:180',
            'time_range'     => 'required|string|max:80',
            'venue_location' => 'required|string|max:120',
            'speaker1_name'  => 'required|string|max:100',
            'speaker1_role'  => 'nullable|string|max:100',
            'speaker2_name'  => 'nullable|string|max:100',
            'speaker2_role'  => 'nullable|string|max:100',
            'button_text'    => 'nullable|string|max:60',
            'sort_order'     => 'nullable|integer',
            'image_file'     => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('image_file')) {
            $sch->image = ModuleCmsResolver::storeModuleFile($request->file('image_file'), 'schedules', 'EventManagement');
        }

        $dayLabels = ['day1' => 'Day One', 'day2' => 'Day Two', 'day3' => 'Day Three'];

        $sch->update([
            'day_key'        => $validated['day_key'],
            'day_label'      => $dayLabels[$validated['day_key']] ?? $sch->day_label,
            'session_number' => $validated['session_number'],
            'title'          => $validated['title'],
            'time_range'     => $validated['time_range'],
            'venue_location' => $validated['venue_location'],
            'speaker1_name'  => $validated['speaker1_name'],
            'speaker1_role'  => $validated['speaker1_role'] ?: $sch->speaker1_role,
            'speaker2_name'  => $validated['speaker2_name'] ?: $sch->speaker2_name,
            'speaker2_role'  => $validated['speaker2_role'] ?: $sch->speaker2_role,
            'button_text'    => $validated['button_text'] ?: $sch->button_text,
            'sort_order'     => $validated['sort_order'] ?? $sch->sort_order,
            'is_active'      => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Schedule session [{$sch->title}] updated successfully.");
    }

    public function deleteSchedule(int $id)
    {
        EmSchedule::findOrFail($id)->delete();

        return back()->with('success', 'Schedule session deleted from em_schedules.');
    }

    // =========================================================================
    // 3. TICKET PRICING PLANS CRUD (em_pricing_plans)
    // =========================================================================
    public function pricing()
    {
        $this->dataService->ensureSeeded();
        $plans = EmPricingPlan::orderBy('sort_order')->orderBy('id')->get();

        return view('eventmanagement::admin.pricing', compact('plans'));
    }

    public function storePricing(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:80',
            'price'          => 'required|numeric|min:0',
            'currency'       => 'nullable|string|max:10',
            'subtitle'       => 'nullable|string|max:80',
            'features_text'  => 'nullable|string|max:500',
            'button_text'    => 'nullable|string|max:60',
            'is_highlighted' => 'nullable|boolean',
            'sort_order'     => 'nullable|integer',
        ]);

        $plan = EmPricingPlan::create([
            'name'           => strtoupper($validated['name']),
            'slug'           => Str::slug($validated['name']) . '-' . Str::random(3),
            'price'          => $validated['price'],
            'currency'       => $validated['currency'] ?: '$',
            'subtitle'       => $validated['subtitle'] ?: 'ATTENDEE',
            'features_text'  => $validated['features_text'] ?? null,
            'button_text'    => $validated['button_text'] ?: 'BUY TICKET',
            'is_highlighted' => $request->boolean('is_highlighted'),
            'sort_order'     => $validated['sort_order'] ?? (EmPricingPlan::max('sort_order') + 1),
            'is_active'      => true,
        ]);

        return back()->with('success', "Ticket plan [{$plan->name}] created successfully.");
    }

    public function updatePricing(Request $request, int $id)
    {
        $plan = EmPricingPlan::findOrFail($id);

        $validated = $request->validate([
            'name'           => 'required|string|max:80',
            'price'          => 'required|numeric|min:0',
            'currency'       => 'nullable|string|max:10',
            'subtitle'       => 'nullable|string|max:80',
            'features_text'  => 'nullable|string|max:500',
            'button_text'    => 'nullable|string|max:60',
            'is_highlighted' => 'nullable|boolean',
            'sort_order'     => 'nullable|integer',
        ]);

        $plan->update([
            'name'           => strtoupper($validated['name']),
            'price'          => $validated['price'],
            'currency'       => $validated['currency'] ?: '$',
            'subtitle'       => $validated['subtitle'] ?: 'ATTENDEE',
            'features_text'  => $validated['features_text'] ?? $plan->features_text,
            'button_text'    => $validated['button_text'] ?: 'BUY TICKET',
            'is_highlighted' => $request->boolean('is_highlighted'),
            'sort_order'     => $validated['sort_order'] ?? $plan->sort_order,
            'is_active'      => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Ticket plan [{$plan->name}] updated successfully.");
    }

    public function deletePricing(int $id)
    {
        EmPricingPlan::findOrFail($id)->delete();

        return back()->with('success', 'Ticket plan deleted from em_pricing_plans.');
    }

    // =========================================================================
    // 4. SPONSORS & PARTNERS CRUD (em_sponsors)
    // =========================================================================
    public function sponsors()
    {
        $this->dataService->ensureSeeded();
        $sponsors = EmSponsor::orderBy('sort_order')->orderBy('id')->get();

        return view('eventmanagement::admin.sponsors', compact('sponsors'));
    }

    public function storeSponsor(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:120',
            'icon_class'  => 'nullable|string|max:80',
            'website_url' => 'nullable|string|max:200',
            'sort_order'  => 'nullable|integer',
        ]);

        $sp = EmSponsor::create([
            'name'        => $validated['name'],
            'icon_class'  => $validated['icon_class'] ?: 'bi-brightness-high-fill',
            'website_url' => $validated['website_url'] ?: '#sponsors',
            'sort_order'  => $validated['sort_order'] ?? (EmSponsor::max('sort_order') + 1),
            'is_active'   => true,
        ]);

        return back()->with('success', "Sponsor [{$sp->name}] added successfully.");
    }

    public function updateSponsor(Request $request, int $id)
    {
        $sp = EmSponsor::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'required|string|max:120',
            'icon_class'  => 'nullable|string|max:80',
            'website_url' => 'nullable|string|max:200',
            'sort_order'  => 'nullable|integer',
        ]);

        $sp->update([
            'name'        => $validated['name'],
            'icon_class'  => $validated['icon_class'] ?: $sp->icon_class,
            'website_url' => $validated['website_url'] ?: $sp->website_url,
            'sort_order'  => $validated['sort_order'] ?? $sp->sort_order,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Sponsor [{$sp->name}] updated successfully.");
    }

    public function deleteSponsor(int $id)
    {
        EmSponsor::findOrFail($id)->delete();

        return back()->with('success', 'Sponsor removed from em_sponsors.');
    }

    // =========================================================================
    // 5. RECENT MEMORIES & GALLERY CRUD (em_memories)
    // =========================================================================
    public function memories()
    {
        $this->dataService->ensureSeeded();
        $memories = EmMemory::orderBy('sort_order')->orderBy('id')->get();

        return view('eventmanagement::admin.memories', compact('memories'));
    }

    public function storeMemory(Request $request)
    {
        $validated = $request->validate([
            'title'             => 'required|string|max:160',
            'year_label'        => 'nullable|string|max:20',
            'is_footer_gallery' => 'nullable|boolean',
            'sort_order'        => 'nullable|integer',
            'image_file'        => 'nullable|image|max:4096',
        ]);

        $imageUrl = '/module-media/EventManagement/theme/memory-2024-1.jpg';
        if ($request->hasFile('image_file')) {
            $imageUrl = ModuleCmsResolver::storeModuleFile($request->file('image_file'), 'memories', 'EventManagement');
        }

        EmMemory::create([
            'title'             => $validated['title'],
            'year_label'        => $validated['year_label'] ?: '2024',
            'image'             => $imageUrl,
            'is_footer_gallery' => $request->boolean('is_footer_gallery'),
            'sort_order'        => $validated['sort_order'] ?? (EmMemory::max('sort_order') + 1),
            'is_active'         => true,
        ]);

        return back()->with('success', 'Event memory photo added successfully.');
    }

    public function updateMemory(Request $request, int $id)
    {
        $mem = EmMemory::findOrFail($id);

        $validated = $request->validate([
            'title'             => 'required|string|max:160',
            'year_label'        => 'nullable|string|max:20',
            'is_footer_gallery' => 'nullable|boolean',
            'sort_order'        => 'nullable|integer',
            'image_file'        => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('image_file')) {
            $mem->image = ModuleCmsResolver::storeModuleFile($request->file('image_file'), 'memories', 'EventManagement');
        }

        $mem->update([
            'title'             => $validated['title'],
            'year_label'        => $validated['year_label'] ?: $mem->year_label,
            'is_footer_gallery' => $request->boolean('is_footer_gallery'),
            'sort_order'        => $validated['sort_order'] ?? $mem->sort_order,
            'is_active'         => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Event memory photo updated successfully.');
    }

    public function deleteMemory(int $id)
    {
        EmMemory::findOrFail($id)->delete();

        return back()->with('success', 'Event memory photo deleted.');
    }

    // =========================================================================
    // 6. TICKET BOOKINGS / ATTENDEE REGISTRATIONS (em_ticket_bookings)
    // =========================================================================
    public function bookings()
    {
        $this->dataService->ensureSeeded();
        $bookings = EmTicketBooking::latest('id')->get();
        $plans    = EmPricingPlan::orderBy('sort_order')->get();

        return view('eventmanagement::admin.bookings', compact('bookings', 'plans'));
    }

    public function storeBooking(Request $request)
    {
        $validated = $request->validate([
            'attendee_name'  => 'required|string|max:120',
            'email'          => 'required|email|max:150',
            'phone'          => 'nullable|string|max:40',
            'company'        => 'nullable|string|max:120',
            'plan_name'      => 'required|string|max:60',
            'quantity'       => 'required|integer|min:1|max:50',
            'session_choice' => 'nullable|string|max:180',
            'status'         => 'nullable|string|max:40',
        ]);

        $plan = EmPricingPlan::where('name', $validated['plan_name'])->first();
        $unitPrice = $plan ? (float) $plan->price : 69.00;
        $qty = (int) $validated['quantity'];

        EmTicketBooking::create([
            'ticket_code'    => 'EVT-' . date('Y') . '-' . random_int(1000, 9999),
            'attendee_name'  => $validated['attendee_name'],
            'email'          => $validated['email'],
            'phone'          => $validated['phone'] ?? null,
            'company'        => $validated['company'] ?? null,
            'plan_name'      => strtoupper($validated['plan_name']),
            'unit_price'     => $unitPrice,
            'quantity'       => $qty,
            'total_amount'   => $unitPrice * $qty,
            'session_choice' => $validated['session_choice'] ?? 'All Conference Sessions',
            'status'         => $validated['status'] ?? 'Confirmed',
        ]);

        return back()->with('success', 'Attendee ticket booking created successfully.');
    }

    public function updateBooking(Request $request, int $id)
    {
        $booking = EmTicketBooking::findOrFail($id);
        $validated = $request->validate([
            'attendee_name' => 'required|string|max:120',
            'email'         => 'required|email|max:150',
            'phone'         => 'nullable|string|max:40',
            'plan_name'     => 'required|string|max:60',
            'quantity'      => 'required|integer|min:1|max:50',
            'status'        => 'required|string|max:40',
        ]);

        $plan = EmPricingPlan::where('name', $validated['plan_name'])->first();
        $unitPrice = $plan ? (float) $plan->price : (float) $booking->unit_price;
        $qty = (int) $validated['quantity'];

        $booking->update([
            'attendee_name' => $validated['attendee_name'],
            'email'         => $validated['email'],
            'phone'         => $validated['phone'] ?? $booking->phone,
            'plan_name'     => strtoupper($validated['plan_name']),
            'unit_price'    => $unitPrice,
            'quantity'      => $qty,
            'total_amount'  => $unitPrice * $qty,
            'status'        => $validated['status'],
        ]);

        return back()->with('success', 'Ticket booking updated successfully.');
    }

    public function deleteBooking(int $id)
    {
        EmTicketBooking::findOrFail($id)->delete();

        return back()->with('success', 'Ticket booking deleted.');
    }

    // =========================================================================
    // 7. HERO, COUNTDOWN & VENUE SETTINGS (em_settings)
    // =========================================================================
    public function settings()
    {
        $this->dataService->ensureSeeded();
        $s = $this->dataService->getSettingsData();

        return view('eventmanagement::admin.settings', compact('s'));
    }

    public function saveSettings(Request $request)
    {
        $textKeys = [
            'brand_name',
            'topbar_text',
            'topbar_cta_text',
            'topbar_email',
            'topbar_phone',
            'hero_eyebrow',
            'hero_title_line1',
            'hero_title_highlight',
            'hero_title_edition',
            'hero_countdown_days',
            'hero_countdown_hours',
            'hero_countdown_minutes',
            'hero_countdown_seconds',
            'hero_btn_text',
            'hero_badge_text',
            'about_eyebrow',
            'about_title',
            'about_description',
            'about_stat1_text',
            'about_stat2_text',
            'about_btn_text',
            'sponsors_heading',
            'speakers_eyebrow',
            'speakers_heading',
            'schedule_eyebrow',
            'schedule_heading',
            'day1_title',
            'day1_date',
            'day2_title',
            'day2_date',
            'day3_title',
            'day3_date',
            'memories_eyebrow',
            'memories_heading',
            'memories_btn_text',
            'pricing_eyebrow',
            'pricing_heading',
            'venue_eyebrow',
            'venue_heading',
            'venue_email_label',
            'venue_email_value',
            'venue_phone_label',
            'venue_phone_value',
            'venue_schedule_label',
            'venue_schedule_value',
            'banner_btn_text',
            'banner_date_time',
            'banner_location',
            'footer_about',
            'footer_phone',
            'footer_location',
            'footer_email',
            'footer_website',
            'footer_copyright',
        ];

        foreach ($textKeys as $k) {
            if ($request->has($k)) {
                ModuleSetting::setValue($k, $request->input($k));
            }
        }

        if ($request->hasFile('hero_speaker_file')) {
            $url = ModuleCmsResolver::storeModuleFile($request->file('hero_speaker_file'), 'hero', 'EventManagement');
            ModuleSetting::setValue('hero_speaker_image', $url);
        }

        if ($request->hasFile('venue_map_file')) {
            $url = ModuleCmsResolver::storeModuleFile($request->file('venue_map_file'), 'venue', 'EventManagement');
            ModuleSetting::setValue('venue_map_image', $url);
        }

        ActivityLog::record('Eventify theme settings updated', 'Saved in em_settings', 'bi-sliders2', '#ECFCCB', '#4D7C0F');

        return back()->with('success', 'Eventify Hero, Countdown & Venue settings saved successfully!');
    }

    public function resetSettings(ModuleManagerService $manager)
    {
        $manager->resetModuleToDefault('EventManagement');

        return back()->with('success', 'EventManagement module restored 100% to original Eventify default design & content!');
    }
}
