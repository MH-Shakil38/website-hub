<?php

use App\Http\Controllers\Admin\GlobalAdminController;
use App\Http\Controllers\Admin\ModuleCmsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FrontendHubController;
use Illuminate\Support\Facades\Route;

// Serve Isolated Module Uploaded Media directly from Modules/{Module}/Public/uploads/{folder}/{filename}
Route::get('/module-media/{module}/{folder}/{filename}', [ModuleCmsController::class, 'serveModuleMedia'])
    ->name('module.media.serve');

// Dynamic Frontend Hub Routes (Single Active Module at '/' OR Multi-Module Showcase Hub + '/{slug}')
Route::get('/', [FrontendHubController::class, 'index'])->name('hub.home');
Route::get('/{subpage}', [FrontendHubController::class, 'singleModuleSubpage'])
    ->where('subpage', 'portfolio|projects|blog|properties|catalog|contact|about|services|pricing|faq|sitemap\.xml|service-[a-z0-9\-]+|project-[a-z0-9\-]+|article-[a-z0-9\-]+')
    ->name('hub.single.subpage');
Route::get('/{slug}/{subpage?}', [FrontendHubController::class, 'moduleRoute'])
    ->whereIn('slug', ['interior-design', 'real-estate', 'pharmacy', 'software-company', 'fertility-clinic', 'clinic', 'developer-portfolio', 'portfolio'])
    ->name('hub.module');

// Global Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.submit');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:5,1')->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/verify-email', [AuthController::class, 'showVerifyEmail'])->name('verification.notice');
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])->middleware('throttle:3,1')->name('verification.send');
    Route::get('/two-factor', [AuthController::class, 'showTwoFactor'])->name('two-factor.login');
    Route::post('/two-factor', [AuthController::class, 'verifyTwoFactor'])->middleware('throttle:5,1')->name('two-factor.verify');
    Route::get('/confirm-password', [AuthController::class, 'showConfirmPassword'])->name('password.confirm');
    Route::post('/confirm-password', [AuthController::class, 'confirmPassword'])->middleware('throttle:5,1')->name('password.confirm.submit');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout']);

// Global Admin Core Routes (login required + active account; each section also needs its permission)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', fn () => redirect()->route('admin.dashboard'));
    Route::get('/dashboard', [GlobalAdminController::class, 'dashboard'])->name('dashboard');

    // Websites Management
    Route::middleware('admin:manage_websites')->group(function () {
        Route::get('/websites', [GlobalAdminController::class, 'websites'])->name('websites');
        Route::post('/websites', [GlobalAdminController::class, 'storeWebsite'])->name('websites.store');
        Route::post('/websites/{website}/toggle', [GlobalAdminController::class, 'toggleWebsite'])->name('websites.toggle');
        Route::post('/websites/{website}/reset', [GlobalAdminController::class, 'resetWebsite'])->name('websites.reset');
        Route::delete('/websites/{website}', [GlobalAdminController::class, 'deleteWebsite'])->name('websites.destroy');
    });

    // 1. Pages
    Route::middleware('admin:manage_pages')->group(function () {
        Route::get('/pages', [ModuleCmsController::class, 'pages'])->name('pages');
        Route::post('/pages', [ModuleCmsController::class, 'storePage'])->name('pages.store');
        Route::delete('/pages/{id}', [ModuleCmsController::class, 'deletePage'])->name('pages.destroy');
    });

    // 2. Menu Builder
    Route::middleware('admin:manage_menus')->group(function () {
        Route::get('/menu-builder', [ModuleCmsController::class, 'menus'])->name('menu-builder');
        Route::post('/menu-builder', [ModuleCmsController::class, 'storeMenu'])->name('menu-builder.store');
        Route::delete('/menu-builder/{id}', [ModuleCmsController::class, 'deleteMenu'])->name('menu-builder.destroy');
    });

    // 3. Slider
    Route::middleware('admin:manage_sliders')->group(function () {
        Route::get('/slider', [ModuleCmsController::class, 'sliders'])->name('slider');
        Route::post('/slider', [ModuleCmsController::class, 'storeSlider'])->name('slider.store');
        Route::delete('/slider/{id}', [ModuleCmsController::class, 'deleteSlider'])->name('slider.destroy');
    });

    // 4. Media Library + 4B. Video Gallery
    Route::middleware('admin:manage_media')->group(function () {
        Route::get('/media-library', [ModuleCmsController::class, 'media'])->name('media-library');
        Route::post('/media-library', [ModuleCmsController::class, 'storeMedia'])->name('media-library.store');
        Route::delete('/media-library/{id}', [ModuleCmsController::class, 'deleteMedia'])->name('media-library.destroy');

        Route::get('/video-gallery', [ModuleCmsController::class, 'videos'])->name('video-gallery');
        Route::post('/video-gallery', [ModuleCmsController::class, 'storeVideo'])->name('video-gallery.store');
        Route::delete('/video-gallery/{id}', [ModuleCmsController::class, 'deleteVideo'])->name('video-gallery.destroy');
    });

    // 5. Themes
    Route::middleware('admin:manage_themes')->group(function () {
        Route::get('/themes', [ModuleCmsController::class, 'themes'])->name('themes');
        Route::post('/themes', [ModuleCmsController::class, 'storeTheme'])->name('themes.store');
        Route::post('/themes/{id}/activate', [ModuleCmsController::class, 'activateTheme'])->name('themes.activate');
        Route::delete('/themes/{id}', [ModuleCmsController::class, 'deleteTheme'])->name('themes.destroy');
    });

    // 6. Users + Roles & Permissions
    Route::middleware('admin:manage_users')->group(function () {
        Route::get('/users', [ModuleCmsController::class, 'users'])->name('users');
        Route::post('/users', [ModuleCmsController::class, 'storeModuleUser'])->name('users.store');
        Route::put('/users/{id}', [ModuleCmsController::class, 'updateModuleUser'])->name('users.update');
        Route::delete('/users/{id}', [ModuleCmsController::class, 'deleteModuleUser'])->name('users.destroy');
    });
    Route::middleware('admin:manage_roles')->group(function () {
        Route::post('/roles', [GlobalAdminController::class, 'storeRole'])->name('roles.store');
        Route::put('/roles/{role}/permissions', [GlobalAdminController::class, 'updateRolePermissions'])->name('roles.permissions');
    });

    // Transactions
    Route::get('/transactions', [GlobalAdminController::class, 'transactions'])->middleware('admin:manage_settings')->name('transactions');

    // 7. Settings: own profile & password for everyone; site/global settings need manage_settings
    Route::get('/settings', [ModuleCmsController::class, 'settings'])->name('settings');
    Route::post('/settings/profile', [GlobalAdminController::class, 'updateProfile'])->name('settings.profile');
    Route::post('/settings/password', [GlobalAdminController::class, 'updatePassword'])->name('settings.password');
    Route::middleware('admin:manage_settings')->group(function () {
        Route::post('/settings/module', [ModuleCmsController::class, 'updateModuleSettings'])->name('settings.module');
        Route::post('/settings/global', [GlobalAdminController::class, 'updateGlobalSettings'])->name('settings.global');
    });
});
