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
    ->where('subpage', 'portfolio|properties|catalog|contact|about|services|pricing|faq|service-[a-z\-]+')
    ->name('hub.single.subpage');
Route::get('/{slug}/{subpage?}', [FrontendHubController::class, 'moduleRoute'])
    ->whereIn('slug', ['interior-design', 'real-estate', 'pharmacy', 'software-company'])
    ->name('hub.module');

// Global Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token?}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
Route::get('/verify-email', [AuthController::class, 'showVerifyEmail'])->name('verification.notice');
Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])->name('verification.send');
Route::get('/two-factor', [AuthController::class, 'showTwoFactor'])->name('two-factor.login');
Route::post('/two-factor', [AuthController::class, 'verifyTwoFactor'])->name('two-factor.verify');
Route::get('/confirm-password', [AuthController::class, 'showConfirmPassword'])->name('password.confirm');
Route::post('/confirm-password', [AuthController::class, 'confirmPassword'])->name('password.confirm.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout']);

// Global Admin Core Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn () => redirect()->route('admin.dashboard'));
    Route::get('/dashboard', [GlobalAdminController::class, 'dashboard'])->name('dashboard');

    // Websites Management
    Route::get('/websites', [GlobalAdminController::class, 'websites'])->name('websites');
    Route::post('/websites', [GlobalAdminController::class, 'storeWebsite'])->name('websites.store');
    Route::post('/websites/{website}/toggle', [GlobalAdminController::class, 'toggleWebsite'])->name('websites.toggle');
    Route::post('/websites/{website}/reset', [GlobalAdminController::class, 'resetWebsite'])->name('websites.reset');
    Route::delete('/websites/{website}', [GlobalAdminController::class, 'deleteWebsite'])->name('websites.destroy');

    // 1. Standalone Per-Module Pages Management (int_pages / re_pages / pharma_pages)
    Route::get('/pages', [ModuleCmsController::class, 'pages'])->name('pages');
    Route::post('/pages', [ModuleCmsController::class, 'storePage'])->name('pages.store');
    Route::delete('/pages/{id}', [ModuleCmsController::class, 'deletePage'])->name('pages.destroy');

    // 2. Standalone Per-Module Menu Builder (int_menus / re_menus / pharma_menus)
    Route::get('/menu-builder', [ModuleCmsController::class, 'menus'])->name('menu-builder');
    Route::post('/menu-builder', [ModuleCmsController::class, 'storeMenu'])->name('menu-builder.store');
    Route::delete('/menu-builder/{id}', [ModuleCmsController::class, 'deleteMenu'])->name('menu-builder.destroy');

    // 3. Standalone Per-Module Slider (int_sliders / re_sliders / pharma_sliders)
    Route::get('/slider', [ModuleCmsController::class, 'sliders'])->name('slider');
    Route::post('/slider', [ModuleCmsController::class, 'storeSlider'])->name('slider.store');
    Route::delete('/slider/{id}', [ModuleCmsController::class, 'deleteSlider'])->name('slider.destroy');

    // 4. Standalone Per-Module Media Library (int_media / re_media / pharma_media)
    Route::get('/media-library', [ModuleCmsController::class, 'media'])->name('media-library');
    Route::post('/media-library', [ModuleCmsController::class, 'storeMedia'])->name('media-library.store');
    Route::delete('/media-library/{id}', [ModuleCmsController::class, 'deleteMedia'])->name('media-library.destroy');

    // 4B. Standalone Per-Module Video Gallery (int_videos / re_videos / pharma_videos)
    Route::get('/video-gallery', [ModuleCmsController::class, 'videos'])->name('video-gallery');
    Route::post('/video-gallery', [ModuleCmsController::class, 'storeVideo'])->name('video-gallery.store');
    Route::delete('/video-gallery/{id}', [ModuleCmsController::class, 'deleteVideo'])->name('video-gallery.destroy');

    // 5. Standalone Per-Module Themes (int_themes / re_themes / pharma_themes)
    Route::get('/themes', [ModuleCmsController::class, 'themes'])->name('themes');
    Route::post('/themes', [ModuleCmsController::class, 'storeTheme'])->name('themes.store');
    Route::post('/themes/{id}/activate', [ModuleCmsController::class, 'activateTheme'])->name('themes.activate');
    Route::delete('/themes/{id}', [ModuleCmsController::class, 'deleteTheme'])->name('themes.destroy');

    // 6. Standalone Per-Module Users + Roles & Permissions (int_users / re_users / pharma_users)
    Route::get('/users', [ModuleCmsController::class, 'users'])->name('users');
    Route::post('/users', [ModuleCmsController::class, 'storeModuleUser'])->name('users.store');
    Route::put('/users/{id}', [ModuleCmsController::class, 'updateModuleUser'])->name('users.update');
    Route::delete('/users/{id}', [ModuleCmsController::class, 'deleteModuleUser'])->name('users.destroy');
    Route::post('/roles', [GlobalAdminController::class, 'storeRole'])->name('roles.store');
    Route::put('/roles/{role}/permissions', [GlobalAdminController::class, 'updateRolePermissions'])->name('roles.permissions');

    // Transactions & Filter Table (Screenshot 8)
    Route::get('/transactions', [GlobalAdminController::class, 'transactions'])->name('transactions');

    // 7. Standalone Per-Module Settings + Profile & Password Change (int_settings / re_settings / pharma_settings)
    Route::get('/settings', [ModuleCmsController::class, 'settings'])->name('settings');
    Route::post('/settings/module', [ModuleCmsController::class, 'updateModuleSettings'])->name('settings.module');
    Route::post('/settings/profile', [GlobalAdminController::class, 'updateProfile'])->name('settings.profile');
    Route::post('/settings/password', [GlobalAdminController::class, 'updatePassword'])->name('settings.password');
    Route::post('/settings/global', [GlobalAdminController::class, 'updateGlobalSettings'])->name('settings.global');
});
