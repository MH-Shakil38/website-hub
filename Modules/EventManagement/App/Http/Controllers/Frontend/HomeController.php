<?php

namespace Modules\EventManagement\App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Modules\EventManagement\App\Models\EmMemory;
use Modules\EventManagement\App\Models\EmPricingPlan;
use Modules\EventManagement\App\Models\EmSchedule;
use Modules\EventManagement\App\Models\EmSpeaker;
use Modules\EventManagement\App\Models\EmSponsor;
use Modules\EventManagement\App\Models\EmTicketBooking;
use Modules\EventManagement\App\Models\ModuleMedia;
use Modules\EventManagement\App\Models\ModuleMenu;
use Modules\EventManagement\App\Models\ModulePage;
use Modules\EventManagement\App\Models\ModuleSlider;
use Modules\EventManagement\App\Models\ModuleTheme;
use Modules\EventManagement\App\Models\ModuleVideo;
use Modules\EventManagement\App\Services\EventManagementDataService;

class HomeController extends Controller
{
    public function __construct(
        protected EventManagementDataService $dataService
    ) {}

    public function index(?Request $request = null)
    {
        $this->dataService->ensureSeeded();

        $s              = $this->dataService->getSettingsData();
        $headerMenus    = ModuleMenu::where('location', 'header')->where('is_active', true)->orderBy('sort_order')->get();
        $activeTheme    = ModuleTheme::where('is_active', true)->first();
        $sponsors       = EmSponsor::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        $speakers       = EmSpeaker::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        $schedules      = EmSchedule::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        $pricingPlans   = EmPricingPlan::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        $recentMemories = EmMemory::where('is_active', true)->where('is_footer_gallery', false)->orderBy('sort_order')->orderBy('id')->get();
        $footerGallery  = EmMemory::where('is_active', true)->where('is_footer_gallery', true)->orderBy('sort_order')->orderBy('id')->take(6)->get();
        $pages          = ModulePage::where('status', 'Published')->orderBy('id')->get();
        $videos         = ModuleVideo::where('is_active', true)->orderBy('sort_order')->get();
        $mediaItems     = ModuleMedia::latest('id')->get();
        $sliders        = ModuleSlider::where('is_active', true)->orderBy('sort_order')->get();

        return view('eventmanagement::frontend.home', compact(
            's',
            'headerMenus',
            'activeTheme',
            'sponsors',
            'speakers',
            'schedules',
            'pricingPlans',
            'recentMemories',
            'footerGallery',
            'pages',
            'videos',
            'mediaItems',
            'sliders'
        ));
    }

    public function submitBooking(Request $request)
    {
        $validated = $request->validate([
            'attendee_name'  => 'required|string|max:120',
            'email'          => 'required|email|max:150',
            'phone'          => 'nullable|string|max:40',
            'company'        => 'nullable|string|max:120',
            'plan_name'      => 'required|string|max:60',
            'quantity'       => 'required|integer|min:1|max:20',
            'session_choice' => 'nullable|string|max:180',
        ]);

        $plan = EmPricingPlan::where('name', $validated['plan_name'])->first();
        $unitPrice = $plan ? (float) $plan->price : 69.00;
        $qty = (int) $validated['quantity'];
        $total = $unitPrice * $qty;
        $ticketCode = 'EVT-' . date('Y') . '-' . random_int(1000, 9999);

        $booking = EmTicketBooking::create([
            'ticket_code'    => $ticketCode,
            'attendee_name'  => $validated['attendee_name'],
            'email'          => $validated['email'],
            'phone'          => $validated['phone'] ?? null,
            'company'        => $validated['company'] ?? null,
            'plan_name'      => strtoupper($validated['plan_name']),
            'unit_price'     => $unitPrice,
            'quantity'       => $qty,
            'total_amount'   => $total,
            'session_choice' => $validated['session_choice'] ?? 'All Conference Sessions',
            'status'         => 'Confirmed',
        ]);

        ActivityLog::record(
            'New Eventify Ticket Booked',
            "{$booking->attendee_name} ({$booking->plan_name} x{$booking->quantity} — \${$booking->total_amount}) [{$booking->ticket_code}]",
            'bi-ticket-perforated-fill',
            '#ECFCCB',
            '#4D7C0F'
        );

        return back()->with(
            'em_booking_success',
            "Your seat is reserved, {$booking->attendee_name}! Pass ID: {$booking->ticket_code} ({$booking->plan_name} Ticket x{$booking->quantity} — Total \${$booking->total_amount})."
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
        $xml .= '  <url><loc>' . url('/event-management') . '</loc><priority>1.0</priority></url>' . "\n";
        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function asset(string $type, string $file)
    {
        $path = module_path('EventManagement', "Public/assets/{$type}/" . basename($file));
        abort_unless(is_file($path), 404);

        $mime = $type === 'css' ? 'text/css' : 'application/javascript';

        return response()->file($path, [
            'Content-Type'  => $mime . '; charset=UTF-8',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
