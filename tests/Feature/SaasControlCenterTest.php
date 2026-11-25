<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class SaasControlCenterTest extends TestCase
{
    use DatabaseTransactions;

    protected function makeUser(string $type): User
    {
        return User::create([
            'type' => $type,
            'name' => ucfirst($type).' CC',
            'email' => $type.'_cc_'.Str::lower(Str::random(6)).'@example.com',
            'password' => Hash::make('password'),
        ]);
    }

    public function test_super_admin_sees_the_control_center(): void
    {
        $this->actingAs($this->makeUser('super_admin'))
            ->get(route('admin.saas.control'))
            ->assertOk()
            ->assertSee('SaaS Control Center')
            ->assertSee('Payments to approve')
            ->assertSee('Production health');
    }

    public function test_clients_and_resellers_cannot_open_it(): void
    {
        $this->actingAs($this->makeUser('client'))->get(route('admin.saas.control'))->assertForbidden();
        $this->actingAs($this->makeUser('reseller'))->get(route('admin.saas.control'))->assertForbidden();
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.saas.control'))->assertRedirect(route('login'));
    }
}
