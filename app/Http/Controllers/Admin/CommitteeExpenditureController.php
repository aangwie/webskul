<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\CommitteeActivity;
use App\Models\CommitteeProgram;
use App\Models\CommitteeExpenditure;
use App\Models\SchoolProfile;
use Illuminate\Http\Request;

class CommitteeExpenditureController extends Controller
{
    public function index(Request $request)
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $selectedYearId = $request->get('academic_year_id', $activeYear ? $activeYear->id : null);

        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $selectedYear = AcademicYear::find($selectedYearId);

        // Fetch programs for the selected year with activities and their expenditures
        $programs = [];
        if ($selectedYearId) {
            $programs = CommitteeProgram::where('academic_year_id', $selectedYearId)
                ->with(['activities.expenditures'])
                ->get()
                ->map(function ($program) {
                    // Calculate totals
                    $program->total_budget = $program->activities->sum('cost');
                    $program->total_used = $program->activities->sum(function ($activity) {
                        return $activity->expenditures->sum('amount');
                    });
                    $program->balance = $program->total_budget - $program->total_used;
                    return $program;
                });
        }

        // Fetch expenditures listing (filter by year if possible, but currently showing all or valid ones)
        // Ideally we should filter expenditures by the selected year's date range, but expenditures are linked to activities which are linked to programs which are linked to years. 
        // So a better filter is expenditures where activity.program.academic_year_id = selectedYearId

        $query = CommitteeExpenditure::with(['activity.program.academicYear']);

        if ($selectedYearId) {
            $query->whereHas('activity.program', function ($q) use ($selectedYearId) {
                $q->where('academic_year_id', $selectedYearId);
            });
        }

        $expenditures = $query->orderBy('date', 'desc')->get();

        return view('admin.committee.expenditures.index', compact('expenditures', 'academicYears', 'selectedYear', 'programs'));
    }

    public function create()
    {
        // Generate expenditure number: EXP-YYYYMMDD-XXXX
        $count = CommitteeExpenditure::whereDate('created_at', now()->toDateString())->count() + 1;
        $expNumber = 'EXP-' . now()->format('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $activeYear = AcademicYear::where('is_active', true)->first();
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();

        return view('admin.committee.expenditures.create', compact('expNumber', 'activeYear', 'academicYears'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expenditure_number' => 'required|string|unique:committee_expenditures',
            'date' => 'required|date',
            'committee_activity_id' => 'required|exists:committee_activities,id',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $activity = CommitteeActivity::findOrFail($request->committee_activity_id);

        if ($request->amount > $activity->remaining_budget) {
            return back()->withErrors(['amount' => 'Jumlah pengeluaran melebihi sisa anggaran (Rp ' . number_format($activity->remaining_budget, 0, ',', '.') . ')'])->withInput();
        }

        CommitteeExpenditure::create($validated);

        return redirect()->route('admin.committee.expenditures.index')
            ->with('success', 'Penggunaan dana berhasil dicatat.');
    }

    public function getPrograms(Request $request)
    {
        $programs = CommitteeProgram::where('academic_year_id', $request->year_id)
            ->with('activities')
            ->get();
        return response()->json($programs);
    }

    public function getActivities(Request $request)
    {
        $activities = CommitteeActivity::where('committee_program_id', $request->program_id)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'name' => $activity->name,
                    'remaining_budget' => $activity->remaining_budget,
                    'formatted_remaining_budget' => number_format($activity->remaining_budget, 0, ',', '.')
                ];
            });
        return response()->json($activities);
    }

    public function edit(CommitteeExpenditure $expenditure)
    {
        return view('admin.committee.expenditures.edit', compact('expenditure'));
    }

    public function update(Request $request, CommitteeExpenditure $expenditure)
    {
        $validated = $request->validate([
            'expenditure_number' => 'required|string|unique:committee_expenditures,expenditure_number,' . $expenditure->id,
            'date' => 'required|date',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $expenditure->update($validated);

        return redirect()->route('admin.committee.expenditures.index')
            ->with('success', 'Penggunaan dana berhasil diperbarui.');
    }

    public function destroy(CommitteeExpenditure $expenditure)
    {
        $expenditure->delete();

        return redirect()->route('admin.committee.expenditures.index')
            ->with('success', 'Penggunaan dana berhasil dihapus.');
    }

    public function print(CommitteeExpenditure $expenditure)
    {
        $school = SchoolProfile::first();
        return view('admin.committee.expenditures.print', compact('expenditure', 'school'));
    }

    public function report(Request $request)
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $selectedYearId = $request->get('academic_year_id', $activeYear ? $activeYear->id : null);

        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $selectedYear = AcademicYear::find($selectedYearId);

        $query = CommitteeExpenditure::with(['activity.program']);

        if ($selectedYearId) {
            $query->whereHas('activity.program', function ($q) use ($selectedYearId) {
                $q->where('academic_year_id', $selectedYearId);
            });
        }

        $expenditures = $query->orderBy('date', 'asc')->get();

        // Group by Activity ID for the view
        $groupedExpenditures = $expenditures->groupBy(function ($item) {
            return $item->activity->name; // Group by name as requested, or ID if preferred. Name is better for display key.
        });

        $school = SchoolProfile::first();
        $total = $expenditures->sum('amount');

        if ($request->has('print')) {
            return view('admin.committee.expenditures.report-print', compact('groupedExpenditures', 'school', 'selectedYear', 'total', 'academicYears'));
        }

        return view('admin.committee.expenditures.report', compact('groupedExpenditures', 'selectedYear', 'total', 'academicYears'));
    }
}
