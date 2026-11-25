<?php

namespace App\Services\Billing;

use App\Models\Invoice;
use App\Models\Payment;

class ManualPaymentDriver implements PaymentGatewayInterface
{
    public function initiate(Invoice $invoice, array $payload = []): array
    {
        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'user_id' => $invoice->user_id,
            'gateway' => 'manual',
            'method' => $payload['method'] ?? 'bKash',
            'sender_number' => $payload['sender_number'] ?? null,
            'gateway_txn_id' => $payload['gateway_txn_id'] ?? null,
            'amount' => $invoice->total,
            'currency' => $invoice->currency,
            'status' => 'pending',
            'gateway_payload' => $payload,
        ]);

        $invoice->update(['status' => 'pending_approval']);

        return [
            'status' => 'pending_approval',
            'redirect_url' => null,
            'payment' => $payment,
            'message' => "Manual payment ({$payment->method} Txn: {$payment->gateway_txn_id}) submitted for verification.",
        ];
    }
}
