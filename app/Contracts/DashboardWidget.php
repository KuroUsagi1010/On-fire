<?php

namespace App\Contracts;

use App\DTO\DashboardWidgetResult;

/**
 * Base class for all dashboard widgets.
 *
 * Widgets must be invokable and return a DashboardWidgetResult DTO.
 * Each widget has a stable id derived from its class name by default.
 */
abstract class DashboardWidget
{
    /**
     * Default widget id is the kebab-case short class name.
     * Example: PagesTotalWidget -> pages-total-widget
     */
    public function getId(): string
    {
        $fqcn = static::class;
        $parts = explode('\\', $fqcn);
        $short = end($parts) ?: $fqcn;

        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $short));
    }

    abstract public function __invoke(): DashboardWidgetResult;
}
