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
        Schema::create('visit_record_payloads', function (Blueprint $table) {
            $table->bigIncrements('id');

            // One-to-one with visit_records. Payload holds heavy data separated from fast-analytics table
            $table->unsignedBigInteger('visit_record_id');
            $table->foreign('visit_record_id')
                ->references('id')
                ->on('visit_records')
                ->cascadeOnDelete();
            $table->unique('visit_record_id');

            // Heavy fields stored here
            $table->json('response_headers')->nullable();
            $table->longText('response_body')->nullable();

            // Timestamps: match lightweight table style (created_at only)
            $table->timestamp('created_at')->useCurrent();

            // Optional time-based queries
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_record_payloads');
    }
};
