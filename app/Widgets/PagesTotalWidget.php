<?php

namespace App\Widgets;

use App\Models\SitePage;
use App\DTO\DashboardWidgetResult;
use App\Filters\Pages\SitePageTeamFilter;
use App\Contracts\DashboardWidget;
use App\Services\Dashboard\WidgetDataService;

class PagesTotalWidget extends DashboardWidget
{
    public function __construct(private readonly WidgetDataService $data)
    {
    }

    /**
     * Invoke the widget to return a typed DTO carrying id and data.
     *
     * Data contains:
     * - total: total monitored pages
     * - up: pages whose latest visit record reports no error
     */
    public function __invoke(): DashboardWidgetResult
    {
        $totals = $this->data->getTotals();

        return new DashboardWidgetResult($this->getId(), $totals);
    }
}
