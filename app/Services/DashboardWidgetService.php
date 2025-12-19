<?php

namespace App\Services;

use App\Widgets\PagesTotalWidget;
use App\Widgets\PagesOnFireWidget;
use App\Services\Dashboard\TeamDashboardCache;
use App\Contracts\DashboardWidget as DashboardWidgetContract;
use App\Exceptions\DashboardWidgetInvalidException;

/**
 * DashboardWidgetService
 *
 * Keeps a simple registry of dashboard widget classes and resolves their data.
 */
class DashboardWidgetService
{
    public function __construct(private readonly TeamDashboardCache $cache)
    {
    }

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
            // Add other widgets here later
        ];
    }

    /**
     * Build the data array for all registered widgets.
     * Widgets must implement the DashboardWidget contract (invokable + DTO return type).
     * Returned payload for the frontend stays as tuples: [widgetId, data]
     *
     * @return array<int, array{0: string, 1: array}>
     */
    public function resolve(): array
    {
        $payload = [];

        $teamId = (string) auth()->user()->getCurrentTeamId();

        foreach ($this->widgets() as $widgetClass) {
            // Prefer class constant ID if present so we can build the cache key without resolving data first
            $widgetId = \defined("{$widgetClass}::ID") ? \constant("{$widgetClass}::ID") : null;

            if ($widgetId) {
                $result = $this->cache->rememberWidget($teamId, $widgetId, function () use ($widgetClass) {
                    $instance = app($widgetClass);
                    if (!$instance instanceof DashboardWidgetContract) {
                        throw DashboardWidgetInvalidException::notImplementingInterface($widgetClass);
                    }
                    // Contract guarantees invokable and return type; rely on PHP type system
                    $dto = $instance();
                    return $dto->toTuple();
                });
            } else {
                // Fallback: resolve once, then cache using returned id if structure is valid
                $instance = app($widgetClass);
                if (!$instance instanceof DashboardWidgetContract) {
                    throw DashboardWidgetInvalidException::notImplementingInterface($widgetClass);
                }
                $dto = $instance();
                $result = $dto->toTuple();
                $this->cache->putWidget($teamId, $dto->id(), $result);
            }

            // Basic guard to ensure proper structure
            if (is_array($result) && count($result) === 2) {
                $payload[] = $result;
            }
        }

        return $payload;
    }
}
