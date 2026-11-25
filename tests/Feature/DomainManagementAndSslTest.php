<?php

namespace Tests\Feature;

use App\Models\Domain;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class DomainManagementAndSslTest extends TestCase
{
    protected array $createdTenants = [];

    protected array $createdUsers = [];

    protected function tearDown(): void
    {
        $manager = app(\App\Services\TenantDatabaseManager::class);
        $manager->disconnect();

        foreach ($this->createdTenants as $tenant) {
            try {
                $manager->dropDatabase($tenant);
            } catch (\Throwable) {
            }
            Domain::where('tenant_id', $tenant->id)->delete();
            $tenant->forceDelete();
        }

        foreach ($this->createdUsers as $user) {
            $user->delete();
        }

        parent::tearDown();
    }

    public function test_custom_domain_lifecycle_verification_caddy_ssl_and_primary_301_redirect(): void
    {
        $suffix = strtolower(Str::random(5));

        $client = User::create([
            'type' => 'client',
            'name' => 'Domain Test Client',
            'email' => "dom_client_{$suffix}@example.com",
            'password' => Hash::make('password'),
            'status' => 'Active',
        ]);
        $this->createdUsers[] = $client;

        $tenant = Tenant::create([
            'owner_id' => $client->id,
            'theme_module' => 'Interior',
            'name' => 'Client Custom Domain Studio',
            'subdomain' => 'studio-' . $suffix,
            'status' => 'active',
            'db_name' => 'hub_test_dom_' . $suffix,
        ]);
        $this->createdTenants[] = $tenant;

        // Default primary subdomain
        $subDomainRecord = Domain::create([
            'tenant_id' => $tenant->id,
            'domain' => "studio-{$suffix}.localhost",
            'type' => 'subdomain',
            'is_primary' => true,
            'verified_at' => now(),
            'ssl_status' => 'active',
        ]);

        $customHost = "royalstudio-{$suffix}.com";

        // 1. Client adds custom domain
        $storeRes = $this->actingAs($client)->post(route('admin.domains.store'), [
            'tenant_id' => $tenant->id,
            'domain' => "https://www.{$customHost}/",
        ]);
        $storeRes->assertRedirect();

        $customDomain = Domain::where('domain', $customHost)->first();
        $this->assertNotNull($customDomain);
        $this->assertEquals('custom', $customDomain->type);
        $this->assertNull($customDomain->verified_at);
        $this->assertEquals('pending', $customDomain->ssl_status);
        $this->assertStringStartsWith('yourhub-verify=', (string) $customDomain->verification_token);

        // 2. Before DNS verification, Caddy domain-check MUST reject (404)
        $caddyBefore = $this->get(route('saas.caddy.domain-check', ['domain' => $customHost]));
        $caddyBefore->assertStatus(404);

        // 3. Before verification, cannot set as primary domain
        $prematurePrimary = $this->actingAs($client)->post(route('admin.domains.primary', $customDomain->id));
        $prematurePrimary->assertSessionHasErrors('domain');

        // 4. Verify DNS (using local/testing simulation flag)
        $verifyRes = $this->actingAs($client)->post(route('admin.domains.verify', $customDomain->id), [
            'simulate_local' => true,
        ]);
        $verifyRes->assertRedirect();

        $customDomain->refresh();
        $this->assertNotNull($customDomain->verified_at);
        $this->assertEquals('active', $customDomain->ssl_status);

        // 5. After verification, Caddy domain-check MUST authorize (200) for both root and www
        $caddyAfterRoot = $this->get(route('saas.caddy.domain-check', ['domain' => $customHost]));
        $caddyAfterRoot->assertStatus(200);

        $caddyAfterWww = $this->get(route('saas.caddy.domain-check', ['domain' => 'www.' . $customHost]));
        $caddyAfterWww->assertStatus(200);

        // 6. Set custom domain as Primary
        $primaryRes = $this->actingAs($client)->post(route('admin.domains.primary', $customDomain->id));
        $primaryRes->assertRedirect();

        $this->assertTrue($customDomain->fresh()->is_primary);
        $this->assertFalse($subDomainRecord->fresh()->is_primary);

        // 7. Visiting the old subdomain now 301 redirects to the verified Primary custom domain
        $redirectRes = $this->get("http://studio-{$suffix}.localhost/portfolio");
        $redirectRes->assertStatus(301);
        $redirectRes->assertRedirect("http://{$customHost}/portfolio");

        // 8. If tenant is suspended, Caddy domain-check must return 404
        $tenant->update(['status' => 'suspended']);
        $caddySuspended = $this->get(route('saas.caddy.domain-check', ['domain' => $customHost]));
        $caddySuspended->assertStatus(404);
    }
}
