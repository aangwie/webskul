<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\TeachingModule;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class TeachingModuleController extends Controller
{
    public function index(Request $request)
    {
        $years = AcademicYear::orderBy('year', 'desc')->get();
        $activeYear = AcademicYear::where('is_active', true)->first();

        $selectedYearId = $request->academic_year_id ?? ($activeYear ? $activeYear->id : null);

        $query = TeachingModule::with(['subject', 'academicYear', 'schoolClass']);

        if ($selectedYearId) {
            $query->where('academic_year_id', $selectedYearId);
        }

        $modules = $query->latest()->get();

        return view('frontend.modules.index', compact('modules', 'years', 'selectedYearId'));
    }

    public function showPdf(TeachingModule $teachingModule)
    {
        $path = storage_path('app/public/' . $teachingModule->file_path);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }

    public function downloadPdf(TeachingModule $teachingModule)
    {
        $path = storage_path('app/public/' . $teachingModule->file_path);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->download($path);
    }
}
