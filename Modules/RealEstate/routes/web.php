<?php

use App\Http\Middleware\EnsureModuleIsActive;
use Illuminate\Support\Facades\Route;
use Modules\RealEstate\App\Http\Controllers\Admin\PropertyController;

// GLOBAL ADMIN PANEL ROUTES FOR REALESTATE MODULE (Guarded by EnsureModuleIsActive)
Route::prefix('admin/realestate')
    ->name('admin.realestate.')
    ->middleware(['web', EnsureModuleIsActive::class . ':RealEstate'])
    ->group(function () {
        Route::resource('properties', PropertyController::class);
        Route::get('inquiries', [PropertyController::class, 'inquiries'])->name('inquiries.index');
        Route::get('theme-settings', [PropertyController::class, 'themeSettings'])->name('theme.settings');
    });
