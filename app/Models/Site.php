<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * $pages_count - might be loaded as query modifiers / Filters $model->withCount('pages')
 */
class Site extends Model
{
    /** @use HasFactory<\Database\Factories\SiteFactory> */
    use HasFactory, HasUuids;

    protected $guarded = [];

    /**
     * @return HasMany|SitePage[]
     */
    public function pages(): HasMany
    {
        return $this->hasMany(SitePage::class);
    }
}
