<?php

namespace App\Contracts;

use App\DTO\DashboardWidgetResult;

/**
 * Contract for all dashboard widgets.
 *
 * Widgets must be invokable and return a DashboardWidgetResult DTO.
 */
interface DashboardWidget
{
    public function __invoke(): DashboardWidgetResult;
}
