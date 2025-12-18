<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VisitRecord extends Model
{
    /** @use HasFactory<\Database\Factories\VisitRecordFactory> */
    use HasFactory;
    protected $guarded = [];

    public function payload(): HasOne
    {
        return $this->hasOne(VisitRecordPayload::class);
    }


    public function sitePage(): BelongsTo
    {
        return $this->belongsTo(SitePage::class);
    }
}
