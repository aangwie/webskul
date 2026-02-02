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

    public function editPayment(CommitteePayment $committeePayment)
    {
        $committeePayment->load(['student', 'committeeFee']);

        // Calculate remaining amount (excluding current payment)
        $totalPaidExcludingCurrent = CommitteePayment::where('student_id', $committeePayment->student_id)
            ->where('committee_fee_id', $committeePayment->committee_fee_id)
            ->where('id', '!=', $committeePayment->id)
            ->sum('amount');

        $maxAmount = $committeePayment->committeeFee->amount - $totalPaidExcludingCurrent;

        return view('admin.committee.payments.edit', compact('committeePayment', 'maxAmount'));
    }

    public function updatePayment(Request $request, CommitteePayment $committeePayment)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // Calculate max allowed amount
        $totalPaidExcludingCurrent = CommitteePayment::where('student_id', $committeePayment->student_id)
            ->where('committee_fee_id', $committeePayment->committee_fee_id)
            ->where('id', '!=', $committeePayment->id)
            ->sum('amount');

        $maxAmount = $committeePayment->committeeFee->amount - $totalPaidExcludingCurrent;

        if ($request->amount > $maxAmount) {
            return redirect()->back()->with('error', 'Jumlah pembayaran melebihi sisa tagihan.');
        }

        $committeePayment->update([
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.committee.payments.record', $committeePayment->student_id)
            ->with('success', 'Pembayaran berhasil diperbarui.');
    }

    public function destroyPayment(CommitteePayment $committeePayment)
    {
        $studentId = $committeePayment->student_id;
        $committeePayment->delete();

        return redirect()->route('admin.committee.payments.record', $studentId)
            ->with('success', 'Pembayaran berhasil dihapus.');
    }

    public function reportIndex()
    {
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $classes = SchoolClass::where('is_active', true)->ordered()->get();
        $activeYear = AcademicYear::where('is_active', true)->first();

        $classSummaries = [];
        if ($activeYear) {
            foreach ($classes as $class) {
                $studentIds = \App\Models\Student::where('school_class_id', $class->id)->pluck('id');
                $fee = \App\Models\CommitteeFee::where('academic_year_id', $activeYear->id)
                    ->where('school_class_id', $class->id)
                    ->first();

                $totalPaid = \App\Models\CommitteePayment::whereIn('student_id', $studentIds)
                    ->whereHas('committeeFee', function ($q) use ($activeYear) {
                        $q->where('academic_year_id', $activeYear->id);
                    })->sum('amount');

                $totalStudents = $studentIds->count();
                $totalTarget = $fee ? $fee->amount * $totalStudents : 0;

                $classSummaries[] = [
                    'class' => $class,
                    'total_students' => $totalStudents,
                    'total_target' => $totalTarget,
                    'total_paid' => $totalPaid,
                    'remaining' => max(0, $totalTarget - $totalPaid),
                ];
            }
        }

        return view('admin.committee.report.index', compact('academicYears', 'classes', 'classSummaries', 'activeYear'));
    }

    public function reportGenerate(Request $request)
    {
        $filterType = $request->input('filter_type', 'academic_year');

        // Validate based on filter type
        if ($filterType === 'academic_year') {
            $request->validate([
                'academic_year_id' => 'required|exists:academic_years,id',
                'school_class_id' => 'required',
                'report_type' => 'required|in:detail,recapitulation,class_summary,all_summary',
            ]);
            $academicYear = AcademicYear::findOrFail($request->academic_year_id);
            $dateFrom = null;
            $dateTo = null;
        } else {
            $request->validate([
                'date_from' => 'required|date',
                'date_to' => 'required|date|after_or_equal:date_from',
                'school_class_id' => 'required',
                'report_type' => 'required|in:detail,recapitulation,class_summary,all_summary',
            ]);
            $academicYear = null;
            $dateFrom = $request->date_from;
            $dateTo = $request->date_to;
        }

        $reportType = $request->report_type;

        // Handle case where report type is class summary/all summary
        if ($reportType == 'class_summary' || $reportType == 'all_summary') {
            if ($request->school_class_id == 'all') {
                $classes = SchoolClass::where('is_active', true)->ordered()->get();
            } else {
                $classes = SchoolClass::where('id', $request->school_class_id)->get();
            }

            $reportData = [];
            $grandTotalTagihan = 0;
            $grandTotalTerbayar = 0;
            $grandTotalStudents = 0;

            foreach ($classes as $class) {
                $studentIds = \App\Models\Student::where('school_class_id', $class->id)->pluck('id');

                if ($filterType === 'academic_year') {
                    $fee = CommitteeFee::where('academic_year_id', $academicYear->id)
                        ->where('school_class_id', $class->id)
                        ->first();

                    $totalPaid = CommitteePayment::whereIn('student_id', $studentIds)
                        ->whereHas('committeeFee', function ($q) use ($academicYear) {
                            $q->where('academic_year_id', $academicYear->id);
                        })->sum('amount');

                    $totalStudents = $studentIds->count();
                    $totalTarget = $fee ? $fee->amount * $totalStudents : 0;
                } else {
                    // Date period filter - sum all payments in date range
                    $totalPaid = CommitteePayment::whereIn('student_id', $studentIds)
                        ->whereBetween('payment_date', [$dateFrom, $dateTo])
                        ->sum('amount');

                    $totalStudents = $studentIds->count();
                    // For date period, get the most recent fee for target calculation
                    $fee = CommitteeFee::where('school_class_id', $class->id)
                        ->latest('created_at')
                        ->first();
                    $totalTarget = $fee ? $fee->amount * $totalStudents : 0;
                }

                $reportData[] = [
                    'class' => $class,
                    'total_students' => $totalStudents,
                    'total_target' => $totalTarget,
                    'total_paid' => $totalPaid,
                    'remaining' => max(0, $totalTarget - $totalPaid),
                ];

                $grandTotalTagihan += $totalTarget;
                $grandTotalTerbayar += $totalPaid;
                $grandTotalStudents += $totalStudents;
            }

            $summary = [
                'total_students' => $grandTotalStudents,
                'total_tagihan' => $grandTotalTagihan,
                'total_terbayar' => $grandTotalTerbayar,
                'total_sisa' => max(0, $grandTotalTagihan - $grandTotalTerbayar),
            ];

            $schoolClass = ($request->school_class_id == 'all')
                ? (object) ['name' => 'Semua Kelas']
                : $classes->first();

            $committeeFee = null;

            return view('admin.committee.report.result', compact(
                'academicYear',
                'schoolClass',
                'committeeFee',
                'reportData',
                'summary',
                'reportType',
                'filterType',
                'dateFrom',
                'dateTo'
            ));
        }

        // Handle Detail / Recapitulation for One OR All Classes
        if ($request->school_class_id == 'all') {
            $classes = SchoolClass::where('is_active', true)->ordered()->get();
            $schoolClass = (object) ['name' => 'Semua Kelas'];
            $committeeFee = null;
        } else {
            $class = SchoolClass::findOrFail($request->school_class_id);
            $classes = collect([$class]);
            $schoolClass = $class;

            if ($filterType === 'academic_year') {
                $committeeFee = CommitteeFee::where('academic_year_id', $academicYear->id)
                    ->where('school_class_id', $class->id)
                    ->first();
            } else {
                $committeeFee = CommitteeFee::where('school_class_id', $class->id)
                    ->latest('created_at')
                    ->first();
            }
        }

        $reportData = [];
        $totalTagihan = 0;
        $totalTerbayar = 0;
        $totalStudentsCount = 0;

        foreach ($classes as $class) {
            if ($filterType === 'academic_year') {
                $fee = CommitteeFee::where('academic_year_id', $academicYear->id)
                    ->where('school_class_id', $class->id)
                    ->first();
            } else {
                $fee = CommitteeFee::where('school_class_id', $class->id)
                    ->latest('created_at')
                    ->first();
            }

            if (!$fee)
                continue;

            $students = Student::where('school_class_id', $class->id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            foreach ($students as $student) {
                if ($filterType === 'academic_year') {
                    $payments = CommitteePayment::where('student_id', $student->id)
                        ->where('committee_fee_id', $fee->id)
                        ->orderBy('payment_date', 'asc')
                        ->get();
                } else {
                    $payments = CommitteePayment::where('student_id', $student->id)
                        ->whereBetween('payment_date', [$dateFrom, $dateTo])
                        ->orderBy('payment_date', 'asc')
                        ->get();
                }

                $totalPaid = $payments->sum('amount');
                $remaining = $fee->amount - $totalPaid;

                $reportData[] = [
                    'student' => $student,
                    'class_name' => $class->name,
                    'fee_amount' => $fee->amount,
                    'payments' => $payments,
                    'total_paid' => $totalPaid,
                    'remaining' => max(0, $remaining),
                    'is_paid_full' => $totalPaid >= $fee->amount,
                ];

                $totalTagihan += $fee->amount;
                $totalTerbayar += $totalPaid;
            }
            $totalStudentsCount += $students->count();
        }

        $summary = [
            'total_students' => $totalStudentsCount,
            'total_tagihan' => $totalTagihan,
            'total_terbayar' => $totalTerbayar,
            'total_sisa' => max(0, $totalTagihan - $totalTerbayar),
            'lunas_count' => collect($reportData)->where('is_paid_full', true)->count(),
            'belum_lunas_count' => collect($reportData)->where('is_paid_full', false)->count(),
        ];

        return view('admin.committee.report.result', compact(
            'academicYear',
            'schoolClass',
            'committeeFee',
            'reportData',
            'summary',
            'reportType',
            'filterType',
            'dateFrom',
            'dateTo'
        ));
    }

    public function reportPdf(Request $request)
    {
        $filterType = $request->input('filter_type', 'academic_year');

        // Validate based on filter type
        if ($filterType === 'academic_year') {
            $request->validate([
                'academic_year_id' => 'required|exists:academic_years,id',
                'school_class_id' => 'required',
                'report_type' => 'required|in:detail,recapitulation,class_summary,all_summary',
            ]);
            $academicYear = AcademicYear::findOrFail($request->academic_year_id);
            $dateFrom = null;
            $dateTo = null;
        } else {
            $request->validate([
                'date_from' => 'required|date',
                'date_to' => 'required|date|after_or_equal:date_from',
                'school_class_id' => 'required',
                'report_type' => 'required|in:detail,recapitulation,class_summary,all_summary',
            ]);
            $academicYear = null;
            $dateFrom = $request->date_from;
            $dateTo = $request->date_to;
        }

        $reportType = $request->report_type;

        if ($reportType == 'class_summary' || $reportType == 'all_summary') {
            if ($request->school_class_id == 'all') {
                $classes = SchoolClass::where('is_active', true)->ordered()->get();
            } else {
                $classes = SchoolClass::where('id', $request->school_class_id)->get();
            }

            $reportData = [];
            $grandTotalTagihan = 0;
            $grandTotalTerbayar = 0;
            $grandTotalStudents = 0;

            foreach ($classes as $class) {
                $studentIds = \App\Models\Student::where('school_class_id', $class->id)->pluck('id');

                if ($filterType === 'academic_year') {
                    $fee = CommitteeFee::where('academic_year_id', $academicYear->id)
                        ->where('school_class_id', $class->id)
                        ->first();

                    $totalPaid = CommitteePayment::whereIn('student_id', $studentIds)
                        ->whereHas('committeeFee', function ($q) use ($academicYear) {
                            $q->where('academic_year_id', $academicYear->id);
                        })->sum('amount');

                    $totalStudents = $studentIds->count();
                    $totalTarget = $fee ? $fee->amount * $totalStudents : 0;
                } else {
                    $totalPaid = CommitteePayment::whereIn('student_id', $studentIds)
                        ->whereBetween('payment_date', [$dateFrom, $dateTo])
                        ->sum('amount');

                    $totalStudents = $studentIds->count();
                    $fee = CommitteeFee::where('school_class_id', $class->id)
                        ->latest('created_at')
                        ->first();
                    $totalTarget = $fee ? $fee->amount * $totalStudents : 0;
                }

                $reportData[] = [
                    'class' => $class,
                    'total_students' => $totalStudents,
                    'total_target' => $totalTarget,
                    'total_paid' => $totalPaid,
                    'remaining' => max(0, $totalTarget - $totalPaid),
                ];

                $grandTotalTagihan += $totalTarget;
                $grandTotalTerbayar += $totalPaid;
                $grandTotalStudents += $totalStudents;
            }

            $summary = [
                'total_students' => $grandTotalStudents,
                'total_tagihan' => $grandTotalTagihan,
                'total_terbayar' => $grandTotalTerbayar,
                'total_sisa' => max(0, $grandTotalTagihan - $grandTotalTerbayar),
            ];

            $schoolClass = ($request->school_class_id == 'all')
                ? (object) ['name' => 'Semua Kelas']
                : $classes->first();

            $committeeFee = null;
        }

        // Handle Detail / Recapitulation for One OR All Classes
        if ($reportType != 'class_summary' && $reportType != 'all_summary') {
            if ($request->school_class_id == 'all') {
                $classes = SchoolClass::where('is_active', true)->ordered()->get();
                $schoolClass = (object) ['name' => 'Semua Kelas'];
                $committeeFee = null;
            } else {
                $class = SchoolClass::findOrFail($request->school_class_id);
                $classes = collect([$class]);
                $schoolClass = $class;

                if ($filterType === 'academic_year') {
                    $committeeFee = CommitteeFee::where('academic_year_id', $academicYear->id)
                        ->where('school_class_id', $class->id)
                        ->first();
                } else {
                    $committeeFee = CommitteeFee::where('school_class_id', $class->id)
                        ->latest('created_at')
                        ->first();
                }
            }

            $reportData = [];
            $totalTagihan = 0;
            $totalTerbayar = 0;
            $totalStudentsCount = 0;

            foreach ($classes as $class) {
                if ($filterType === 'academic_year') {
                    $fee = CommitteeFee::where('academic_year_id', $academicYear->id)
                        ->where('school_class_id', $class->id)
                        ->first();
                } else {
                    $fee = CommitteeFee::where('school_class_id', $class->id)
                        ->latest('created_at')
                        ->first();
                }

                if (!$fee)
                    continue;

                $students = Student::where('school_class_id', $class->id)
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get();

                foreach ($students as $student) {
                    if ($filterType === 'academic_year') {
                        $payments = CommitteePayment::where('student_id', $student->id)
                            ->where('committee_fee_id', $fee->id)
                            ->orderBy('payment_date', 'asc')
                            ->get();
                    } else {
                        $payments = CommitteePayment::where('student_id', $student->id)
                            ->whereBetween('payment_date', [$dateFrom, $dateTo])
                            ->orderBy('payment_date', 'asc')
                            ->get();
                    }

                    $totalPaid = $payments->sum('amount');
                    $remaining = $fee->amount - $totalPaid;

                    $reportData[] = [
                        'student' => $student,
                        'class_name' => $class->name,
                        'fee_amount' => $fee->amount,
                        'payments' => $payments,
                        'total_paid' => $totalPaid,
                        'remaining' => max(0, $remaining),
                        'is_paid_full' => $totalPaid >= $fee->amount,
                    ];

                    $totalTagihan += $fee->amount;
                    $totalTerbayar += $totalPaid;
                }
                $totalStudentsCount += $students->count();
            }

            $summary = [
                'total_students' => $totalStudentsCount,
                'total_tagihan' => $totalTagihan,
                'total_terbayar' => $totalTerbayar,
                'total_sisa' => max(0, $totalTagihan - $totalTerbayar),
                'lunas_count' => collect($reportData)->where('is_paid_full', true)->count(),
                'belum_lunas_count' => collect($reportData)->where('is_paid_full', false)->count(),
            ];
        }

        $school = \App\Models\SchoolProfile::first();
        $signatory = auth()->user();

        return view('admin.committee.report.print', compact(
            'academicYear',
            'schoolClass',
            'committeeFee',
            'reportData',
            'summary',
            'reportType',
            'school',
            'signatory',
            'filterType',
            'dateFrom',
            'dateTo'
        ));
    }
}
