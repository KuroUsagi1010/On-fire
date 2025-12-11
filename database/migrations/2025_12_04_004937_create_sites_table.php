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
        Schema::create('sites', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string("display_name");
            $table->foreignUuid('team_id')
                ->constrained('teams')
                ->nullOnDelete();
            $table->foreignUuid('user_id')
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();

            $table->index(['team_id', 'display_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
