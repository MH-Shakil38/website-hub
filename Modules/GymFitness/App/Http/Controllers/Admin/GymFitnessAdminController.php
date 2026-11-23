<?php

namespace Modules\GymFitness\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\ModuleManagerService;
use App\Support\ModuleCmsResolver;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
use Modules\GymFitness\App\Models\GymSubscription;
use Modules\GymFitness\App\Models\GymTestimonial;
use Modules\GymFitness\App\Models\GymTrainer;
use Modules\GymFitness\App\Models\ModuleSetting;
use Modules\GymFitness\App\Services\GymFitnessDataService;

class GymFitnessAdminController extends Controller
{
    public function __construct(
        protected GymFitnessDataService $dataService,
        protected ModuleManagerService $managerService
    ) {}

    // =========================================================================
    // A. GYM MANAGEMENT DASHBOARD
    // =========================================================================
    public function dashboard()
    {
        $this->dataService->ensureSeeded();

        $totalMembers       = GymMember::count();
        $activeMembers      = GymMember::where('membership_status', 'Active')->count();
        $expiredMembers     = GymMember::where('membership_status', 'Expired')->count();
        $todayRegistrations = GymMember::whereDate('created_at', Carbon::today())->count();

        $monthlyRevenue  = (float) GymPayment::where('payment_status', 'Paid')
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('paid_amount');
        $totalRevenue    = (float) GymPayment::where('payment_status', 'Paid')->sum('paid_amount');
        $pendingPayments = (float) GymPayment::whereIn('payment_status', ['Pending', 'Due'])->sum('due_amount');

        $todayAttendance = GymAttendance::whereDate('attendance_date', Carbon::today())->count();
        $upcomingClasses = GymClass::with('trainer')->where('is_active', true)->where('category', '!=', 'Rest')->take(6)->get();

        $recentMembers  = GymMember::with(['package', 'trainer'])->latest('id')->take(6)->get();
        $recentPayments = GymPayment::with(['member', 'package'])->latest('id')->take(6)->get();
        $packages       = GymPackage::withCount('members')->orderBy('sort_order')->get();
        $trainersCount  = GymTrainer::where('is_active', true)->count();

        return view('gymfitness::admin.dashboard', compact(
            'totalMembers',
            'activeMembers',
            'expiredMembers',
            'todayRegistrations',
            'monthlyRevenue',
            'totalRevenue',
            'pendingPayments',
            'todayAttendance',
            'upcomingClasses',
            'recentMembers',
            'recentPayments',
            'packages',
            'trainersCount'
        ));
    }

    // =========================================================================
    // B. MEMBER MANAGEMENT & MEMBERSHIP LIFECYCLE
    // =========================================================================
    public function members(Request $request)
    {
        $this->dataService->ensureSeeded();

        $search    = trim((string) $request->query('search', ''));
        $status    = $request->query('status');
        $packageId = $request->query('package_id');

        $members = GymMember::with(['package', 'trainer', 'workoutPlan', 'dietPlan', 'subscriptions'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('member_code', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($status, fn ($q) => $q->where('membership_status', $status))
            ->when($packageId, fn ($q) => $q->where('package_id', $packageId))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $packages     = GymPackage::orderBy('sort_order')->get();
        $trainers     = GymTrainer::where('is_active', true)->orderBy('name')->get();
        $workoutPlans = GymFitnessPlan::where('plan_type', 'Workout')->get();
        $dietPlans    = GymFitnessPlan::where('plan_type', 'Diet')->get();

        return view('gymfitness::admin.members', compact(
            'members',
            'packages',
            'trainers',
            'workoutPlans',
            'dietPlans',
            'search',
            'status',
            'packageId'
        ));
    }

    public function storeMember(Request $request)
    {
        $validated = $request->validate([
            'name'                    => 'required|string|max:150',
            'email'                   => 'required|email|max:150',
            'phone'                   => 'required|string|max:40',
            'date_of_birth'           => 'nullable|date',
            'gender'                  => 'required|in:Male,Female,Other',
            'blood_group'             => 'nullable|string|max:10',
            'address'                 => 'nullable|string|max:255',
            'emergency_contact_name'  => 'nullable|string|max:150',
            'emergency_contact_phone' => 'nullable|string|max:40',
            'package_id'              => 'required|exists:gym_packages,id',
            'trainer_id'              => 'nullable|exists:gym_trainers,id',
            'workout_plan_id'         => 'nullable|exists:gym_fitness_plans,id',
            'diet_plan_id'            => 'nullable|exists:gym_fitness_plans,id',
            'billing_cycle'           => 'required|in:monthly,quarterly,six_month,yearly,custom',
            'membership_status'       => 'required|in:Active,Expired,Suspended,Frozen,Pending',
            'payment_status'          => 'required|in:Paid,Partial,Due,Pending',
            'payment_method'          => 'nullable|string|max:60',
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
        $cycleMonthsMap = ['monthly' => 1, 'quarterly' => 3, 'six_month' => 6, 'yearly' => 12, 'custom' => 2];

        $amount = $cyclePriceMap[$validated['billing_cycle']] ?? (float) $pkg->monthly_price;
        $months = $cycleMonthsMap[$validated['billing_cycle']] ?? 1;

        $photo = '/module-media/GymFitness/theme/trainer-1.jpg';
        if ($request->hasFile('photo_file')) {
            $photo = ModuleCmsResolver::storeModuleFile($request->file('photo_file'), 'members', 'GymFitness');
        }

        $nextNum = GymMember::count() + 1;
        $code = 'FP-' . date('Y') . '-' . str_pad((string) $nextNum, 4, '0', STR_PAD_LEFT);
        if (GymMember::where('member_code', $code)->exists()) {
            $code = 'FP-' . date('Y') . '-' . strtoupper(Str::random(4));
        }

        $joinDate   = Carbon::today();
        $expiryDate = Carbon::today()->addMonths($months);
        $dueAmount  = $validated['payment_status'] === 'Paid' ? 0 : $amount;

        $member = GymMember::create([
            'member_code'             => $code,
            'qr_token'                => 'QR-' . $code,
            'name'                    => $validated['name'],
            'email'                   => $validated['email'],
            'phone'                   => $validated['phone'],
            'password'                => Hash::make($validated['phone']),
            'photo'                   => $photo,
            'date_of_birth'           => $validated['date_of_birth'] ?? null,
            'gender'                  => $validated['gender'],
            'blood_group'             => $validated['blood_group'] ?? 'O+',
            'address'                 => $validated['address'] ?? 'Dhaka, Bangladesh',
            'emergency_contact_name'  => $validated['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
            'package_id'              => $pkg->id,
            'trainer_id'              => $validated['trainer_id'] ?? null,
            'workout_plan_id'         => $validated['workout_plan_id'] ?? null,
            'diet_plan_id'            => $validated['diet_plan_id'] ?? null,
            'billing_cycle'           => $validated['billing_cycle'],
            'join_date'               => $joinDate->toDateString(),
            'expiry_date'             => $expiryDate->toDateString(),
            'membership_status'       => $validated['membership_status'],
            'payment_status'          => $validated['payment_status'],
            'due_amount'              => $dueAmount,
        ]);

        GymSubscription::create([
            'member_id'        => $member->id,
            'package_id'       => $pkg->id,
            'plan_name'        => $pkg->name,
            'billing_cycle'    => $validated['billing_cycle'],
            'action_type'      => 'Registration',
            'amount'           => $amount,
            'registration_fee' => (float) $pkg->registration_fee,
            'start_date'       => $joinDate->toDateString(),
            'end_date'         => $expiryDate->toDateString(),
            'status'           => $validated['membership_status'],
        ]);

        $payment = GymPayment::create([
            'invoice_no'            => 'INV-GYM-' . strtoupper(Str::random(6)),
            'member_id'             => $member->id,
            'package_id'            => $pkg->id,
            'payment_type'          => 'Membership Fee',
            'amount'                => $amount,
            'discount_amount'       => 0,
            'paid_amount'           => $validated['payment_status'] === 'Paid' ? $amount : 0,
            'due_amount'            => $dueAmount,
            'payment_method'        => $validated['payment_method'] ?? 'Cash at Desk',
            'transaction_reference' => 'TXN-' . strtoupper(Str::random(6)),
            'payment_status'        => $validated['payment_status'] === 'Paid' ? 'Paid' : 'Pending',
            'paid_at'               => $validated['payment_status'] === 'Paid' ? now() : null,
            'notes'                 => 'Registered via Gym Admin Panel',
        ]);

        $this->dataService->syncWithCoreTransactions($payment, $member->phone);

        ActivityLog::record('Gym Member Added', "{$member->name} ({$member->member_code}) [gym_members]", 'bi-person-plus-fill', '#DCFCE7', '#10B981');

        return back()->with('success', "Member '{$member->name}' ({$member->member_code}) registered in gym_members!");
    }

    public function updateMember(Request $request, int $id)
    {
        $member = GymMember::findOrFail($id);
        $validated = $request->validate([
            'name'                    => 'required|string|max:150',
            'email'                   => 'required|email|max:150',
            'phone'                   => 'required|string|max:40',
            'date_of_birth'           => 'nullable|date',
            'gender'                  => 'required|in:Male,Female,Other',
            'blood_group'             => 'nullable|string|max:10',
            'address'                 => 'nullable|string|max:255',
            'emergency_contact_name'  => 'nullable|string|max:150',
            'emergency_contact_phone' => 'nullable|string|max:40',
            'package_id'              => 'required|exists:gym_packages,id',
            'trainer_id'              => 'nullable|exists:gym_trainers,id',
            'workout_plan_id'         => 'nullable|exists:gym_fitness_plans,id',
            'diet_plan_id'            => 'nullable|exists:gym_fitness_plans,id',
            'billing_cycle'           => 'required|in:monthly,quarterly,six_month,yearly,custom',
            'expiry_date'             => 'nullable|date',
            'membership_status'       => 'required|in:Active,Expired,Suspended,Frozen,Pending',
            'payment_status'          => 'required|in:Paid,Partial,Due,Pending',
            'due_amount'              => 'nullable|numeric|min:0',
            'photo_file'              => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('photo_file')) {
            $validated['photo'] = ModuleCmsResolver::storeModuleFile($request->file('photo_file'), 'members', 'GymFitness');
        }
        unset($validated['photo_file']);

        $member->update($validated);

        return back()->with('success', "Member '{$member->name}' updated in gym_members!");
    }

    /**
     * Handle Renew, Upgrade, Downgrade, Freeze, Suspend, Activate actions
     */
    public function memberLifecycleAction(Request $request, int $id)
    {
        $member = GymMember::findOrFail($id);
        $action = $request->input('action_type', 'Renew'); // Renew, Upgrade, Downgrade, Freeze, Suspend, Activate

        if (in_array($action, ['Freeze', 'Suspend', 'Activate'], true)) {
            $newStatus = $action === 'Freeze' ? 'Frozen' : ($action === 'Suspend' ? 'Suspended' : 'Active');
            $member->update(['membership_status' => $newStatus]);

            GymSubscription::create([
                'member_id'        => $member->id,
                'package_id'       => $member->package_id,
                'plan_name'        => $member->package?->name ?? 'Plan',
                'billing_cycle'    => $member->billing_cycle,
                'action_type'      => $action,
                'amount'           => 0,
                'registration_fee' => 0,
                'start_date'       => Carbon::today()->toDateString(),
                'end_date'         => $member->expiry_date?->toDateString(),
                'status'           => $newStatus,
            ]);

            return back()->with('success', "Member '{$member->name}' status updated to {$newStatus}!");
        }

        // Renew / Upgrade / Downgrade
        $pkgId = $request->input('package_id', $member->package_id);
        $cycle = $request->input('billing_cycle', $member->billing_cycle ?: 'monthly');
        $pkg   = GymPackage::findOrFail($pkgId);

        $cyclePriceMap = [
            'monthly'   => (float) $pkg->monthly_price,
            'quarterly' => (float) $pkg->quarterly_price,
            'six_month' => (float) $pkg->six_month_price,
            'yearly'    => (float) $pkg->yearly_price,
            'custom'    => (float) ($pkg->custom_duration_price ?: $pkg->monthly_price),
        ];
        $cycleMonthsMap = ['monthly' => 1, 'quarterly' => 3, 'six_month' => 6, 'yearly' => 12, 'custom' => 2];

        $amount = $cyclePriceMap[$cycle] ?? (float) $pkg->monthly_price;
        $months = $cycleMonthsMap[$cycle] ?? 1;

        $baseDate  = ($member->expiry_date && $member->expiry_date->isFuture()) ? $member->expiry_date : Carbon::today();
        $newExpiry = $baseDate->copy()->addMonths($months);

        $member->update([
            'package_id'        => $pkg->id,
            'billing_cycle'     => $cycle,
            'expiry_date'       => $newExpiry->toDateString(),
            'membership_status' => 'Active',
            'payment_status'    => 'Paid',
            'due_amount'        => 0,
        ]);

        GymSubscription::create([
            'member_id'        => $member->id,
            'package_id'       => $pkg->id,
            'plan_name'        => $pkg->name,
            'billing_cycle'    => $cycle,
            'action_type'      => $action,
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
            'payment_method'        => 'Cash at Desk',
            'transaction_reference' => 'TXN-' . strtoupper(Str::random(6)),
            'payment_status'        => 'Paid',
            'paid_at'               => now(),
            'notes'                 => "Admin {$action} ({$pkg->name} - {$cycle})",
        ]);

        $this->dataService->syncWithCoreTransactions($payment, $member->phone);

        return back()->with('success', "Membership {$action} completed for '{$member->name}' until {$newExpiry->format('M d, Y')}!");
    }

    public function deleteMember(int $id)
    {
        GymMember::findOrFail($id)->delete();
        return back()->with('success', 'Member removed from gym_members.');
    }

    // =========================================================================
    // C. MEMBERSHIP PACKAGES CRUD (gym_packages)
    // =========================================================================
    public function packages()
    {
        $this->dataService->ensureSeeded();
        $packages = GymPackage::withCount('members')->orderBy('sort_order')->orderBy('id')->get();

        return view('gymfitness::admin.packages', compact('packages'));
    }

    public function storePackage(Request $request)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:120',
            'name_bn'               => 'nullable|string|max:120',
            'subtitle'              => 'nullable|string|max:180',
            'subtitle_bn'           => 'nullable|string|max:180',
            'description'           => 'nullable|string',
            'description_bn'        => 'nullable|string',
            'currency'              => 'nullable|string|max:15',
            'monthly_price'         => 'required|numeric|min:0',
            'quarterly_price'       => 'required|numeric|min:0',
            'six_month_price'       => 'required|numeric|min:0',
            'yearly_price'          => 'required|numeric|min:0',
            'custom_duration_label' => 'nullable|string|max:80',
            'custom_duration_price' => 'nullable|numeric|min:0',
            'registration_fee'      => 'nullable|numeric|min:0',
            'features_raw'          => 'nullable|string|max:1000',
            'features_bn_raw'       => 'nullable|string|max:1000',
            'badge_text'            => 'nullable|string|max:60',
            'badge_text_bn'         => 'nullable|string|max:60',
            'button_text'           => 'nullable|string|max:60',
            'button_text_bn'        => 'nullable|string|max:60',
            'sort_order'            => 'nullable|integer',
        ]);

        $features   = array_values(array_filter(array_map('trim', explode(',', $validated['features_raw'] ?? 'Gym Access, Locker Access'))));
        $featuresBn = array_values(array_filter(array_map('trim', explode(',', $validated['features_bn_raw'] ?? ''))));

        $pkg = GymPackage::create([
            'name'                  => $validated['name'],
            'name_bn'               => $validated['name_bn'] ?: $validated['name'],
            'slug'                  => Str::slug($validated['name']) . '-' . Str::lower(Str::random(3)),
            'subtitle'              => $validated['subtitle'],
            'subtitle_bn'           => $validated['subtitle_bn'],
            'description'           => $validated['description'],
            'description_bn'        => $validated['description_bn'],
            'currency'              => $validated['currency'] ?: '$',
            'monthly_price'         => $validated['monthly_price'],
            'quarterly_price'       => $validated['quarterly_price'],
            'six_month_price'       => $validated['six_month_price'],
            'yearly_price'          => $validated['yearly_price'],
            'custom_duration_label' => $validated['custom_duration_label'],
            'custom_duration_price' => $validated['custom_duration_price'],
            'registration_fee'      => $validated['registration_fee'] ?? 0,
            'features'              => $features,
            'features_bn'           => !empty($featuresBn) ? $featuresBn : $features,
            'has_personal_trainer'  => $request->boolean('has_personal_trainer'),
            'has_group_classes'     => $request->boolean('has_group_classes'),
            'has_locker'            => $request->boolean('has_locker', true),
            'has_nutrition'         => $request->boolean('has_nutrition'),
            'badge_text'            => $validated['badge_text'],
            'badge_text_bn'         => $validated['badge_text_bn'],
            'button_text'           => $validated['button_text'] ?: 'Join Now',
            'button_text_bn'        => $validated['button_text_bn'] ?: 'জয়েন করুন',
            'is_featured'           => $request->boolean('is_featured'),
            'is_active'             => $request->boolean('is_active', true),
            'sort_order'            => $validated['sort_order'] ?? (GymPackage::max('sort_order') + 1),
        ]);

        return back()->with('success', "Membership Package '{$pkg->name}' saved in gym_packages!");
    }

    public function updatePackage(Request $request, int $id)
    {
        $pkg = GymPackage::findOrFail($id);
        $validated = $request->validate([
            'name'                  => 'required|string|max:120',
            'name_bn'               => 'nullable|string|max:120',
            'subtitle'              => 'nullable|string|max:180',
            'subtitle_bn'           => 'nullable|string|max:180',
            'description'           => 'nullable|string',
            'description_bn'        => 'nullable|string',
            'currency'              => 'nullable|string|max:15',
            'monthly_price'         => 'required|numeric|min:0',
            'quarterly_price'       => 'required|numeric|min:0',
            'six_month_price'       => 'required|numeric|min:0',
            'yearly_price'          => 'required|numeric|min:0',
            'custom_duration_label' => 'nullable|string|max:80',
            'custom_duration_price' => 'nullable|numeric|min:0',
            'registration_fee'      => 'nullable|numeric|min:0',
            'features_raw'          => 'nullable|string|max:1000',
            'features_bn_raw'       => 'nullable|string|max:1000',
            'badge_text'            => 'nullable|string|max:60',
            'badge_text_bn'         => 'nullable|string|max:60',
            'button_text'           => 'nullable|string|max:60',
            'button_text_bn'        => 'nullable|string|max:60',
            'sort_order'            => 'nullable|integer',
        ]);

        $features   = array_values(array_filter(array_map('trim', explode(',', $validated['features_raw'] ?? ''))));
        $featuresBn = array_values(array_filter(array_map('trim', explode(',', $validated['features_bn_raw'] ?? ''))));

        $pkg->update([
            'name'                  => $validated['name'],
            'name_bn'               => $validated['name_bn'] ?: $pkg->name_bn,
            'subtitle'              => $validated['subtitle'],
            'subtitle_bn'           => $validated['subtitle_bn'],
            'description'           => $validated['description'],
            'description_bn'        => $validated['description_bn'],
            'currency'              => $validated['currency'] ?: '$',
            'monthly_price'         => $validated['monthly_price'],
            'quarterly_price'       => $validated['quarterly_price'],
            'six_month_price'       => $validated['six_month_price'],
            'yearly_price'          => $validated['yearly_price'],
            'custom_duration_label' => $validated['custom_duration_label'],
            'custom_duration_price' => $validated['custom_duration_price'],
            'registration_fee'      => $validated['registration_fee'] ?? 0,
            'features'              => !empty($features) ? $features : $pkg->features,
            'features_bn'           => !empty($featuresBn) ? $featuresBn : $pkg->features_bn,
            'has_personal_trainer'  => $request->boolean('has_personal_trainer'),
            'has_group_classes'     => $request->boolean('has_group_classes'),
            'has_locker'            => $request->boolean('has_locker'),
            'has_nutrition'         => $request->boolean('has_nutrition'),
            'badge_text'            => $validated['badge_text'],
            'badge_text_bn'         => $validated['badge_text_bn'],
            'button_text'           => $validated['button_text'] ?: $pkg->button_text,
            'button_text_bn'        => $validated['button_text_bn'] ?: $pkg->button_text_bn,
            'is_featured'           => $request->boolean('is_featured'),
            'is_active'             => $request->boolean('is_active'),
            'sort_order'            => $validated['sort_order'] ?? $pkg->sort_order,
        ]);

        return back()->with('success', "Package '{$pkg->name}' updated in gym_packages!");
    }

    public function deletePackage(int $id)
    {
        GymPackage::findOrFail($id)->delete();
        return back()->with('success', 'Package deleted from gym_packages.');
    }

    // =========================================================================
    // D. TRAINER MANAGEMENT CRUD (gym_trainers)
    // =========================================================================
    public function trainers()
    {
        $this->dataService->ensureSeeded();
        $trainers = GymTrainer::withCount(['members', 'classes'])->orderBy('sort_order')->orderBy('id')->get();

        return view('gymfitness::admin.trainers', compact('trainers'));
    }

    public function storeTrainer(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:120',
            'name_bn'           => 'nullable|string|max:120',
            'specialization'    => 'required|string|max:120',
            'specialization_bn' => 'nullable|string|max:120',
            'experience'        => 'nullable|string|max:60',
            'experience_bn'     => 'nullable|string|max:60',
            'phone'             => 'nullable|string|max:40',
            'email'             => 'nullable|email|max:150',
            'bio'               => 'nullable|string',
            'bio_bn'            => 'nullable|string',
            'availability'      => 'nullable|string|max:150',
            'schedule_summary'  => 'nullable|string|max:150',
            'salary'            => 'nullable|numeric|min:0',
            'commission_rate'   => 'nullable|numeric|min:0|max:100',
            'facebook_url'      => 'nullable|string|max:255',
            'instagram_url'     => 'nullable|string|max:255',
            'youtube_url'       => 'nullable|string|max:255',
            'photo_file'        => 'nullable|image|max:4096',
            'sort_order'        => 'nullable|integer',
        ]);

        $photo = '/module-media/GymFitness/theme/trainer-1.jpg';
        if ($request->hasFile('photo_file')) {
            $photo = ModuleCmsResolver::storeModuleFile($request->file('photo_file'), 'trainers', 'GymFitness');
        }

        GymTrainer::create([
            'name'              => $validated['name'],
            'name_bn'           => $validated['name_bn'] ?: $validated['name'],
            'slug'              => Str::slug($validated['name']) . '-' . Str::lower(Str::random(3)),
            'specialization'    => $validated['specialization'],
            'specialization_bn' => $validated['specialization_bn'] ?: $validated['specialization'],
            'experience'        => $validated['experience'] ?: '5+ Years Exp',
            'experience_bn'     => $validated['experience_bn'] ?: '৫+ বছরের অভিজ্ঞতা',
            'phone'             => $validated['phone'],
            'email'             => $validated['email'],
            'photo'             => $photo,
            'bio'               => $validated['bio'],
            'bio_bn'            => $validated['bio_bn'],
            'availability'      => $validated['availability'] ?: 'Mon - Sat (6:00 AM - 9:00 PM)',
            'schedule_summary'  => $validated['schedule_summary'],
            'salary'            => $validated['salary'] ?? 35000,
            'commission_rate'   => $validated['commission_rate'] ?? 10,
            'facebook_url'      => $validated['facebook_url'],
            'instagram_url'     => $validated['instagram_url'],
            'youtube_url'       => $validated['youtube_url'],
            'is_active'         => $request->boolean('is_active', true),
            'sort_order'        => $validated['sort_order'] ?? (GymTrainer::max('sort_order') + 1),
        ]);

        return back()->with('success', "Trainer '{$validated['name']}' added to gym_trainers!");
    }

    public function updateTrainer(Request $request, int $id)
    {
        $trainer = GymTrainer::findOrFail($id);
        $validated = $request->validate([
            'name'              => 'required|string|max:120',
            'name_bn'           => 'nullable|string|max:120',
            'specialization'    => 'required|string|max:120',
            'specialization_bn' => 'nullable|string|max:120',
            'experience'        => 'nullable|string|max:60',
            'experience_bn'     => 'nullable|string|max:60',
            'phone'             => 'nullable|string|max:40',
            'email'             => 'nullable|email|max:150',
            'bio'               => 'nullable|string',
            'bio_bn'            => 'nullable|string',
            'availability'      => 'nullable|string|max:150',
            'schedule_summary'  => 'nullable|string|max:150',
            'salary'            => 'nullable|numeric|min:0',
            'commission_rate'   => 'nullable|numeric|min:0|max:100',
            'facebook_url'      => 'nullable|string|max:255',
            'instagram_url'     => 'nullable|string|max:255',
            'youtube_url'       => 'nullable|string|max:255',
            'photo_file'        => 'nullable|image|max:4096',
            'sort_order'        => 'nullable|integer',
        ]);

        $photo = $trainer->photo;
        if ($request->hasFile('photo_file')) {
            $photo = ModuleCmsResolver::storeModuleFile($request->file('photo_file'), 'trainers', 'GymFitness');
        }

        $trainer->update([
            'name'              => $validated['name'],
            'name_bn'           => $validated['name_bn'],
            'specialization'    => $validated['specialization'],
            'specialization_bn' => $validated['specialization_bn'],
            'experience'        => $validated['experience'] ?: $trainer->experience,
            'experience_bn'     => $validated['experience_bn'],
            'phone'             => $validated['phone'],
            'email'             => $validated['email'],
            'photo'             => $photo,
            'bio'               => $validated['bio'],
            'bio_bn'            => $validated['bio_bn'],
            'availability'      => $validated['availability'],
            'schedule_summary'  => $validated['schedule_summary'],
            'salary'            => $validated['salary'] ?? $trainer->salary,
            'commission_rate'   => $validated['commission_rate'] ?? $trainer->commission_rate,
            'facebook_url'      => $validated['facebook_url'],
            'instagram_url'     => $validated['instagram_url'],
            'youtube_url'       => $validated['youtube_url'],
            'is_active'         => $request->boolean('is_active'),
            'sort_order'        => $validated['sort_order'] ?? $trainer->sort_order,
        ]);

        return back()->with('success', "Trainer '{$trainer->name}' updated in gym_trainers!");
    }

    public function deleteTrainer(int $id)
    {
        GymTrainer::findOrFail($id)->delete();
        return back()->with('success', 'Trainer removed from gym_trainers.');
    }

    // =========================================================================
    // E. CLASS & SCHEDULE MANAGEMENT CRUD (gym_classes & gym_class_bookings)
    // =========================================================================
    public function classes(Request $request)
    {
        $this->dataService->ensureSeeded();
        $dayFilter = $request->query('day');

        $classes = GymClass::with('trainer')
            ->when($dayFilter, fn ($q) => $q->where('day_of_week', $dayFilter))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $trainers = GymTrainer::where('is_active', true)->orderBy('name')->get();
        $bookings = GymClassBooking::with(['gymClass.trainer', 'member'])->latest('id')->take(25)->get();

        return view('gymfitness::admin.classes', compact('classes', 'trainers', 'bookings', 'dayFilter'));
    }

    public function storeClass(Request $request)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:120',
            'title_bn'         => 'nullable|string|max:120',
            'category'         => 'required|string|max:80',
            'trainer_id'       => 'nullable|exists:gym_trainers,id',
            'day_of_week'      => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'time_slot'        => 'required|string|max:80',
            'duration_minutes' => 'nullable|integer|min:15|max:240',
            'room_zone'        => 'nullable|string|max:100',
            'max_participants' => 'nullable|integer|min:1|max:200',
            'sort_order'       => 'nullable|integer',
        ]);

        GymClass::create([
            'title'            => $validated['title'],
            'title_bn'         => $validated['title_bn'] ?: $validated['title'],
            'category'         => $validated['category'],
            'category_bn'      => $validated['title_bn'] ?: $validated['category'],
            'trainer_id'       => $validated['trainer_id'] ?? null,
            'day_of_week'      => $validated['day_of_week'],
            'time_slot'        => $validated['time_slot'],
            'duration_minutes' => $validated['duration_minutes'] ?? 60,
            'room_zone'        => $validated['room_zone'] ?: 'Main Gym Floor',
            'max_participants' => $validated['max_participants'] ?? 25,
            'booked_count'     => 0,
            'is_active'        => $request->boolean('is_active', true),
            'sort_order'       => $validated['sort_order'] ?? (GymClass::max('sort_order') + 1),
        ]);

        return back()->with('success', "Class '{$validated['title']}' ({$validated['day_of_week']} {$validated['time_slot']}) saved in gym_classes!");
    }

    public function updateClass(Request $request, int $id)
    {
        $cls = GymClass::findOrFail($id);
        $validated = $request->validate([
            'title'            => 'required|string|max:120',
            'title_bn'         => 'nullable|string|max:120',
            'category'         => 'required|string|max:80',
            'trainer_id'       => 'nullable|exists:gym_trainers,id',
            'day_of_week'      => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'time_slot'        => 'required|string|max:80',
            'duration_minutes' => 'nullable|integer|min:15|max:240',
            'room_zone'        => 'nullable|string|max:100',
            'max_participants' => 'nullable|integer|min:1|max:200',
            'sort_order'       => 'nullable|integer',
        ]);

        $cls->update(array_merge($validated, [
            'is_active' => $request->boolean('is_active'),
        ]));

        return back()->with('success', "Class '{$cls->title}' updated in gym_classes!");
    }

    public function deleteClass(int $id)
    {
        GymClass::findOrFail($id)->delete();
        return back()->with('success', 'Class removed from gym_classes.');
    }

    public function updateBookingStatus(Request $request, int $id)
    {
        $booking = GymClassBooking::findOrFail($id);
        $booking->update(['status' => $request->input('status', 'Attended')]);

        return back()->with('success', 'Class booking status updated!');
    }

    // =========================================================================
    // F. ATTENDANCE & QR CHECK-IN MANAGEMENT (gym_attendances)
    // =========================================================================
    public function attendance(Request $request)
    {
        $this->dataService->ensureSeeded();
        $dateFilter = $request->query('date');

        $attendances = GymAttendance::with('member.package')
            ->when($dateFilter, fn ($q) => $q->whereDate('attendance_date', $dateFilter))
            ->latest('attendance_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $members      = GymMember::orderBy('name')->get();
        $todayCount   = GymAttendance::whereDate('attendance_date', Carbon::today())->count();
        $monthlyCount = GymAttendance::whereMonth('attendance_date', Carbon::now()->month)->count();

        return view('gymfitness::admin.attendance', compact(
            'attendances',
            'members',
            'dateFilter',
            'todayCount',
            'monthlyCount'
        ));
    }

    public function checkInMember(Request $request)
    {
        $member = null;
        if ($request->filled('qr_or_code')) {
            $code = trim((string) $request->input('qr_or_code'));
            $member = GymMember::where('member_code', $code)
                ->orWhere('qr_token', $code)
                ->orWhere('phone', $code)
                ->first();
        } elseif ($request->filled('member_id')) {
            $member = GymMember::find($request->input('member_id'));
        }

        if (!$member) {
            return back()->withErrors(['checkin' => 'Member not found! Please check the Member ID or QR Code.']);
        }

        GymAttendance::create([
            'member_id'       => $member->id,
            'attendance_date' => Carbon::today()->toDateString(),
            'check_in_time'   => now()->format('h:i A'),
            'check_in_method' => $request->input('check_in_method', 'QR Code'),
            'status'          => 'Present',
            'notes'           => $request->input('notes', 'Checked in at Gym Reception'),
        ]);

        return back()->with('success', "Check-in recorded for {$member->name} ({$member->member_code}) at " . now()->format('h:i A') . '!');
    }

    public function checkOutMember(int $id)
    {
        $att = GymAttendance::findOrFail($id);
        $att->update([
            'check_out_time' => now()->format('h:i A'),
            'status'         => 'Completed',
        ]);

        return back()->with('success', 'Member check-out time recorded!');
    }

    public function deleteAttendance(int $id)
    {
        GymAttendance::findOrFail($id)->delete();
        return back()->with('success', 'Attendance log removed.');
    }

    // =========================================================================
    // G. PAYMENT & REVENUE MANAGEMENT (gym_payments)
    // =========================================================================
    public function payments(Request $request)
    {
        $this->dataService->ensureSeeded();
        $statusFilter = $request->query('status');
        $typeFilter   = $request->query('type');

        $payments = GymPayment::with(['member', 'package'])
            ->when($statusFilter, fn ($q) => $q->where('payment_status', $statusFilter))
            ->when($typeFilter, fn ($q) => $q->where('payment_type', $typeFilter))
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $members      = GymMember::orderBy('name')->get();
        $packages     = GymPackage::orderBy('sort_order')->get();
        $totalPaid    = (float) GymPayment::where('payment_status', 'Paid')->sum('paid_amount');
        $totalDue     = (float) GymPayment::whereIn('payment_status', ['Pending', 'Due'])->sum('due_amount');
        $totalRefunds = (float) GymPayment::where('payment_status', 'Refunded')->sum('amount');

        return view('gymfitness::admin.payments', compact(
            'payments',
            'members',
            'packages',
            'statusFilter',
            'typeFilter',
            'totalPaid',
            'totalDue',
            'totalRefunds'
        ));
    }

    public function storePayment(Request $request)
    {
        $validated = $request->validate([
            'member_id'       => 'required|exists:gym_members,id',
            'package_id'      => 'nullable|exists:gym_packages,id',
            'payment_type'    => 'required|string|max:80',
            'amount'          => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'paid_amount'     => 'required|numeric|min:0',
            'payment_method'  => 'required|string|max:80',
            'payment_status'  => 'required|in:Paid,Pending,Due,Refunded',
            'notes'           => 'nullable|string|max:500',
        ]);

        $discount = (float) ($validated['discount_amount'] ?? 0);
        $due      = max(0, (float) $validated['amount'] - $discount - (float) $validated['paid_amount']);
        $member   = GymMember::findOrFail($validated['member_id']);

        $payment = GymPayment::create([
            'invoice_no'            => 'INV-GYM-' . strtoupper(Str::random(6)),
            'member_id'             => $member->id,
            'package_id'            => $validated['package_id'] ?? $member->package_id,
            'payment_type'          => $validated['payment_type'],
            'amount'                => $validated['amount'],
            'discount_amount'       => $discount,
            'paid_amount'           => $validated['paid_amount'],
            'due_amount'            => $due,
            'payment_method'        => $validated['payment_method'],
            'transaction_reference' => 'TXN-' . strtoupper(Str::random(6)),
            'payment_status'        => $validated['payment_status'],
            'paid_at'               => $validated['payment_status'] === 'Paid' ? now() : null,
            'notes'                 => $validated['notes'],
        ]);

        $this->dataService->syncWithCoreTransactions($payment, $member->phone);

        return back()->with('success', "Payment Invoice {$payment->invoice_no} recorded in gym_payments!");
    }

    public function updatePaymentStatus(Request $request, int $id)
    {
        $payment   = GymPayment::with('member')->findOrFail($id);
        $newStatus = $request->input('payment_status', 'Paid');

        if ($newStatus === 'Paid') {
            $payment->update([
                'payment_status' => 'Paid',
                'paid_amount'    => $payment->amount - $payment->discount_amount,
                'due_amount'     => 0,
                'paid_at'        => now(),
            ]);
            if ($payment->member) {
                $payment->member->update(['payment_status' => 'Paid', 'due_amount' => 0]);
                $this->dataService->syncWithCoreTransactions($payment, $payment->member->phone);
            }
        } elseif ($newStatus === 'Refunded') {
            $payment->update(['payment_status' => 'Refunded', 'due_amount' => 0]);
        } else {
            $payment->update(['payment_status' => $newStatus]);
        }

        return back()->with('success', "Invoice {$payment->invoice_no} marked as {$newStatus}!");
    }

    public function deletePayment(int $id)
    {
        GymPayment::findOrFail($id)->delete();
        return back()->with('success', 'Payment record removed from gym_payments.');
    }

    // =========================================================================
    // H1. TRAINING PROGRAMS CRUD (gym_programs)
    // =========================================================================
    public function programs()
    {
        $this->dataService->ensureSeeded();
        $programs = GymProgram::orderBy('sort_order')->orderBy('id')->get();

        return view('gymfitness::admin.programs', compact('programs'));
    }

    public function storeProgram(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:120',
            'title_bn'       => 'nullable|string|max:120',
            'subtitle'       => 'nullable|string|max:180',
            'subtitle_bn'    => 'nullable|string|max:180',
            'description'    => 'nullable|string',
            'description_bn' => 'nullable|string',
            'icon_class'     => 'nullable|string|max:80',
            'duration_label' => 'nullable|string|max:60',
            'calories_burn'  => 'nullable|string|max:60',
            'image_file'     => 'nullable|image|max:4096',
            'sort_order'     => 'nullable|integer',
        ]);

        $image = '/module-media/GymFitness/theme/prog-weight.jpg';
        if ($request->hasFile('image_file')) {
            $image = ModuleCmsResolver::storeModuleFile($request->file('image_file'), 'programs', 'GymFitness');
        }

        GymProgram::create([
            'title'          => $validated['title'],
            'title_bn'       => $validated['title_bn'] ?: $validated['title'],
            'slug'           => Str::slug($validated['title']) . '-' . Str::lower(Str::random(3)),
            'subtitle'       => $validated['subtitle'],
            'subtitle_bn'    => $validated['subtitle_bn'],
            'description'    => $validated['description'],
            'description_bn' => $validated['description_bn'],
            'image'          => $image,
            'icon_class'     => $validated['icon_class'] ?: 'fa-solid fa-dumbbell',
            'duration_label' => $validated['duration_label'] ?: '60 Mins',
            'calories_burn'  => $validated['calories_burn'] ?: '600 kcal',
            'is_active'      => $request->boolean('is_active', true),
            'sort_order'     => $validated['sort_order'] ?? (GymProgram::max('sort_order') + 1),
        ]);

        return back()->with('success', "Training Program '{$validated['title']}' added to gym_programs!");
    }

    public function updateProgram(Request $request, int $id)
    {
        $prog = GymProgram::findOrFail($id);
        $validated = $request->validate([
            'title'          => 'required|string|max:120',
            'title_bn'       => 'nullable|string|max:120',
            'subtitle'       => 'nullable|string|max:180',
            'subtitle_bn'    => 'nullable|string|max:180',
            'description'    => 'nullable|string',
            'description_bn' => 'nullable|string',
            'icon_class'     => 'nullable|string|max:80',
            'duration_label' => 'nullable|string|max:60',
            'calories_burn'  => 'nullable|string|max:60',
            'image_file'     => 'nullable|image|max:4096',
            'sort_order'     => 'nullable|integer',
        ]);

        $image = $prog->image;
        if ($request->hasFile('image_file')) {
            $image = ModuleCmsResolver::storeModuleFile($request->file('image_file'), 'programs', 'GymFitness');
        }

        $prog->update([
            'title'          => $validated['title'],
            'title_bn'       => $validated['title_bn'],
            'subtitle'       => $validated['subtitle'],
            'subtitle_bn'    => $validated['subtitle_bn'],
            'description'    => $validated['description'],
            'description_bn' => $validated['description_bn'],
            'image'          => $image,
            'icon_class'     => $validated['icon_class'] ?: $prog->icon_class,
            'duration_label' => $validated['duration_label'] ?: $prog->duration_label,
            'calories_burn'  => $validated['calories_burn'] ?: $prog->calories_burn,
            'is_active'      => $request->boolean('is_active'),
            'sort_order'     => $validated['sort_order'] ?? $prog->sort_order,
        ]);

        return back()->with('success', "Program '{$prog->title}' updated in gym_programs!");
    }

    public function deleteProgram(int $id)
    {
        GymProgram::findOrFail($id)->delete();
        return back()->with('success', 'Program removed from gym_programs.');
    }

    // =========================================================================
    // H2. GYM EQUIPMENT & MAINTENANCE CRUD (gym_equipments)
    // =========================================================================
    public function equipment()
    {
        $this->dataService->ensureSeeded();
        $equipments = GymEquipment::orderBy('sort_order')->orderBy('id')->get();

        return view('gymfitness::admin.equipment', compact('equipments'));
    }

    public function storeEquipment(Request $request)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:120',
            'name_bn'               => 'nullable|string|max:120',
            'category'              => 'required|string|max:60',
            'quantity'              => 'required|integer|min:1',
            'brand'                 => 'nullable|string|max:120',
            'condition_status'      => 'required|in:Operational,Under Maintenance,Repair Needed',
            'last_maintenance_date' => 'nullable|date',
            'next_maintenance_date' => 'nullable|date',
            'notes'                 => 'nullable|string|max:500',
            'image_file'            => 'nullable|image|max:4096',
            'sort_order'            => 'nullable|integer',
        ]);

        $image = '/module-media/GymFitness/theme/equip-dumbbells.jpg';
        if ($request->hasFile('image_file')) {
            $image = ModuleCmsResolver::storeModuleFile($request->file('image_file'), 'equipment', 'GymFitness');
        }

        GymEquipment::create([
            'name'                  => $validated['name'],
            'name_bn'               => $validated['name_bn'] ?: $validated['name'],
            'category'              => $validated['category'],
            'quantity'              => $validated['quantity'],
            'brand'                 => $validated['brand'] ?: 'Hammer Strength',
            'image'                 => $image,
            'condition_status'      => $validated['condition_status'],
            'last_maintenance_date' => $validated['last_maintenance_date'] ?? Carbon::today(),
            'next_maintenance_date' => $validated['next_maintenance_date'] ?? Carbon::today()->addDays(45),
            'notes'                 => $validated['notes'],
            'is_active'             => $request->boolean('is_active', true),
            'sort_order'            => $validated['sort_order'] ?? (GymEquipment::max('sort_order') + 1),
        ]);

        return back()->with('success', "Equipment '{$validated['name']}' added to gym_equipments!");
    }

    public function updateEquipment(Request $request, int $id)
    {
        $eq = GymEquipment::findOrFail($id);
        $validated = $request->validate([
            'name'                  => 'required|string|max:120',
            'name_bn'               => 'nullable|string|max:120',
            'category'              => 'required|string|max:60',
            'quantity'              => 'required|integer|min:1',
            'brand'                 => 'nullable|string|max:120',
            'condition_status'      => 'required|in:Operational,Under Maintenance,Repair Needed',
            'last_maintenance_date' => 'nullable|date',
            'next_maintenance_date' => 'nullable|date',
            'notes'                 => 'nullable|string|max:500',
            'image_file'            => 'nullable|image|max:4096',
            'sort_order'            => 'nullable|integer',
        ]);

        $image = $eq->image;
        if ($request->hasFile('image_file')) {
            $image = ModuleCmsResolver::storeModuleFile($request->file('image_file'), 'equipment', 'GymFitness');
        }

        $eq->update([
            'name'                  => $validated['name'],
            'name_bn'               => $validated['name_bn'],
            'category'              => $validated['category'],
            'quantity'              => $validated['quantity'],
            'brand'                 => $validated['brand'],
            'image'                 => $image,
            'condition_status'      => $validated['condition_status'],
            'last_maintenance_date' => $validated['last_maintenance_date'],
            'next_maintenance_date' => $validated['next_maintenance_date'],
            'notes'                 => $validated['notes'],
            'is_active'             => $request->boolean('is_active'),
            'sort_order'            => $validated['sort_order'] ?? $eq->sort_order,
        ]);

        return back()->with('success', "Equipment '{$eq->name}' updated in gym_equipments!");
    }

    public function deleteEquipment(int $id)
    {
        GymEquipment::findOrFail($id)->delete();
        return back()->with('success', 'Equipment removed from gym_equipments.');
    }

    // =========================================================================
    // H3. WORKOUT PLANS, DIET PLANS & PROGRESS TRACKING
    // =========================================================================
    public function plans()
    {
        $this->dataService->ensureSeeded();
        $plans        = GymFitnessPlan::latest('id')->get();
        $progressLogs = GymProgressLog::with('member')->latest('recorded_date')->latest('id')->take(25)->get();
        $members      = GymMember::orderBy('name')->get();

        return view('gymfitness::admin.plans', compact('plans', 'progressLogs', 'members'));
    }

    public function storePlan(Request $request)
    {
        $validated = $request->validate([
            'title'               => 'required|string|max:150',
            'title_bn'            => 'nullable|string|max:150',
            'plan_type'           => 'required|in:Workout,Diet',
            'goal'                => 'required|string|max:80',
            'level'               => 'required|in:Beginner,Intermediate,Advanced',
            'duration_weeks'      => 'required|integer|min:1|max:52',
            'calories_target'     => 'nullable|string|max:80',
            'schedule_details'    => 'required|string',
            'schedule_details_bn' => 'nullable|string',
        ]);

        GymFitnessPlan::create(array_merge($validated, ['is_active' => true]));

        return back()->with('success', "{$validated['plan_type']} Plan '{$validated['title']}' saved in gym_fitness_plans!");
    }

    public function updatePlan(Request $request, int $id)
    {
        $plan = GymFitnessPlan::findOrFail($id);
        $validated = $request->validate([
            'title'               => 'required|string|max:150',
            'title_bn'            => 'nullable|string|max:150',
            'plan_type'           => 'required|in:Workout,Diet',
            'goal'                => 'required|string|max:80',
            'level'               => 'required|in:Beginner,Intermediate,Advanced',
            'duration_weeks'      => 'required|integer|min:1|max:52',
            'calories_target'     => 'nullable|string|max:80',
            'schedule_details'    => 'required|string',
            'schedule_details_bn' => 'nullable|string',
        ]);

        $plan->update(array_merge($validated, ['is_active' => $request->boolean('is_active', true)]));

        return back()->with('success', "Plan '{$plan->title}' updated in gym_fitness_plans!");
    }

    public function deletePlan(int $id)
    {
        GymFitnessPlan::findOrFail($id)->delete();
        return back()->with('success', 'Fitness plan deleted.');
    }

    public function storeProgressLog(Request $request)
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
            'notes'         => $validated['notes'] ?? 'Recorded by Gym Trainer',
        ]);

        return back()->with('success', 'Member body measurement & progress log saved!');
    }

    public function deleteProgressLog(int $id)
    {
        GymProgressLog::findOrFail($id)->delete();
        return back()->with('success', 'Progress log deleted.');
    }

    // =========================================================================
    // H4. TESTIMONIALS & FAQS CRUD (gym_testimonials & gym_faqs)
    // =========================================================================
    public function reviews()
    {
        $this->dataService->ensureSeeded();
        $testimonials = GymTestimonial::orderBy('sort_order')->orderBy('id')->get();
        $faqs         = GymFaq::orderBy('sort_order')->orderBy('id')->get();

        return view('gymfitness::admin.reviews', compact('testimonials', 'faqs'));
    }

    public function storeTestimonial(Request $request)
    {
        $validated = $request->validate([
            'member_name'           => 'required|string|max:120',
            'member_name_bn'        => 'nullable|string|max:120',
            'transformation_tag'    => 'nullable|string|max:120',
            'transformation_tag_bn' => 'nullable|string|max:120',
            'review_text'           => 'required|string',
            'review_text_bn'        => 'nullable|string',
            'rating'                => 'required|integer|min:1|max:5',
            'avatar_file'           => 'nullable|image|max:3072',
            'sort_order'            => 'nullable|integer',
        ]);

        $avatar = '/module-media/GymFitness/theme/trainer-1.jpg';
        if ($request->hasFile('avatar_file')) {
            $avatar = ModuleCmsResolver::storeModuleFile($request->file('avatar_file'), 'testimonials', 'GymFitness');
        }

        GymTestimonial::create([
            'member_name'           => $validated['member_name'],
            'member_name_bn'        => $validated['member_name_bn'] ?: $validated['member_name'],
            'transformation_tag'    => $validated['transformation_tag'],
            'transformation_tag_bn' => $validated['transformation_tag_bn'],
            'review_text'           => $validated['review_text'],
            'review_text_bn'        => $validated['review_text_bn'],
            'rating'                => $validated['rating'],
            'avatar'                => $avatar,
            'is_active'             => $request->boolean('is_active', true),
            'sort_order'            => $validated['sort_order'] ?? (GymTestimonial::max('sort_order') + 1),
        ]);

        return back()->with('success', 'Member testimonial saved in gym_testimonials!');
    }

    public function updateTestimonial(Request $request, int $id)
    {
        $t = GymTestimonial::findOrFail($id);
        $validated = $request->validate([
            'member_name'           => 'required|string|max:120',
            'member_name_bn'        => 'nullable|string|max:120',
            'transformation_tag'    => 'nullable|string|max:120',
            'transformation_tag_bn' => 'nullable|string|max:120',
            'review_text'           => 'required|string',
            'review_text_bn'        => 'nullable|string',
            'rating'                => 'required|integer|min:1|max:5',
            'avatar_file'           => 'nullable|image|max:3072',
            'sort_order'            => 'nullable|integer',
        ]);

        $avatar = $t->avatar;
        if ($request->hasFile('avatar_file')) {
            $avatar = ModuleCmsResolver::storeModuleFile($request->file('avatar_file'), 'testimonials', 'GymFitness');
        }

        $t->update([
            'member_name'           => $validated['member_name'],
            'member_name_bn'        => $validated['member_name_bn'],
            'transformation_tag'    => $validated['transformation_tag'],
            'transformation_tag_bn' => $validated['transformation_tag_bn'],
            'review_text'           => $validated['review_text'],
            'review_text_bn'        => $validated['review_text_bn'],
            'rating'                => $validated['rating'],
            'avatar'                => $avatar,
            'is_active'             => $request->boolean('is_active'),
            'sort_order'            => $validated['sort_order'] ?? $t->sort_order,
        ]);

        return back()->with('success', 'Testimonial updated in gym_testimonials!');
    }

    public function deleteTestimonial(int $id)
    {
        GymTestimonial::findOrFail($id)->delete();
        return back()->with('success', 'Testimonial deleted.');
    }

    public function storeFaq(Request $request)
    {
        $validated = $request->validate([
            'question'    => 'required|string|max:255',
            'question_bn' => 'nullable|string|max:255',
            'answer'      => 'required|string',
            'answer_bn'   => 'nullable|string',
            'sort_order'  => 'nullable|integer',
        ]);

        GymFaq::create([
            'question'    => $validated['question'],
            'question_bn' => $validated['question_bn'] ?: $validated['question'],
            'answer'      => $validated['answer'],
            'answer_bn'   => $validated['answer_bn'] ?: $validated['answer'],
            'is_active'   => $request->boolean('is_active', true),
            'sort_order'  => $validated['sort_order'] ?? (GymFaq::max('sort_order') + 1),
        ]);

        return back()->with('success', 'FAQ added to gym_faqs!');
    }

    public function updateFaq(Request $request, int $id)
    {
        $faq = GymFaq::findOrFail($id);
        $validated = $request->validate([
            'question'    => 'required|string|max:255',
            'question_bn' => 'nullable|string|max:255',
            'answer'      => 'required|string',
            'answer_bn'   => 'nullable|string',
            'sort_order'  => 'nullable|integer',
        ]);

        $faq->update(array_merge($validated, ['is_active' => $request->boolean('is_active', true)]));

        return back()->with('success', 'FAQ updated in gym_faqs!');
    }

    public function deleteFaq(int $id)
    {
        GymFaq::findOrFail($id)->delete();
        return back()->with('success', 'FAQ removed.');
    }

    // =========================================================================
    // I. WEBSITE BUILDER, HOMEPAGE SECTION ORDERING & MULTILINGUAL (EN/BN)
    // =========================================================================
    public function settings(Request $request)
    {
        $this->dataService->ensureSeeded();
        $t = $this->dataService->getSettingsData();
        $activeTab = $request->query('tab', 'builder');

        return view('gymfitness::admin.settings', compact('t', 'activeTab'));
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

        if ($request->hasFile('header_logo')) {
            $current['header']['logo'] = ModuleCmsResolver::storeModuleFile($request->file('header_logo'), 'branding', 'GymFitness');
        }
        if ($request->hasFile('hero_image_file')) {
            $current['hero']['hero_image'] = ModuleCmsResolver::storeModuleFile($request->file('hero_image_file'), 'hero', 'GymFitness');
        }
        if ($request->hasFile('why_gym_image_file')) {
            $current['why_choose']['gym_image'] = ModuleCmsResolver::storeModuleFile($request->file('why_gym_image_file'), 'why_choose', 'GymFitness');
        }
        if ($request->hasFile('cta_image_file')) {
            $current['cta_section']['cta_image'] = ModuleCmsResolver::storeModuleFile($request->file('cta_image_file'), 'cta', 'GymFitness');
        }

        ModuleSetting::setValue('theme_sections_json', json_encode($current), 'theme');

        ActivityLog::record(
            'Gym & Fitness Website Builder Updated',
            'Saved in gym_settings',
            'bi-lightning-charge-fill',
            '#DCFCE7',
            '#10B981'
        );

        return back()->with('success', '[Gym & Fitness] Homepage Builder, Section Order & Bilingual (EN/BN) Settings saved in gym_settings!');
    }

    public function resetSettings()
    {
        $this->managerService->resetModuleToDefault('GymFitness');

        return back()->with('success', 'Gym & Fitness module has been reset 100% to its original default template and seeded data!');
    }
}
