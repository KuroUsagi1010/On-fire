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
        Schema::create('visit_records', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignUuid('site_page_id')
                ->references('id')->on('site_pages')
                ->cascadeOnDelete();

            $table->unsignedSmallInteger('status');   // HTTP status
            $table->integer('duration_ms');           // response time
            $table->integer('content_length')->nullable();
            $table->boolean('has_error')->default(false);
            $table->boolean('has_met_expected_status')->default(false);

            $table->timestamps();

            // fast queries
            $table->index(['site_page_id', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_records');
    }
};
