<?php

namespace App\Widgets;

use App\DTO\DashboardWidgetResult;
use App\Filters\Pages\SitePageTeamFilter;
use App\Models\SitePage;
use App\Contracts\DashboardWidget;

class PagesOnFireWidget implements DashboardWidget
{
    public const ID = 'pages-on-fire';

    public function __invoke(): DashboardWidgetResult
    {
        // Get currently down pages for the current team, eager-loading only the latest visit
        $pages = SitePage::query()
            ->where('is_down', true)
            ->tap(new SitePageTeamFilter())
            ->with([
                'latestVisit' => function ($query) {
                    // Qualify columns to avoid ambiguity in Postgres when using latestOfMany
                    $query->select(
                        'visit_records.id',
                        'visit_records.site_page_id',
                        'visit_records.status',
                        'visit_records.created_at'
                    );
                },
            ])
            // also select expected_status to explain why a seemingly OK status may still be down
            ->select(['id', 'url', 'expected_status'])
            ->orderBy('down_at', 'desc')
            ->limit(50) // avoid over-fetching in extreme cases
            ->get();

        $items = $pages->map(function (SitePage $p) {
            return [
                'id' => (string) $p->getKey(),
                'url' => $p->url,
                'status' => optional($p->latestVisit)->status,
                // expose expected statuses so the UI can clarify mismatches
                'expected' => $p->expected_status,
            ];
        })->all();

        return new DashboardWidgetResult(self::ID, [
            'count' => count($items),
            'items' => $items,
        ]);
    }
}
