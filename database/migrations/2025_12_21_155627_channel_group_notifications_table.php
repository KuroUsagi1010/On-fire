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
        Schema::create('channel_group_notifications', function (Blueprint $table) {
            // Pivot table between channels and group_notifications
            $table->foreignUuid('channel_id')
                ->constrained('channels')
                ->cascadeOnDelete();

            $table->foreignUuid('group_notification_id')
                ->constrained('group_notifications')
                ->cascadeOnDelete();

            // Avoid duplicate mappings
            $table->unique(['channel_id', 'group_notification_id'], 'chn_grp_notif_unique');

            // Optional timestamps for auditing
            $table->timestamps();

            // Helpful indexes
            $table->index('channel_id');
            $table->index('group_notification_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_group_notifications');
    }
};
