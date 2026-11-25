<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use App\Support\LandingLocale;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class SaasLandingLanguageTest extends TestCase
{
    protected array $createdUsers = [];

    protected array $savedSettings = [];

    protected function setUp(): void
    {
        parent::setUp();

        // Remember existing values so the test leaves the database as it found it.
        foreach ([LandingLocale::SETTING_LOCALE, LandingLocale::SETTING_SWITCHER] as $key) {
            $this->savedSettings[$key] = Setting::where('key', $key)->first()?->only(['value', 'group']);
            Setting::where('key', $key)->delete();
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->savedSettings as $key => $row) {
            Setting::where('key', $key)->delete();
            if ($row) {
                Setting::setValue($key, $row['value'], $row['group']);
            }
        }

        foreach ($this->createdUsers as $user) {
            $user->delete();
        }

        parent::tearDown();
    }

    protected function makeUser(string $type): User
    {
        $suffix = strtolower(Str::random(6));
        $user = User::create([
            'type' => $type,
            'name' => 'Lang ' . $type,
            'email' => "lang_{$type}_{$suffix}@example.com",
            'password' => Hash::make('password'),
        ]);
        $this->createdUsers[] = $user;

        return $user;
    }

    public function test_landing_page_defaults_to_bangla(): void
    {
        $res = $this->get('/');

        $res->assertOk();
        $res->assertSee('<html lang="bn" dir="ltr">', false);
        $res->assertSee('সাধারণ জিজ্ঞাসা');
        $res->assertDontSee('bootstrap.rtl.min.css', false);
    }

    public function test_super_admin_can_switch_landing_page_to_arabic_with_rtl_and_to_english(): void
    {
        $admin = $this->makeUser('super_admin');

        $this->actingAs($admin)->get(route('admin.saas.language'))->assertOk()->assertSee('العربية');

        $this->actingAs($admin)
            ->post(route('admin.saas.language.update'), ['landing_locale' => 'ar', 'language_switcher' => '0'])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertSame('ar', LandingLocale::adminDefault());

        $ar = $this->get('/');
        $ar->assertSee('<html lang="ar" dir="rtl">', false);
        $ar->assertSee('bootstrap.rtl.min.css', false);
        $ar->assertSee('الأسئلة الشائعة');

        $this->actingAs($admin)
            ->post(route('admin.saas.language.update'), ['landing_locale' => 'en'])
            ->assertRedirect();

        $en = $this->get('/');
        $en->assertSee('<html lang="en" dir="ltr">', false);
        $en->assertSee('Frequently Asked Questions');
    }

    public function test_unknown_language_is_rejected(): void
    {
        $admin = $this->makeUser('super_admin');

        $this->actingAs($admin)
            ->post(route('admin.saas.language.update'), ['landing_locale' => 'fr'])
            ->assertSessionHasErrors('landing_locale');

        $this->assertSame('bn', LandingLocale::adminDefault());
    }

    public function test_visitor_lang_parameter_only_works_when_switcher_is_enabled(): void
    {
        // Switcher off: ?lang is ignored and the admin's language (Bangla) is used.
        $this->get('/?lang=en')->assertSee('<html lang="bn" dir="ltr">', false);

        Setting::setValue(LandingLocale::SETTING_SWITCHER, '1', 'language');

        $this->get('/?lang=en')->assertSee('<html lang="en" dir="ltr">', false);
        $this->get('/?lang=ar')->assertSee('<html lang="ar" dir="rtl">', false);
    }

    public function test_only_super_admin_can_open_language_settings(): void
    {
        $client = $this->makeUser('client');

        $status = $this->actingAs($client)->get(route('admin.saas.language'))->getStatusCode();
        $this->assertNotSame(200, $status);

        $this->actingAs($client)->post(route('admin.saas.language.update'), ['landing_locale' => 'ar']);
        $this->assertSame('bn', LandingLocale::adminDefault());
    }
}
