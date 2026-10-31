<?php

use App\Http\Middleware\EnsureModuleIsActive;
use Illuminate\Support\Facades\Route;
use Modules\Portfolio\App\Http\Controllers\Admin\PortfolioAdminController;
use Modules\Portfolio\App\Http\Controllers\Frontend\HomeController;

// Self-contained static theme assets: Modules/Portfolio/Public/assets/{css|js}/{file}
Route::get('portfolio-assets/{type}/{file}', [HomeController::class, 'asset'])
    ->where(['type' => 'css|js', 'file' => '[A-Za-z0-9_\-\.]+'])
    ->name('portfolio.asset');

// Public Frontend Routes for Portfolio (Supports both Single-Module Root Mode & Multi-Module Hub Mode)
Route::prefix('developer-portfolio')->name('portfolio.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/projects', [HomeController::class, 'projects'])->name('projects');
    Route::get('/project/{slug}', [HomeController::class, 'projectShow'])->name('project.show');
    Route::get('/service/{slug}', [HomeController::class, 'serviceShow'])->name('service.show');
    Route::get('/blog', [HomeController::class, 'blog'])->name('blog');
    Route::get('/article/{slug}', [HomeController::class, 'blogShow'])->name('blog.show');
    Route::get('/sitemap.xml', [HomeController::class, 'sitemap'])->name('sitemap');
});

// Public Form Submissions (Contact Form & Newsletter Subscription)
Route::post('/portfolio/contact-submit', [HomeController::class, 'submitContact'])->name('portfolio.contact.submit');
Route::post('/portfolio/newsletter-submit', [HomeController::class, 'submitNewsletter'])->name('portfolio.newsletter.submit');

// GLOBAL ADMIN PANEL ROUTES FOR PORTFOLIO MODULE
Route::prefix('admin/portfolio')
    ->name('admin.portfolio.')
    ->middleware(['web', EnsureModuleIsActive::class . ':Portfolio'])
    ->group(function () {
        // Visual Section & Theme Settings (Hero, About, Process, Stats, General/SEO/Theme Mode)
        Route::get('settings', [PortfolioAdminController::class, 'settings'])->name('settings');
        Route::post('settings', [PortfolioAdminController::class, 'saveSettings'])->name('settings.save');
        Route::post('settings/reset', [PortfolioAdminController::class, 'resetSettings'])->name('settings.reset');

        // Portfolio Projects CRUD
        Route::get('projects', [PortfolioAdminController::class, 'projects'])->name('projects.index');
        Route::post('projects', [PortfolioAdminController::class, 'storeProject'])->name('projects.store');
        Route::put('projects/{id}', [PortfolioAdminController::class, 'updateProject'])->name('projects.update');
        Route::delete('projects/{id}', [PortfolioAdminController::class, 'deleteProject'])->name('projects.destroy');

        // Skills & Technologies CRUD
        Route::get('skills', [PortfolioAdminController::class, 'skills'])->name('skills.index');
        Route::post('skills', [PortfolioAdminController::class, 'storeSkill'])->name('skills.store');
        Route::put('skills/{id}', [PortfolioAdminController::class, 'updateSkill'])->name('skills.update');
        Route::delete('skills/{id}', [PortfolioAdminController::class, 'deleteSkill'])->name('skills.destroy');

        // Services CRUD
        Route::get('services', [PortfolioAdminController::class, 'services'])->name('services.index');
        Route::post('services', [PortfolioAdminController::class, 'storeService'])->name('services.store');
        Route::put('services/{id}', [PortfolioAdminController::class, 'updateService'])->name('services.update');
        Route::delete('services/{id}', [PortfolioAdminController::class, 'deleteService'])->name('services.destroy');

        // Experience Timeline & Education/Certifications CRUD
        Route::get('experience', [PortfolioAdminController::class, 'experience'])->name('experience.index');
        Route::post('experience', [PortfolioAdminController::class, 'storeExperience'])->name('experience.store');
        Route::put('experience/{id}', [PortfolioAdminController::class, 'updateExperience'])->name('experience.update');
        Route::delete('experience/{id}', [PortfolioAdminController::class, 'deleteExperience'])->name('experience.destroy');

        Route::post('education', [PortfolioAdminController::class, 'storeEducation'])->name('education.store');
        Route::put('education/{id}', [PortfolioAdminController::class, 'updateEducation'])->name('education.update');
        Route::delete('education/{id}', [PortfolioAdminController::class, 'deleteEducation'])->name('education.destroy');

        // Client Testimonials CRUD
        Route::get('testimonials', [PortfolioAdminController::class, 'testimonials'])->name('testimonials.index');
        Route::post('testimonials', [PortfolioAdminController::class, 'storeTestimonial'])->name('testimonials.store');
        Route::put('testimonials/{id}', [PortfolioAdminController::class, 'updateTestimonial'])->name('testimonials.update');
        Route::delete('testimonials/{id}', [PortfolioAdminController::class, 'deleteTestimonial'])->name('testimonials.destroy');

        // Blog Posts CRUD
        Route::get('blogs', [PortfolioAdminController::class, 'blogs'])->name('blogs.index');
        Route::post('blogs', [PortfolioAdminController::class, 'storeBlog'])->name('blogs.store');
        Route::put('blogs/{id}', [PortfolioAdminController::class, 'updateBlog'])->name('blogs.update');
        Route::delete('blogs/{id}', [PortfolioAdminController::class, 'deleteBlog'])->name('blogs.destroy');

        // FAQ Accordion CRUD
        Route::get('faqs', [PortfolioAdminController::class, 'faqs'])->name('faqs.index');
        Route::post('faqs', [PortfolioAdminController::class, 'storeFaq'])->name('faqs.store');
        Route::put('faqs/{id}', [PortfolioAdminController::class, 'updateFaq'])->name('faqs.update');
        Route::delete('faqs/{id}', [PortfolioAdminController::class, 'deleteFaq'])->name('faqs.destroy');

        // Contact Messages & Newsletter Subscribers Management
        Route::get('messages', [PortfolioAdminController::class, 'messages'])->name('messages.index');
        Route::put('messages/{id}/status', [PortfolioAdminController::class, 'updateMessageStatus'])->name('messages.status');
        Route::delete('messages/{id}', [PortfolioAdminController::class, 'deleteMessage'])->name('messages.destroy');
        Route::delete('subscribers/{id}', [PortfolioAdminController::class, 'deleteSubscriber'])->name('subscribers.destroy');
    });
