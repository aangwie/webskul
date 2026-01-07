<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PublicComplaint;
use App\Models\SchoolProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\ComplaintCodeMail;

class PublicComplaintController extends Controller
{
    public function create()
    {
        $school = SchoolProfile::first();
        return view('pages.complaints.create', compact('school'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'type' => 'required|in:Aduan,Saran',
            'description' => 'required|string',
        ]);

        $complaint_code = 'ADU-' . strtoupper(Str::random(8));

        $complaint = PublicComplaint::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'type' => $request->type,
            'description' => $request->description,
            'complaint_code' => $complaint_code,
            'status' => 'pending',
        ]);

        try {
            Mail::to($request->email)->send(new ComplaintCodeMail($complaint));
        } catch (\Exception $e) {
            // Log error or handle gracefully
        }

        return redirect()->route('public-complaints.status')->with([
            'success' => 'Aduan/Saran berhasil dikirim!',
            'complaint_code' => $complaint_code
        ]);
    }

    public function status()
    {
        $school = SchoolProfile::first();
        return view('pages.complaints.status', compact('school'));
    }

    public function check(Request $request)
    {
        $request->validate([
            'complaint_code' => 'required|string',
        ]);

        $complaint = PublicComplaint::where('complaint_code', $request->complaint_code)->first();

        if (!$complaint) {
            return back()->with('error', 'Kode aduan tidak ditemukan.');
        }

        $school = SchoolProfile::first();
        return view('pages.complaints.status', compact('complaint', 'school'));
    }
}
