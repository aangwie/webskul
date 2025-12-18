<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Information extends Model
{
    protected $table = 'information';

    protected $fillable = [
        'title',
        'content',
        'is_important',
        'is_active',
    ];

    protected $casts = [
        'is_important' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeImportant($query)
    {
        return $query->where('is_important', true);
    }
}
