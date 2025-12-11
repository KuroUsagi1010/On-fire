<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Http\Requests\Team\TeamStoreRequest;
use App\Models\Team;
use App\Services\Team\TeamService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TeamController extends Controller
{
    public function create(Request $request) {
        return Inertia::render('team/Create', []);
    }

    public function store(TeamStoreRequest $request, TeamService $teamService) {
        $teamService->create($request->validated());

        return redirect()->route('dashboard');
    }

    public function update(TeamStoreRequest $request, TeamService $teamService, Team $team) {
        $teamService->update($request->validated(), $team);
        return redirect()->route('dashboard');
    }
}
