<?php

namespace App\Widgets;

use App\DTO\DashboardWidgetResult;
use App\Contracts\DashboardWidget;
use App\Services\Dashboard\WidgetDataService;

class PagesOnFireWidget extends DashboardWidget
{
    public function __construct(private readonly WidgetDataService $data) { }

    public function __invoke(): DashboardWidgetResult
    {
        $data = $this->data->getOnFire();

        return new DashboardWidgetResult($this->getId(), $data);
    }
}
