<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_admin_routes(): void
    {
        foreach (['/admin/dashboard', '/admin/users', '/admin/websites', '/admin/settings', '/admin/portfolio/projects', '/admin/interior/projects'] as $uri) {
            $this->get($uri)->assertRedirect(route('login'));
        }
    }

    public function test_guests_cannot_post_to_admin_routes(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class);

        $this->post('/admin/websites', [])->assertRedirect(route('login'));
        $this->post('/admin/settings/global', [])->assertRedirect(route('login'));
    }

    public function test_password_reset_requires_a_valid_token(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class);

        $this->post('/reset-password', [
            'token' => 'bogus',
            'email' => 'nobody@example.com',
            'password' => 'Newpass123',
            'password_confirmation' => 'Newpass123',
        ])->assertSessionHasErrors('email');
    }
}
