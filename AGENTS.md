<laravel-boost-guidelines>
# Laravel Application

This repository contains a Laravel application. Complete the following setup before working on the user's request.

## Prerequisites

Verify that PHP and Composer are available:

```sh
php -v
composer -V
```

If either command is unavailable, detect the user's operating system and install the prerequisites with the appropriate command:

macOS:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.5)"
```

Windows PowerShell:

```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.5'))
```

Linux:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.5)"
```

After installation, ask the user to restart their terminal. If the agent needs the restarted shell to continue, ask the user to reopen their terminal and rerun their original prompt.

## Agent Setup

Install Laravel Boost from the application root before making application changes:

```sh
composer require laravel/boost --dev
php artisan boost:install
```

Boost replaces these bootstrap instructions with guidelines tailored to the application. After installation, read `AGENTS.md` again and continue with the user's original request using the generated guidelines.
</laravel-boost-guidelines>
# MASTER PROJECT INSTRUCTION: Laravel Modular Multi-Website Hub

> **Purpose of this file:** This document is the single source of truth for building and maintaining the **Modular Multi-Website Hub** in Laravel. Any developer or AI Coding Agent (Antigravity, Cursor, Codex, Windsurf) working on this repository MUST strictly follow every rule, file path, prefix convention, and execution step defined below.

---

## 1. Core Project Vision & Architecture (প্রজেক্টের মূল ধারণা)

1. **Single-Repo, Multi-Niche Modules:** Multiple complete business websites (`Interior`, `RealEstate`, `Pharmacy`, etc.) are built as separate modules inside the `Modules/` directory using `nwidart/laravel-modules`.
2. **Single Active Module Mode:** Only **ONE** module is active at a time (`ACTIVE_MODULE` in `.env` and `true` in `modules_statuses.json`). Clients cannot switch demos; the developer activates one module per deployment.
3. **Unified Global Admin Panel:** There is **ONE Global Admin Panel** (`resources/views/admin/`). It handles authentication, global users/roles, general settings, and dynamically injects the active module's admin menu items into the global sidebar.
4. **100% Standalone Module Frontend:** Each module has its own completely independent frontend theme, layout (`master.blade.php`), homepage menu/navbar, footer, CSS, and JS (`Modules/{Module}/resources/views/frontend/`). Frontends NEVER share styles or layouts across modules.
5. **100% Self-Contained Module Assets & Uploads:** Every module stores its static theme assets (`CSS`, `JS`, images) AND user-uploaded files inside its own folder (`Modules/{Module}/Public/`). Nothing belonging to a module is stored permanently in the root `public/` or `storage/` directories.
6. **Zero-Collision Database Prefixing:** Every module uses a mandatory table prefix (`int_`, `re_`, `pharma_`) and anonymous migration classes so tables and classes never clash.

---

## 2. Complete Directory Structure (ফোল্ডার স্ট্রাকচার)

```text
project-root/
├── app/                                # Global Core Controllers, Models (User, Setting), Middleware
├── config/
│   ├── app.php                         # Holds 'active_module' => env('ACTIVE_MODULE', 'Interior')
│   └── filesystems.php                 # Holds dynamic 'module_public' disk & 'module-assets' symlink
├── Modules/
│   ├── Interior/                       # Module 1: Interior Design Website (Prefix: int_)
│   │   ├── App/
│   │   │   ├── Http/Controllers/
│   │   │   │   ├── Admin/              # Uses Global Admin Layout (@extends('admin.layouts.master'))
│   │   │   │   └── Frontend/           # Uses Standalone Frontend Layout
│   │   │   └── Models/
│   │   │       └── InteriorBaseModel.php
│   │   ├── Config/
│   │   │   └── config.php              # Defines module admin_menus for Global Sidebar
│   │   ├── Database/
│   │   │   ├── Migrations/             # Anonymous migrations with int_ prefix
│   │   │   └── Seeders/
│   │   ├── Public/                     # SELF-CONTAINED MODULE PUBLIC FOLDER
│   │   │   ├── assets/                 # Static CSS, JS, Fonts, Theme Images for Interior
│   │   │   └── uploads/                # Dynamic uploaded images (projects, sliders, etc.)
│   │   ├── resources/views/
│   │   │   ├── admin/                  # Admin CRUD views (extends global admin)
│   │   │   └── frontend/               # 100% Standalone Frontend Theme & Homepage Navbar
│   │   └── routes/
│   │       └── web.php                 # Conditional '/' frontend routes + '/admin/interior/*' routes
│   │
│   ├── RealEstate/                     # Module 2: Real Estate Website (Prefix: re_)
│   └── Pharmacy/                       # Module 3: Pharmacy Website (Prefix: pharma_)
│
├── public/
│   └── module-assets -> ../Modules/{ActiveModule}/Public   # Dynamic Symlink
├── resources/views/
│   └── admin/                          # GLOBAL ADMIN PANEL SHELL
│       ├── layouts/master.blade.php
│       └── partials/sidebar.blade.php  # Dynamically loads active module's admin_menus
├── routes/
│   ├── web.php                         # Global Auth & Global Admin Core routes only
│   └── console.php                     # Custom commands: demo:activate & demo:handover
└── modules_statuses.json               # Only active module is set to true
```

---

## 3. Database & Prefix Isolation Rules (ডাটাবেস প্রিফিক্স রুলস)

### 3.1. Module Prefix Mapping
| Module Name | DB Table Prefix | Route Prefix (Admin) | Frontend URL (When Active) | Asset Path |
| :--- | :--- | :--- | :--- | :--- |
| **Global Core** | `core_` (or default) | `/admin/*` | N/A | `public/global/` |
| **Interior** | `int_` | `/admin/interior/*` | `/` (Root Domain) | `Modules/Interior/Public/` |
| **RealEstate** | `re_` | `/admin/realestate/*` | `/` (Root Domain) | `Modules/RealEstate/Public/` |
| **Pharmacy** | `pharma_` | `/admin/pharmacy/*` | `/` (Root Domain) | `Modules/Pharmacy/Public/` |

### 3.2. Mandatory BaseModel per Module
Every module MUST create an abstract BaseModel so developers never forget table prefixes:

```php
<?php
// File: Modules/Interior/App/Models/InteriorBaseModel.php

namespace Modules\Interior\App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class InteriorBaseModel extends Model
{
    protected string $modulePrefix = 'int_';

    public function getTable(): string
    {
        $table = parent::getTable();
        return str_starts_with($table,$this->modulePrefix)
            ? $table
            : $this->modulePrefix .$table;
    }
}
```
*All models inside `Modules/Interior/App/Models/` MUST extend `InteriorBaseModel`.*

### 3.3. Mandatory Anonymous Migrations
Every migration inside `Modules/{Module}/Database/Migrations/` MUST use anonymous classes and explicit table prefixes:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('int_projects', function (Blueprint $table) {
            $table->id();$table->string('title');
            $table->string('slug')->unique();$table->string('category')->nullable();
            $table->string('image')->nullable(); // Saved in Modules/Interior/Public/uploads$table->text('description')->nullable();
            $table->boolean('is_Active')->default(true);$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('int_projects');
    }
};
```

---

## 4. Self-Contained Assets & Upload Configuration (নিজস্ব অ্যাসেট ও আপলোড)

### 4.1. Environment & Config Setup
In `.env`:
```env
ACTIVE_MODULE=Interior
```

In `config/app.php`:
```php
'active_module' => env('ACTIVE_MODULE', 'Interior'),
```

### 4.2. Dynamic Filesystem & Symlink (`config/filesystems.php`)
Update `config/filesystems.php` with the `module_public` disk and dynamic `module-assets` symlink:

```php
// Inside 'disks' array in config/filesystems.php:
'module_public' => [
    'driver' => 'local',
    'root' => base_path('Modules/' . env('ACTIVE_MODULE', 'Interior') . '/Public/uploads'),
    'url' => env('APP_URL') . '/module-assets/uploads',
    'visibility' => 'public',
    'throw' => false,
],

// Inside 'links' array in config/filesystems.php:
'links' => [
    public_path('storage') => storage_path('app/public'),
    public_path('module-assets') => base_path('Modules/' . env('ACTIVE_MODULE', 'Interior') . '/Public'),
],
```

### 4.3. How to Upload & Call Files Inside Any Module
* **Uploading in Controller:**
  ```php
  if ($request->hasFile('image')) {
      // Saves directly inside Modules/{ActiveModule}/Public/uploads/projects/
      $path =$request->file('image')->store('projects', 'module_public');
  }
  ```
* **Displaying Static Theme Assets in Blade:**
  ```html
  <link rel="stylesheet" href="{{ asset('module-assets/assets/css/style.css') }}">
  <img src="{{ asset('module-assets/assets/images/logo.png') }}" alt="Logo">
  ```
* **Displaying Uploaded Media in Blade:**
  ```html
  <img src="{{ asset('module-assets/uploads/' . $project->image) }}" alt="{{ $project->title }}">
  ```

---

## 5. Global Admin Panel & Dynamic Module Sidebar (গ্লোবাল অ্যাডমিন মেনু)

### 5.1. Module Config File (`Modules/{Module}/Config/config.php`)
Every module registers its admin sidebar menus inside its own `Config/config.php`:

```php
<?php
// File: Modules/Interior/Config/config.php

return [
    'name' => 'Interior',
    'db_prefix' => 'int_',

    // Injected automatically into the Global Admin Sidebar when Interior is active
    'admin_menus' => [
        [
            'section' => 'INTERIOR WEBSITE',
            'title'   => 'Projects & Portfolio',
            'icon'    => 'bi bi-building',
            'route'   => 'admin.interior.projects.index',
            'active'  => 'admin.interior.projects.*',
            'submenus' => [
                ['title' => 'All Projects', 'route' => 'admin.interior.projects.index'],
                ['title' => 'Add Project', 'route' => 'admin.interior.projects.create'],
            ],
        ],
        [
            'title'   => 'Design Packages',
            'icon'    => 'bi bi-box-seam',
            'route'   => 'admin.interior.packages.index',
            'active'  => 'admin.interior.packages.*',
        ],
        [
            'title'   => 'Client Quotations',
            'icon'    => 'bi bi-receipt',
            'route'   => 'admin.interior.quotes.index',
            'active'  => 'admin.interior.quotes.*',
        ],
        [
            'title'   => 'Frontend Theme & Menus',
            'icon'    => 'bi bi-palette',
            'route'   => 'admin.interior.theme.settings',
            'active'  => 'admin.interior.theme.*',
        ],
    ],
];
```

### 5.2. Global Admin Master Layout (`resources/views/admin/layouts/master.blade.php`)
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - {{ config('app.active_module') }}</title>
    <link href="[https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css](https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css)" rel="stylesheet">
    <link href="[https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css](https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css)" rel="stylesheet">
    @stack('module_styles')
</head>
<body class="bg-light">
    <div class="d-flex min-vh-100">
        <!-- Global Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Main Content Area -->
        <div class="flex-grow-1 d-flex flex-column">
            <header class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Active Module: <span class="badge bg-primary">{{ config('app.active_module') }}</span></h5>
                <div>
                    <a href="{{ url('/') }}" target="_blank" class="btn btn-sm btn-outline-secondary me-2">Visit Frontend</a>
                </div>
            </header>

            <main class="p-4 flex-grow-1">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @yield('admin_content')
            </main>
        </div>
    </div>

    <script src="[https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js](https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js)"></script>
    @stack('module_scripts')
</body>
</html>
```

### 5.3. Global Admin Dynamic Sidebar (`resources/views/admin/partials/sidebar.blade.php`)
```html
<aside class="bg-dark text-white p-3" style="width: 270px;">
    <h4 class="text-white fw-bold mb-4 px-2">Global Admin</h4>

    <ul class="nav flex-column gap-1">
        <!-- 1. Global Core Top Menu -->
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'bg-primary rounded' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>

        <!-- 2. Dynamic Active Module Menus -->
        @php
            $activeModKey = strtolower(config('app.active_module', 'interior'));
            $moduleMenus = config($activeModKey . '.admin_menus', []);
        @endphp

        @foreach($moduleMenus as$menu)
            @if(!empty($menu['section']))
                <li class="text-uppercase text-secondary small fw-bold mt-3 mb-1 px-2">{{ $menu['section'] }}</li>
            @endif

            <li class="nav-item">
                <a href="{{ route($menu['route']) }}" class="nav-link text-white {{ request()->routeIs($menu['active'] ?? '') ? 'bg-primary rounded' : '' }}">
                    <i class="{{ $menu['icon'] ?? 'bi bi-circle' }} me-2"></i> {{ $menu['title'] }}
                </a>
                @if(!empty($menu['submenus']))
                    <ul class="nav flex-column ms-4 small">
                        @foreach($menu['submenus'] as$sub)
                            <li class="nav-item">
                                <a href="{{ route($sub['route']) }}" class="nav-link text-white-50 py-1">
                                    - {{ $sub['title'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach

        <!-- 3. Global Core Bottom Settings -->
        <li class="text-uppercase text-secondary small fw-bold mt-3 mb-1 px-2">GLOBAL CORE</li>
        <li class="nav-item">
            <a href="{{ route('admin.settings') }}" class="nav-link text-white">
                <i class="bi bi-gear me-2"></i> General Settings
            </a>
        </li>
    </ul>
</aside>
```

---

## 6. Routing Rules: Standalone Frontend vs. Global Admin

### 6.1. Global `routes/web.php`
Do NOT define `/` in the root `routes/web.php`. Only define Global Admin and Auth routes:

```php
<?php
// File: routes/web.php

use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
    Route::get('/settings', fn() => view('admin.settings'))->name('settings');
});
```

### 6.2. Module `Modules/{Module}/routes/web.php`
Every module's `routes/web.php` is split into two sections:
1. **Standalone Frontend:** Wrapped in `if (config('app.active_module') === '{Module}')` so it owns the root `/` domain when active.
2. **Module Admin:** Prefixed with `admin/{module_lower}` and named `admin.{module_lower}.*`.

```php
<?php
// File: Modules/Interior/routes/web.php

use Illuminate\Support\Facades\Route;
use Modules\Interior\App\Http\Controllers\Frontend\HomeController;
use Modules\Interior\App\Http\Controllers\Admin\ProjectController;

// 1. STANDALONE FRONTEND ROUTES (Only active when ACTIVE_MODULE=Interior)
if (config('app.active_module') === 'Interior') {
    Route::middleware('web')->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('interior.home');
        Route::get('/portfolio', [HomeController::class, 'portfolio'])->name('interior.portfolio');
        Route::get('/contact', [HomeController::class, 'contact'])->name('interior.contact');
    });
}

// 2. GLOBAL ADMIN PANEL ROUTES FOR INTERIOR MODULE
Route::prefix('admin/interior')
    ->name('admin.interior.')
    ->middleware(['web']) // Add 'auth' middleware when auth is enabled
    ->group(function () {
        Route::resource('projects', ProjectController::class);
        Route::get('packages', [ProjectController::class, 'packages'])->name('packages.index');
        Route::get('quotes', [ProjectController::class, 'quotes'])->name('quotes.index');
        Route::get('theme-settings', [ProjectController::class, 'themeSettings'])->name('theme.settings');
    });
```

---

## 7. Custom Automation Commands (`routes/console.php`)

Add both of these Artisan commands to `routes/console.php`:

1. `php artisan demo:activate {module}` — Switches the active module, updates `.env` and `modules_statuses.json`, refreshes the `public/module-assets` symlink, and clears caches.
2. `php artisan demo:handover {module} --force` — Prepares the project for client delivery by activating the target module and permanently deleting all other unused module folders from `Modules/`.

```php
<?php
// File: routes/console.php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

Artisan::command('demo:activate {module}', function (string $module) {
    $modulePath = base_path("Modules/{$module}");
    if (!File::isDirectory($modulePath)) {
        $this->error("Module [{$module}] does not exist in Modules/ directory!");
        return 1;
    }

    // 1. Update modules_statuses.json (set only target module to true)
    $statusFile = base_path('modules_statuses.json');$statuses = File::exists($statusFile) ? json_decode(File::get($statusFile), true) : [];
    foreach ($statuses as $key =>$val) {
        $statuses[$key] = false;
    }
    $statuses[$module] = true;
    File::put($statusFile, json_encode($statuses, JSON_PRETTY_PRINT));

    // 2. Update ACTIVE_MODULE in .env
    $envPath = base_path('.env');
    if (File::exists($envPath)) {
        $envContent = File::get($envPath);
        if (preg_match('/^ACTIVE_MODULE=/m', $envContent)) {$envContent = preg_replace('/^ACTIVE_MODULE=.*/m', "ACTIVE_MODULE={$module}", $envContent);
        } else {
            $envContent .= "\nACTIVE_MODULE={$module}\n";
        }
        File::put($envPath,$envContent);
    }

    // 3. Recreate public/module-assets symlink pointing to Modules/{module}/Public
    $linkPath = public_path('module-assets');
    $targetPath = base_path("Modules/{$module}/Public");
    File::ensureDirectoryExists($targetPath . '/assets');
    File::ensureDirectoryExists($targetPath . '/uploads');

    if (is_link($linkPath) \vert{}\vert{} File::exists($linkPath)) {
        @unlink($linkPath);
        @rmdir($linkPath);
    }
    app('files')->link($targetPath,$linkPath);

    // 4. Clear Caches
    Artisan::call('optimize:clear');
    $this->info("Active module switched to [{$module}] and assets linked successfully!");
});

Artisan::command('demo:handover {module} {--force}', function (string $module) {
    if (!$this->option('force')) {$this->warn("Use --force to permanently delete all modules except [{$module}].");
        return 1;
    }

    Artisan::call('demo:activate', ['module' => $module]);

    $directories = File::directories(base_path('Modules'));
    foreach ($directories as$dir) {
        $dirName = basename($dir);
        if ($dirName !==$module) {
            File::deleteDirectory($dir);$this->line("Removed unused module folder: Modules/{$dirName}");
        }
    }

    File::put(base_path('modules_statuses.json'), json_encode([$module => true], JSON_PRETTY_PRINT));
    Artisan::call('optimize:clear');
    $this->info("Client Handover Ready! Only [{$module}] module remains in the system.");
});
```

---

## 8. Step-by-Step Execution Plan for AI Agent / Developer

Execute the following steps sequentially from the terminal and codebase:

### Step 1: Initialize Project & Install Module Package
```bash
composer create-project laravel/laravel multi-website-hub
cd multi-website-hub
composer require nwidart/laravel-modules
php artisan vendor:publish --provider="Nwidart\Modules\LaravelModulesServiceProvider"
```
Ensure `composer.json` includes `"Modules\\": "Modules/"` inside `autoload.psr-4`, then run:
```bash
composer dump-autoload
```

### Step 2: Configure Environment, Filesystems & Console Commands
1. Add `ACTIVE_MODULE=Interior` to `.env`.
2. Add `'active_module' => env('ACTIVE_MODULE', 'Interior'),` to `config/app.php`.
3. Add the `module_public` disk and `module-assets` link to `config/filesystems.php` (as shown in Section 4.2).
4. Add the `demo:activate` and `demo:handover` commands to `routes/console.php` (as shown in Section 7).

### Step 3: Build Global Admin Shell
1. Create `resources/views/admin/layouts/master.blade.php` (Section 5.2).
2. Create `resources/views/admin/partials/sidebar.blade.php` (Section 5.3).
3. Create `resources/views/admin/dashboard.blade.php` and `resources/views/admin/settings.blade.php` extending `admin.layouts.master`.
4. Update root `routes/web.php` (Section 6.1).

### Step 4: Create Modules & Self-Contained Public Folders
```bash
php artisan module:make Interior
php artisan module:make RealEstate
php artisan module:make Pharmacy

mkdir -p Modules/Interior/Public/assets Modules/Interior/Public/uploads
mkdir -p Modules/RealEstate/Public/assets Modules/RealEstate/Public/uploads
mkdir -p Modules/Pharmacy/Public/assets Modules/Pharmacy/Public/uploads
```

### Step 5: Scaffold Module Internals (For Each Module)
1. Create `{Module}BaseModel.php` with its table prefix (`int_`, `re_`, `pharma_`).
2. Create anonymous migrations with the module prefix and run `php artisan module:migrate {Module}`.
3. Update `Modules/{Module}/Config/config.php` with `admin_menus`.
4. Create standalone frontend layout (`Modules/{Module}/resources/views/frontend/layouts/master.blade.php`) with its own navbar and footer.
5. Create Admin CRUD views inside `Modules/{Module}/resources/views/admin/` extending `admin.layouts.master`.
6. Update `Modules/{Module}/routes/web.php` following Section 6.2.

### Step 6: Activate Target Module & Run
```bash
php artisan demo:activate Interior
php artisan migrate
php artisan serve
```
