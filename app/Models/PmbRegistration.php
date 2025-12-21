<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PmbRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_number',
        'nama',
        'nisn',
        'nik',
        'birth_place',
        'birth_date',
        'address',
        'registration_type',
        'mother_name',
        'father_name',
        'guardian_name',
        'phone_number',
        'academic_year',
        'kk_attachment',
        'birth_certificate_attachment',
        'ijazah_attachment',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];
}
