<?php

namespace App\Filters\Pages;

use Illuminate\Database\Eloquent\Builder;

class SitePageTeamFilter
{
    public function __invoke(Builder $query): Builder
    {
        return $query->whereHas('site', function (Builder $query) {
            $query->where('team_id', auth()->user()->getCurrentTeamId());
        });
    }
}
