<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkmRespondent extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'year',
    ];

    public function responses()
    {
        return $this->hasMany(SkmResponse::class, 'respondent_id');
    }

    public function getAverageScoreAttribute()
    {
        $total = $this->responses()->sum('score');
        $count = $this->responses()->count();
        return $count > 0 ? $total / $count : 0;
    }

    public function getIkmAttribute()
    {
        return $this->average_score * 25;
    }
}