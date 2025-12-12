<?php

namespace App\Services\Sites;

use App\Filters\Pages\SitePageTeamFilter;
use App\Filters\Pages\IncludeVisitRecords;
use App\Filters\Pages\MultiSiteFilter;
use App\Filters\Sites\SpecificSite;
use App\Filters\Sites\TeamFilter;
use App\Models\Site;
use App\Models\SitePage;
use Illuminate\Database\Eloquent\Collection;

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
            ->tap(new TeamFilter())
            ->tap(new SpecificSite($siteId));

        return $query->get();
    }

    public function pages($siteIds = []): Collection
    {
        $query = SitePage::query()
            ->withCount('records') // total records
            ->tap(new MultiSiteFilter($siteIds))
            ->tap(new SitePageTeamFilter())
            ->tap(new IncludeVisitRecords());

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
            'next_check_at' => data_get($data, 'next_check_at', now()->plus(minutes: 5)->startOfMinute()),
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
}
