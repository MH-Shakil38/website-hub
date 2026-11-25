<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;

/**
 * Real numbers for the Super Admin dashboard KPI cards: totals, growth this
 * month, and a six-month sparkline, all read from the database.
 */
class DashboardStats
{
    public const SPARK_MONTHS = 6;

    /**
     * KPI for one Eloquent query (or several, e.g. media spread over module tables).
     *
     * @param  array<int, Builder>  $queries
     * @return array{total:int, new_this_month:int, trend:float, trend_up:bool, spark:array<int,int>}
     */
    public static function kpi(array $queries, ?CarbonImmutable $now = null): array
    {
        $now = $now ?? CarbonImmutable::now();
        $monthStart = $now->startOfMonth();

        $total = 0;
        $newThisMonth = 0;
        $spark = array_fill(0, self::SPARK_MONTHS, 0);

        foreach ($queries as $query) {
            try {
                $total += (clone $query)->count();
                $newThisMonth += (clone $query)->where('created_at', '>=', $monthStart)->count();

                // Running total at the end of each of the last N months (oldest first).
                for ($i = 0; $i < self::SPARK_MONTHS; $i++) {
                    $end = $monthStart->subMonths(self::SPARK_MONTHS - 1 - $i)->endOfMonth();
                    $spark[$i] += (clone $query)->where(function ($q) use ($end) {
                        $q->where('created_at', '<=', $end)->orWhereNull('created_at');
                    })->count();
                }
            } catch (\Throwable) {
                // A module table that is missing or unreachable simply adds nothing.
            }
        }

        $before = $total - $newThisMonth;
        $trend = $before > 0 ? round($newThisMonth / $before * 100, 1) : ($newThisMonth > 0 ? 100.0 : 0.0);

        return [
            'total' => $total,
            'new_this_month' => $newThisMonth,
            'trend' => $trend,
            'trend_up' => $newThisMonth > 0,
            'spark' => $spark,
        ];
    }

    /** Media rows across every installed industry module. */
    public static function mediaQueries(): array
    {
        $queries = [];
        foreach (array_keys(ModuleCmsResolver::SUPPORTED_MODULES) as $module) {
            $class = ModuleCmsResolver::mediaModel($module);
            if (class_exists($class)) {
                try {
                    $queries[] = $class::query();
                } catch (\Throwable) {
                }
            }
        }

        return $queries;
    }

    /**
     * SVG path for a small sparkline inside an 86x42 box.
     *
     * @param  array<int, int|float>  $values
     * @return array{line:string, area:string}
     */
    public static function sparkPath(array $values, int $width = 86, int $height = 42): array
    {
        $values = array_values($values);
        $count = count($values);
        if ($count < 2) {
            $values = [0, 0];
            $count = 2;
        }

        $max = max($values);
        $min = min($values);
        $range = $max - $min;
        $top = 6;
        $bottom = $height - 6;

        $points = [];
        foreach ($values as $i => $v) {
            $x = 2 + ($width - 4) * $i / ($count - 1);
            $y = $range > 0 ? $bottom - ($v - $min) / $range * ($bottom - $top) : ($max > 0 ? $top + ($bottom - $top) / 2 : $bottom);
            $points[] = [round($x, 1), round($y, 1)];
        }

        $line = 'M' . $points[0][0] . ' ' . $points[0][1];
        for ($i = 1; $i < $count; $i++) {
            $line .= ' L' . $points[$i][0] . ' ' . $points[$i][1];
        }
        $area = $line . ' L' . $points[$count - 1][0] . ' ' . ($height - 2) . ' L' . $points[0][0] . ' ' . ($height - 2) . ' Z';

        return ['line' => $line, 'area' => $area];
    }

    /**
     * Donut slices (stroke-dasharray / offset) for a circle of the given radius.
     *
     * @param  array<string, int>  $parts
     * @return array<string, array{dash:string, offset:float}>
     */
    public static function donut(array $parts, float $radius = 68): array
    {
        $circ = 2 * M_PI * $radius;
        $sum = array_sum($parts);
        $out = [];
        $start = 0.0;

        foreach ($parts as $key => $value) {
            $len = $sum > 0 ? $value / $sum * $circ : 0;
            // Start at 12 o'clock: a quarter turn back.
            $out[$key] = [
                'dash' => round($len, 2) . ' ' . round($circ, 2),
                'offset' => round($circ / 4 - $start, 2),
            ];
            $start += $len;
        }

        return $out;
    }

    /**
     * Website traffic (visitors and page views) for the dashboard chart, for
     * four ranges: last 24 hours, 7 days, 30 days and 12 months.
     *
     * @return array<string, array{labels: array<int,string>, visitors: array<int,int>, views: array<int,int>}>
     */
    public static function traffic(?CarbonImmutable $now = null): array
    {
        $now = $now ?? CarbonImmutable::now();

        $ranges = [
            'day' => ['buckets' => 24, 'unit' => 'hour', 'fmt' => '%Y-%m-%d %H', 'key' => 'Y-m-d H', 'label' => 'ga'],
            'week' => ['buckets' => 7, 'unit' => 'day', 'fmt' => '%Y-%m-%d', 'key' => 'Y-m-d', 'label' => 'D'],
            'month' => ['buckets' => 30, 'unit' => 'day', 'fmt' => '%Y-%m-%d', 'key' => 'Y-m-d', 'label' => 'M j'],
            'year' => ['buckets' => 12, 'unit' => 'month', 'fmt' => '%Y-%m', 'key' => 'Y-m', 'label' => 'M'],
        ];

        $out = [];
        foreach ($ranges as $name => $r) {
            $first = match ($r['unit']) {
                'hour' => $now->startOfHour()->subHours($r['buckets'] - 1),
                'day' => $now->startOfDay()->subDays($r['buckets'] - 1),
                'month' => $now->startOfMonth()->subMonths($r['buckets'] - 1),
            };

            $rows = collect();
            try {
                $rows = \App\Models\SiteVisit::query()
                    ->where('visited_at', '>=', $first)
                    ->selectRaw("DATE_FORMAT(visited_at, '{$r['fmt']}') as bucket, COUNT(*) as views, COUNT(DISTINCT visitor_hash) as visitors")
                    ->groupBy('bucket')
                    ->get()
                    ->keyBy('bucket');
            } catch (\Throwable) {
                // Table not migrated yet: show an empty chart.
            }

            $labels = $visitors = $views = [];
            for ($i = 0; $i < $r['buckets']; $i++) {
                $at = match ($r['unit']) {
                    'hour' => $first->addHours($i),
                    'day' => $first->addDays($i),
                    'month' => $first->addMonths($i),
                };
                $row = $rows->get($at->format($r['key']));
                $labels[] = $at->format($r['label']);
                $visitors[] = (int) ($row->visitors ?? 0);
                $views[] = (int) ($row->views ?? 0);
            }

            $out[$name] = ['labels' => $labels, 'visitors' => $visitors, 'views' => $views];
        }

        return $out;
    }

    public static function monthRangeLabel(?CarbonImmutable $now = null): string
    {
        $now = $now ?? CarbonImmutable::now();

        return $now->startOfMonth()->format('M j, Y') . ' - ' . $now->endOfMonth()->format('M j, Y');
    }
}
