<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->whereKey('019aec21-8d2e-7083-8d5c-34b14ab091f7')->first();

        if (! $user) {
            $this->command?->warn('No users found. Skipping Site seeding.');
            return;
        }

        // Try to get the user's first team (method exists on User model)
        $team = method_exists($user, 'firstTeam') ? $user->firstTeam() : null;

        if (! $team) {
            $this->command?->warn('User has no team. Skipping Site seeding.');
            return;
        }

        $now = now();

        $rows = [];
        for ($i = 1; $i <= 2; $i++) {
            $rows[] = [
                'id' => (string) Str::uuid(),
                'display_name' => "Example Site {$i}",
                'team_id' => (string) $team->getKey(),
                'user_id' => (string) $user->getKey(),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('sites')->insert($rows);

        $this->command?->info('Seeded Sites for the first user and their first team.');
    }
}
