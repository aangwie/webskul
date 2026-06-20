<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkmQuestion extends Model
{
    protected $fillable = [
        'question_text',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function responses()
    {
        return $this->hasMany(SkmResponse::class, 'question_id');
    }
}