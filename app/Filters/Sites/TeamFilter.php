<?php

namespace App\Filters\Sites;

use Illuminate\Database\Eloquent\Builder;

/**
 * Modifies the query to add a team filter
 */
class TeamFilter
{
    public function __construct() { }

    public function __invoke(Builder $query): Builder
    {
        $teamId = auth()->user()->getCurrentTeamId();
        return $query->where('team_id', $teamId);
    }
}
