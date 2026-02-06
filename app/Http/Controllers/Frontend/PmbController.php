<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PmbRegistration;
use App\Models\Setting;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\SchoolProfile;

class PmbController extends Controller
{
    public function index()
    {
        if (!Setting::isPmbOpen()) {
            $startDate = Setting::get('pmb_start_date', '');
            $endDate = Setting::get('pmb_end_date', '');
            return view('pages.pmb-closed', compact('startDate', 'endDate'));
        }

        $academicYears = AcademicYear::active()->get();

        return view('pages.pmb', compact('academicYears'));
    }

    public function store(Request $request)
    {
        if (!Setting::isPmbOpen()) {
            return redirect()->route('home')->with('error', 'Penerimaan Murid Baru saat ini sedang ditutup.');
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nisn' => 'required|string|max:20|unique:pmb_registrations,nisn',
            'nik' => 'required|string|max:20|unique:pmb_registrations,nik',
            'birth_place' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'address' => 'required|string',
            'registration_type' => 'required|in:baru,pindahan',
            'mother_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'guardian_name' => 'nullable|string|max:255',
            'phone_number' => 'required|string|max:20',
            'academic_year' => 'required|string|max:20',
            'kk_attachment' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'birth_certificate_attachment' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'ijazah_attachment' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'nik.unique' => 'NIK ini sudah terdaftar dalam sistem.',
            'nisn.unique' => 'NISN ini sudah terdaftar dalam sistem.',
        ]);

        // Generate Registration Number: PMB-YYYY-RANDOM
        $registrationNumber = 'PMB-' . date('Y') . '-' . strtoupper(Str::random(6));
        $validated['registration_number'] = $registrationNumber;

        // Convert attachments to Base64
        $attachments = ['kk_attachment', 'birth_certificate_attachment', 'ijazah_attachment'];
        foreach ($attachments as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $base64 = 'data:' . $file->getClientMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
                $validated[$field] = $base64;
            }
        }

        PmbRegistration::create($validated);

        return redirect()->route('pmb.status', ['search' => $registrationNumber])->with('success', 'Pendaftaran Anda berhasil dikirim! Silakan unduh Bukti Pendaftaran Anda di bawah ini.');
    }

    public function status(Request $request)
    {
        $registration = null;
        if ($request->has('search')) {
            $registration = PmbRegistration::where('registration_number', $request->search)
                ->orWhere('nisn', $request->search)
                ->orWhere('nik', $request->search)
                ->first();

            if (!$registration) {
                session()->flash('error', 'Data pendaftaran tidak ditemukan. Pastikan NISN, NIK, atau Nomor Pendaftaran sudah benar.');
            }
        }

        return view('pages.check-status', compact('registration'));
    }

    public function printCard($registration_number)
    {
        $registration = PmbRegistration::where('registration_number', $registration_number)->firstOrFail();

        if ($registration->status !== 'approved') {
            return redirect()->back()->with('error', 'Kartu pendaftaran hanya bisa dicetak jika sudah disetujui (Approved).');
        }

        return view('pages.print-card', compact('registration'));
    }

    public function downloadPdf($registration_number)
    {
        $registration = PmbRegistration::where('registration_number', $registration_number)->firstOrFail();
        $school = SchoolProfile::first();

        // Use app container to resolve DomPDF to avoid facade issues on some hosting
        $pdf = app('dompdf.wrapper')->loadView('pdf.registration-pdf', compact('registration', 'school'));

        return $pdf->download('Bukti_Pendaftaran_' . $registration->registration_number . '.pdf');
    }
}
