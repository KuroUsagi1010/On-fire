<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Channel>
 */
class ChannelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['email', 'slack', 'discord']);

        $config = match ($type) {
            'email' => [
                'to' => $this->faker->safeEmail(),
            ],
            'slack' => [
                'webhook_url' => 'https://hooks.slack.com/services/'.Str::random(24),
                'channel' => '#alerts',
            ],
            'discord' => [
                'webhook_url' => 'https://discord.com/api/webhooks/'.Str::random(30),
            ],
            default => [],
        };

        $name = match ($type) {
            'email' => 'Email Alerts',
            'slack' => 'Slack #alerts',
            'discord' => 'Discord Webhook',
            default => ucfirst($type) . ' Channel',
        };

        return [
            'id' => (string) Str::uuid(),
            'type' => $type,
            'name' => $name,
            'config' => $config,
            'active' => $this->faker->boolean(85),
            // team_id should be provided via state/for() in seeder
        ];
    }
}
