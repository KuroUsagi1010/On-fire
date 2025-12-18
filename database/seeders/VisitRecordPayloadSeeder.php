<?php

namespace Database\Seeders;

use App\Models\VisitRecord;
use App\Models\VisitRecordPayload;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class VisitRecordPayloadSeeder extends Seeder
{
    /**
     * Seed VisitRecordPayload for each VisitRecord (if missing).
     */
    public function run(): void
    {
        $faker = Faker::create();

        VisitRecord::query()
            ->with('payload')
            ->orderBy('id')
            ->chunkById(500, function ($records) use ($faker) {
                foreach ($records as $record) {
                    if ($record->payload) {
                        continue; // already has payload
                    }

                    // Create some realistic-looking headers
                    $headers = [
                        'content-type' => $faker->randomElement(['application/json', 'text/html; charset=utf-8', 'text/plain']),
                        'server' => $faker->randomElement(['nginx', 'Apache', 'cloudflare']),
                        'cache-control' => $faker->randomElement(['no-cache', 'max-age=60', 'public, max-age=300']),
                        'date' => now()->toRfc7231String(),
                    ];

                    // Generate a plausible body depending on status
                    if ($record->status >= 200 && $record->status < 300) {
                        // JSON success sample
                        $body = json_encode([
                            'ok' => true,
                            'message' => $faker->sentence(),
                            'data' => [
                                'id' => $record->id,
                                'duration_ms' => $record->duration_ms,
                            ],
                        ], JSON_PRETTY_PRINT);
                    } elseif ($record->status >= 400 && $record->status < 600) {
                        // Error HTML/text sample
                        $body = "Error {$record->status}: " . $faker->sentence(8);
                    } else {
                        // Plain text
                        $body = $faker->paragraphs(2, true);
                    }

                    VisitRecordPayload::create([
                        'visit_record_id' => $record->getKey(),
                        'response_headers' => $headers,
                        'response_body' => $body,
                    ]);
                }
            });
    }
}
