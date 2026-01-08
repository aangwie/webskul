<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'archive_type_id',
        'title',
        'file_path',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function archiveType()
    {
        return $this->belongsTo(ArchiveType::class);
    }
}
