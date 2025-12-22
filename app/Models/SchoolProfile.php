<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolProfile extends Model
{
    protected $fillable = [
        'name',
        'address',
        'city',
        'phone',
        'email',
        'vision',
        'mission',
        'history',
        'logo',
    ];
}
