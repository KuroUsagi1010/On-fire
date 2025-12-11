<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed onboarding (creates a user and a team and assigns membership)
        $this->call([
            OnboardSeeder::class,
            SiteSeeder::class,
            SitePageSeeder::class,
            VisitRecordSeeder::class,
        ]);
    }
}
