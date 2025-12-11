<?php

namespace App\Filters\Sites;

use Illuminate\Database\Eloquent\Builder;

class SpecificSite
{
    public function __construct(public $siteId = null) { }

    public function __invoke(Builder $query): Builder
    {
        return $query->when($this->siteId, fn(Builder $q) => $q->whereKey($this->siteId));
    }
}
