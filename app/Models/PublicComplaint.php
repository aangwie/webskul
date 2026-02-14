<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicComplaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'type',
        'description',
        'complaint_code',
        'response',
        'status',
    ];
}
