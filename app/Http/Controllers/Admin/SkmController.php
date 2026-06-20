<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SkmQuestion;
use App\Models\SkmRespondent;
use App\Models\SkmResponse;
use Illuminate\Http\Request;

class SkmController extends Controller
{
    public function index()
    {
        $questions = SkmQuestion::orderBy('order')->orderBy('id')->get();
        return view('admin.skm.index', compact('questions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question_text' => 'required|string|max:500',
        ]);

        $maxOrder = SkmQuestion::max('order') ?? 0;

        SkmQuestion::create([
            'question_text' => $request->question_text,
            'order' => $maxOrder + 1,
        ]);

        return back()->with('success', 'Pertanyaan SKM berhasil ditambahkan!');
    }

    public function update(Request $request, SkmQuestion $skmQuestion)
    {
        $request->validate([
            'question_text' => 'required|string|max:500',
        ]);

        $skmQuestion->update([
            'question_text' => $request->question_text,
        ]);

        return back()->with('success', 'Pertanyaan SKM berhasil diperbarui!');
    }

    public function toggleActive(SkmQuestion $skmQuestion)
    {
        $skmQuestion->update([
            'is_active' => !$skmQuestion->is_active,
        ]);

        return back()->with('success', 'Status pertanyaan berhasil diubah!');
    }

    public function destroy(SkmQuestion $skmQuestion)
    {
        $skmQuestion->delete();
        return back()->with('success', 'Pertanyaan SKM berhasil dihapus!');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*' => 'integer|exists:skm_questions,id',
        ]);

        foreach ($request->orders as $index => $id) {
            SkmQuestion::where('id', $id)->update(['order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    public function reports()
    {
        $years = SkmRespondent::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');
        $selectedYear = request('year', date('Y'));

        $respondents = SkmRespondent::where('year', $selectedYear)->with('responses.question')->get();
        $questions = SkmQuestion::where('is_active', true)->orderBy('order')->get();

        // Calculate average per question
        $averages = [];
        $totalScore = 0;
        $totalCount = 0;

        foreach ($questions as $q) {
            $scores = SkmResponse::where('question_id', $q->id)
                ->whereHas('respondent', function ($query) use ($selectedYear) {
                    $query->where('year', $selectedYear);
                })
                ->pluck('score');

            $avg = $scores->count() > 0 ? $scores->average() : 0;
            $averages[$q->id] = [
                'question' => $q->question_text,
                'average' => $avg,
                'count' => $scores->count(),
            ];
            $totalScore += $scores->sum();
            $totalCount += $scores->count();
        }

        $ikm = $totalCount > 0 ? ($totalScore / $totalCount) * 25 : 0;
        $respondentCount = $respondents->count();

        // Score distribution
        $distributions = [];
        for ($i = 1; $i <= 4; $i++) {
            $distributions[$i] = SkmResponse::where('score', $i)
                ->whereHas('respondent', function ($query) use ($selectedYear) {
                    $query->where('year', $selectedYear);
                })
                ->count();
        }

        return view('admin.skm.reports', compact(
            'years',
            'selectedYear',
            'questions',
            'respondents',
            'averages',
            'ikm',
            'respondentCount',
            'totalCount',
            'distributions'
        ));
    }

    public function respondentDetail(SkmRespondent $skmRespondent)
    {
        $skmRespondent->load('responses.question');
        return view('admin.skm.respondent_detail', compact('skmRespondent'));
    }

    public function deleteRespondent(SkmRespondent $skmRespondent)
    {
        $skmRespondent->delete();
        return back()->with('success', 'Data responden berhasil dihapus!');
    }
}