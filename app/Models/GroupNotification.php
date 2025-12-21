<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GroupNotification extends Model
{
    /** @use HasFactory<\Database\Factories\GroupNotificationFactory> */
    use HasFactory;

    protected $guarded = [];

    /**
     * The team that owns the group notification.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Channels associated with this group notification.
     */
    public function channels(): BelongsToMany
    {
        return $this->belongsToMany(Channel::class, 'channel_group_notifications')
            ->withTimestamps();
    }
}
