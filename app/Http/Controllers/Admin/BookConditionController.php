<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCondition;
use Illuminate\Http\Request;

class BookConditionController extends Controller
{
    public function index()
    {
        $conditions = BookCondition::with(['book.bookType'])->latest()->get();
        // Get books that don't have both 'laik' and 'tidak_laik' conditions
        $books = Book::with('bookType')->where(function ($query) {
            $query->whereDoesntHave('conditions', function ($q) {
                $q->where('kondisi', 'laik');
            })->orWhereDoesntHave('conditions', function ($q) {
                $q->where('kondisi', 'tidak_laik');
            });
        })->get();
        $allBooks = Book::with('bookType')->get();
        return view('admin.library.conditions.index', compact('conditions', 'books', 'allBooks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id|unique:book_conditions,book_id,NULL,id,kondisi,' . $request->kondisi,
            'jumlah_buku' => 'required|integer|min:0',
            'kondisi' => 'required|in:laik,tidak_laik',
        ], [
            'book_id.unique' => 'Buku ini sudah memiliki data untuk kondisi yang dipilih.'
        ]);

        BookCondition::create($request->all());

        return redirect()->back()->with('success', 'Data kondisi buku berhasil ditambahkan');
    }

    public function update(Request $request, BookCondition $condition)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id|unique:book_conditions,book_id,' . $condition->id . ',id,kondisi,' . $request->kondisi,
            'jumlah_buku' => 'required|integer|min:0',
            'kondisi' => 'required|in:laik,tidak_laik',
        ], [
            'book_id.unique' => 'Buku ini sudah memiliki data untuk kondisi yang dipilih.'
        ]);

        $condition->update($request->all());

        return redirect()->back()->with('success', 'Data kondisi buku berhasil diperbarui');
    }

    public function destroy(BookCondition $condition)
    {
        $condition->delete();
        return redirect()->back()->with('success', 'Data kondisi buku berhasil dihapus');
    }
}
