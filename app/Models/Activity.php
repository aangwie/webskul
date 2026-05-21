<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Activity extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'image',
        'category',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($activity) {
            if (empty($activity->slug) || $activity->isDirty('title') || $activity->isDirty('slug')) {
                $baseSlug = Str::slug($activity->slug ?: $activity->title);
                $activity->slug = static::generateUniqueSlug($baseSlug, $activity->id);
            }
        });
    }

    public static function generateUniqueSlug($slug, $excludeId = null)
    {
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->when($excludeId, function ($query, $id) {
            return $query->where('id', '!=', $id);
        })->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeNews($query)
    {
        return $query->where('category', 'news');
    }

    public function scopeEvents($query)
    {
        return $query->where('category', 'event');
    }
}
