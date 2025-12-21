<?php

namespace App\Services\Dashboard;

use App\Filters\Pages\SitePageTeamFilter;
use App\Models\SitePage;

/**
 * WidgetDataService
 *
 * Centralizes data access for dashboard widgets so that querying logic
 * is shared and easily testable.
 */
class WidgetDataService
{
    /**
     * Get total monitored pages and how many are currently up for the
     * authenticated user's current team.
     *
     * @return array{total:int, up:int}
     */
    public function getTotals(): array
    {
        $total = SitePage::query()
            ->tap(new SitePageTeamFilter())
            ->count();

        $up = SitePage::query()
            ->tap(new SitePageTeamFilter())
            ->where('is_down', false)
            ->count();

        return [
            'total' => $total,
            'up' => $up,
        ];
    }

    /**
     * Get pages that are currently down ("on fire") for the authenticated
     * user's current team.
     *
     * @param int $limit Max number of items to return
     * @return array{count:int, items:array<int, array{id:string, url:string, status: mixed, expected: mixed}>}
     */
    public function getOnFire(int $limit = 50): array
    {
        $pages = SitePage::query()
            ->where('is_down', true)
            ->tap(new SitePageTeamFilter())
            ->with([
                'latestVisit' => function ($query) {
                    $query->select(
                        'visit_records.id',
                        'visit_records.site_page_id',
                        'visit_records.status',
                        'visit_records.created_at'
                    );
                },
            ])
            ->select(['id', 'url', 'expected_status'])
            ->orderBy('down_at', 'desc')
            ->limit($limit)
            ->get();

        $items = $pages->map(function (SitePage $p) {
            return [
                'id' => (string) $p->getKey(),
                'url' => $p->url,
                'status' => optional($p->latestVisit)->status,
                'expected' => $p->expected_status,
            ];
        })->all();

        return [
            'count' => count($items),
            'items' => $items,
        ];
    }
}
