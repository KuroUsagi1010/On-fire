<?php

namespace App\Services\Dashboard;

use App\Contracts\DashboardWidget as DashboardWidgetBase;
use App\Exceptions\DashboardWidgetInvalidException;
use App\Widgets\PagesOnFireWidget;
use App\Widgets\PagesTotalWidget;

/**
 * DashboardWidgetService
 *
 * Keeps a simple registry of dashboard widget classes and resolves their data.
 */
readonly class DashboardWidgetService
{
    public function __construct(private TeamDashboardCache $cache) { }

    /**
     * Return the list of widget classes to be used on the dashboard.
     *
     * @return array<class-string>
     */
    public function widgets(): array
    {
        return [
            PagesTotalWidget::class,
            PagesOnFireWidget::class,
        ];
    }

    /**
     * Build the data array for all registered widgets.
     * Widgets must extend the DashboardWidget base (invokable + DTO return type).
     * Returned payload for the frontend stays as tuples: [widgetId, data]
     *
     * @return array<int, array{0: string, 1: array}>
     */
    public function resolve(): array
    {
        $payload = [];

        $teamId = (string) auth()->user()->getCurrentTeamId();

        foreach ($this->widgets() as $widgetClass) {
            $instance = app($widgetClass);
            if (!$instance instanceof DashboardWidgetBase) {
                throw DashboardWidgetInvalidException::notImplementingInterface($widgetClass);
            }

            $widgetId = $instance->getId();

            $result = $this->cache->rememberWidget($teamId, $widgetId, function () use ($instance) {
                $dto = $instance();
                return $dto->toTuple();
            });

            $payload[] = $result;
        }

        return $payload;
    }
}
