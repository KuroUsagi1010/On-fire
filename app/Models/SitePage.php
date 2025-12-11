<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SitePage extends Model
{
    /** @use HasFactory<\Database\Factories\SitePageFactory> */
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'next_check_at' => 'datetime',
        'expected_status' => 'array',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function records(): HasMany
    {
        return $this->hasMany(VisitRecord::class);
    }
}
