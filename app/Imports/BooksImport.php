<?php

namespace App\Imports;

use App\Models\Book;
use App\Models\BookType;
use App\Models\BookCondition;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class BooksImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Find Book Type ID
        $bookType = BookType::where('name', $row['jenis_buku'])->first();

        if (!$bookType) {
            return null;
        }

        $book = Book::create([
            'book_type_id' => $bookType->id,
            'judul_buku' => $row['judul_buku'],
            'penerbit' => $row['penerbit'],
            'pengarang' => $row['pengarang'],
            'tahun_perolehan' => $row['tahun_perolehan'],
            'asal_usul' => $row['asal_usul'],
        ]);

        // Create Condition
        if (isset($row['jumlah']) && isset($row['kondisi'])) {
            $kondisi = strtolower($row['kondisi']) == 'laik' ? 'laik' : 'tidak_laik';

            BookCondition::create([
                'book_id' => $book->id,
                'jumlah_buku' => $row['jumlah'],
                'kondisi' => $kondisi,
            ]);
        }

        return $book;
    }

    public function rules(): array
    {
        return [
            'judul_buku' => 'required',
            'jenis_buku' => 'required|exists:book_types,name',
            'jumlah' => 'required|integer|min:1',
            'kondisi' => 'required', // Validation logic for specific values handled in model step implicitly via logic, but good to strict check
        ];
    }
}
