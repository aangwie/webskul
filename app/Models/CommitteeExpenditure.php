<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommitteeExpenditure extends Model
{
    protected $fillable = [
        'expenditure_number',
        'date',
        'description',
        'amount',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];
}
