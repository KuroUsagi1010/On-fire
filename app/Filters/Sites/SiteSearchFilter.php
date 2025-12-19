<?php

namespace App\Filters\Sites;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class SiteSearchFilter
{
    public function __construct(public array $filters) {}

    public function __invoke(Builder $query) {
        if($this->matchingFilters() == []) return $query;

        $query->when(
            $site = $this->filters['site'] ?? null,
            fn (Builder $q) => $this->applySiteFilter($q, $site)
        );

        $query->when(
            $name = $this->filters['display_name'] ?? null,
            fn (Builder $q) => $q->where('display_name', 'ilike', "%{$name}%")
        );

        return $query;
    }

    private function applySiteFilter(Builder $query, string $site): void
    {
        if (Str::isUuid($site)) {
            $query->where('id', $site);

            return;
        }

        $query->where('display_name', 'ilike', "%{$site}%");
    }

    private function matchingFilters(): array
    {
        return array_intersect_key(
            $this->filters,
            array_flip($this->availableFilters())
        );
    }

    public function availableFilters(): array {
        return [
            'display_name', // queries display_name
            'site' // queries display_name or id
        ];
    }
}
