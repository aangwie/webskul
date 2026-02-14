<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommitteeActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'committee_program_id',
        'name',
        'unit_price',
        'quantity',
        'cost',
        'description',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
        'cost' => 'decimal:2',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(CommitteeProgram::class, 'committee_program_id');
    }

    public function expenditures()
    {
        return $this->hasMany(CommitteeExpenditure::class, 'committee_activity_id');
    }

    public function getUsedBudgetAttribute()
    {
        return $this->expenditures()->sum('amount');
    }

    public function getRemainingBudgetAttribute()
    {
        return $this->cost - $this->used_budget;
    }
}
