<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SitePage extends Model
{
    /** @use HasFactory<\Database\Factories\SitePageFactory> */
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'next_check_at' => 'datetime',
        'expected_status' => 'array',
        'headers' => 'array',
        'authorization_payload' => 'array',
        'payload' => 'array',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function records(): HasMany
    {
        return $this->hasMany(VisitRecord::class);
    }

    public function latestRecords(): HasMany
    {
        return $this->hasMany(VisitRecord::class)->limit(30)->latest();
    }

    /**
     * Latest single visit record for this page.
     */
    public function latestVisit(): HasOne
    {
        // Use created_at to determine the latest record
        return $this->hasOne(VisitRecord::class)->latestOfMany('created_at');
    }
}
