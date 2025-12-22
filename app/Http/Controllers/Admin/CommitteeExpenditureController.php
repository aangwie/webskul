<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommitteeExpenditure;
use App\Models\SchoolProfile;
use Illuminate\Http\Request;

class CommitteeExpenditureController extends Controller
{
    public function index()
    {
        $expenditures = CommitteeExpenditure::orderBy('date', 'desc')->get();
        return view('admin.committee.expenditures.index', compact('expenditures'));
    }

    public function create()
    {
        // Generate expenditure number: EXP-YYYYMMDD-XXXX
        $count = CommitteeExpenditure::whereDate('created_at', now()->toDateString())->count() + 1;
        $expNumber = 'EXP-' . now()->format('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        return view('admin.committee.expenditures.create', compact('expNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expenditure_number' => 'required|string|unique:committee_expenditures',
            'date' => 'required|date',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        CommitteeExpenditure::create($validated);

        return redirect()->route('admin.committee.expenditures.index')
            ->with('success', 'Penggunaan dana berhasil dicatat.');
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
        $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? now()->endOfMonth()->format('Y-m-d');

        $expenditures = CommitteeExpenditure::whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'asc')
            ->get();

        $school = SchoolProfile::first();
        $total = $expenditures->sum('amount');

        if ($request->has('print')) {
            return view('admin.committee.expenditures.report-print', compact('expenditures', 'school', 'startDate', 'endDate', 'total'));
        }

        return view('admin.committee.expenditures.report', compact('expenditures', 'startDate', 'endDate', 'total'));
    }
}
