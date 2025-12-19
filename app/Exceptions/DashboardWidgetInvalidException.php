<?php

namespace App\Exceptions;

use RuntimeException;

class DashboardWidgetInvalidException extends RuntimeException
{
    public static function notImplementingInterface(string $class): self
    {
        return new self(sprintf(
            'Dashboard widget "%s" must implement App\\Contracts\\DashboardWidget.',
            $class
        ));
    }

    public static function notInvokable(string $class): self
    {
        return new self(sprintf(
            'Dashboard widget "%s" must be invokable (define __invoke()).',
            $class
        ));
    }

    public static function notReturningDto(string $class): self
    {
        return new self(sprintf(
            'Dashboard widget "%s" must return an instance of App\\DTO\\DashboardWidgetResult.',
            $class
        ));
    }
}
