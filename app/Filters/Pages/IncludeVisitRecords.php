<?php

namespace App\Filters\Pages;

use Illuminate\Database\Eloquent\Builder;

class IncludeVisitRecords
{

    public function __construct(public $limit = null) {}

    public function __invoke(Builder $query): Builder
    {
        return $query->with(['records' => function ($query) {
            $query->select(['id', 'site_page_id', 'has_met_expected_status'])
                ->when($this->limit, function ($query) {
                    return $query->limit($this->limit);
                });
        }]);
    }
}
