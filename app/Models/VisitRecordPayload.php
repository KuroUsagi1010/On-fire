<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitRecordPayload extends Model
{

    protected $guarded = [];

    protected $casts = [
        'response_headers' => 'array',
    ];
}
