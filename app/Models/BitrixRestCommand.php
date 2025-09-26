<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BitrixRestCommand extends Model
{
    protected $fillable = [
        'job_id',
        'group_id',
        'method',
        'params',
        'status',
        'source',
        'debug',
        'debug_payload',
        'sent_at'
    ];

    protected $casts = [
        'params' => 'array',
        'debug' => 'boolean',
        'sent_at' => 'datetime',
    ];
}
