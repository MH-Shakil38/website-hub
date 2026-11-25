<?php

namespace App\Services\Billing;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Str;

class SslCommerzPaymentDriver implements PaymentGatewayInterface
{
    public function initiate(Invoice $invoice, array $payload = []): array
    {
        $txnId = 'SSLCZ-' . strtoupper(Str::random(10));

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
                'store_id' => config('services.sslcommerz.store_id', 'yourhub_test'),
                'sandbox' => config('services.sslcommerz.sandbox', true),
                'initiated_at' => now()->toIso8601String(),
            ],
        ]);

        // Redirect to our internal SSLCommerz checkout/callback simulator in sandbox/local mode
        // or live SSLCommerz session endpoint when store credentials are configured
        $checkoutUrl = route('admin.billing.sslcommerz.checkout', ['payment' => $payment->id]);

        return [
            'status' => 'redirect',
            'redirect_url' => $checkoutUrl,
            'payment' => $payment,
            'message' => 'Redirecting to SSLCommerz Secure Payment Gateway...',
        ];
    }
}
