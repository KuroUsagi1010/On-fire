<?php

namespace App\Services\Dashboard;

use App\Contracts\DashboardWidget;
use Closure;
use Illuminate\Support\Facades\Cache;

/**
 * TeamDashboardCache
 *
 * Encapsulates dashboard caching concerns: per-team versioning and
 * per-widget cache keys/TTLs. Keeps logic reusable outside of any
 * specific widget service.
 */
class TeamDashboardCache
{
    /**
     * Build the cache key for a given team and widget id, including the team
     * cache version so we can invalidate all widgets at once.
     */
    public function key(string $teamId, string $widgetId): string
    {
        return sprintf('%s:dashboard:widget:%s', $teamId, $widgetId);
    }

    /**
     * Default cache duration for dashboard widgets.
     */
    public function widgetTtl(): \DateTimeInterface
    {
        return now()->addMinutes(30);
    }

    public function refresh($teamId) {
        $dashboardWidgetService = resolve(DashboardWidgetService::class);
        foreach ($dashboardWidgetService->widgets() as $widgetString) {
            /** @var DashboardWidget $widget */
            $widget = resolve($widgetString);
            Cache::forget($this->key($teamId, $widget->getId()));
        }
    }

    /**
     * Helper to remember a widget value using the composed key and default TTL.
     *
     * @template T
     * @param Closure():T $callback
     * @return T
     */
    public function rememberWidget(string $teamId, string $widgetId, Closure $callback): mixed
    {
        return Cache::remember($this->key($teamId, $widgetId), $this->widgetTtl(), $callback);
    }
}
