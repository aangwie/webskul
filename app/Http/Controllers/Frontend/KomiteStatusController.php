<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\CommitteeFee;
use App\Models\CommitteePayment;
use App\Models\Student;
use Illuminate\Http\Request;

class KomiteStatusController extends Controller
{
    public function index(Request $request)
    {
        $student = null;
        $paymentData = null;

        if ($request->has('nis') && $request->nis) {
            $student = Student::where('nis', $request->nis)
                ->where('is_active', true)
                ->with('schoolClass')
                ->first();

            if ($student) {
                // Get active academic year
                $activeYear = AcademicYear::where('is_active', true)->first();

                if ($activeYear) {
                    // Get committee fee for this student's class
                    $committeeFee = CommitteeFee::where('academic_year_id', $activeYear->id)
                        ->where('school_class_id', $student->school_class_id)
                        ->first();

                    if ($committeeFee) {
                        // Get all payments for this student
                        $payments = CommitteePayment::where('student_id', $student->id)
                            ->where('committee_fee_id', $committeeFee->id)
                            ->orderBy('payment_date', 'desc')
                            ->get();

                        $totalPaid = $payments->sum('amount');
                        $remaining = $committeeFee->amount - $totalPaid;
                        $isPaidFull = $totalPaid >= $committeeFee->amount;

                        $paymentData = [
                            'academic_year' => $activeYear,
                            'committee_fee' => $committeeFee,
                            'payments' => $payments,
                            'total_paid' => $totalPaid,
                            'remaining' => max(0, $remaining),
                            'is_paid_full' => $isPaidFull,
                        ];
                    }
                }
            } else {
                session()->flash('error', 'Data siswa tidak ditemukan. Pastikan NIS yang dimasukkan sudah benar.');
            }
        }

        return view('pages.komite-status', compact('student', 'paymentData'));
    }
}
