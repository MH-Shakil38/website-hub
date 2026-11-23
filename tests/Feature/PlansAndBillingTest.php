<?php

namespace Tests\Feature;

use App\Models\Domain;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Billing\BillingService;
use App\Services\TenantDatabaseManager;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class PlansAndBillingTest extends TestCase
{
    protected array $createdTenants = [];

    protected array $createdUsers = [];

    protected function tearDown(): void
    {
        $manager = app(TenantDatabaseManager::class);
        $manager->disconnect();

        foreach ($this->createdTenants as $tenant) {
            try {
                $manager->dropDatabase($tenant);
            } catch (\Throwable) {
            }
            Domain::where('tenant_id', $tenant->id)->delete();
            Subscription::where('tenant_id', $tenant->id)->delete();
            Invoice::where('tenant_id', $tenant->id)->delete();
            $tenant->forceDelete();
        }

        foreach ($this->createdUsers as $user) {
            Payment::where('user_id', $user->id)->delete();
            Invoice::where('user_id', $user->id)->delete();
            Subscription::where('user_id', $user->id)->delete();
            $user->delete();
        }

        parent::tearDown();
    }

    public function test_manual_payment_approval_and_sslcommerz_callback_activate_subscription_and_enforce_plan_limits(): void
    {
        Plan::ensureDefaultsSeeded();
        $starterPlan = Plan::where('slug', 'single-theme-starter')->firstOrFail();
        $proPlan = Plan::where('slug', 'business-pro')->firstOrFail();

        $suffix = strtolower(Str::random(5));

        $superAdmin = User::create([
            'type' => 'super_admin',
            'name' => 'Billing Admin',
            'email' => "bill_sa_{$suffix}@example.com",
            'password' => Hash::make('password'),
        ]);
        $this->createdUsers[] = $superAdmin;

        $client = User::create([
            'type' => 'client',
            'name' => 'Billing Client',
            'email' => "bill_client_{$suffix}@example.com",
            'password' => Hash::make('password'),
        ]);
        $this->createdUsers[] = $client;

        $tenant = Tenant::create([
            'owner_id' => $client->id,
            'theme_module' => 'Interior',
            'name' => 'Client Billing Site',
            'subdomain' => 'bill-' . $suffix,
            'status' => 'suspended',
            'db_name' => 'hub_test_bill_' . $suffix,
        ]);
        $this->createdTenants[] = $tenant;

        // 1. Client subscribes to Single Theme Starter Plan (site_limit = 1, custom_domain = false)
        $subRes = $this->actingAs($client)->post(route('admin.plans.subscribe', $starterPlan->id), [
            'billing_cycle' => 'monthly',
            'tenant_id' => $tenant->id,
        ]);
        $subRes->assertRedirect();

        $invoice = Invoice::where('user_id', $client->id)->where('plan_id', $starterPlan->id)->first();
        $this->assertNotNull($invoice);
        $this->assertEquals('unpaid', $invoice->status);

        // 2. Client submits Manual bKash Payment
        $manualRes = $this->actingAs($client)->post(route('admin.billing.pay-manual', $invoice->id), [
            'method' => 'bKash',
            'sender_number' => '01711223344',
            'gateway_txn_id' => 'TRX-' . strtoupper($suffix),
        ]);
        $manualRes->assertRedirect();
        $this->assertEquals('pending_approval', $invoice->fresh()->status);

        $payment = Payment::where('invoice_id', $invoice->id)->firstOrFail();
        $this->assertEquals('pending', $payment->status);

        // 3. Super Admin approves Manual Payment -> Invoice becomes paid, Subscription & Tenant become active
        $approveRes = $this->actingAs($superAdmin)->post(route('admin.billing.payments.approve', $payment->id));
        $approveRes->assertRedirect();

        $this->assertEquals('completed', $payment->fresh()->status);
        $this->assertEquals('paid', $invoice->fresh()->status);
        $this->assertEquals('active', $tenant->fresh()->status);

        // 4. Enforce Plan Limits: Starter plan allows only 1 site and NO custom domain
        $secondSiteAttempt = $this->actingAs($client)->post(route('admin.tenants.store'), [
            'name' => 'Second Site Over Limit',
            'theme_module' => 'Pharmacy',
            'subdomain' => 'overlimit-' . $suffix,
        ]);
        $secondSiteAttempt->assertSessionHasErrors('name');

        $customDomainAttempt = $this->actingAs($client)->post(route('admin.domains.store'), [
            'tenant_id' => $tenant->id,
            'domain' => "notallowed-{$suffix}.com",
        ]);
        $customDomainAttempt->assertSessionHasErrors('domain');

        // 5. Client upgrades to Business Pro via SSLCommerz Online Payment
        $proSubRes = $this->actingAs($client)->post(route('admin.plans.subscribe', $proPlan->id), [
            'billing_cycle' => 'yearly',
            'tenant_id' => $tenant->id,
        ]);
        $proSubRes->assertRedirect();

        $proInvoice = Invoice::where('user_id', $client->id)->where('plan_id', $proPlan->id)->firstOrFail();

        $sslInitRes = $this->actingAs($client)->post(route('admin.billing.pay-sslcommerz', $proInvoice->id));
        $sslInitRes->assertRedirect();

        $sslPayment = Payment::where('invoice_id', $proInvoice->id)->where('gateway', 'sslcommerz')->firstOrFail();

        // Simulate SSLCommerz Success Callback
        $callbackRes = $this->actingAs($client)->post(route('admin.billing.sslcommerz.callback', $sslPayment->id), [
            'outcome' => 'success',
            'channel' => 'bKash - SSLCommerz',
        ]);
        $callbackRes->assertRedirect(route('admin.billing.index'));

        $this->assertEquals('paid', $proInvoice->fresh()->status);
        $this->assertEquals($proPlan->id, $tenant->fresh()->plan_id);

        // 6. Now on Business Pro (custom_domain = true), client CAN connect a custom domain
        $allowedDomainRes = $this->actingAs($client)->post(route('admin.domains.store'), [
            'tenant_id' => $tenant->id,
            'domain' => "proallowed-{$suffix}.com",
        ]);
        $allowedDomainRes->assertSessionDoesntHaveErrors();

        // 7. Printable Invoice view renders 200 OK
        $printRes = $this->actingAs($client)->get(route('admin.billing.invoices.show', $proInvoice->id));
        $printRes->assertStatus(200);
        $printRes->assertSee($proInvoice->invoice_number);

        // 8. Test trial/subscription expiration auto-suspension after 3-day grace period
        $tenant->update(['status' => 'trial', 'trial_ends_at' => now()->subDays(5)]);
        $proInvoice->subscription->update([
            'status' => 'active',
            'ends_at' => now()->subDays(5),
            'grace_ends_at' => now()->subDays(2),
        ]);

        $stats = app(BillingService::class)->processExpirations();
        $this->assertGreaterThanOrEqual(1, $stats['suspended_tenants']);
        $this->assertEquals('suspended', $tenant->fresh()->status);
        $this->assertEquals('expired', $proInvoice->subscription->fresh()->status);
    }
}
