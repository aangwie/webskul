<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommitteePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'committee_fee_id',
        'amount',
        'payment_date',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function committeeFee(): BelongsTo
    {
        return $this->belongsTo(CommitteeFee::class);
    }
}
