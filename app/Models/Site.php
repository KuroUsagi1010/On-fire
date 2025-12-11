<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    /** @use HasFactory<\Database\Factories\SiteFactory> */
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $appends = [
        'page_count'
    ];

    /**
     * @return HasMany|SitePage[]
     */
    public function pages(): HasMany
    {
        return $this->hasMany(SitePage::class);
    }

    public function getPageCountAttribute() {
        return $this->pages()->count();
    }
}
