<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::latest()->get();
        return view('admin.academic-years.index', compact('academicYears'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|string|unique:academic_years,year|max:20',
        ]);

        AcademicYear::create($validated);

        return redirect()->back()->with('success', 'Tahun Pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $academicYear->update($validated);

        return redirect()->back()->with('success', 'Status Tahun Pelajaran berhasil diperbarui.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();
        return redirect()->back()->with('success', 'Tahun Pelajaran berhasil dihapus.');
    }
}
