<?php

namespace Modules\GymFitness\App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Support\ModuleCmsResolver;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\GymFitness\App\Models\GymAttendance;
use Modules\GymFitness\App\Models\GymClass;
use Modules\GymFitness\App\Models\GymClassBooking;
use Modules\GymFitness\App\Models\GymEquipment;
use Modules\GymFitness\App\Models\GymFaq;
use Modules\GymFitness\App\Models\GymFitnessPlan;
use Modules\GymFitness\App\Models\GymMember;
use Modules\GymFitness\App\Models\GymPackage;
use Modules\GymFitness\App\Models\GymPayment;
use Modules\GymFitness\App\Models\GymProgram;
use Modules\GymFitness\App\Models\GymProgressLog;
use Modules\GymFitness\App\Models\GymSubscriber;
use Modules\GymFitness\App\Models\GymSubscription;
use Modules\GymFitness\App\Models\GymTestimonial;
use Modules\GymFitness\App\Models\GymTrainer;
use Modules\GymFitness\App\Models\ModuleMedia;
use Modules\GymFitness\App\Models\ModuleMenu;
use Modules\GymFitness\App\Models\ModulePage;
use Modules\GymFitness\App\Models\ModuleSlider;
use Modules\GymFitness\App\Models\ModuleTheme;
use Modules\GymFitness\App\Models\ModuleVideo;
use Modules\GymFitness\App\Services\GymFitnessDataService;

class HomeController extends Controller
{
    public function __construct(
        protected GymFitnessDataService $dataService
    ) {}

    protected function resolveLocale(Request $request): string
    {
        if ($request->filled('lang') && in_array($request->query('lang'), ['en', 'bn'], true)) {
            $loc = $request->query('lang');
            session(['gym_locale_explicit' => $loc]);
            return $loc;
        }

        $t = $this->dataService->getSettingsData();
        $defaultLang = $t['general']['default_language'] ?? 'en';
        $loc = session('gym_locale_explicit', $defaultLang);

        return in_array($loc, ['en', 'bn'], true) ? $loc : 'en';
    }

    public function switchLanguage(string $locale)
    {
        if (in_array($locale, ['en', 'bn'], true)) {
            session(['gym_locale_explicit' => $locale]);
        }

        return back();
    }

    protected function sharedData(Request $request): array
    {
        $this->dataService->ensureSeeded();
        $t = $this->dataService->getSettingsData();
        $locale = $this->resolveLocale($request);
        $tr = $t['translations'][$locale] ?? $t['translations']['en'];

        $headerMenus = ModuleMenu::where('location', 'header')->where('is_active', true)->orderBy('sort_order')->get();
        $activeTheme = ModuleTheme::where('is_active', true)->first();

        return compact('t', 'locale', 'tr', 'headerMenus', 'activeTheme');
    }

    public function index(Request $request = null)
    {
        $request = $request ?? request();
        $shared = $this->sharedData($request);

        $packages     = GymPackage::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        $programs     = GymProgram::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        $trainers     = GymTrainer::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        $classes      = GymClass::with('trainer')->where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        $equipments   = GymEquipment::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        $testimonials = GymTestimonial::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        $faqs         = GymFaq::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        $videos       = ModuleVideo::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        $mediaItems   = ModuleMedia::latest('id')->get();
        $fitnessPlans = GymFitnessPlan::where('is_active', true)->orderBy('id')->get();
        $pages        = ModulePage::where('status', 'Published')->orderBy('id')->get();
        $sliders      = ModuleSlider::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();

        // Build Weekly Schedule Matrix grouped by time_slot and day_of_week
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $timeSlots = $classes->pluck('time_slot')->unique()->values();
        $scheduleMatrix = [];
        foreach ($timeSlots as $slot) {
            $scheduleMatrix[$slot] = [];
            foreach ($daysOfWeek as $day) {
                $scheduleMatrix[$slot][$day] = $classes->first(fn ($c) => $c->time_slot === $slot && strcasecmp($c->day_of_week, $day) === 0);
            }
        }

        return view('gymfitness::frontend.home', array_merge($shared, compact(
            'packages',
            'programs',
            'trainers',
            'classes',
            'daysOfWeek',
            'timeSlots',
            'scheduleMatrix',
            'equipments',
            'testimonials',
            'faqs',
            'videos',
            'mediaItems',
            'fitnessPlans',
            'pages',
            'sliders'
        )));
    }

    /**
     * Complete Frontend Membership Registration Workflow
     */
    public function registerMember(Request $request)
    {
        $validated = $request->validate([
            'name'                    => 'required|string|max:150',
            'email'                   => 'required|email|max:150',
            'phone'                   => 'required|string|max:40',
            'date_of_birth'           => 'nullable|date',
            'gender'                  => 'required|in:Male,Female,Other',
            'blood_group'             => 'nullable|string|max:10',
            'address'                 => 'required|string|max:255',
            'emergency_contact_name'  => 'required|string|max:150',
            'emergency_contact_phone' => 'required|string|max:40',
            'package_id'              => 'required|exists:gym_packages,id',
            'billing_cycle'           => 'required|in:monthly,quarterly,six_month,yearly,custom',
            'payment_method'          => 'required|string|max:80',
            'transaction_reference'   => 'nullable|string|max:100',
            'photo_file'              => 'nullable|image|max:4096',
        ]);

        $pkg = GymPackage::findOrFail($validated['package_id']);

        $cyclePriceMap = [
            'monthly'   => (float) $pkg->monthly_price,
            'quarterly' => (float) $pkg->quarterly_price,
            'six_month' => (float) $pkg->six_month_price,
            'yearly'    => (float) $pkg->yearly_price,
            'custom'    => (float) ($pkg->custom_duration_price ?: $pkg->monthly_price),
        ];

        $cycleMonthsMap = [
            'monthly'   => 1,
            'quarterly' => 3,
            'six_month' => 6,
            'yearly'    => 12,
            'custom'    => 2,
        ];

        $planPrice = $cyclePriceMap[$validated['billing_cycle']] ?? (float) $pkg->monthly_price;
        $regFee    = (float) ($pkg->registration_fee ?? 0);
        $totalAmt  = $planPrice + $regFee;
        $months    = $cycleMonthsMap[$validated['billing_cycle']] ?? 1;

        $photoUrl = '/module-media/GymFitness/theme/trainer-1.jpg';
        if ($request->hasFile('photo_file')) {
            $photoUrl = ModuleCmsResolver::storeModuleFile($request->file('photo_file'), 'members', 'GymFitness');
        }

        $member = DB::transaction(function () use ($validated, $pkg, $totalAmt, $regFee, $months, $photoUrl) {
            $nextNumber = GymMember::count() + 1;
            $memberCode = 'FP-' . date('Y') . '-' . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
            if (GymMember::where('member_code', $memberCode)->exists()) {
                $memberCode = 'FP-' . date('Y') . '-' . strtoupper(Str::random(4));
            }

            $defaultTrainer = GymTrainer::where('is_active', true)->first();
            $defaultWorkout = GymFitnessPlan::where('plan_type', 'Workout')->where('is_active', true)->first();
            $defaultDiet    = GymFitnessPlan::where('plan_type', 'Diet')->where('is_active', true)->first();

            $isCashPending = stripos($validated['payment_method'], 'Cash') !== false && empty($validated['transaction_reference']);
            $payStatus     = $isCashPending ? 'Pending' : 'Paid';
            $paidAmt       = $isCashPending ? 0 : $totalAmt;
            $dueAmt        = $isCashPending ? $totalAmt : 0;

            $joinDate   = Carbon::today();
            $expiryDate = Carbon::today()->addMonths($months);

            $member = GymMember::create([
                'member_code'             => $memberCode,
                'qr_token'                => 'QR-' . $memberCode . '-' . strtoupper(Str::random(4)),
                'name'                    => $validated['name'],
                'email'                   => $validated['email'],
                'phone'                   => $validated['phone'],
                'password'                => Hash::make($validated['phone']),
                'photo'                   => $photoUrl,
                'date_of_birth'           => $validated['date_of_birth'] ?? null,
                'gender'                  => $validated['gender'],
                'blood_group'             => $validated['blood_group'] ?? 'O+',
                'address'                 => $validated['address'],
                'emergency_contact_name'  => $validated['emergency_contact_name'],
                'emergency_contact_phone' => $validated['emergency_contact_phone'],
                'package_id'              => $pkg->id,
                'trainer_id'              => $pkg->has_personal_trainer ? $defaultTrainer?->id : null,
                'workout_plan_id'         => $defaultWorkout?->id,
                'diet_plan_id'            => $defaultDiet?->id,
                'billing_cycle'           => $validated['billing_cycle'],
                'join_date'               => $joinDate->toDateString(),
                'expiry_date'             => $expiryDate->toDateString(),
                'membership_status'       => 'Active',
                'payment_status'          => $payStatus,
                'due_amount'              => $dueAmt,
            ]);

            GymSubscription::create([
                'member_id'        => $member->id,
                'package_id'       => $pkg->id,
                'plan_name'        => $pkg->name,
                'billing_cycle'    => $validated['billing_cycle'],
                'action_type'      => 'Registration',
                'amount'           => $totalAmt,
                'registration_fee' => $regFee,
                'start_date'       => $joinDate->toDateString(),
                'end_date'         => $expiryDate->toDateString(),
                'status'           => 'Active',
            ]);

            $invoiceNo = 'INV-GYM-' . strtoupper(Str::random(6));
            $payment = GymPayment::create([
                'invoice_no'            => $invoiceNo,
                'member_id'             => $member->id,
                'package_id'            => $pkg->id,
                'payment_type'          => 'Membership Fee',
                'amount'                => $totalAmt,
                'discount_amount'       => 0,
                'paid_amount'           => $paidAmt,
                'due_amount'            => $dueAmt,
                'payment_method'        => $validated['payment_method'],
                'transaction_reference' => $validated['transaction_reference'] ?: ('TXN-' . strtoupper(Str::random(8))),
                'payment_status'        => $payStatus,
                'paid_at'               => $payStatus === 'Paid' ? now() : null,
                'notes'                 => "Online Registration ({$pkg->name} - {$validated['billing_cycle']})",
            ]);

            $this->dataService->syncWithCoreTransactions($payment, $member->phone);

            ActivityLog::record(
                'New Gym Member Registered',
                "{$member->name} ({$member->member_code}) - {$pkg->name} [gym_members]",
                'bi-person-check-fill',
                '#DCFCE7',
                '#10B981'
            );

            return $member;
        });

        session(['gym_portal_member_id' => $member->id]);

        return redirect()->route('gymfitness.portal')->with(
            'gym_success',
            "Welcome to FitPulse Gym, {$member->name}! Your Member ID is {$member->member_code}. Your Member Portal & QR Check-in Pass are now active!"
        );
    }

    /**
     * Dedicated Member Portal
     */
    public function portal(Request $request)
    {
        $shared = $this->sharedData($request);

        // Allow quick switching between demo/registered members or session member
        if ($request->filled('member_id')) {
            $switchMember = GymMember::find($request->query('member_id'));
            if ($switchMember) {
                session(['gym_portal_member_id' => $switchMember->id]);
            }
        }

        $memberId = session('gym_portal_member_id');
        $member = $memberId
            ? GymMember::with(['package', 'trainer', 'workoutPlan', 'dietPlan'])->find($memberId)
            : GymMember::with(['package', 'trainer', 'workoutPlan', 'dietPlan'])->first();

        if ($member) {
            session(['gym_portal_member_id' => $member->id]);
        }

        $allMembers    = GymMember::orderByDesc('id')->take(10)->get();
        $packages      = GymPackage::where('is_active', true)->orderBy('sort_order')->get();
        $classes       = GymClass::with('trainer')->where('is_active', true)->where('category', '!=', 'Rest')->orderBy('sort_order')->get();
        $subscriptions = $member ? $member->subscriptions()->latest('id')->get() : collect();
        $payments      = $member ? $member->payments()->latest('id')->get() : collect();
        $attendances   = $member ? $member->attendances()->latest('attendance_date')->latest('id')->take(15)->get() : collect();
        $bookings      = $member ? $member->classBookings()->with('gymClass.trainer')->latest('id')->get() : collect();
        $progressLogs  = $member ? $member->progressLogs()->orderBy('recorded_date')->get() : collect();

        return view('gymfitness::frontend.portal', array_merge($shared, compact(
            'member',
            'allMembers',
            'packages',
            'classes',
            'subscriptions',
            'payments',
            'attendances',
            'bookings',
            'progressLogs'
        )));
    }

    public function portalLogin(Request $request)
    {
        $identifier = trim((string) $request->input('identifier', ''));
        $member = GymMember::where('member_code', $identifier)
            ->orWhere('email', $identifier)
            ->orWhere('phone', $identifier)
            ->first();

        if (!$member) {
            return back()->withErrors(['identifier' => 'No member found with that Member ID, Email, or Phone.']);
        }

        session(['gym_portal_member_id' => $member->id]);

        return redirect()->route('gymfitness.portal')->with('gym_success', "Logged into Member Portal as {$member->name} ({$member->member_code}).");
    }

    public function portalLogout()
    {
        session()->forget('gym_portal_member_id');
        return redirect()->route('gymfitness.home')->with('gym_success', 'Logged out of Member Portal.');
    }

    public function portalRenew(Request $request)
    {
        $validated = $request->validate([
            'member_id'      => 'required|exists:gym_members,id',
            'package_id'     => 'required|exists:gym_packages,id',
            'billing_cycle'  => 'required|in:monthly,quarterly,six_month,yearly',
            'payment_method' => 'required|string|max:80',
        ]);

        $member = GymMember::findOrFail($validated['member_id']);
        $pkg    = GymPackage::findOrFail($validated['package_id']);

        $cyclePriceMap = [
            'monthly'   => (float) $pkg->monthly_price,
            'quarterly' => (float) $pkg->quarterly_price,
            'six_month' => (float) $pkg->six_month_price,
            'yearly'    => (float) $pkg->yearly_price,
        ];
        $cycleMonthsMap = [
            'monthly'   => 1,
            'quarterly' => 3,
            'six_month' => 6,
            'yearly'    => 12,
        ];

        $amount = $cyclePriceMap[$validated['billing_cycle']] ?? (float) $pkg->monthly_price;
        $months = $cycleMonthsMap[$validated['billing_cycle']] ?? 1;

        $baseDate = ($member->expiry_date && $member->expiry_date->isFuture())
            ? $member->expiry_date
            : Carbon::today();
        $newExpiry = $baseDate->copy()->addMonths($months);

        $actionType = ($member->package_id !== $pkg->id) ? 'Upgrade' : 'Renewal';

        $member->update([
            'package_id'        => $pkg->id,
            'billing_cycle'     => $validated['billing_cycle'],
            'expiry_date'       => $newExpiry->toDateString(),
            'membership_status' => 'Active',
            'payment_status'    => 'Paid',
            'due_amount'        => 0,
        ]);

        GymSubscription::create([
            'member_id'        => $member->id,
            'package_id'       => $pkg->id,
            'plan_name'        => $pkg->name,
            'billing_cycle'    => $validated['billing_cycle'],
            'action_type'      => $actionType,
            'amount'           => $amount,
            'registration_fee' => 0,
            'start_date'       => Carbon::today()->toDateString(),
            'end_date'         => $newExpiry->toDateString(),
            'status'           => 'Active',
        ]);

        $payment = GymPayment::create([
            'invoice_no'            => 'INV-GYM-' . strtoupper(Str::random(6)),
            'member_id'             => $member->id,
            'package_id'            => $pkg->id,
            'payment_type'          => 'Renewal Payment',
            'amount'                => $amount,
            'discount_amount'       => 0,
            'paid_amount'           => $amount,
            'due_amount'            => 0,
            'payment_method'        => $validated['payment_method'],
            'transaction_reference' => 'TXN-RNW-' . strtoupper(Str::random(6)),
            'payment_status'        => 'Paid',
            'paid_at'               => now(),
            'notes'                 => "Member Portal {$actionType} ({$pkg->name})",
        ]);

        $this->dataService->syncWithCoreTransactions($payment, $member->phone);

        return back()->with('gym_success', "Membership {$actionType} completed! Your {$pkg->name} is now active until {$newExpiry->format('M d, Y')}.");
    }

    public function portalBookClass(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:gym_members,id',
            'class_id'  => 'required|exists:gym_classes,id',
        ]);

        $gymClass = GymClass::findOrFail($validated['class_id']);
        if ($gymClass->booked_count >= $gymClass->max_participants) {
            return back()->withErrors(['class' => 'This class session is already at maximum participant capacity.']);
        }

        GymClassBooking::create([
            'class_id'     => $gymClass->id,
            'member_id'    => $validated['member_id'],
            'booking_date' => Carbon::today()->toDateString(),
            'status'       => 'Booked',
            'notes'        => 'Booked from Member Portal',
        ]);

        $gymClass->increment('booked_count');

        return back()->with('gym_success', "Booked '{$gymClass->title}' ({$gymClass->day_of_week} {$gymClass->time_slot}) successfully!");
    }

    public function portalCancelClass(int $id)
    {
        $booking = GymClassBooking::findOrFail($id);
        if ($booking->status !== 'Cancelled') {
            $booking->update(['status' => 'Cancelled']);
            if ($booking->gymClass && $booking->gymClass->booked_count > 0) {
                $booking->gymClass->decrement('booked_count');
            }
        }

        return back()->with('gym_success', 'Class booking cancelled.');
    }

    public function portalCheckIn(Request $request)
    {
        $member = GymMember::findOrFail($request->input('member_id'));

        GymAttendance::create([
            'member_id'       => $member->id,
            'attendance_date' => Carbon::today()->toDateString(),
            'check_in_time'   => now()->format('h:i A'),
            'check_in_method' => 'QR Code',
            'status'          => 'Present',
            'notes'           => 'Digital QR Check-In via Member Portal',
        ]);

        return back()->with('gym_success', "QR Check-In recorded for {$member->name} ({$member->member_code}) at " . now()->format('h:i A') . '!');
    }

    public function portalLogProgress(Request $request)
    {
        $validated = $request->validate([
            'member_id'    => 'required|exists:gym_members,id',
            'weight_kg'    => 'required|numeric|min:25|max:250',
            'height_cm'    => 'nullable|numeric|min:100|max:240',
            'body_fat_pct' => 'nullable|numeric|min:3|max:60',
            'chest_cm'     => 'nullable|numeric|min:40|max:200',
            'waist_cm'     => 'nullable|numeric|min:40|max:200',
            'arms_cm'      => 'nullable|numeric|min:15|max:80',
            'notes'        => 'nullable|string|max:255',
        ]);

        $heightM = !empty($validated['height_cm']) ? ($validated['height_cm'] / 100) : 1.75;
        $bmi = round($validated['weight_kg'] / ($heightM * $heightM), 1);

        GymProgressLog::create([
            'member_id'     => $validated['member_id'],
            'recorded_date' => Carbon::today()->toDateString(),
            'weight_kg'     => $validated['weight_kg'],
            'height_cm'     => $validated['height_cm'] ?? 175,
            'bmi'           => $bmi,
            'body_fat_pct'  => $validated['body_fat_pct'] ?? null,
            'chest_cm'      => $validated['chest_cm'] ?? null,
            'waist_cm'      => $validated['waist_cm'] ?? null,
            'arms_cm'       => $validated['arms_cm'] ?? null,
            'notes'         => $validated['notes'] ?? 'Logged from Member Portal',
        ]);

        return back()->with('gym_success', "Body measurement & weight progress logged (Weight: {$validated['weight_kg']}kg, BMI: {$bmi})!");
    }

    public function submitNewsletter(Request $request)
    {
        $validated = $request->validate([
            'newsletter_email' => 'required|email|max:150',
        ]);

        GymSubscriber::updateOrCreate(
            ['email' => $validated['newsletter_email']],
            ['status' => 'Subscribed']
        );

        return back()->with('gym_success', 'Thank you for subscribing to FitPulse Gym updates & special offers!');
    }

    public function page(string $page)
    {
        if (str_starts_with($page, 'lang-')) {
            $loc = substr($page, 5, 2);
            return $this->switchLanguage($loc);
        }
        if ($page === 'portal') {
            return $this->portal(request());
        }
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
        $xml .= '  <url><loc>' . url('/gym-fitness') . '</loc><priority>1.0</priority></url>' . "\n";
        $xml .= '  <url><loc>' . route('gymfitness.portal') . '</loc><priority>0.9</priority></url>' . "\n";
        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function asset(string $type, string $file)
    {
        $path = module_path('GymFitness', "Public/assets/{$type}/" . basename($file));
        abort_unless(is_file($path), 404);

        $mime = $type === 'css' ? 'text/css' : 'application/javascript';

        return response()->file($path, [
            'Content-Type' => $mime . '; charset=UTF-8',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
