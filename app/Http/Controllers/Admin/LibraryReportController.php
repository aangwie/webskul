<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookType;
use App\Models\BookCondition;
use App\Models\BookBorrowing;
use Illuminate\Http\Request;

class LibraryReportController extends Controller
{
    public function index(Request $request)
    {
        $bookTypes = BookType::all();
        $query = Book::with(['bookType', 'condition', 'borrowings']);

        // Filter by book type
        if ($request->filled('book_type_id')) {
            $query->where('book_type_id', $request->book_type_id);
        }

        // Filter by year
        if ($request->filled('tahun_perolehan')) {
            $query->where('tahun_perolehan', $request->tahun_perolehan);
        }

        // Filter by condition
        if ($request->filled('kondisi')) {
            $query->whereHas('condition', function ($q) use ($request) {
                $q->where('kondisi', $request->kondisi);
            });
        }

        $books = $query->get();

        // Statistics
        $stats = [
            'total_books' => Book::count(),
            'total_types' => BookType::count(),
            'total_laik' => BookCondition::where('kondisi', 'laik')->sum('jumlah_buku'),
            'total_tidak_laik' => BookCondition::where('kondisi', 'tidak_laik')->sum('jumlah_buku'),
            'total_borrowed' => BookBorrowing::where('is_returned', false)->sum('jumlah_pinjam'),
        ];

        return view('admin.library.reports.index', compact('books', 'bookTypes', 'stats'));
    }
}
