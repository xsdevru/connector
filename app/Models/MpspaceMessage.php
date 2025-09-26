<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MpspaceMessage extends Model
{
    protected $fillable = [
        'message_id',
        'payload',
        'status',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
