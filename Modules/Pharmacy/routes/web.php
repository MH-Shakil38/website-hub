<?php

use App\Http\Middleware\EnsureModuleIsActive;
use Illuminate\Support\Facades\Route;
use Modules\Pharmacy\App\Http\Controllers\Admin\MedicineController;

// GLOBAL ADMIN PANEL ROUTES FOR PHARMACY MODULE (Guarded by EnsureModuleIsActive)
Route::prefix('admin/pharmacy')
    ->name('admin.pharmacy.')
    ->middleware(['web', EnsureModuleIsActive::class . ':Pharmacy'])
    ->group(function () {
        Route::resource('medicines', MedicineController::class);
        Route::get('orders', [MedicineController::class, 'orders'])->name('orders.index');
        Route::get('theme-settings', [MedicineController::class, 'themeSettings'])->name('theme.settings');
    });
