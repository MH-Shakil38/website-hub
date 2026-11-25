<?php

use App\Http\Controllers\Admin\GlobalAdminController;
use App\Http\Controllers\Admin\ModuleCmsController;
use App\Http\Controllers\Admin\SaasBillingController;
use App\Http\Controllers\Admin\SaasControlCenterController;
use App\Http\Controllers\Admin\SaasManagementController;
use App\Http\Controllers\Admin\SaasOperationsController;
use App\Http\Controllers\Admin\SaasResellerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FrontendHubController;
use App\Http\Controllers\SaasOnboardingController;
use App\Http\Controllers\SslCommerzWebhookController;
use App\Http\Middleware\EnsureDashboardAccess;
use Illuminate\Support\Facades\Route;

// Caddy On-Demand TLS Domain Verification Endpoint (Phase 3: returns 200 if domain is verified & active, 404 otherwise)
Route::get('/internal/domain-check', [SaasManagementController::class, 'caddyDomainCheck'])
    ->name('saas.caddy.domain-check');

// SSLCommerz return URLs + IPN (public, CSRF-exempt; payments are confirmed via the SSLCommerz Validation API)
Route::prefix('payments/sslcommerz')->name('payments.sslcommerz.')->middleware('throttle:60,1')->group(function () {
    Route::post('/success', [SslCommerzWebhookController::class, 'success'])->name('success');
    Route::post('/fail', [SslCommerzWebhookController::class, 'fail'])->name('fail');
    Route::post('/cancel', [SslCommerzWebhookController::class, 'cancel'])->name('cancel');
    Route::post('/ipn', [SslCommerzWebhookController::class, 'ipn'])->name('ipn');
});

// Public Step-by-Step Theme Installation Wizard & Email Verification Onboarding (Phase 6 + Phase 7 Rate Limiting)
Route::get('/install/check-subdomain', [SaasOnboardingController::class, 'checkSubdomain'])
    ->middleware('throttle:60,1')
    ->name('saas.install.check-subdomain');
Route::post('/install/register', [SaasOnboardingController::class, 'registerPendingInstall'])
    ->middleware('throttle:20,1')
    ->name('saas.install.register');
Route::get('/install/verify/{uuid}', [SaasOnboardingController::class, 'showVerifyNotice'])
    ->name('saas.install.verify.notice');
Route::post('/install/verify/{uuid}/resend', [SaasOnboardingController::class, 'resendVerifyEmail'])
    ->middleware('throttle:10,1')
    ->name('saas.install.verify.resend');
Route::match(['get', 'post'], '/install/verify/{uuid}/{token}', [SaasOnboardingController::class, 'verifyAndProvision'])
    ->name('saas.install.verify.confirm');
Route::get('/install/success/{uuid}', [SaasOnboardingController::class, 'showInstallSuccess'])
    ->name('saas.install.success');

// Serve Isolated Module Uploaded Media directly from Modules/{Module}/Public/uploads/{t{id}/}{folder}/{filename}
Route::get('/module-media/{module}/{folder}/{filename}/{subfilename?}', [ModuleCmsController::class, 'serveModuleMedia'])
    ->name('module.media.serve');

// Dynamic Frontend Hub Routes (Single Active Module at '/' OR Multi-Module Showcase Hub + '/{slug}')
Route::get('/', [FrontendHubController::class, 'index'])->name('hub.home');
Route::get('/{subpage}', [FrontendHubController::class, 'singleModuleSubpage'])
    ->where('subpage', 'portfolio|projects|blog|properties|catalog|contact|about|services|pricing|faq|packages|hajj|umrah|facilities|tour|gallery|portal|schedule|trainers|equipment|sitemap\.xml|service-[a-z0-9\-]+|project-[a-z0-9\-]+|article-[a-z0-9\-]+|package-[a-z0-9\-]+|lang-[a-z]{2}')
    ->name('hub.single.subpage');
Route::get('/{slug}/{subpage?}', [FrontendHubController::class, 'moduleRoute'])
    ->whereIn('slug', ['interior-design', 'real-estate', 'pharmacy', 'software-company', 'fertility-clinic', 'clinic', 'developer-portfolio', 'portfolio', 'ivf-center', 'hajj-umrah', 'gym-fitness', 'wonder-learn', 'kids-learning', 'event-management', 'eventify'])
    ->name('hub.module');

// Global Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.submit');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:5,1')->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/verify-email', [AuthController::class, 'showVerifyEmail'])->name('verification.notice');
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])->middleware('throttle:3,1')->name('verification.send');
    Route::get('/two-factor', [AuthController::class, 'showTwoFactor'])->name('two-factor.login');
    Route::post('/two-factor', [AuthController::class, 'verifyTwoFactor'])->middleware('throttle:5,1')->name('two-factor.verify');
    Route::get('/confirm-password', [AuthController::class, 'showConfirmPassword'])->name('password.confirm');
    Route::post('/confirm-password', [AuthController::class, 'confirmPassword'])->middleware('throttle:5,1')->name('password.confirm.submit');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Global Admin Core Routes (Unified SaaS Dashboard for Super Admin, Reseller, and Client)
Route::prefix('admin')->name('admin.')->middleware([EnsureDashboardAccess::class])->group(function () {
    Route::get('/', fn () => redirect()->route('admin.dashboard'));
    Route::get('/dashboard', [GlobalAdminController::class, 'dashboard'])->name('dashboard');

    // SaaS Homepage Builder (Phase 6: Control SaaS Landing Page from Dashboard)
    Route::get('/saas-homepage', [SaasOnboardingController::class, 'editHomepage'])->name('saas.homepage');
    Route::post('/saas-homepage', [SaasOnboardingController::class, 'updateHomepage'])->name('saas.homepage.update');

    // SaaS Multi-Database Tenants & Website Context Switcher (Phase 2)
    Route::get('/tenants', [SaasManagementController::class, 'tenants'])->name('tenants.index');
    Route::post('/tenants', [SaasManagementController::class, 'storeTenant'])->name('tenants.store');
    Route::match(['get', 'post'], '/tenants/{tenant}/switch', [SaasManagementController::class, 'switchTenant'])->name('tenants.switch');
    Route::post('/tenants/{tenant}/toggle-status', [SaasManagementController::class, 'toggleTenantStatus'])->name('tenants.toggle-status');
    Route::delete('/tenants/{tenant}', [SaasManagementController::class, 'destroyTenant'])->name('tenants.destroy');

    // SaaS Custom Domains, DNS Verification & Primary Domain Routing (Phase 3)
    Route::get('/domains', [SaasManagementController::class, 'domains'])->name('domains.index');
    Route::post('/domains', [SaasManagementController::class, 'storeDomain'])->name('domains.store');
    Route::post('/domains/{domain}/verify', [SaasManagementController::class, 'verifyDomain'])->name('domains.verify');
    Route::post('/domains/{domain}/primary', [SaasManagementController::class, 'setPrimaryDomain'])->name('domains.primary');
    Route::delete('/domains/{domain}', [SaasManagementController::class, 'destroyDomain'])->name('domains.destroy');

    // SaaS Plans, Subscriptions, Invoices, SSLCommerz & Manual Payments (Phase 4)
    Route::get('/plans', [SaasBillingController::class, 'plans'])->name('plans.index');
    Route::post('/plans', [SaasBillingController::class, 'storePlan'])->name('plans.store');
    Route::delete('/plans/{plan}', [SaasBillingController::class, 'destroyPlan'])->name('plans.destroy');
    Route::post('/plans/{plan}/subscribe', [SaasBillingController::class, 'subscribe'])->name('plans.subscribe');

    Route::get('/billing', [SaasBillingController::class, 'billingIndex'])->name('billing.index');
    Route::post('/billing/invoices/{invoice}/pay-manual', [SaasBillingController::class, 'payManual'])->name('billing.pay-manual');
    Route::post('/billing/invoices/{invoice}/pay-sslcommerz', [SaasBillingController::class, 'paySslCommerz'])->name('billing.pay-sslcommerz');
    Route::get('/billing/sslcommerz/{payment}/checkout', [SaasBillingController::class, 'sslCommerzCheckout'])->name('billing.sslcommerz.checkout');
    Route::post('/billing/sslcommerz/{payment}/callback', [SaasBillingController::class, 'sslCommerzCallback'])->name('billing.sslcommerz.callback');
    Route::post('/billing/payments/{payment}/approve', [SaasBillingController::class, 'approvePayment'])->name('billing.payments.approve');
    Route::post('/billing/payments/{payment}/reject', [SaasBillingController::class, 'rejectPayment'])->name('billing.payments.reject');
    Route::get('/billing/invoices/{invoice}/print', [SaasBillingController::class, 'showInvoice'])->name('billing.invoices.show');

    // SaaS Reseller Hub: Referral Link, Mode A & Mode B Billing, Wallet, Commissions & Payouts (Phase 5)
    Route::get('/reseller-hub', [SaasResellerController::class, 'index'])->name('reseller.hub');
    Route::post('/reseller-hub/profile', [SaasResellerController::class, 'updateProfile'])->name('reseller.profile.update');
    Route::post('/reseller-hub/wallet/topup', [SaasResellerController::class, 'topUpWallet'])->name('reseller.wallet.topup');
    Route::post('/reseller-hub/payouts', [SaasResellerController::class, 'requestPayout'])->name('reseller.payouts.request');
    Route::post('/reseller-hub/payouts/{payout}/process', [SaasResellerController::class, 'processPayout'])->name('reseller.payouts.process');
    Route::post('/reseller-hub/commissions/{commission}/approve', [SaasResellerController::class, 'approveCommission'])->name('reseller.commissions.approve');
    Route::post('/reseller-hub/resellers/{reseller}/kyc', [SaasResellerController::class, 'updateKycStatus'])->name('reseller.kyc.update');
    Route::post('/reseller-hub/tenants/{tenant}/renew', [SaasResellerController::class, 'renewClientTenant'])->name('reseller.tenants.renew');

    // SaaS Per-Tenant Database Backups & One-Click Restore + Support Tickets (Phase 7)
    Route::get('/backups', [SaasOperationsController::class, 'backups'])->name('backups.index');
    Route::post('/backups', [SaasOperationsController::class, 'createBackup'])->name('backups.store');
    Route::post('/backups/{backup}/restore', [SaasOperationsController::class, 'restoreBackup'])->name('backups.restore');
    Route::get('/backups/{backup}/download', [SaasOperationsController::class, 'downloadBackup'])->name('backups.download');
    Route::delete('/backups/{backup}', [SaasOperationsController::class, 'destroyBackup'])->name('backups.destroy');

    Route::get('/support-tickets', [SaasOperationsController::class, 'tickets'])->name('support.tickets');
    Route::post('/support-tickets', [SaasOperationsController::class, 'storeTicket'])->name('support.tickets.store');
    Route::post('/support-tickets/{ticket}/reply', [SaasOperationsController::class, 'replyTicket'])->name('support.tickets.reply');
    Route::post('/support-tickets/{ticket}/close', [SaasOperationsController::class, 'closeTicket'])->name('support.tickets.close');

    // SaaS Super Admin Email & SMTP Setup + Client Notification Triggers & Test Mailer
    Route::get('/email-settings', [SaasOperationsController::class, 'emailSettings'])->name('email.settings');
    Route::post('/email-settings', [SaasOperationsController::class, 'updateEmailSettings'])->name('email.settings.update');
    Route::post('/email-settings/test', [SaasOperationsController::class, 'sendTestEmail'])->name('email.settings.test');
    Route::post('/email-settings/clear-logs', [SaasOperationsController::class, 'clearEmailLogs'])->name('email.settings.clear-logs');

    // SaaS Resellers & Clients Management + One-Click Impersonation (Phase 2)
    Route::get('/accounts', [SaasManagementController::class, 'accounts'])->name('saas.accounts');
    Route::post('/accounts', [SaasManagementController::class, 'storeAccount'])->name('saas.accounts.store');
    Route::post('/impersonate/leave', [SaasManagementController::class, 'leaveImpersonation'])->name('impersonate.leave');
    Route::post('/impersonate/{targetUser}', [SaasManagementController::class, 'impersonate'])->name('impersonate.start');

    Route::middleware([EnsureDashboardAccess::class . ':super_admin'])->group(function () {
        // SaaS Control Center: platform KPIs + approval queues (Super Admin only)
        Route::get('/saas', [SaasControlCenterController::class, 'index'])->name('saas.control');

        // Websites / Themes Management (Super Admin only)
        Route::get('/websites', [GlobalAdminController::class, 'websites'])->name('websites');
        Route::post('/websites', [GlobalAdminController::class, 'storeWebsite'])->name('websites.store');
        Route::post('/websites/{website}/toggle', [GlobalAdminController::class, 'toggleWebsite'])->name('websites.toggle');
        Route::post('/websites/{website}/reset', [GlobalAdminController::class, 'resetWebsite'])->name('websites.reset');
        Route::delete('/websites/{website}', [GlobalAdminController::class, 'deleteWebsite'])->name('websites.destroy');

    });

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
    // Per-module users page is part of every tenant's sidebar (clients/resellers), so it stays open to any dashboard user.
    Route::get('/users', [ModuleCmsController::class, 'users'])->name('users');
    Route::post('/users', [ModuleCmsController::class, 'storeModuleUser'])->name('users.store');
    Route::put('/users/{id}', [ModuleCmsController::class, 'updateModuleUser'])->name('users.update');
    Route::delete('/users/{id}', [ModuleCmsController::class, 'deleteModuleUser'])->name('users.destroy');

    // Platform-wide roles/permissions and the transactions ledger are Super Admin only.
    Route::middleware([EnsureDashboardAccess::class . ':super_admin'])->group(function () {
        Route::post('/roles', [GlobalAdminController::class, 'storeRole'])->name('roles.store');
        Route::put('/roles/{role}/permissions', [GlobalAdminController::class, 'updateRolePermissions'])->name('roles.permissions');

        // Transactions & Filter Table (Screenshot 8)
        Route::get('/transactions', [GlobalAdminController::class, 'transactions'])->name('transactions');
    });

    // 7. Standalone Per-Module Settings + Profile & Password Change (int_settings / re_settings / pharma_settings)
    Route::get('/settings', [ModuleCmsController::class, 'settings'])->name('settings');
    Route::post('/settings/module', [ModuleCmsController::class, 'updateModuleSettings'])->name('settings.module');
    Route::post('/settings/profile', [GlobalAdminController::class, 'updateProfile'])->name('settings.profile');
    Route::post('/settings/password', [GlobalAdminController::class, 'updatePassword'])->name('settings.password');
    Route::post('/settings/global', [GlobalAdminController::class, 'updateGlobalSettings'])->middleware(EnsureDashboardAccess::class . ':super_admin')->name('settings.global');
});
