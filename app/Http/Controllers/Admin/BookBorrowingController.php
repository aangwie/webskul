<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookBorrowing;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;

class BookBorrowingController extends Controller
{
    public function index()
    {
        $borrowings = BookBorrowing::with(['book.bookType', 'student.schoolClass', 'teacher'])->latest()->get();
        $studentBorrowings = $borrowings->where('borrower_type', 'student');
        $teacherBorrowings = $borrowings->where('borrower_type', 'teacher');
        $otherBorrowings = $borrowings->whereNull('borrower_type');

        $books = Book::with('bookType')->get();
        $students = Student::with('schoolClass')->active()->get();
        $teachers = Teacher::active()->get();
        return view('admin.library.borrowings.index', compact('studentBorrowings', 'teacherBorrowings', 'otherBorrowings', 'books', 'students', 'teachers'));
    }

    public function store(Request $request)
    {

        $rules = [
            'book_id' => 'required|exists:books,id',
            'nomor_buku' => 'nullable|string|max:255',
            'borrower_type' => 'required|in:student,teacher',
            'tanggal_pinjam' => 'required|date',
            'jumlah_pinjam' => 'required|integer|min:1',
        ];

        // Only validate the relevant borrower ID field
        if ($request->borrower_type === 'student') {
            $rules['student_id'] = 'required|exists:students,id';
        } else {
            $rules['teacher_id'] = 'required|exists:teachers,id';
        }

        $request->validate($rules);

        try {
            $data = [
                'book_id' => $request->book_id,
                'nomor_buku' => $request->nomor_buku,
                'borrower_type' => $request->borrower_type,
                'tanggal_pinjam' => $request->tanggal_pinjam,
                'jumlah_pinjam' => $request->jumlah_pinjam,
                'is_returned' => false,
            ];

            if ($request->borrower_type === 'student') {
                $student = Student::with('schoolClass')->find($request->student_id);
                $data['student_id'] = $student->id;
                $data['teacher_id'] = null;
                $data['peminjam'] = $student->name;
                $data['identitas_peminjam'] = $student->nis;
                $data['kelas_peminjam'] = $student->schoolClass->name ?? '-';
            } else {
                $teacher = Teacher::find($request->teacher_id);
                $data['teacher_id'] = $teacher->id;
                $data['student_id'] = null;
                $data['peminjam'] = $teacher->name;
                $data['identitas_peminjam'] = $teacher->nip;
                $data['kelas_peminjam'] = null;
            }

            $borrowing = BookBorrowing::create($data);

            return redirect()->route('admin.library.borrowings.index')->with('success', 'Data peminjaman berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit(BookBorrowing $borrowing)
    {
        $books = Book::with('bookType')->get();
        $students = Student::with('schoolClass')->active()->get();
        $teachers = Teacher::active()->get();
        return view('admin.library.borrowings.index', compact('borrowing', 'books', 'students', 'teachers'));
    }

    public function update(Request $request, BookBorrowing $borrowing)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'nomor_buku' => 'nullable|string|max:255',
            'borrower_type' => 'required|in:student,teacher',
            'student_id' => 'required_if:borrower_type,student|exists:students,id',
            'teacher_id' => 'required_if:borrower_type,teacher|exists:teachers,id',
            'tanggal_pinjam' => 'required|date',
            'jumlah_pinjam' => 'required|integer|min:1',
            'is_returned' => 'nullable|boolean',
            'tanggal_kembali' => 'nullable|date|after_or_equal:tanggal_pinjam',
        ]);

        $data = $request->only(['book_id', 'nomor_buku', 'borrower_type', 'tanggal_pinjam', 'jumlah_pinjam']);
        $data['is_returned'] = $request->has('is_returned') ? true : false;
        $data['tanggal_kembali'] = $data['is_returned'] ? $request->tanggal_kembali : null;

        if ($request->borrower_type === 'student') {
            $student = Student::with('schoolClass')->find($request->student_id);
            $data['student_id'] = $student->id;
            $data['teacher_id'] = null;
            $data['peminjam'] = $student->name;
            $data['identitas_peminjam'] = $student->nis;
            $data['kelas_peminjam'] = $student->schoolClass->name ?? '-';
        } else {
            $teacher = Teacher::find($request->teacher_id);
            $data['teacher_id'] = $teacher->id;
            $data['student_id'] = null;
            $data['peminjam'] = $teacher->name;
            $data['identitas_peminjam'] = $teacher->nip;
            $data['kelas_peminjam'] = null;
        }

        $borrowing->update($data);

        return redirect()->route('admin.library.borrowings.index')->with('success', 'Data peminjaman berhasil diperbarui');
    }

    public function destroy(BookBorrowing $borrowing)
    {
        try {
            $borrowing->delete();

            return redirect()->back()->with('success', 'Data peminjaman berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function markReturned(Request $request, BookBorrowing $borrowing)
    {
        $request->validate([
            'tanggal_kembali' => 'required|date|after_or_equal:' . $borrowing->tanggal_pinjam->format('Y-m-d'),
        ]);

        try {
            $borrowing->update([
                'is_returned' => true,
                'tanggal_kembali' => $request->tanggal_kembali,
            ]);

            return redirect()->back()->with('success', 'Buku berhasil dikembalikan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengembalikan buku: ' . $e->getMessage());
        }
    }
}
