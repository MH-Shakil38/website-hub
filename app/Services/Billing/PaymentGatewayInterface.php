<?php

namespace App\Services\Billing;

use App\Models\Invoice;
use App\Models\Payment;

interface PaymentGatewayInterface
{
    /**
     * Initiate a payment for the given invoice.
     * Returns ['status' => string, 'redirect_url' => ?string, 'payment' => Payment, 'message' => string].
     */
    public function initiate(Invoice $invoice, array $payload = []): array;
}
