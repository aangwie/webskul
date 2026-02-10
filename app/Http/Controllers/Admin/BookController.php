<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookType;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with('bookType')->latest()->get();
        $bookTypes = BookType::all();
        return view('admin.library.books.index', compact('books', 'bookTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul_buku' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'pengarang' => 'required|string|max:255',
            'tahun_perolehan' => 'required|integer|min:1900|max:' . date('Y'),
            'asal_usul' => 'required|string|max:255',
            'book_type_id' => 'required|exists:book_types,id',
        ]);

        Book::create($request->all());

        return redirect()->back()->with('success', 'Buku berhasil ditambahkan');
    }

    public function edit(Book $book)
    {
        $bookTypes = BookType::all();
        return view('admin.library.books.edit', compact('book', 'bookTypes'));
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'judul_buku' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'pengarang' => 'required|string|max:255',
            'tahun_perolehan' => 'required|integer|min:1900|max:' . date('Y'),
            'asal_usul' => 'required|string|max:255',
            'book_type_id' => 'required|exists:book_types,id',
        ]);

        $book->update($request->all());

        return redirect()->route('admin.library.books.index')->with('success', 'Buku berhasil diperbarui');
    }

    public function destroy(Book $book)
    {
        $book->delete();
        return redirect()->back()->with('success', 'Buku berhasil dihapus');
    }
}
