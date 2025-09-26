<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FromDjangoToBitrixEvent extends Model
{
    protected $fillable = [
        'payload',
        'processed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'processed_at' => 'datetime',
    ];
}
