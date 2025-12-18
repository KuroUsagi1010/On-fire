<?php

namespace App\Services\Sites;

use App\Filters\Pages\SitePageTeamFilter;
use App\Filters\Pages\IncludeVisitRecords;
use App\Filters\Pages\MultiSiteFilter;
use App\Filters\Sites\SpecificSite;
use App\Filters\Sites\TeamFilter;
use App\Models\Site;
use App\Models\SitePage;
use App\Models\VisitRecord;
use App\Records\SpecificPage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Makes sure team owned sites appear.
 *
 * Note: All data must already be validated before this point
 */
class CustomerSiteService
{
    /**
     * retrieve all sites or specific site
     * @param $siteId
     * @return Collection
     */
    public function sites($siteId = null, $relations = []): Collection
    {
        $query = Site::query()
            ->withCount('pages')
            ->with($relations)
            ->tap(new TeamFilter)
            ->tap(new SpecificSite($siteId));

        return $query->get();
    }

    public function pages($siteIds = []): Collection
    {
        $query = SitePage::query()
            ->withCount('records') // total records
            ->tap(new MultiSiteFilter($siteIds))
            ->tap(new SitePageTeamFilter)
            ->tap(new IncludeVisitRecords);

        return $query->get();
    }

    public function new($data = []): Site {
        $auth = auth()->user();

        return Site::create([
            'display_name' => $data['display_name'],
            'team_id' => $auth->getCurrentTeamId(),
            'user_id' => $auth->getKey()
        ]);
    }

    public function newPage(Site $site, $data = []): SitePage {
        $auth = auth()->user();

        return SitePage::create([
            'site_id' => $site->getKey(),
            'user_id' => $auth->getKey(),
            'display_name' => $data['display_name'],
            'url' => $data['url'],
            'description' => data_get($data, 'description'),
            'paused' => (bool) data_get($data, 'paused', false),
            'check_interval_seconds' => $data['check_interval_seconds'] ?? 60,
            'timeout_seconds' => $data['timeout_seconds'] ?? 30,
            'expected_status' => data_get($data, 'expected_status', [200]),
            'next_check_at' => data_get($data, 'next_check_at', now()->plus(minutes: 1)->startOfMinute()),
            'verify_ssl' => (bool) data_get($data, 'verify_ssl', false),
            'headers' => data_get($data, 'headers', []),
            'payload' => data_get($data, 'payload', []),
            'authorization_type' => data_get($data, 'authorization_type'),
            'authorization_payload' => data_get($data, 'authorization_payload'),
            'retries' => data_get($data, 'retries', 0),
            'is_down' => false,
            'down_at' => null,
        ]);
    }

    public function getPageData(SitePage|string $sitePage) {
        if(is_string($sitePage)) {
            $sitePage = (new SitePage())->resolveRouteBinding($sitePage);
        }

        return [
            'site' => Site::find($sitePage->site_id),
            'page' => $sitePage,
            'records' => $this->getVisitRecords($sitePage->getKey()),
            'uptime_percentage' => $this->getUptimePercentage($sitePage)
        ];
    }

    public function getVisitRecords($sitePageId, $limit = 100) {
        return VisitRecord::query()
            ->with('payload')
            ->tap(new SpecificPage($sitePageId))
            ->when($limit, fn($q) => $q->limit($limit))
            ->latest()
            ->get();
    }

    /**
     * Compute and cache for 1 hour the uptime percentage for the last 7 days.
     * Uptime is derived from visit records where a record is considered DOWN when
     * it has an error or did not meet the expected status.
     */
    private function getUptimePercentage(SitePage $page): ?float
    {
        $cacheKey = sprintf('sitepage:%s:uptime:last7d', $page->getKey());

        Cache::forget($cacheKey);
        return Cache::remember($cacheKey, now()->addHour(), function () use ($page) {
            $since = now()->subDays(7);

            $base = VisitRecord::query()
                ->where('site_page_id', $page->getKey())
                ->where('created_at', '>=', $since);

            $total = (clone $base)->count();
            if ($total === 0) {
                return 100; // no data
            }

            $downs = (clone $base)
                ->where(function($q) {
                    $q->where('has_met_expected_status', false);
                })
                ->count();

            $uptime = (($total - $downs) / $total) * 100;
            return round($uptime, 2);
        });
    }
}
