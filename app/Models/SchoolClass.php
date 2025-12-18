<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    protected $table = 'school_classes';

    protected $fillable = [
        'name',
        'grade',
        'academic_year',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the students for the class.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Get active students for the class.
     */
    public function activeStudents(): HasMany
    {
        return $this->hasMany(Student::class)->where('is_active', true);
    }

    /**
     * Scope for active classes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordering by grade and name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('grade')->orderBy('name');
    }

    /**
     * Get male students count.
     */
    public function getMaleCountAttribute(): int
    {
        return $this->activeStudents()->where('gender', 'male')->count();
    }

    /**
     * Get female students count.
     */
    public function getFemaleCountAttribute(): int
    {
        return $this->activeStudents()->where('gender', 'female')->count();
    }

    /**
     * Get total students count.
     */
    public function getTotalStudentsAttribute(): int
    {
        return $this->activeStudents()->count();
    }
}
