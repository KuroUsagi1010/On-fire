<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Channel extends Model
{
    /** @use HasFactory<\Database\Factories\ChannelFactory> */
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'config' => 'array',
    ];

    /**
     * The team that owns the channel.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Group notifications that are linked to this channel.
     */
    public function groupNotifications(): BelongsToMany
    {
        return $this->belongsToMany(GroupNotification::class, 'channel_group_notifications')
            ->withTimestamps();
    }
}
