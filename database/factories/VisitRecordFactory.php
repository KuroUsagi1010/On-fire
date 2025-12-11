<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\VisitRecord;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VisitRecord>
 */
class VisitRecordFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<VisitRecord>
     */
    protected $model = VisitRecord::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Choose a status with strong bias towards successful (200) responses
        $status = fake()->biasedNumberBetween(200, 599, fn($x) => 1 - $x) < 400
            ? 200
            : fake()->numberBetween(400, 599);

        $hasError = $status >= 400;

        return [
            // Foreign key (site_page_id) is provided by the seeder/state
            'status' => $status,
            'duration_ms' => fake()->numberBetween(50, 3_000),
            'content_length' => fake()->optional(0.7)->numberBetween(256, 1_500_000),
            'has_error' => $hasError,
            'has_met_expected_status' => !$hasError, // refined in seeder if page has explicit expectations
            // created_at is set in seeder to control chronology
        ];
    }
}
