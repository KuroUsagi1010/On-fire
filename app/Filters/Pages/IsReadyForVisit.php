<?php

namespace App\Filters\Pages;

use Illuminate\Database\Eloquent\Builder;

/**
 * we can add more logic here without making the main classes murky.
 * TODO: add offlineModes to designate time where this page should not be checked
 */
class IsReadyForVisit
{
    public function __construct() { }

    public function __invoke(Builder $query) : Builder {
        return $query->whereNowOrPast('next_check_at');
    }
}
