<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Page;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GlobalAdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $selectedWebsiteId = $request->query('website_id');

        $totalWebsites = Website::count();
        $pagesQuery = Page::query();
        if ($selectedWebsiteId) {
            $pagesQuery->where('website_id', $selectedWebsiteId);
        }

        $totalPages = (clone $pagesQuery)->count();
        $publishedPages = (clone $pagesQuery)->where('status', 'Published')->count();
        $draftPages = (clone $pagesQuery)->where('status', 'Draft')->count();
        $privatePages = (clone $pagesQuery)->where('status', 'Private')->count();
        $scheduledPages = (clone $pagesQuery)->where('status', 'Scheduled')->count();

        $mediaFilesCount = Setting::getValue('media_files_count', '1,284');
        $totalUsers = User::count();
        $dateRangeLabel = Setting::getValue('date_range_label', 'Mar 1, 2025 - Mar 31, 2025');

        $recentWebsites = Website::latest('updated_at')->take(4)->get();
        $allWebsites = Website::orderBy('name')->get();
        $recentActivities = ActivityLog::latest('id')->take(5)->get();

        return view('admin.dashboard', compact(
            'totalWebsites',
            'totalPages',
            'publishedPages',
            'draftPages',
            'privatePages',
            'scheduledPages',
            'mediaFilesCount',
            'totalUsers',
            'dateRangeLabel',
            'recentWebsites',
            'allWebsites',
            'selectedWebsiteId',
            'recentActivities'
        ));
    }

    public function transactions(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $type = $request->query('type');

        $query = Transaction::query()->orderByDesc('transacted_at');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                    ->orWhere('wallet_number', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
            });
        }

        if ($type) {
            $query->where('type', $type);
        }

        $transactions = $query->paginate(10)->withQueryString();
        $merchantAccount = Setting::getValue('merchant_account', '01812-XXXXXX');
        $chargesRate = Setting::getValue('charges_rate', '1.80% + ৳5.00');
        $availableBalance = Setting::getValue('available_balance', '125,482.36');

        return view('admin.transactions', compact(
            'transactions',
            'search',
            'merchantAccount',
            'chargesRate',
            'availableBalance'
        ));
    }

    public function websites(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $websites = Website::when($search !== '', function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('domain', 'like', "%{$search}%")
                ->orWhere('module', 'like', "%{$search}%");
        })->latest('updated_at')->paginate(10)->withQueryString();

        return view('admin.websites', compact('websites', 'search'));
    }

    public function storeWebsite(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255',
            'module' => 'required|string|max:100',
            'status' => 'required|in:Published,Draft,Private',
            'thumbnail' => 'nullable|url',
        ]);

        $badgeMap = [
            'Real Estate' => 'pink',
            'Interior Design' => 'purple',
            'Booking' => 'blue',
            'Software' => 'green',
            'Pharmacy' => 'green',
            'Fertility Clinic' => 'green',
            'Clinic' => 'green',
            'Developer Portfolio' => 'blue',
            'Portfolio' => 'blue',
        ];
        $moduleMapping = [
            'Real Estate' => ['module_name' => 'RealEstate', 'slug' => 'real-estate', 'admin_route' => 'admin.realestate.properties.index'],
            'Interior Design' => ['module_name' => 'Interior', 'slug' => 'interior-design', 'admin_route' => 'admin.interior.projects.index'],
            'Pharmacy' => ['module_name' => 'Pharmacy', 'slug' => 'pharmacy', 'admin_route' => 'admin.pharmacy.medicines.index'],
            'Booking' => ['module_name' => null, 'slug' => 'booking', 'admin_route' => 'admin.transactions'],
            'Software' => ['module_name' => 'TechCompany', 'slug' => 'software-company', 'admin_route' => 'admin.websites'],
            'Fertility Clinic' => ['module_name' => 'Clinic', 'slug' => 'fertility-clinic', 'admin_route' => 'admin.pages'],
            'Clinic' => ['module_name' => 'Clinic', 'slug' => 'fertility-clinic', 'admin_route' => 'admin.pages'],
            'Developer Portfolio' => ['module_name' => 'Portfolio', 'slug' => 'developer-portfolio', 'admin_route' => 'admin.portfolio.projects.index'],
            'Portfolio' => ['module_name' => 'Portfolio', 'slug' => 'developer-portfolio', 'admin_route' => 'admin.portfolio.projects.index'],
        ];

        $map = $moduleMapping[$validated['module']] ?? ['module_name' => null, 'slug' => Str::slug($validated['name']), 'admin_route' => 'admin.websites'];

        $validated['module_badge'] = $badgeMap[$validated['module']] ?? 'blue';
        $validated['module_name'] = $map['module_name'];
        $validated['slug'] = $map['slug'];
        $validated['admin_route'] = $map['admin_route'];
        $validated['is_active'] = $validated['status'] === 'Published';
        $validated['thumbnail'] = $validated['thumbnail'] ?: 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=400&q=80';

        $site = Website::create($validated);
        Website::syncModuleStatuses();

        ActivityLog::record(
            'New website added',
            "{$site->name} ({$site->domain})",
            'bi-globe2',
            '#FFE4EC',
            '#EC134A'
        );

        return back()->with('success', "Website [{$site->name}] created successfully.");
    }

    public function toggleWebsite(Website $website)
    {
        $newState = !$website->is_active;
        $website->update([
            'is_active' => $newState,
            'status' => $newState ? 'Published' : 'Draft',
        ]);

        Website::syncModuleStatuses();

        $activeCount = Website::activeModules()->count();
        $modeText = $activeCount === 1
            ? 'Single Active Module Mode (Running directly on /)'
            : ($activeCount > 1 ? "Multi-Module Hub Mode ({$activeCount} active themes on /)" : 'All Modules Off');

        ActivityLog::record(
            $newState ? 'Module activated' : 'Module deactivated',
            "{$website->name} ({$website->module}) is now " . ($newState ? 'Active' : 'Off'),
            $newState ? 'bi-toggle-on' : 'bi-toggle-off',
            $newState ? '#DCFCE7' : '#FEF3C7',
            $newState ? '#10B981' : '#F59E0B'
        );

        return back()->with('success', "{$website->name} ({$website->module}) turned " . ($newState ? 'ACTIVE' : 'OFF') . ". System Mode: {$modeText}");
    }

    public function resetWebsite(Website $website, \App\Services\ModuleManagerService $managerService)
    {
        if (!$website->module_name) {
            return back()->with('success', "Website [{$website->name}] restored to default configuration.");
        }

        $mod = $managerService->resetWebsiteToDefault($website);

        return back()->with('success', "[{$website->name} / {$mod}] has been reset 100% to its original default template, images, and content!");
    }

    public function deleteWebsite(Website $website)
    {
        $name = $website->name;
        $website->delete();
        Website::syncModuleStatuses();

        return back()->with('success', "Website [{$name}] deleted successfully.");
    }

    public function pages(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status');

        $pages = Page::with('website')
            ->when($search !== '', fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('slug', 'like', "%{$search}%"))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $websites = Website::orderBy('name')->get();

        return view('admin.pages', compact('pages', 'websites', 'search', 'status'));
    }

    public function storePage(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'website_id' => 'nullable|exists:core_websites,id',
            'status' => 'required|in:Published,Draft,Private,Scheduled',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['views'] = 0;

        $page = Page::create($validated);
        $domain = $page->website?->domain ?? 'sitebuilder.com';

        ActivityLog::record(
            'New page created',
            "{$page->title} ({$domain})",
            'bi-plus-circle-fill',
            '#DCFCE7',
            '#10B981'
        );

        return back()->with('success', "Page [{$page->title}] created successfully.");
    }

    public function deletePage(Page $page)
    {
        $page->delete();

        return back()->with('success', 'Page deleted successfully.');
    }

    public function users(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $roleId = $request->query('role_id');

        $users = User::with('role.permissions')
            ->when($search !== '', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })
            ->when($roleId, fn ($q) => $q->where('role_id', $roleId))
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        $roles = Role::with('permissions')->withCount('users')->get();
        $permissions = Permission::orderBy('group')->get();

        return view('admin.users', compact('users', 'roles', 'permissions', 'search', 'roleId'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:Active,Inactive',
            'password' => 'required|string|min:8',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['email_verified_at'] = now();
        $validated['two_factor_code'] = '274916';

        $user = User::create($validated);

        ActivityLog::record(
            'New user registered',
            $user->email,
            'bi-person-fill',
            '#FEF3C7',
            '#F59E0B'
        );

        return back()->with('success', "User [{$user->name}] created successfully.");
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:Active,Inactive',
            'password' => 'nullable|string|min:8',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return back()->with('success', "User [{$user->name}] updated successfully.");
    }

    public function deleteUser(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->withErrors(['user' => 'You cannot delete your own active account.']);
        }

        $user->delete();

        return back()->with('success', 'User removed successfully.');
    }

    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        ActivityLog::record(
            'Role created',
            "{$role->name} role with " . count($validated['permissions'] ?? []) . ' permissions',
            'bi-shield-check',
            '#F3E8FF',
            '#A855F7'
        );

        return back()->with('success', "Role [{$role->name}] created and permissions assigned.");
    }

    public function updateRolePermissions(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        ActivityLog::record(
            'Role permissions updated',
            "{$role->name} permissions updated",
            'bi-key-fill',
            '#DBEAFE',
            '#3B82F6'
        );

        return back()->with('success', "Permissions for role [{$role->name}] updated successfully.");
    }

    public function settings()
    {
        $user = Auth::user() ?? User::with('role')->first();
        $roles = Role::all();
        $settings = Setting::pluck('value', 'key')->toArray();

        return view('admin.settings', compact('user', 'roles', 'settings'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user() ?? User::first();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30',
            'two_factor_enabled' => 'nullable|boolean',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $validated['two_factor_enabled'] = $request->boolean('two_factor_enabled');

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);

        ActivityLog::record(
            'Profile updated',
            "{$user->name} ({$user->email})",
            'bi-person-gear',
            '#DBEAFE',
            '#3B82F6'
        );

        return back()->with('success', 'Your profile details have been updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user() ?? User::first();

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Your current password does not match our records.',
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        ActivityLog::record(
            'Password changed',
            "Security password updated for {$user->email}",
            'bi-shield-lock-fill',
            '#FFE4EC',
            '#EC134A'
        );

        return back()->with('success', 'Your password has been changed successfully!');
    }

    public function updateGlobalSettings(Request $request)
    {
        $fields = [
            'site_name',
            'site_tagline',
            'support_email',
            'merchant_account',
            'charges_rate',
            'available_balance',
            'media_files_count',
            'date_range_label',
        ];

        if ($request->hasFile('site_logo')) {
            $logoPath = $request->file('site_logo')->store('branding', 'public');
            Setting::setValue('site_logo', $logoPath);
        } elseif ($request->boolean('reset_logo')) {
            Setting::setValue('site_logo', null);
        }

        foreach ($fields as $field) {
            if ($request->has($field)) {
                Setting::setValue($field, $request->input($field));
            }
        }

        if ($request->filled('active_module')) {
            $targetMod = $request->input('active_module');
            if (is_dir(base_path("Modules/{$targetMod}"))) {
                \Illuminate\Support\Facades\Artisan::call('demo:activate', ['module' => $targetMod]);
            }
        }

        ActivityLog::record(
            'System settings updated',
            'Global configuration saved',
            'bi-gear-fill',
            '#F3E8FF',
            '#A855F7'
        );

        return back()->with('success', 'Global settings saved successfully.');
    }
}
