<?php

namespace Tests\Feature;

use App\Http\Middleware\TrackResellerReferral;
use App\Models\Commission;
use App\Models\Domain;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PayoutRequest;
use App\Models\Plan;
use App\Models\Reseller;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\Billing\BillingService;
use App\Services\ResellerService;
use App\Services\TenantDatabaseManager;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class ResellerSystemTest extends TestCase
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
            $resellerProfile = Reseller::where('user_id', $user->id)->first();
            if ($resellerProfile) {
                WalletTransaction::where('reseller_id', $resellerProfile->id)->delete();
                Commission::where('reseller_id', $resellerProfile->id)->delete();
                PayoutRequest::where('reseller_id', $resellerProfile->id)->delete();
                $resellerProfile->delete();
            }
            Payment::where('user_id', $user->id)->delete();
            Invoice::where('user_id', $user->id)->delete();
            Subscription::where('user_id', $user->id)->delete();
            $user->delete();
        }

        parent::tearDown();
    }

    public function test_reseller_referral_link_mode_a_slot_and_wallet_deduction_mode_b_commission_and_payouts(): void
    {
        Plan::ensureDefaultsSeeded();
        $clientPlan = Plan::where('slug', 'business-pro')->firstOrFail();

        $suffix = strtolower(Str::random(5));

        $superAdmin = User::create([
            'type' => 'super_admin',
            'name' => 'Reseller Admin',
            'email' => "res_sa_{$suffix}@example.com",
            'password' => Hash::make('password'),
        ]);
        $this->createdUsers[] = $superAdmin;

        $resellerUser = User::create([
            'type' => 'reseller',
            'name' => 'Karim Digital Agency',
            'email' => "karim_res_{$suffix}@example.com",
            'password' => Hash::make('password'),
        ]);
        $this->createdUsers[] = $resellerUser;

        $reseller = $resellerUser->ensureResellerProfile();
        $reseller->update([
            'site_quota' => 1,
            'sites_used' => 0,
            'commission_rate' => 20.00,
            'wallet_balance' => 1000.00,
        ]);

        // 1. Referral Link Tracking (?ref=CODE) increments clicks & resolves referred reseller
        $refResponse = $this->get('/?ref=' . $reseller->referral_code);
        $refResponse->assertStatus(200);
        $this->assertEquals(1, $reseller->fresh()->referral_clicks);

        $resolvedResellerId = TrackResellerReferral::resolveReferredResellerUserId(request(), "newclient_{$suffix}@example.com");
        $this->assertEquals($resellerUser->id, $resolvedResellerId);

        // Self-referral check: using the reseller's own email returns null
        $selfReferralId = TrackResellerReferral::resolveReferredResellerUserId(request(), $resellerUser->email);
        $this->assertNull($selfReferralId);

        // 2. Mode A: First site uses 1 package slot (cost = 0), second site above quota deducts ৳450 wholesale from wallet
        $resellerService = app(ResellerService::class);
        $firstSlot = $resellerService->consumeSlotOrWalletForNewSite($reseller->fresh(), 'Slot Site 1');
        $this->assertEquals('slot', $firstSlot['method']);
        $this->assertEquals(0.00, $firstSlot['cost']);
        $this->assertEquals(1, $reseller->fresh()->sites_used);
        $this->assertEquals(1000.00, (float) $reseller->fresh()->wallet_balance);

        $secondWallet = $resellerService->consumeSlotOrWalletForNewSite($reseller->fresh(), 'Extra Site 2');
        $this->assertEquals('wallet', $secondWallet['method']);
        $this->assertEquals(450.00, $secondWallet['cost']);
        $this->assertEquals(2, $reseller->fresh()->sites_used);
        $this->assertEquals(550.00, (float) $reseller->fresh()->wallet_balance);

        // 3. Mode B: Referred Client pays an Invoice (৳2490) -> Reseller earns 20% commission (৳498)
        $referredClient = User::create([
            'type' => 'client',
            'reseller_id' => $resellerUser->id,
            'referred_via' => 'referral_link',
            'name' => 'Referred Client Alpha',
            'email' => "ref_alpha_{$suffix}@example.com",
            'password' => Hash::make('password'),
        ]);
        $this->createdUsers[] = $referredClient;

        $billing = app(BillingService::class);
        $subResult = $billing->createSubscriptionWithInvoice($referredClient, $clientPlan, 'monthly');
        $invoice = $subResult['invoice'];
        $this->assertNotNull($invoice);

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'user_id' => $referredClient->id,
            'gateway' => 'sslcommerz',
            'method' => 'bKash',
            'gateway_txn_id' => 'SSLCZ-REF-' . strtoupper($suffix),
            'amount' => $invoice->total,
            'currency' => 'BDT',
            'status' => 'pending',
        ]);

        $billing->completePayment($payment, $superAdmin);

        $commission = Commission::where('invoice_id', $invoice->id)->first();
        $this->assertNotNull($commission);
        $this->assertEquals('pending', $commission->status);
        $this->assertEquals(498.00, (float) $commission->amount); // 20% of 2490

        // 4. Super Admin approves/releases the pending commission -> Wallet increases by ৳498 (550 + 498 = 1048)
        $approveCommRes = $this->actingAs($superAdmin)->post(route('admin.reseller.commissions.approve', $commission->id));
        $approveCommRes->assertRedirect();

        $this->assertEquals('approved', $commission->fresh()->status);
        $this->assertEquals(1048.00, (float) $reseller->fresh()->wallet_balance);

        // 5. Reseller requests a Payout of ৳600 via bKash -> Wallet deducted to ৳448, Super Admin marks Paid
        $payoutRes = $this->actingAs($resellerUser)->post(route('admin.reseller.payouts.request'), [
            'amount' => 600,
            'method' => 'bKash',
            'account_info' => '01799887766',
        ]);
        $payoutRes->assertRedirect();

        $this->assertEquals(448.00, (float) $reseller->fresh()->wallet_balance);
        $payout = PayoutRequest::where('reseller_id', $reseller->id)->firstOrFail();
        $this->assertEquals('pending', $payout->status);

        $processRes = $this->actingAs($superAdmin)->post(route('admin.reseller.payouts.process', $payout->id), [
            'action' => 'paid',
            'transaction_reference' => 'BKS-PAID-' . strtoupper($suffix),
        ]);
        $processRes->assertRedirect();
        $this->assertEquals('paid', $payout->fresh()->status);
    }
}
