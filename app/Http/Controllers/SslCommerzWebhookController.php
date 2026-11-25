<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\Billing\BillingService;
use App\Services\Billing\SslCommerzPaymentDriver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Public SSLCommerz return URLs (success / fail / cancel) and the IPN listener.
 * These are hit by SSLCommerz or the customer's browser coming back from the gateway,
 * so they are outside the auth group and CSRF-exempt. Nothing is trusted from the POST
 * body except tran_id and val_id: a payment is only completed after the Validation API confirms it.
 */
class SslCommerzWebhookController extends Controller
{
    public function __construct(
        protected BillingService $billing,
        protected SslCommerzPaymentDriver $driver,
    ) {}

    public function success(Request $request)
    {
        $payment = $this->findPayment($request);

        if ($payment && $this->confirm($payment, (string) $request->input('val_id', ''))) {
            return redirect()->route('admin.billing.index')
                ->with('success', "Payment [{$payment->gateway_txn_id}] successful. Your invoice is paid and the subscription is active.");
        }

        return redirect()->route('admin.billing.index')
            ->withErrors(['payment' => 'We could not verify this payment with SSLCommerz yet. If money was deducted, it will be confirmed automatically or contact support.']);
    }

    public function fail(Request $request)
    {
        $this->markFailed($this->findPayment($request), 'failed');

        return redirect()->route('admin.billing.index')
            ->withErrors(['payment' => 'SSLCommerz payment failed. Please try again.']);
    }

    public function cancel(Request $request)
    {
        $this->markFailed($this->findPayment($request), 'cancelled');

        return redirect()->route('admin.billing.index')
            ->withErrors(['payment' => 'SSLCommerz payment was cancelled.']);
    }

    public function ipn(Request $request)
    {
        $payment = $this->findPayment($request);

        if (! $payment) {
            return response('Unknown transaction', 404);
        }

        return $this->confirm($payment, (string) $request->input('val_id', ''))
            ? response('OK', 200)
            : response('Not validated', 422);
    }

    protected function findPayment(Request $request): ?Payment
    {
        $tranId = (string) $request->input('tran_id', '');

        return $tranId === ''
            ? null
            : Payment::where('gateway', 'sslcommerz')->where('gateway_txn_id', $tranId)->first();
    }

    /**
     * Validate with SSLCommerz and complete the payment once (idempotent for success + IPN racing).
     */
    protected function confirm(Payment $payment, string $valId): bool
    {
        if ($payment->status === 'completed') {
            return true;
        }

        $validated = $this->driver->validate($payment, $valId);
        if (! $validated) {
            return false;
        }

        return DB::transaction(function () use ($payment, $validated) {
            $locked = Payment::whereKey($payment->id)->lockForUpdate()->first();
            if (! $locked || $locked->status === 'completed') {
                return (bool) $locked;
            }

            $locked->update([
                'method' => 'SSLCommerz - '.($validated['card_type'] ?? 'Online'),
                'gateway_payload' => array_merge($locked->gateway_payload ?? [], [
                    'val_id' => $validated['val_id'] ?? null,
                    'bank_tran_id' => $validated['bank_tran_id'] ?? null,
                    'card_type' => $validated['card_type'] ?? null,
                    'validated_at' => now()->toIso8601String(),
                ]),
            ]);

            $this->billing->completePayment($locked, null, 'Verified via SSLCommerz Validation API');

            return true;
        });
    }

    protected function markFailed(?Payment $payment, string $reason): void
    {
        if ($payment && $payment->status === 'pending') {
            $payment->update([
                'status' => 'failed',
                'gateway_payload' => array_merge($payment->gateway_payload ?? [], ['closed_reason' => $reason]),
            ]);
        }
    }
}
