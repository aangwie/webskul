<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul_buku',
        'penerbit',
        'pengarang',
        'tahun_perolehan',
        'asal_usul',
        'book_type_id',
    ];

    public function bookType()
    {
        return $this->belongsTo(BookType::class);
    }

    public function condition()
    {
        return $this->hasOne(BookCondition::class);
    }

    public function borrowings()
    {
        return $this->hasMany(BookBorrowing::class);
    }
}
