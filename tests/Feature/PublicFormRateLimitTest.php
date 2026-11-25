<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicFormRateLimitTest extends TestCase
{
    public function test_public_contact_form_is_rate_limited(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class);

        // Empty payloads fail validation (302) but still count against the limiter.
        for ($i = 0; $i < 10; $i++) {
            $this->post('/portfolio/contact-submit', [])->assertStatus(302);
        }

        $this->post('/portfolio/contact-submit', [])->assertStatus(429);
    }
}
