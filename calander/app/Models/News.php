<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $table = 'news';

    protected $fillable = [
        'title',
        'title_nep',
        'excerpt',
        'excerpt_nep',
        'content',
        'content_nep',
        'image',
        'source_url',
        'source_name',
        'sources',
        'source_type',
        'status',
        'priority',
        'published_at'
    ];

    protected $casts = [
        'sources' => 'array',
        'status' => 'boolean',
        'priority' => 'integer',
        'published_at' => 'datetime',
    ];


    public function scopeActive($query)
    {
        return $query->where('status', true);
    }


    public function scopePublished($query)
    {
        return $query->where(function($q) {
            $q->where('published_at', '<=', now())
              ->orWhereNull('published_at');
        });
    }


    public function scopeByPriority($query)
    {
        return $query->orderByDesc('priority')
                     ->orderByDesc('published_at')
                     ->orderByDesc('id');
    }
}
