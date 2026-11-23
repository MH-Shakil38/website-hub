<?php

use Illuminate\Support\Facades\Route;
use Modules\IvfCenter\App\Http\Controllers\Admin\AdminController;
use Modules\IvfCenter\App\Http\Controllers\Frontend\HomeController;

// 1. STANDALONE FRONTEND ROUTES (Only in legacy single-module mode when active module is IvfCenter)
if (config('app.mode') === 'single' && config('app.active_module') === 'IvfCenter') {
    Route::middleware('web')->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('ivfcenter.home');
        Route::post('/book-consultation', [HomeController::class, 'bookConsultation'])->name('ivfcenter.consultation.submit');
    });
}

// 2. STATIC ASSETS SERVING ROUTE (For CSS / JS)
Route::get('ivfcenter-assets/{type}/{file}', [HomeController::class, 'asset'])
    ->where(['type' => 'css|js', 'file' => '[A-Za-z0-9_\-\.]+'])
    ->name('ivfcenter.asset');

// Self-contained uploaded/theme media: Modules/IvfCenter/Public/uploads/{folder}/{file}
Route::get('ivfcenter-media/{folder}/{file}', [HomeController::class, 'media'])
    ->where(['folder' => '[A-Za-z0-9_\-]+', 'file' => '[A-Za-z0-9_\-\.]+'])
    ->name('ivfcenter.media');

// Consultation Submission Route (available always)
Route::post('ivfcenter/book-consultation', [HomeController::class, 'bookConsultation'])
    ->middleware('web')
    ->name('ivfcenter.consultation.post');

// 3. GLOBAL ADMIN PANEL ROUTES FOR IVF CENTER MODULE
Route::prefix('admin/ivfcenter')
    ->name('admin.ivfcenter.')
    ->middleware(['web'])
    ->group(function () {
        Route::get('consultations', [AdminController::class, 'consultations'])->name('consultations');
        Route::delete('consultations/{id}', [AdminController::class, 'destroyConsultation'])->name('consultations.destroy');
        Route::get('settings', [AdminController::class, 'settings'])->name('settings');
        Route::post('settings', [AdminController::class, 'saveSettings'])->name('settings.save');
    });
