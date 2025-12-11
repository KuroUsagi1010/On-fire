<?php

namespace App\Filters\Pages;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class MultiSiteFilter
{
    public function __construct(public $siteIds = []) {
        $this->siteIds = array_filter((array) $siteIds, fn($id) => Str::isUuid($id));
    }

    public function __invoke(Builder $query): Builder
    {
        return $query->when($this->siteIds, fn(Builder $q) => $q->whereIn('site_id', $this->siteIds));
    }
}
