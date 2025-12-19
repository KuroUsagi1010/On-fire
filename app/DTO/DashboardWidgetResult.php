<?php

namespace App\DTO;

/**
 * Value object representing the result of a dashboard widget.
 *
 * Carries a stable widget id and an arbitrary associative data payload.
 */
class DashboardWidgetResult
{
    /**
     * @param string $id Stable widget identifier used on the frontend
     * @param array $data Arbitrary associative payload for the widget
     */
    public function __construct(
        private readonly string $id,
        private readonly array $data,
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }

    /**
     * @return array<string, mixed>
     */
    public function data(): array
    {
        return $this->data;
    }

    /**
     * Convert to transport shape expected by the frontend resolver.
     *
     * @return array{0: string, 1: array}
     */
    public function toTuple(): array
    {
        return [$this->id, $this->data];
    }
}
