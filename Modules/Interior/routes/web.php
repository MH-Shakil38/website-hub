<?php

use App\Http\Middleware\EnsureModuleIsActive;
use Illuminate\Support\Facades\Route;
use Modules\Interior\App\Http\Controllers\Admin\ProjectController;

// GLOBAL ADMIN PANEL ROUTES FOR INTERIOR MODULE (Guarded by EnsureModuleIsActive)
Route::prefix('admin/interior')
    ->name('admin.interior.')
    ->middleware(['web', EnsureModuleIsActive::class . ':Interior'])
    ->group(function () {
        Route::resource('projects', ProjectController::class);
        Route::get('packages', [ProjectController::class, 'packages'])->name('packages.index');
        Route::get('quotes', [ProjectController::class, 'quotes'])->name('quotes.index');
        Route::get('theme-settings', [ProjectController::class, 'themeSettings'])->name('theme.settings');
        Route::post('theme-settings', [ProjectController::class, 'saveThemeSettings'])->name('theme.settings.save');
        Route::post('theme-settings/quick-toggle', [ProjectController::class, 'quickToggleServiceSettings'])->name('theme.settings.quick-toggle');
        Route::post('theme-settings/reset', [ProjectController::class, 'resetThemeSettings'])->name('theme.settings.reset');
    });
