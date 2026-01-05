<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\CommitteeActivity;
use App\Models\CommitteeProgram;
use Illuminate\Http\Request;

class CommitteePlanningController extends Controller
{
    /**
     * Display the planning index page with year selection and program list
     */
    public function index(Request $request)
    {
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $activeYear = AcademicYear::where('is_active', true)->first();

        $selectedYearId = $request->get('year_id', $activeYear?->id);
        $selectedYear = $selectedYearId ? AcademicYear::find($selectedYearId) : $activeYear;

        $programs = [];
        if ($selectedYear) {
            $programs = CommitteeProgram::where('academic_year_id', $selectedYear->id)
                ->with('activities')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('admin.committee.planning.index', compact(
            'academicYears',
            'activeYear',
            'selectedYear',
            'programs'
        ));
    }

    /**
     * Store a new committee program
     */
    public function store(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'budget' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        CommitteeProgram::create([
            'academic_year_id' => $request->academic_year_id,
            'name' => $request->name,
            'budget' => $request->budget,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('admin.committee.planning.index', ['year_id' => $request->academic_year_id])
            ->with('success', 'Rencana program berhasil ditambahkan.');
    }

    /**
     * Update an existing program
     */
    public function update(Request $request, CommitteeProgram $program)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'budget' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        // Check if new budget is less than total activities cost
        $totalCost = $program->total_cost;
        if ($request->budget < $totalCost) {
            return redirect()
                ->back()
                ->with('error', 'Anggaran tidak boleh kurang dari total biaya kegiatan (Rp ' . number_format($totalCost, 0, ',', '.') . ').');
        }

        $program->update([
            'name' => $request->name,
            'budget' => $request->budget,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('admin.committee.planning.index', ['year_id' => $program->academic_year_id])
            ->with('success', 'Rencana program berhasil diperbarui.');
    }

    /**
     * Delete a program
     */
    public function destroy(CommitteeProgram $program)
    {
        $yearId = $program->academic_year_id;
        $program->delete();

        return redirect()
            ->route('admin.committee.planning.index', ['year_id' => $yearId])
            ->with('success', 'Rencana program berhasil dihapus.');
    }

    /**
     * Get program activities for detail modal (JSON response)
     */
    public function showActivities(CommitteeProgram $program)
    {
        $program->load('activities');

        return response()->json([
            'program' => $program,
            'activities' => $program->activities,
            'total_cost' => $program->total_cost,
            'remaining_budget' => $program->remaining_budget,
        ]);
    }

    /**
     * Store a new activity under a program
     */
    public function storeActivity(Request $request, CommitteeProgram $program)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        // Calculate total cost from unit_price and quantity
        $cost = $request->unit_price * $request->quantity;

        // Check if cost exceeds remaining budget
        $remainingBudget = $program->remaining_budget;
        if ($cost > $remainingBudget) {
            return response()->json([
                'success' => false,
                'message' => 'Biaya kegiatan melebihi sisa anggaran (Rp ' . number_format($remainingBudget, 0, ',', '.') . ').',
            ], 422);
        }

        $activity = CommitteeActivity::create([
            'committee_program_id' => $program->id,
            'name' => $request->name,
            'unit_price' => $request->unit_price,
            'quantity' => $request->quantity,
            'cost' => $cost,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kegiatan berhasil ditambahkan.',
            'activity' => $activity,
            'remaining_budget' => $program->fresh()->remaining_budget,
        ]);
    }

    /**
     * Update an activity
     */
    public function updateActivity(Request $request, CommitteeActivity $activity)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        // Calculate total cost from unit_price and quantity
        $cost = $request->unit_price * $request->quantity;

        $program = $activity->program;

        // Calculate remaining budget excluding current activity
        $remainingBudget = $program->budget - ($program->total_cost - $activity->cost);

        if ($cost > $remainingBudget) {
            return response()->json([
                'success' => false,
                'message' => 'Biaya kegiatan melebihi sisa anggaran (Rp ' . number_format($remainingBudget, 0, ',', '.') . ').',
            ], 422);
        }

        $activity->update([
            'name' => $request->name,
            'unit_price' => $request->unit_price,
            'quantity' => $request->quantity,
            'cost' => $cost,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kegiatan berhasil diperbarui.',
            'activity' => $activity,
            'remaining_budget' => $program->fresh()->remaining_budget,
        ]);
    }

    /**
     * Delete an activity
     */
    public function destroyActivity(CommitteeActivity $activity)
    {
        $programId = $activity->committee_program_id;
        $activity->delete();

        $program = CommitteeProgram::find($programId);

        return response()->json([
            'success' => true,
            'message' => 'Kegiatan berhasil dihapus.',
            'remaining_budget' => $program ? $program->remaining_budget : 0,
        ]);
    }
}
