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
            $table->unsignedSmallInteger('timeout_seconds')->default(30);
            $table->boolean('verify_ssl')->default(true);

            $table->json('expected_status')->nullable();

            // Scheduling
            $table->timestamp('next_check_at')->nullable()->index();

            // status
            $table->boolean('is_down')->default(false);
            $table->dateTime('down_at')->nullable();

            $table->timestamps();

            $table->index('url');
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
