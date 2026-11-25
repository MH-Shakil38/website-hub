<?php

use App\Http\Middleware\EnsureModuleIsActive;
use Illuminate\Support\Facades\Route;
use Modules\GymFitness\App\Http\Controllers\Admin\GymFitnessAdminController;
use Modules\GymFitness\App\Http\Controllers\Frontend\HomeController;

// Self-contained static theme assets: Modules/GymFitness/Public/assets/{css|js}/{file}
Route::get('gymfitness-assets/{type}/{file}', [HomeController::class, 'asset'])
    ->where(['type' => 'css|js', 'file' => '[A-Za-z0-9_\-\.]+'])
    ->name('gymfitness.asset');

// Language Switcher Route (Supports ONLY English 'en' and Bengali 'bn')
Route::get('/gym-fitness/lang/{locale}', [HomeController::class, 'switchLanguage'])
    ->whereIn('locale', ['en', 'bn'])
    ->name('gymfitness.lang');

// Public Frontend Routes for Gym & Fitness (Supports both Single-Module Root Mode & Multi-Module Hub Mode)
Route::prefix('gym-fitness')->name('gymfitness.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/portal', [HomeController::class, 'portal'])->name('portal');
    Route::post('/portal/login', [HomeController::class, 'portalLogin'])->name('portal.login');
    Route::get('/portal/logout', [HomeController::class, 'portalLogout'])->name('portal.logout');
    Route::post('/portal/renew', [HomeController::class, 'portalRenew'])->name('portal.renew');
    Route::post('/portal/book-class', [HomeController::class, 'portalBookClass'])->name('portal.book_class');
    Route::post('/portal/cancel-class/{id}', [HomeController::class, 'portalCancelClass'])->name('portal.cancel_class');
    Route::post('/portal/check-in', [HomeController::class, 'portalCheckIn'])->name('portal.checkin');
    Route::post('/portal/progress', [HomeController::class, 'portalLogProgress'])->name('portal.log_progress');
    Route::get('/sitemap.xml', [HomeController::class, 'sitemap'])->name('sitemap');
});

// Public Form Submissions (Membership Registration & Newsletter)
Route::post('/gym-fitness/register-submit', [HomeController::class, 'registerMember'])->name('gymfitness.register.submit');
Route::post('/gym-fitness/newsletter-submit', [HomeController::class, 'submitNewsletter'])->name('gymfitness.newsletter.submit');

// GLOBAL ADMIN PANEL ROUTES FOR GYM & FITNESS MODULE
Route::prefix('admin/gymfitness')
    ->name('admin.gymfitness.')
    ->middleware(['web', EnsureModuleIsActive::class . ':GymFitness'])
    ->group(function () {
        // A. Gym Dashboard
        Route::get('dashboard', [GymFitnessAdminController::class, 'dashboard'])->name('dashboard');

        // B. Member Management (gym_members & gym_subscriptions)
        Route::get('members', [GymFitnessAdminController::class, 'members'])->name('members.index');
        Route::post('members', [GymFitnessAdminController::class, 'storeMember'])->name('members.store');
        Route::put('members/{id}', [GymFitnessAdminController::class, 'updateMember'])->name('members.update');
        Route::post('members/{id}/lifecycle', [GymFitnessAdminController::class, 'memberLifecycleAction'])->name('members.lifecycle');
        Route::delete('members/{id}', [GymFitnessAdminController::class, 'deleteMember'])->name('members.destroy');

        // C. Membership Packages CRUD (gym_packages)
        Route::get('packages', [GymFitnessAdminController::class, 'packages'])->name('packages.index');
        Route::post('packages', [GymFitnessAdminController::class, 'storePackage'])->name('packages.store');
        Route::put('packages/{id}', [GymFitnessAdminController::class, 'updatePackage'])->name('packages.update');
        Route::delete('packages/{id}', [GymFitnessAdminController::class, 'deletePackage'])->name('packages.destroy');

        // D. Trainer Management CRUD (gym_trainers)
        Route::get('trainers', [GymFitnessAdminController::class, 'trainers'])->name('trainers.index');
        Route::post('trainers', [GymFitnessAdminController::class, 'storeTrainer'])->name('trainers.store');
        Route::put('trainers/{id}', [GymFitnessAdminController::class, 'updateTrainer'])->name('trainers.update');
        Route::delete('trainers/{id}', [GymFitnessAdminController::class, 'deleteTrainer'])->name('trainers.destroy');

        // E. Class & Schedule Management CRUD (gym_classes & gym_class_bookings)
        Route::get('classes', [GymFitnessAdminController::class, 'classes'])->name('classes.index');
        Route::post('classes', [GymFitnessAdminController::class, 'storeClass'])->name('classes.store');
        Route::put('classes/{id}', [GymFitnessAdminController::class, 'updateClass'])->name('classes.update');
        Route::delete('classes/{id}', [GymFitnessAdminController::class, 'deleteClass'])->name('classes.destroy');
        Route::match(['post', 'put'], 'class-bookings/{id}/status', [GymFitnessAdminController::class, 'updateBookingStatus'])->name('classes.booking_status');

        // F. Attendance & QR Check-in Management (gym_attendances)
        Route::get('attendance', [GymFitnessAdminController::class, 'attendance'])->name('attendance.index');
        Route::post('attendance/check-in', [GymFitnessAdminController::class, 'checkInMember'])->name('attendance.checkin');
        Route::post('attendance/{id}/check-out', [GymFitnessAdminController::class, 'checkOutMember'])->name('attendance.checkout');
        Route::delete('attendance/{id}', [GymFitnessAdminController::class, 'deleteAttendance'])->name('attendance.destroy');

        // G. Payment & Revenue Management (gym_payments)
        Route::get('payments', [GymFitnessAdminController::class, 'payments'])->name('payments.index');
        Route::post('payments', [GymFitnessAdminController::class, 'storePayment'])->name('payments.store');
        Route::match(['post', 'put'], 'payments/{id}/status', [GymFitnessAdminController::class, 'updatePaymentStatus'])->name('payments.status');
        Route::delete('payments/{id}', [GymFitnessAdminController::class, 'deletePayment'])->name('payments.destroy');

        // H1. Training Programs CRUD (gym_programs)
        Route::get('programs', [GymFitnessAdminController::class, 'programs'])->name('programs.index');
        Route::post('programs', [GymFitnessAdminController::class, 'storeProgram'])->name('programs.store');
        Route::put('programs/{id}', [GymFitnessAdminController::class, 'updateProgram'])->name('programs.update');
        Route::delete('programs/{id}', [GymFitnessAdminController::class, 'deleteProgram'])->name('programs.destroy');

        // H2. Gym Equipment & Maintenance CRUD (gym_equipments)
        Route::get('equipment', [GymFitnessAdminController::class, 'equipment'])->name('equipment.index');
        Route::post('equipment', [GymFitnessAdminController::class, 'storeEquipment'])->name('equipment.store');
        Route::put('equipment/{id}', [GymFitnessAdminController::class, 'updateEquipment'])->name('equipment.update');
        Route::delete('equipment/{id}', [GymFitnessAdminController::class, 'deleteEquipment'])->name('equipment.destroy');

        // H3. Workout Plans, Diet Plans & Member Progress Tracking (gym_fitness_plans & gym_progress_logs)
        Route::get('plans', [GymFitnessAdminController::class, 'plans'])->name('plans.index');
        Route::post('plans', [GymFitnessAdminController::class, 'storePlan'])->name('plans.store');
        Route::put('plans/{id}', [GymFitnessAdminController::class, 'updatePlan'])->name('plans.update');
        Route::delete('plans/{id}', [GymFitnessAdminController::class, 'deletePlan'])->name('plans.destroy');
        Route::post('progress', [GymFitnessAdminController::class, 'storeProgressLog'])->name('plans.progress.store');
        Route::delete('progress/{id}', [GymFitnessAdminController::class, 'deleteProgressLog'])->name('plans.progress.destroy');

        // H4. Member Testimonials & FAQs CRUD (gym_testimonials & gym_faqs)
        Route::get('reviews', [GymFitnessAdminController::class, 'reviews'])->name('reviews.index');
        Route::post('testimonials', [GymFitnessAdminController::class, 'storeTestimonial'])->name('reviews.testimonials.store');
        Route::put('testimonials/{id}', [GymFitnessAdminController::class, 'updateTestimonial'])->name('reviews.testimonials.update');
        Route::delete('testimonials/{id}', [GymFitnessAdminController::class, 'deleteTestimonial'])->name('reviews.testimonials.destroy');
        Route::post('faqs', [GymFitnessAdminController::class, 'storeFaq'])->name('reviews.faqs.store');
        Route::put('faqs/{id}', [GymFitnessAdminController::class, 'updateFaq'])->name('reviews.faqs.update');
        Route::delete('faqs/{id}', [GymFitnessAdminController::class, 'deleteFaq'])->name('reviews.faqs.destroy');

        // I. Website Builder, Section Ordering, Header, Hero, Languages (EN/BN), Footer & SEO (gym_settings)
        Route::get('settings', [GymFitnessAdminController::class, 'settings'])->name('settings');
        Route::post('settings', [GymFitnessAdminController::class, 'saveSettings'])->name('settings.save');
        Route::post('settings/reset', [GymFitnessAdminController::class, 'resetSettings'])->name('settings.reset');
    });
