<?php

namespace Database\Seeders;

use App\Models\SitePage;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VisitRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = SitePage::query()->get(['id', 'expected_status']);

        if ($pages->isEmpty()) {
            $this->command?->warn('No SitePages found. Skipping VisitRecord seeding.');
            return;
        }

        $now = now();

        foreach ($pages as $page) {
            // Avoid unbounded growth if re-seeding repeatedly
            $existing = DB::table('visit_records')->where('site_page_id', $page->id)->count();
            if ($existing >= 10) {
                $this->command?->line("SitePage {$page->id} already has {$existing} visit records. Skipping.");
                continue;
            }

            $target = random_int(5, 15);
            $toInsert = [];

            // Normalize expected statuses; default to [200]
            $expected = $page->expected_status;
            if (!is_array($expected) || empty($expected)) {
                $expected = [200];
            }

            // Create chronological records over the past few hours
            for ($i = $target - 1; $i >= 0; $i--) {
                // 80% chance to meet expected, 20% failure/outage
                $meetsExpected = random_int(1, 100) <= 80;

                if ($meetsExpected) {
                    $status = (int) ($expected[array_rand($expected)] ?? 200);
                } else {
                    // Pick a non-expected failure status (4xx/5xx)
                    $status = random_int(400, 599);
                    // ensure it's not accidentally in expected
                    if (in_array($status, $expected, true)) {
                        $status = 500;
                    }
                }

                $hasError = $status >= 400;
                $createdAt = $now->copy()->subMinutes($i * random_int(1, 6) + random_int(0, 5));

                $toInsert[] = [
                    'site_page_id' => (string) $page->id,
                    'status' => $status,
                    'duration_ms' => random_int(50, 3000),
                    'content_length' => random_int(1, 100) <= 70 ? random_int(256, 1_500_000) : null,
                    'has_error' => $hasError,
                    'has_met_expected_status' => !$hasError && in_array($status, $expected, true),
                    'created_at' => $createdAt,
                ];
            }

            if (!empty($toInsert)) {
                DB::table('visit_records')->insert($toInsert);
                $this->command?->info("Inserted {$target} VisitRecords for SitePage {$page->id}.");
            }
        }
    }
}
