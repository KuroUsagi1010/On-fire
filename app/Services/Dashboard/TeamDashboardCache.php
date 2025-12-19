<?php

namespace App\Services\Dashboard;

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
        $version = $this->getVersion($teamId);
        return sprintf('%s:dashboard:v%s:widget:%s', $teamId, $version, $widgetId);
    }

    /**
     * Default cache duration for dashboard widgets.
     */
    public function widgetTtl(): \DateTimeInterface
    {
        return now()->addMinutes(30);
    }

    /**
     * Cache version key per team. Used to invalidate all widget caches at once.
     */
    public function versionKey(string $teamId): string
    {
        return sprintf('%s:dashboard:version', $teamId);
    }

    /**
     * How long to keep the per-team dashboard cache version key.
     * Using a TTL prevents unbounded growth of long-lived keys.
     */
    public function versionTtl(): \DateTimeInterface
    {
        return now()->addDays(14);
    }

    /**
     * Get current cache version for a team. Defaults to 1 if missing.
     */
    public function getVersion(string $teamId): int
    {
        $key = $this->versionKey($teamId);
        $version = Cache::get($key);
        if (!is_int($version) || $version < 1) {
            $version = 1;
            Cache::put($key, $version, $this->versionTtl());
        }
        return $version;
    }

    /**
     * Bump cache version for a team to invalidate all widget caches.
     * Refreshes the TTL (sliding expiration) after the bump.
     */
    public function bumpVersion(string $teamId): void
    {
        $key = $this->versionKey($teamId);
        if (!Cache::has($key)) {
            Cache::put($key, 1, $this->versionTtl());
        }
        try {
            Cache::increment($key);
            $current = (int) Cache::get($key, 1);
            Cache::put($key, $current, $this->versionTtl());
        } catch (\Throwable $e) {
            $current = (int) Cache::get($key, 1);
            Cache::put($key, $current + 1, $this->versionTtl());
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

    /**
     * Helper to put a widget value directly.
     *
     * @param mixed $value
     */
    public function putWidget(string $teamId, string $widgetId, mixed $value): void
    {
        Cache::put($this->key($teamId, $widgetId), $value, $this->widgetTtl());
    }
}
