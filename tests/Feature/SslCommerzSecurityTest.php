<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class SslCommerzSecurityTest extends TestCase
{
    use DatabaseTransactions;

    protected function makeClientWithInvoice(): array
    {
        $suffix = strtolower(Str::random(6));
        $client = User::create([
            'type' => 'client',
            'name' => 'Pay Client',
            'email' => "pay_{$suffix}@example.com",
            'password' => Hash::make('password'),
        ]);
        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-'.strtoupper($suffix),
            'user_id' => $client->id,
            'billing_cycle' => 'monthly',
            'subtotal' => 1500,
            'total' => 1500,
            'currency' => 'BDT',
            'status' => 'unpaid',
        ]);

        return [$client, $invoice];
    }

    public function test_another_user_cannot_complete_someone_elses_sslcommerz_payment(): void
    {
        [$owner, $invoice] = $this->makeClientWithInvoice();
        [$attacker] = $this->makeClientWithInvoice();

        $this->actingAs($owner)->post(route('admin.billing.pay-sslcommerz', $invoice->id))->assertRedirect();
        $payment = Payment::where('invoice_id', $invoice->id)->firstOrFail();

        $this->actingAs($attacker)->get(route('admin.billing.sslcommerz.checkout', $payment->id))->assertForbidden();
        $this->actingAs($attacker)->post(route('admin.billing.sslcommerz.callback', $payment->id), ['outcome' => 'success'])->assertForbidden();

        $this->assertSame('unpaid', $invoice->fresh()->status);
    }

    public function test_simulator_is_disabled_outside_local_and_testing(): void
    {
        [$client, $invoice] = $this->makeClientWithInvoice();
        $this->actingAs($client)->post(route('admin.billing.pay-sslcommerz', $invoice->id));
        $payment = Payment::where('invoice_id', $invoice->id)->firstOrFail();

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class);
        $this->app['env'] = 'production';

        $this->actingAs($client)->post(route('admin.billing.sslcommerz.callback', $payment->id), ['outcome' => 'success'])->assertNotFound();
        $this->assertSame('unpaid', $invoice->fresh()->status);

        // And no new online payment can even be started without live credentials.
        $this->actingAs($client)->post(route('admin.billing.pay-sslcommerz', $invoice->id))->assertSessionHasErrors('payment');
    }

    public function test_live_ipn_marks_paid_only_after_validation_api_confirms(): void
    {
        config(['services.sslcommerz.store_id' => 'teststore', 'services.sslcommerz.store_password' => 'secret', 'services.sslcommerz.sandbox' => true]);

        Http::fake([
            'sandbox.sslcommerz.com/gwprocess/*' => Http::response(['status' => 'SUCCESS', 'GatewayPageURL' => 'https://sandbox.sslcommerz.com/pay/abc', 'sessionkey' => 'abc']),
            'sandbox.sslcommerz.com/validator/*' => function ($request) {
                parse_str(parse_url($request->url(), PHP_URL_QUERY), $q);
                $payment = Payment::where('gateway', 'sslcommerz')->latest('id')->first();

                return Http::response($q['val_id'] === 'GOOD'
                    ? ['status' => 'VALID', 'tran_id' => $payment->gateway_txn_id, 'amount' => '1500.00', 'currency_type' => 'BDT', 'currency_amount' => '1500.00', 'val_id' => 'GOOD', 'card_type' => 'BKASH-BKash']
                    : ['status' => 'INVALID_TRANSACTION']);
            },
        ]);

        [$client, $invoice] = $this->makeClientWithInvoice();
        $this->actingAs($client)->post(route('admin.billing.pay-sslcommerz', $invoice->id))
            ->assertRedirect('https://sandbox.sslcommerz.com/pay/abc');
        $payment = Payment::where('invoice_id', $invoice->id)->firstOrFail();

        // Live mode: the self-confirm simulator is not reachable.
        $this->actingAs($client)->post(route('admin.billing.sslcommerz.callback', $payment->id), ['outcome' => 'success'])->assertNotFound();

        // Forged IPN with a bad val_id is rejected.
        $this->post(route('payments.sslcommerz.ipn'), ['tran_id' => $payment->gateway_txn_id, 'val_id' => 'FAKE', 'status' => 'VALID'])->assertStatus(422);
        $this->assertSame('unpaid', $invoice->fresh()->status);

        // Real IPN validated by SSLCommerz completes it, and a repeat is harmless.
        $this->post(route('payments.sslcommerz.ipn'), ['tran_id' => $payment->gateway_txn_id, 'val_id' => 'GOOD'])->assertOk();
        $this->post(route('payments.sslcommerz.success'), ['tran_id' => $payment->gateway_txn_id, 'val_id' => 'GOOD'])->assertRedirect(route('admin.billing.index'));

        $this->assertSame('completed', $payment->fresh()->status);
        $this->assertSame('paid', $invoice->fresh()->status);
    }
}
