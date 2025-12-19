<?php

namespace App\Widgets;

use App\Models\SitePage;
use App\DTO\DashboardWidgetResult;
use App\Filters\Pages\SitePageTeamFilter;
use App\Contracts\DashboardWidget;

class PagesTotalWidget implements DashboardWidget
{
    /**
     * Identifier used on the frontend to render the corresponding component.
     */
    public const ID = 'pages-total';

    /**
     * Invoke the widget to return a typed DTO carrying id and data.
     *
     * Data contains:
     * - total: total monitored pages
     * - up: pages whose latest visit record reports no error
     */
    public function __invoke(): DashboardWidgetResult
    {
        // Use SitePage.is_down which is maintained by the visitor job
        $total = SitePage::query()
            ->tap(new SitePageTeamFilter())
            ->count();

        $up = SitePage::query()
            ->tap(new SitePageTeamFilter())
            ->where('is_down', false)
            ->count();

        return new DashboardWidgetResult(self::ID, [
            'total' => $total,
            'up' => $up,
        ]);
    }
}
