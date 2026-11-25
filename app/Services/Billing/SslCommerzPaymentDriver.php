<?php

namespace App\Services\Billing;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * SSLCommerz payment driver.
 *
 * Live mode: when SSLCOMMERZ_STORE_ID + SSLCOMMERZ_STORE_PASSWORD are set, a real SSLCommerz
 * session is created and payments are only marked paid after the server-side Validation API
 * confirms them (see SslCommerzWebhookController).
 *
 * Simulator mode: without credentials, an internal checkout page lets you click "pay". It is
 * only available in local/testing environments, so nobody can self-approve invoices in production.
 */
class SslCommerzPaymentDriver implements PaymentGatewayInterface
{
    public static function isLive(): bool
    {
        return filled(config('services.sslcommerz.store_id')) && filled(config('services.sslcommerz.store_password'));
    }

    public static function simulatorAllowed(): bool
    {
        return ! self::isLive() && app()->environment(['local', 'testing']);
    }

    protected static function baseUrl(): string
    {
        return config('services.sslcommerz.sandbox', true)
            ? 'https://sandbox.sslcommerz.com'
            : 'https://securepay.sslcommerz.com';
    }

    public function initiate(Invoice $invoice, array $payload = []): array
    {
        if (! self::isLive() && ! self::simulatorAllowed()) {
            return [
                'status' => 'error',
                'redirect_url' => null,
                'payment' => null,
                'message' => 'Online payment (SSLCommerz) is not configured yet. Please use manual payment or contact support.',
            ];
        }

        $txnId = 'SSLCZ-'.strtoupper(Str::random(12));

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'user_id' => $invoice->user_id,
            'gateway' => 'sslcommerz',
            'method' => 'SSLCommerz PGW',
            'gateway_txn_id' => $txnId,
            'amount' => $invoice->total,
            'currency' => $invoice->currency,
            'status' => 'pending',
            'gateway_payload' => [
                'mode' => self::isLive() ? 'live' : 'simulator',
                'sandbox' => (bool) config('services.sslcommerz.sandbox', true),
                'initiated_at' => now()->toIso8601String(),
            ],
        ]);

        if (! self::isLive()) {
            return [
                'status' => 'redirect',
                'redirect_url' => route('admin.billing.sslcommerz.checkout', ['payment' => $payment->id]),
                'payment' => $payment,
                'message' => 'Redirecting to SSLCommerz (local simulator)...',
            ];
        }

        $user = $invoice->user;

        try {
            $response = Http::asForm()->timeout(30)->post(self::baseUrl().'/gwprocess/v4/api.php', [
                'store_id' => config('services.sslcommerz.store_id'),
                'store_passwd' => config('services.sslcommerz.store_password'),
                'total_amount' => number_format((float) $invoice->total, 2, '.', ''),
                'currency' => $invoice->currency ?: 'BDT',
                'tran_id' => $txnId,
                'success_url' => route('payments.sslcommerz.success'),
                'fail_url' => route('payments.sslcommerz.fail'),
                'cancel_url' => route('payments.sslcommerz.cancel'),
                'ipn_url' => route('payments.sslcommerz.ipn'),
                'product_name' => 'Subscription '.$invoice->invoice_number,
                'product_category' => 'SaaS Subscription',
                'product_profile' => 'non-physical-goods',
                'shipping_method' => 'NO',
                'num_of_item' => 1,
                'cus_name' => $user?->name ?: 'Customer',
                'cus_email' => $user?->email ?: 'customer@example.com',
                'cus_phone' => $user?->phone ?? '01700000000',
                'cus_add1' => 'N/A',
                'cus_city' => 'Dhaka',
                'cus_country' => 'Bangladesh',
                'value_a' => (string) $payment->id,
            ]);
            $data = $response->json() ?? [];
        } catch (\Throwable $e) {
            Log::error('SSLCommerz init failed', ['payment' => $payment->id, 'error' => $e->getMessage()]);
            $data = [];
        }

        if (($data['status'] ?? null) !== 'SUCCESS' || empty($data['GatewayPageURL'])) {
            $payment->update(['status' => 'failed', 'gateway_payload' => array_merge($payment->gateway_payload ?? [], [
                'init_error' => $data['failedreason'] ?? 'Could not reach SSLCommerz',
            ])]);

            return [
                'status' => 'error',
                'redirect_url' => null,
                'payment' => $payment,
                'message' => 'Could not start SSLCommerz payment: '.($data['failedreason'] ?? 'gateway unavailable').'. Please try again later.',
            ];
        }

        $payment->update(['gateway_payload' => array_merge($payment->gateway_payload ?? [], [
            'sessionkey' => $data['sessionkey'] ?? null,
        ])]);

        return [
            'status' => 'redirect',
            'redirect_url' => $data['GatewayPageURL'],
            'payment' => $payment,
            'message' => 'Redirecting to SSLCommerz Secure Payment Gateway...',
        ];
    }

    /**
     * Ask SSLCommerz (server to server) whether a val_id is a real, valid payment for this Payment.
     * Returns the validation payload when valid, or null.
     */
    public function validate(Payment $payment, string $valId): ?array
    {
        if (! self::isLive() || $valId === '') {
            return null;
        }

        try {
            $data = Http::timeout(30)->get(self::baseUrl().'/validator/api/validationserverAPI.php', [
                'val_id' => $valId,
                'store_id' => config('services.sslcommerz.store_id'),
                'store_passwd' => config('services.sslcommerz.store_password'),
                'format' => 'json',
            ])->json() ?? [];
        } catch (\Throwable $e) {
            Log::error('SSLCommerz validation failed', ['payment' => $payment->id, 'error' => $e->getMessage()]);

            return null;
        }

        $valid = in_array($data['status'] ?? '', ['VALID', 'VALIDATED'], true)
            && ($data['tran_id'] ?? null) === $payment->gateway_txn_id
            && strtoupper((string) ($data['currency_type'] ?? $data['currency'] ?? '')) === strtoupper((string) ($payment->currency ?: 'BDT'))
            && round((float) ($data['currency_amount'] ?? $data['amount'] ?? 0), 2) >= round((float) $payment->amount, 2);

        if (! $valid) {
            Log::warning('SSLCommerz validation rejected', ['payment' => $payment->id, 'response' => $data]);

            return null;
        }

        return $data;
    }
}
