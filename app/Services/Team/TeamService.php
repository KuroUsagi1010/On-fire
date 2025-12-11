<?php

namespace App\Services\Team;

use App\Events\TeamCreatedEvent;
use App\Events\TeamCreatingEvent;
use App\Models\Team;

class TeamService
{
    public function create(array $data) {
        $user = auth()->user();

        event(new TeamCreatingEvent([...$data, 'user_id' => $user->getKey()]));

        $team = Team::create([
            'name' => $data['name'],
            'user_id' => $user->getKey(),
        ]);

        // Ensure the creator is also a member of the team (many-to-many)
        $team->members()->syncWithoutDetaching([$user->getKey()]);

        $user->update(['onboarded' => true]);

        event(new TeamCreatedEvent($team));

        return $team;
    }

    public function update(array $data, Team $team) {
        return tap($team)->update([
            'name' => $data['name'],
        ]);
    }
}
