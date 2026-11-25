<?php

use App\Http\Middleware\EnsureModuleIsActive;
use Illuminate\Support\Facades\Route;
use Modules\WonderLearn\App\Http\Controllers\Admin\WonderLearnAdminController;
use Modules\WonderLearn\App\Http\Controllers\Frontend\HomeController;

// Self-contained static theme assets: Modules/WonderLearn/Public/assets/{css|js}/{file}
Route::get('wonderlearn-assets/{type}/{file}', [HomeController::class, 'asset'])
    ->where(['type' => 'css|js', 'file' => '[A-Za-z0-9_\-\.]+'])
    ->name('wonderlearn.asset');

// Public Frontend Routes for WonderLearn (Supports both Single-Module Root Mode & Multi-Module Hub Mode)
Route::prefix('wonder-learn')->name('wonderlearn.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::post('/join', [HomeController::class, 'submitEnrollment'])->middleware('throttle:10,1')->name('join.submit');
    Route::post('/newsletter', [HomeController::class, 'submitNewsletter'])->middleware('throttle:10,1')->name('newsletter.submit');
    Route::get('/sitemap.xml', [HomeController::class, 'sitemap'])->name('sitemap');
});

// GLOBAL ADMIN PANEL ROUTES FOR WONDERLEARN MODULE
Route::prefix('admin/wonderlearn')
    ->name('admin.wonderlearn.')
    ->middleware(['web', EnsureModuleIsActive::class . ':WonderLearn'])
    ->group(function () {
        // 1. Dashboard
        Route::get('dashboard', [WonderLearnAdminController::class, 'dashboard'])->name('dashboard');

        // 2. Explore by Topic CRUD (wl_topics)
        Route::get('topics', [WonderLearnAdminController::class, 'topics'])->name('topics.index');
        Route::post('topics', [WonderLearnAdminController::class, 'storeTopic'])->name('topics.store');
        Route::put('topics/{id}', [WonderLearnAdminController::class, 'updateTopic'])->name('topics.update');
        Route::delete('topics/{id}', [WonderLearnAdminController::class, 'deleteTopic'])->name('topics.destroy');

        // 3. Magical Stories CRUD (wl_stories)
        Route::get('stories', [WonderLearnAdminController::class, 'stories'])->name('stories.index');
        Route::post('stories', [WonderLearnAdminController::class, 'storeStory'])->name('stories.store');
        Route::put('stories/{id}', [WonderLearnAdminController::class, 'updateStory'])->name('stories.update');
        Route::delete('stories/{id}', [WonderLearnAdminController::class, 'deleteStory'])->name('stories.destroy');

        // 4. Play & Learn Games CRUD (wl_games)
        Route::get('games', [WonderLearnAdminController::class, 'games'])->name('games.index');
        Route::post('games', [WonderLearnAdminController::class, 'storeGame'])->name('games.store');
        Route::put('games/{id}', [WonderLearnAdminController::class, 'updateGame'])->name('games.update');
        Route::delete('games/{id}', [WonderLearnAdminController::class, 'deleteGame'])->name('games.destroy');

        // 5. Featured Adventures CRUD (wl_adventures)
        Route::get('adventures', [WonderLearnAdminController::class, 'adventures'])->name('adventures.index');
        Route::post('adventures', [WonderLearnAdminController::class, 'storeAdventure'])->name('adventures.store');
        Route::put('adventures/{id}', [WonderLearnAdminController::class, 'updateAdventure'])->name('adventures.update');
        Route::delete('adventures/{id}', [WonderLearnAdminController::class, 'deleteAdventure'])->name('adventures.destroy');

        // 6. Loved by Parents Testimonials CRUD (wl_testimonials)
        Route::get('testimonials', [WonderLearnAdminController::class, 'testimonials'])->name('testimonials.index');
        Route::post('testimonials', [WonderLearnAdminController::class, 'storeTestimonial'])->name('testimonials.store');
        Route::put('testimonials/{id}', [WonderLearnAdminController::class, 'updateTestimonial'])->name('testimonials.update');
        Route::delete('testimonials/{id}', [WonderLearnAdminController::class, 'deleteTestimonial'])->name('testimonials.destroy');

        // 7. Kid & Parent Enrollments + Subscribers (wl_enrollments & wl_subscribers)
        Route::get('enrollments', [WonderLearnAdminController::class, 'enrollments'])->name('enrollments.index');
        Route::post('enrollments', [WonderLearnAdminController::class, 'storeEnrollment'])->name('enrollments.store');
        Route::put('enrollments/{id}', [WonderLearnAdminController::class, 'updateEnrollment'])->name('enrollments.update');
        Route::delete('enrollments/{id}', [WonderLearnAdminController::class, 'deleteEnrollment'])->name('enrollments.destroy');
        Route::delete('subscribers/{id}', [WonderLearnAdminController::class, 'deleteSubscriber'])->name('subscribers.destroy');

        // 8. Theme & Hero Settings (wl_settings)
        Route::get('settings', [WonderLearnAdminController::class, 'settings'])->name('settings');
        Route::post('settings', [WonderLearnAdminController::class, 'saveSettings'])->name('settings.save');
        Route::post('settings/reset', [WonderLearnAdminController::class, 'resetSettings'])->name('settings.reset');
    });
