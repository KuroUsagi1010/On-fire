<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OnboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use a deterministic UUID so other seeders (e.g., SiteSeeder) can rely on it
        $fixedUserId = '019aec21-8d2e-7083-8d5c-34b14ab091f7';

        $user = User::query()->find($fixedUserId);
        if (! $user) {
            $user = new User([
                'name' => 'Demo User',
                'email' => 'demo@example.com',
                'password' => Hash::make('password'),
                'onboarded' => true,
            ]);
            // Explicitly set the UUID primary key (bypasses mass-assignment restrictions)
            $user->id = $fixedUserId;
            $user->save();
        }

        // Create or find a team owned by this user
        $team = Team::query()->firstOrCreate(
            [
                'name' => 'Demo Team',
                'user_id' => $user->getKey(),
            ],
            []
        );

        // Ensure the user is a member of the team (many-to-many pivot)
        $team->members()->syncWithoutDetaching([$user->getKey()]);

        $this->command?->info('OnboardSeeder: Created/ensured demo user and team, and linked membership.');
    }
}
