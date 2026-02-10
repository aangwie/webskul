<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookType;
use Illuminate\Http\Request;

class BookTypeController extends Controller
{
    public function index()
    {
        $types = BookType::all();
        return view('admin.library.book_types.index', compact('types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        BookType::create($request->all());

        return redirect()->back()->with('success', 'Jenis Buku berhasil ditambahkan');
    }

    public function update(Request $request, BookType $bookType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $bookType->update($request->all());

        return redirect()->back()->with('success', 'Jenis Buku berhasil diperbarui');
    }

    public function destroy(BookType $bookType)
    {
        $bookType->delete();
        return redirect()->back()->with('success', 'Jenis Buku berhasil dihapus');
    }
}
