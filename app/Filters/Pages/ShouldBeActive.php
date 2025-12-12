<?php

namespace App\Filters\Pages;

use Illuminate\Database\Eloquent\Builder;

/**
 * A designated logic holder for determining if a page is active.
 * we can add more features / queries here in the future without bloating the main classes
 */
class ShouldBeActive
{
    public function __construct() {}

    public function __invoke(Builder $query): Builder {
        return $query->where('paused', false);
    }
}
