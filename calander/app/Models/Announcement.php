<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'nep_title',
        'type',
        'priority',
        'status',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'status' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];
}
