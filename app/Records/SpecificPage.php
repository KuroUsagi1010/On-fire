<?php

namespace App\Records;

use Illuminate\Database\Eloquent\Builder;

class SpecificPage
{
    public function __construct(public string|array $pageId) {}

    public function __invoke(Builder $query) {
        $query->when(is_string($this->pageId), fn(Builder $q) => $q->where('site_page_id', $this->pageId))
            ->when(is_array($this->pageId), fn(Builder $q) => $q->whereIn('site_page_id', $this->pageId));
    }
}
