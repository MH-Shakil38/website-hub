<?php

namespace Tests\Feature;

use App\Models\SiteVisit;
use App\Models\User;
use App\Support\DashboardStats;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class DashboardRealDataTest extends TestCase
{
    protected array $createdUsers = [];

    protected function tearDown(): void
    {
        foreach ($this->createdUsers as $user) {
            $user->delete();
        }
        SiteVisit::where('path', 'like', '/dash-test-%')->delete();

        parent::tearDown();
    }

    protected function makeUser(string $type): User
    {
        $user = User::create([
            'type' => $type,
            'name' => 'Dash ' . $type,
            'email' => 'dash_' . $type . '_' . strtolower(Str::random(6)) . '@example.com',
            'password' => Hash::make('password'),
        ]);
        $this->createdUsers[] = $user;

        return $user;
    }

    public function test_dashboard_shows_no_hardcoded_demo_numbers(): void
    {
        $admin = $this->makeUser('super_admin');

        $res = $this->actingAs($admin)->get(route('admin.dashboard'));

        $res->assertOk();
        $res->assertDontSee('Mar 1, 2025');
        $res->assertDontSee('+196 new files this month');
        $res->assertDontSee('1,284');
        $res->assertSee(DashboardStats::monthRangeLabel());
        $res->assertSee('const TRAFFIC', false);
    }

    public function test_kpi_counts_new_items_this_month(): void
    {
        $before = DashboardStats::kpi([User::query()]);
        $this->makeUser('client');
        $after = DashboardStats::kpi([User::query()]);

        $this->assertSame($before['total'] + 1, $after['total']);
        $this->assertSame($before['new_this_month'] + 1, $after['new_this_month']);
        $this->assertTrue($after['trend_up']);
        $this->assertCount(DashboardStats::SPARK_MONTHS, $after['spark']);
        $this->assertSame($after['total'], end($after['spark']));
    }

    public function test_donut_slices_follow_page_counts(): void
    {
        $d = DashboardStats::donut(['a' => 3, 'b' => 1]);
        $circ = 2 * M_PI * 68;

        $this->assertEqualsWithDelta($circ * 0.75, (float) explode(' ', $d['a']['dash'])[0], 0.05);
        $this->assertEqualsWithDelta($circ * 0.25, (float) explode(' ', $d['b']['dash'])[0], 0.05);

        $empty = DashboardStats::donut(['a' => 0, 'b' => 0]);
        $this->assertSame(0.0, (float) explode(' ', $empty['a']['dash'])[0]);
    }

    public function test_public_page_views_are_recorded_and_charted(): void
    {
        $this->withHeader('User-Agent', 'Mozilla/5.0 (Windows NT 10.0) Chrome/120')
            ->get('/dash-test-missing-page'); // 404: must not be counted

        $this->assertSame(0, SiteVisit::where('path', '/dash-test-missing-page')->count());

        $now = CarbonImmutable::now();
        SiteVisit::create(['host' => 'x.test', 'path' => '/dash-test-a', 'visitor_hash' => sha1('v1'), 'visited_at' => $now]);
        SiteVisit::create(['host' => 'x.test', 'path' => '/dash-test-b', 'visitor_hash' => sha1('v1'), 'visited_at' => $now]);
        SiteVisit::create(['host' => 'x.test', 'path' => '/dash-test-c', 'visitor_hash' => sha1('v2'), 'visited_at' => $now]);

        $week = DashboardStats::traffic($now)['week'];
        $this->assertCount(7, $week['labels']);
        $this->assertGreaterThanOrEqual(3, end($week['views']));
        $this->assertGreaterThanOrEqual(2, end($week['visitors']));
    }

    public function test_landing_page_visit_is_recorded_but_admin_pages_are_not(): void
    {
        $count = SiteVisit::count();

        $this->withHeader('User-Agent', 'Mozilla/5.0 (Windows NT 10.0) Chrome/120')->get('/')->assertOk();
        $this->assertSame($count + 1, SiteVisit::count());
        SiteVisit::latest('id')->first()->update(['path' => '/dash-test-landing']);

        $this->withHeader('User-Agent', 'Googlebot/2.1')->get('/');
        $this->assertSame($count + 1, SiteVisit::count());

        $admin = $this->makeUser('super_admin');
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $this->assertSame($count + 1, SiteVisit::count());
    }

    public function test_transactions_and_activity_pages_use_real_totals(): void
    {
        $admin = $this->makeUser('super_admin');

        $res = $this->actingAs($admin)->get(route('admin.transactions'));
        $res->assertOk();
        $res->assertSee('of ' . number_format(\App\Models\Transaction::count()) . ' transactions');
        $res->assertDontSee('12 Oct 2025 - 19 Oct 2025');
        $res->assertDontSee('125,482.36');
        $res->assertSee(number_format(\App\Models\Transaction::availableBalance(), 2));

        $this->actingAs($admin)->get(route('admin.activity'))->assertOk()->assertSee('Activity Log');

        $client = $this->makeUser('client');
        $this->assertNotSame(200, $this->actingAs($client)->get(route('admin.activity'))->getStatusCode());
    }
}
