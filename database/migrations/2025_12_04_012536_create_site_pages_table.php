<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('site_pages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('site_id')
                ->constrained('sites')
                ->cascadeOnDelete();

            $table->string('display_name')->nullable();
            $table->string('url');
            $table->longText('description')->nullable();

            $table->foreignUuid('user_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->boolean('paused')->default(false);

            // Checking Configuration
            $table->unsignedSmallInteger('check_interval_seconds')->default(300); // 5 minutes
            $table->unsignedTinyInteger('timeout_seconds')->default(30);

            // Client configuration
            $table->boolean('verify_ssl')->default(true);
            $table->string('http_method')->default('GET');
            $table->json('payload')->nullable();
            $table->string('authorization_type')->nullable(); // bearer|digest|basic|grant
            $table->json('authorization_payload')->nullable();
            $table->json('headers')->nullable();
            $table->unsignedTinyInteger('retries')->default(0);

            $table->json('expected_status')->nullable();

            // Scheduling
            $table->timestamp('next_check_at')->nullable()->index();

            // status
            $table->boolean('is_down')->default(false);
            $table->dateTime('down_at')->nullable();


            $table->timestamps();

            $table->index(['url', 'next_check_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_pages');
    }
};
