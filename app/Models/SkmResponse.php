<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkmResponse extends Model
{
    protected $fillable = [
        'respondent_id',
        'question_id',
        'score',
    ];

    public function respondent()
    {
        return $this->belongsTo(SkmRespondent::class, 'respondent_id');
    }

    public function question()
    {
        return $this->belongsTo(SkmQuestion::class, 'question_id');
    }
}