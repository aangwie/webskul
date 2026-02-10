<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookCondition extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'jumlah_buku',
        'kondisi',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
