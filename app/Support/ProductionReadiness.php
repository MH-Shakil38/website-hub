<?php

namespace App\Support;

use App\Services\Billing\SslCommerzPaymentDriver;

/**
 * Production readiness checks shared by `php artisan saas:doctor` and the SaaS Control Center.
 */
class ProductionReadiness
{
    /**
     * @return array<int, string> human readable problems, empty when everything looks good
     */
    public static function issues(): array
    {
        $checks = [
            [app()->environment('production'), 'APP_ENV is "'.app()->environment().'" (use production on the live server).'],
            [! config('app.debug'), 'APP_DEBUG is true (must be false in production: it leaks stack traces and secrets).'],
            [filled(config('app.key')), 'APP_KEY is empty (run php artisan key:generate).'],
            [str_starts_with((string) config('app.url'), 'https://'), 'APP_URL is not https.'],
            [! config('app.admin_auto_login_local'), 'ADMIN_AUTO_LOGIN_LOCAL is on (turn it off).'],
            [config('database.default') !== 'sqlite', 'DB_CONNECTION is sqlite (SaaS tenancy needs MySQL).'],
            [! in_array(config('mail.default'), ['log', 'array'], true), 'MAIL_MAILER is "'.config('mail.default').'" (signup, verification and invoice emails will not be delivered).'],
            [config('queue.default') !== 'sync', 'QUEUE_CONNECTION is sync (use database or redis and run a queue worker).'],
            [SslCommerzPaymentDriver::isLive(), 'SSLCommerz store credentials are missing (online payment is disabled in production).'],
            [! config('services.sslcommerz.sandbox', true), 'SSLCOMMERZ_SANDBOX is true (no real money will be collected).'],
            [(bool) config('session.secure'), 'SESSION_SECURE_COOKIE is not true (session cookie can travel over plain http).'],
        ];

        return array_values(array_map(fn ($c) => $c[1], array_filter($checks, fn ($c) => ! $c[0])));
    }
}
