<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $team = Team::query()->first();
        if (! $team) {
            $this->command?->warn('ChannelSeeder: No team found. Run OnboardSeeder first.');
            return;
        }

        // Create sample channels for the team
        $channels = [
            ['type' => 'email', 'config' => ['to' => 'alerts@example.com']],
            ['type' => 'slack', 'config' => ['channel' => '#alerts', 'webhook_url' => 'https://hooks.slack.com/services/demo']],
            ['type' => 'discord', 'config' => ['webhook_url' => 'https://discord.com/api/webhooks/demo']],
        ];

        foreach ($channels as $data) {
            /** @var Channel $channel */
            $channel = Channel::factory()
                ->state(array_merge($data, ['team_id' => $team->getKey()]))
                ->create();
        }

        $this->command?->info('ChannelSeeder: Seeded channels.');
    }
}
