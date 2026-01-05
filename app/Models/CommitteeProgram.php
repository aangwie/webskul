<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommitteeProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'name',
        'budget',
        'description',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(CommitteeActivity::class);
    }

    /**
     * Get total cost of all activities
     */
    public function getTotalCostAttribute(): float
    {
        return $this->activities()->sum('cost');
    }

    /**
     * Get remaining budget
     */
    public function getRemainingBudgetAttribute(): float
    {
        return $this->budget - $this->total_cost;
    }
}
