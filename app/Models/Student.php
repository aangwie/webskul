<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    protected $fillable = [
        'school_class_id',
        'name',
        'gender',
        'nis',
        'enrollment_year',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'enrollment_year' => 'integer',
    ];

    /**
     * Get the class that the student belongs to.
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    /**
     * Scope for active students.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for male students.
     */
    public function scopeMale($query)
    {
        return $query->where('gender', 'male');
    }

    /**
     * Scope for female students.
     */
    public function scopeFemale($query)
    {
        return $query->where('gender', 'female');
    }

    /**
     * Get gender label in Indonesian.
     */
    public function getGenderLabelAttribute(): string
    {
        return $this->gender === 'male' ? 'Laki-laki' : 'Perempuan';
    }
}
