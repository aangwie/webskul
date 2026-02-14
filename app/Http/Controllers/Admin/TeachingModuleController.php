<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeachingModule;
use App\Models\Subject;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeachingModuleController extends Controller
{
    public function index()
    {
        $modules = TeachingModule::with(['subject', 'academicYear', 'schoolClass'])->latest()->get();
        $subjects = Subject::all();
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $classes = SchoolClass::where('is_active', true)->ordered()->get();
        return view('admin.teaching_modules.index', compact('modules', 'subjects', 'academicYears', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'subject_id' => 'required|exists:subjects,id',
            'school_class_id' => 'nullable|exists:school_classes,id',
            'file' => 'required|mimes:pdf|max:5120', // Max 5MB
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('teaching-modules', 'public');

            TeachingModule::create([
                'academic_year_id' => $request->academic_year_id,
                'subject_id' => $request->subject_id,
                'school_class_id' => $request->school_class_id,
                'title' => $file->getClientOriginalName(), // Use filename as title initially
                'description' => $request->description,
                'file_path' => $path,
            ]);
        }

        return redirect()->route('admin.teaching-modules.index')->with('success', 'Modul Ajar berhasil diunggah.');
    }

    public function destroy(TeachingModule $teachingModule)
    {
        if ($teachingModule->file_path && Storage::disk('public')->exists($teachingModule->file_path)) {
            Storage::disk('public')->delete($teachingModule->file_path);
        }

        $teachingModule->delete();
        return redirect()->route('admin.teaching-modules.index')->with('success', 'Modul Ajar berhasil dihapus.');
    }
}
