<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommitteeExpenditure extends Model
{
    protected $fillable = [
        'committee_activity_id',
        'expenditure_number',
        'date',
        'description',
        'amount',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function activity()
    {
        return $this->belongsTo(CommitteeActivity::class, 'committee_activity_id');
    }
}
