<?php

use App\Http\Middleware\EnsureModuleIsActive;
use Illuminate\Support\Facades\Route;
use Modules\EventManagement\App\Http\Controllers\Admin\EventManagementAdminController;
use Modules\EventManagement\App\Http\Controllers\Frontend\HomeController;

// Self-contained static theme assets: Modules/EventManagement/Public/assets/{css|js}/{file}
Route::get('eventmanagement-assets/{type}/{file}', [HomeController::class, 'asset'])
    ->where(['type' => 'css|js', 'file' => '[A-Za-z0-9_\-\.]+'])
    ->name('eventmanagement.asset');

// Public Frontend Routes for EventManagement (Supports both Single-Module Root Mode & Multi-Module Hub Mode)
Route::prefix('event-management')->name('eventmanagement.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::post('/book-ticket', [HomeController::class, 'submitBooking'])->middleware('throttle:10,1')->name('book.submit');
    Route::get('/sitemap.xml', [HomeController::class, 'sitemap'])->name('sitemap');
});

// GLOBAL ADMIN PANEL ROUTES FOR EVENTMANAGEMENT MODULE
Route::prefix('admin/eventmanagement')
    ->name('admin.eventmanagement.')
    ->middleware(['web', EnsureModuleIsActive::class . ':EventManagement'])
    ->group(function () {
        // 1. Dashboard
        Route::get('dashboard', [EventManagementAdminController::class, 'dashboard'])->name('dashboard');

        // 2. Event Speakers CRUD (em_speakers)
        Route::get('speakers', [EventManagementAdminController::class, 'speakers'])->name('speakers.index');
        Route::post('speakers', [EventManagementAdminController::class, 'storeSpeaker'])->name('speakers.store');
        Route::put('speakers/{id}', [EventManagementAdminController::class, 'updateSpeaker'])->name('speakers.update');
        Route::delete('speakers/{id}', [EventManagementAdminController::class, 'deleteSpeaker'])->name('speakers.destroy');

        // 3. Event Schedule Plan CRUD (em_schedules)
        Route::get('schedules', [EventManagementAdminController::class, 'schedules'])->name('schedules.index');
        Route::post('schedules', [EventManagementAdminController::class, 'storeSchedule'])->name('schedules.store');
        Route::put('schedules/{id}', [EventManagementAdminController::class, 'updateSchedule'])->name('schedules.update');
        Route::delete('schedules/{id}', [EventManagementAdminController::class, 'deleteSchedule'])->name('schedules.destroy');

        // 4. Ticket Pricing Plans CRUD (em_pricing_plans)
        Route::get('pricing', [EventManagementAdminController::class, 'pricing'])->name('pricing.index');
        Route::post('pricing', [EventManagementAdminController::class, 'storePricing'])->name('pricing.store');
        Route::put('pricing/{id}', [EventManagementAdminController::class, 'updatePricing'])->name('pricing.update');
        Route::delete('pricing/{id}', [EventManagementAdminController::class, 'deletePricing'])->name('pricing.destroy');

        // 5. Sponsors & Companies CRUD (em_sponsors)
        Route::get('sponsors', [EventManagementAdminController::class, 'sponsors'])->name('sponsors.index');
        Route::post('sponsors', [EventManagementAdminController::class, 'storeSponsor'])->name('sponsors.store');
        Route::put('sponsors/{id}', [EventManagementAdminController::class, 'updateSponsor'])->name('sponsors.update');
        Route::delete('sponsors/{id}', [EventManagementAdminController::class, 'deleteSponsor'])->name('sponsors.destroy');

        // 6. Recent Memories & Gallery CRUD (em_memories)
        Route::get('memories', [EventManagementAdminController::class, 'memories'])->name('memories.index');
        Route::post('memories', [EventManagementAdminController::class, 'storeMemory'])->name('memories.store');
        Route::put('memories/{id}', [EventManagementAdminController::class, 'updateMemory'])->name('memories.update');
        Route::delete('memories/{id}', [EventManagementAdminController::class, 'deleteMemory'])->name('memories.destroy');

        // 7. Ticket Bookings / Attendee Registrations (em_ticket_bookings)
        Route::get('bookings', [EventManagementAdminController::class, 'bookings'])->name('bookings.index');
        Route::post('bookings', [EventManagementAdminController::class, 'storeBooking'])->name('bookings.store');
        Route::put('bookings/{id}', [EventManagementAdminController::class, 'updateBooking'])->name('bookings.update');
        Route::delete('bookings/{id}', [EventManagementAdminController::class, 'deleteBooking'])->name('bookings.destroy');

        // 8. Hero, Countdown & Venue Settings (em_settings)
        Route::get('settings', [EventManagementAdminController::class, 'settings'])->name('settings');
        Route::post('settings', [EventManagementAdminController::class, 'saveSettings'])->name('settings.save');
        Route::post('settings/reset', [EventManagementAdminController::class, 'resetSettings'])->name('settings.reset');
    });
