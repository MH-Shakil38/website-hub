<?php

use App\Http\Middleware\EnsureModuleIsActive;
use Illuminate\Support\Facades\Route;
use Modules\HajjUmrah\App\Http\Controllers\Admin\HajjUmrahAdminController;
use Modules\HajjUmrah\App\Http\Controllers\Frontend\HomeController;

// Self-contained static theme assets: Modules/HajjUmrah/Public/assets/{css|js}/{file}
Route::get('hajjumrah-assets/{type}/{file}', [HomeController::class, 'asset'])
    ->where(['type' => 'css|js', 'file' => '[A-Za-z0-9_\-\.]+'])
    ->name('hajjumrah.asset');

// Language Switcher Route (Supports EN, BN, AR with RTL)
Route::get('/hajj-umrah/lang/{locale}', [HomeController::class, 'switchLanguage'])
    ->whereIn('locale', ['en', 'bn', 'ar'])
    ->name('hajjumrah.lang.switch');

// Public Frontend Routes for Hajj & Umrah (Supports both Single-Module Root Mode & Multi-Module Hub Mode)
Route::prefix('hajj-umrah')->name('hajjumrah.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/packages', [HomeController::class, 'packages'])->name('packages');
    Route::get('/package/{slug}', [HomeController::class, 'packageShow'])->name('package.show');
    Route::get('/blog', [HomeController::class, 'blog'])->name('blog');
    Route::get('/article/{slug}', [HomeController::class, 'blogShow'])->name('blog.show');
    Route::get('/sitemap.xml', [HomeController::class, 'sitemap'])->name('sitemap');
});

// Public Form Submissions (Package Booking Inquiry & Newsletter Subscription)
Route::post('/hajj-umrah/inquiry-submit', [HomeController::class, 'submitInquiry'])->name('hajjumrah.inquiry.submit');
Route::post('/hajj-umrah/newsletter-submit', [HomeController::class, 'submitNewsletter'])->name('hajjumrah.newsletter.submit');

// GLOBAL ADMIN PANEL ROUTES FOR HAJJ & UMRAH MODULE
Route::prefix('admin/hajjumrah')
    ->name('admin.hajjumrah.')
    ->middleware(['web', EnsureModuleIsActive::class . ':HajjUmrah'])
    ->group(function () {
        // Homepage Builder, Section Ordering, Header, Hero, Languages (EN/BN/AR), Footer & SEO
        Route::get('settings', [HajjUmrahAdminController::class, 'settings'])->name('settings');
        Route::post('settings', [HajjUmrahAdminController::class, 'saveSettings'])->name('settings.save');
        Route::post('settings/reset', [HajjUmrahAdminController::class, 'resetSettings'])->name('settings.reset');

        // Special Offers & Hajj / Umrah Packages CRUD (hajj_packages)
        Route::get('packages', [HajjUmrahAdminController::class, 'packages'])->name('packages.index');
        Route::post('packages', [HajjUmrahAdminController::class, 'storePackage'])->name('packages.store');
        Route::put('packages/{id}', [HajjUmrahAdminController::class, 'updatePackage'])->name('packages.update');
        Route::delete('packages/{id}', [HajjUmrahAdminController::class, 'deletePackage'])->name('packages.destroy');

        // All-Inclusive Package Pricing Plans CRUD (hajj_pricing_plans)
        Route::get('pricing', [HajjUmrahAdminController::class, 'pricing'])->name('pricing.index');
        Route::post('pricing', [HajjUmrahAdminController::class, 'storePricing'])->name('pricing.store');
        Route::put('pricing/{id}', [HajjUmrahAdminController::class, 'updatePricing'])->name('pricing.update');
        Route::delete('pricing/{id}', [HajjUmrahAdminController::class, 'deletePricing'])->name('pricing.destroy');

        // What Do We Offer Services CRUD (hajj_services)
        Route::get('services', [HajjUmrahAdminController::class, 'services'])->name('services.index');
        Route::post('services', [HajjUmrahAdminController::class, 'storeService'])->name('services.store');
        Route::put('services/{id}', [HajjUmrahAdminController::class, 'updateService'])->name('services.update');
        Route::delete('services/{id}', [HajjUmrahAdminController::class, 'deleteService'])->name('services.destroy');

        // Affiliated Airlines, Hotels & Trust Badges CRUD (hajj_partners)
        Route::get('partners', [HajjUmrahAdminController::class, 'partners'])->name('partners.index');
        Route::post('partners', [HajjUmrahAdminController::class, 'storePartner'])->name('partners.store');
        Route::put('partners/{id}', [HajjUmrahAdminController::class, 'updatePartner'])->name('partners.update');
        Route::delete('partners/{id}', [HajjUmrahAdminController::class, 'deletePartner'])->name('partners.destroy');

        // Pilgrim Testimonials CRUD (hajj_testimonials)
        Route::get('testimonials', [HajjUmrahAdminController::class, 'testimonials'])->name('testimonials.index');
        Route::post('testimonials', [HajjUmrahAdminController::class, 'storeTestimonial'])->name('testimonials.store');
        Route::put('testimonials/{id}', [HajjUmrahAdminController::class, 'updateTestimonial'])->name('testimonials.update');
        Route::delete('testimonials/{id}', [HajjUmrahAdminController::class, 'deleteTestimonial'])->name('testimonials.destroy');

        // Blog & Travel Guides CRUD (hajj_blogs)
        Route::get('blogs', [HajjUmrahAdminController::class, 'blogs'])->name('blogs.index');
        Route::post('blogs', [HajjUmrahAdminController::class, 'storeBlog'])->name('blogs.store');
        Route::put('blogs/{id}', [HajjUmrahAdminController::class, 'updateBlog'])->name('blogs.update');
        Route::delete('blogs/{id}', [HajjUmrahAdminController::class, 'deleteBlog'])->name('blogs.destroy');

        // Booking Inquiries & Newsletter Subscribers Management (hajj_inquiries, hajj_subscribers)
        Route::get('inquiries', [HajjUmrahAdminController::class, 'inquiries'])->name('inquiries.index');
        Route::put('inquiries/{id}/status', [HajjUmrahAdminController::class, 'updateInquiryStatus'])->name('inquiries.status');
        Route::delete('inquiries/{id}', [HajjUmrahAdminController::class, 'deleteInquiry'])->name('inquiries.destroy');
        Route::delete('subscribers/{id}', [HajjUmrahAdminController::class, 'deleteSubscriber'])->name('subscribers.destroy');
    });
