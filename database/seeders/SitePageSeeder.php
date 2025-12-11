<?php

namespace Database\Seeders;

use App\Models\Site;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SitePageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sites = Site::query()->withCount('pages')->get();

        if ($sites->isEmpty()) {
            $this->command?->warn('No sites found. Skipping SitePage seeding.');
            return;
        }

        $now = now();
        $insertRows = [];

        foreach ($sites as $site) {
            // Prevent unbounded growth when re-seeding; only seed pages for sites without pages yet
            if ($site->pages_count > 0) {
                continue;
            }

            $pagesToCreate = random_int(1, 4);

            for ($i = 1; $i <= $pagesToCreate; $i++) {
                $insertRows[] = [
                    'id' => (string) Str::uuid(),
                    'site_id' => (string) $site->getKey(),
                    'display_name' => "Page {$i} for {$site->display_name}",
                    'url' => sprintf('https://example.com/%s/page-%d', Str::slug($site->display_name), $i),
                    'description' => null,
                    'user_id' => (string) $site->user_id,
                    'paused' => false,
                    'check_interval_seconds' => 300,
                    'timeout_seconds' => 30,
                    'verify_ssl' => true,
                    'expected_status' => json_encode([200, 201]),
                    'next_check_at' => $now->copy()->addMinutes(random_int(1, 30)),
                    'created_at' => $now,
                    'updated_at' => $now,
                    'is_down' => fake()->boolean(20),
                ];
            }
        }

        if (! empty($insertRows)) {
            DB::table('site_pages')->insert($insertRows);
            $this->command?->info('Seeded Site Pages for existing Sites.');
        } else {
            $this->command?->line('No Site Pages were inserted (sites already have pages).');
        }
    }
}
