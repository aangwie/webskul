<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookBorrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'borrower_type',
        'student_id',
        'teacher_id',
        'peminjam',
        'identitas_peminjam',
        'kelas_peminjam',
        'tanggal_pinjam',
        'jumlah_pinjam',
        'tanggal_kembali',
        'is_returned',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali' => 'date',
        'is_returned' => 'boolean',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
