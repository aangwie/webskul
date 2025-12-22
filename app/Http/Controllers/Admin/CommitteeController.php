<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\CommitteeFee;
use App\Models\CommitteePayment;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;

class CommitteeController extends Controller
{
    public function indexNominal()
    {
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        return view('admin.committee.nominal.index', compact('academicYears'));
    }

    public function setNominal(AcademicYear $academicYear)
    {
        $classes = SchoolClass::where('is_active', true)->ordered()->get();
        $existingFees = CommitteeFee::where('academic_year_id', $academicYear->id)
            ->get()
            ->pluck('amount', 'school_class_id');

        return view('admin.committee.nominal.set', compact('academicYear', 'classes', 'existingFees'));
    }

    public function storeNominal(Request $request, AcademicYear $academicYear)
    {
        $request->validate([
            'nominal' => 'required|array',
            'nominal.*' => 'required|numeric|min:0',
        ]);

        foreach ($request->nominal as $classId => $amount) {
            CommitteeFee::updateOrCreate(
                [
                    'academic_year_id' => $academicYear->id,
                    'school_class_id' => $classId,
                ],
                ['amount' => $amount]
            );
        }

        return redirect()->route('admin.committee.nominal.index')->with('success', 'Nominal dana komite berhasil disimpan.');
    }

    public function indexPayments()
    {
        $classes = SchoolClass::where('is_active', true)->ordered()->get();
        return view('admin.committee.payments.index', compact('classes'));
    }

    public function studentPayments(SchoolClass $schoolClass)
    {
        $students = Student::where('school_class_id', $schoolClass->id)
            ->where('is_active', true)
            ->get();

        // Get the active academic year to find the relevant committee fee
        $activeYear = AcademicYear::where('is_active', true)->first();

        if (!$activeYear) {
            return redirect()->back()->with('error', 'Tidak ada tahun ajaran aktif. Silakan aktifkan tahun ajaran terlebih dahulu.');
        }

        $committeeFee = CommitteeFee::where('academic_year_id', $activeYear->id)
            ->where('school_class_id', $schoolClass->id)
            ->first();

        if (!$committeeFee) {
            return redirect()->back()->with('error', 'Nominal dana komite untuk kelas ini belum diatur untuk tahun ajaran aktif.');
        }

        foreach ($students as $student) {
            $totalPaid = CommitteePayment::where('student_id', $student->id)
                ->where('committee_fee_id', $committeeFee->id)
                ->sum('amount');

            $student->total_paid = $totalPaid;
            $student->remaining = $committeeFee->amount - $totalPaid;
            $student->is_paid_full = $totalPaid >= $committeeFee->amount;
        }

        return view('admin.committee.payments.students', compact('schoolClass', 'students', 'committeeFee'));
    }

    public function recordPayment(Student $student)
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $committeeFee = CommitteeFee::where('academic_year_id', $activeYear->id)
            ->where('school_class_id', $student->school_class_id)
            ->first();

        if (!$committeeFee) {
            return redirect()->back()->with('error', 'Nominal dana komite belum diatur.');
        }

        $payments = CommitteePayment::where('student_id', $student->id)
            ->where('committee_fee_id', $committeeFee->id)
            ->orderBy('payment_date', 'desc')
            ->get();

        $totalPaid = $payments->sum('amount');
        $remaining = $committeeFee->amount - $totalPaid;

        return view('admin.committee.payments.record', compact('student', 'committeeFee', 'payments', 'totalPaid', 'remaining'));
    }

    public function storePayment(Request $request, Student $student)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
            'committee_fee_id' => 'required|exists:committee_fees,id',
        ]);

        $fee = CommitteeFee::find($request->committee_fee_id);
        $totalPaid = CommitteePayment::where('student_id', $student->id)
            ->where('committee_fee_id', $fee->id)
            ->sum('amount');

        $remaining = $fee->amount - $totalPaid;

        if ($request->amount > $remaining) {
            return redirect()->back()->with('error', 'Jumlah pembayaran melebihi sisa tagihan.');
        }

        $payment = CommitteePayment::create([
            'student_id' => $student->id,
            'committee_fee_id' => $request->committee_fee_id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.committee.payments.record', $student->id)->with('success', 'Pembayaran berhasil dicatat.');
    }

    public function receipt(CommitteePayment $committeePayment)
    {
        $committeePayment->load(['student.schoolClass', 'committeeFee.academicYear']);

        $totalPaid = CommitteePayment::where('student_id', $committeePayment->student_id)
            ->where('committee_fee_id', $committeePayment->committee_fee_id)
            ->sum('amount');

        $isPaidFull = $totalPaid >= $committeePayment->committeeFee->amount;

        return view('admin.committee.payments.receipt', compact('committeePayment', 'isPaidFull'));
    }
}
