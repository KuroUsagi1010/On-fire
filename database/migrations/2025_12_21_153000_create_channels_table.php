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
        Schema::create('channels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable()->after('type');
            $table->string('type'); // email | slack | discord
            $table->json('config'); // we create a service that will validate the config based on the type
            $table->boolean('active')->default(true);
            $table->foreignUuid('team_id')
                ->constrained('teams')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->index('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channels');
    }
};
