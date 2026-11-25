<?php

namespace Tests\Feature;

use App\Models\Domain;
use App\Models\Plan;
use App\Models\SaasSection;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantDatabaseManager;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class SaasHomepageAndOnboardingTest extends TestCase
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
            $tenant->forceDelete();
        }

        foreach ($this->createdUsers as $user) {
            Subscription::where('user_id', $user->id)->delete();
            $user->delete();
        }

        parent::tearDown();
    }

    public function test_saas_homepage_builder_updates_landing_page_and_step_by_step_theme_install_wizard_provisions_db_after_email_verification(): void
    {
        Plan::ensureDefaultsSeeded();
        $starterPlan = Plan::where('slug', 'single-theme-starter')->firstOrFail();
        $suffix = strtolower(Str::random(5));

        $superAdmin = User::create([
            'type' => 'super_admin',
            'name' => 'Homepage Admin',
            'email' => "home_sa_{$suffix}@example.com",
            'password' => Hash::make('password'),
        ]);
        $this->createdUsers[] = $superAdmin;

        // 1. Super Admin updates SaaS Homepage Hero Heading via /admin/saas-homepage
        $customHeroHeading = 'Build Next-Gen SaaS Websites ' . strtoupper($suffix);
        $updateRes = $this->actingAs($superAdmin)->post(route('admin.saas.homepage.update'), [
            'sections' => [
                'hero' => [
                    'is_visible' => 1,
                    'content' => [
                        'heading_line_1' => $customHeroHeading,
                        'heading_gradient' => 'With Isolated MySQL DBs',
                    ],
                ],
                'themes' => ['is_visible' => 1],
                'features' => ['is_visible' => 1],
                'pricing' => ['is_visible' => 1],
                'faq' => ['is_visible' => 1],
                'cta_footer' => ['is_visible' => 1],
                'seo' => ['is_visible' => 1],
            ],
        ]);
        $updateRes->assertRedirect();

        // Landing page (/) now renders the custom heading from core_saas_sections
        $landingRes = $this->get('/');
        $landingRes->assertStatus(200);
        $landingRes->assertSee($customHeroHeading);

        // 2. Step 2 Wizard: AJAX Live Subdomain Availability Check
        $availCheck = $this->getJson(route('saas.install.check-subdomain', ['subdomain' => 'royal-' . $suffix]));
        $availCheck->assertStatus(200)->assertJson(['available' => true]);

        $reservedCheck = $this->getJson(route('saas.install.check-subdomain', ['subdomain' => 'app']));
        $reservedCheck->assertStatus(200)->assertJson(['available' => false]);

        // 3. Step 3 Wizard: Client submits Theme Installation Registration
        $sub = 'wizard-' . $suffix;
        $clientEmail = "wizard_client_{$suffix}@example.com";

        $regRes = $this->post(route('saas.install.register'), [
            'theme_module' => 'Interior',
            'plan_id' => $starterPlan->id,
            'billing_cycle' => 'monthly',
            'site_name' => 'Wizard Interior Studio',
            'subdomain' => $sub,
            'name' => 'Wizard Owner',
            'email' => $clientEmail,
            'phone' => '01700112233',
            'password' => 'password123',
        ]);

        $createdClient = User::where('email', $clientEmail)->first();
        $this->assertNotNull($createdClient);
        $this->createdUsers[] = $createdClient;

        $pendingTenant = Tenant::where('subdomain', $sub)->first();
        $this->assertNotNull($pendingTenant);
        $this->createdTenants[] = $pendingTenant;

        // Crucial check: Before email verification, status MUST be 'pending_verification' and db_name MUST be null!
        $this->assertEquals('pending_verification', $pendingTenant->status);
        $this->assertNull($pendingTenant->db_name);
        $this->assertNotNull($pendingTenant->verification_token);

        $regRes->assertRedirect(route('saas.install.verify.notice', ['uuid' => $pendingTenant->uuid]));

        // 4. Step 4 Wizard: Client clicks Email Verification link -> Database is created, theme migrated & 14-day trial started!
        $verifyRes = $this->post(route('saas.install.verify.confirm', [
            'uuid' => $pendingTenant->uuid,
            'token' => $pendingTenant->verification_token,
        ]));

        $verifyRes->assertRedirect(route('saas.install.success', ['uuid' => $pendingTenant->uuid]));

        $pendingTenant->refresh();
        $this->assertEquals('trial', $pendingTenant->status);
        $this->assertNotNull($pendingTenant->db_name);
        $this->assertNotNull($pendingTenant->email_verified_at);
        $this->assertEquals($createdClient->id, auth()->id());

        // Subscription record created in 'trial' status
        $trialSub = Subscription::where('tenant_id', $pendingTenant->id)->first();
        $this->assertNotNull($trialSub);
        $this->assertEquals('trial', $trialSub->status);

        // Success screen renders 200 OK with database name and subdomain
        $successScreen = $this->get(route('saas.install.success', ['uuid' => $pendingTenant->uuid]));
        $successScreen->assertStatus(200);
        $successScreen->assertSee($pendingTenant->db_name);

        // 5. Stale Unverified Cleanup Command removes 48h+ unverified pending_verification rows
        $staleTenant = Tenant::create([
            'owner_id' => $createdClient->id,
            'theme_module' => 'Pharmacy',
            'name' => 'Stale Bot Signup',
            'subdomain' => 'stale-' . $suffix,
            'status' => 'pending_verification',
            'db_name' => null,
        ]);
        Tenant::where('id', $staleTenant->id)->update(['created_at' => now()->subHours(50)]);

        Artisan::call('tenants:cleanup-unverified', ['--hours' => 48]);
        $this->assertNull(Tenant::withTrashed()->find($staleTenant->id));

        // Restore default hero heading
        SaasSection::where('section_key', 'hero')->delete();
    }
}
