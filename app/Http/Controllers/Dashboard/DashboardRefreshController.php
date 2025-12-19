<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\TeamDashboardCache;
use Illuminate\Http\RedirectResponse;

class DashboardRefreshController extends Controller
{
    public function __invoke(TeamDashboardCache $cache): RedirectResponse
    {
        // Invalidate all dashboard widget caches for the current team by bumping the cache version
        $cache->bumpVersion((string) auth()->user()->getCurrentTeamId());

        // Redirect back to dashboard; Inertia will reload fresh data
        return redirect()->route('dashboard');
    }
}
