<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PublicComplaint;
use App\Models\SchoolProfile;
use App\Models\WhatsappApiSetting;
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
            'attachment' => 'nullable|file|max:500',
        ]);

        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $mime = $file->getMimeType();

            if (str_starts_with($mime, 'image/')) {
                $img = imagecreatefromstring(file_get_contents($file->path()));
                if ($img === false) {
                    return back()->withInput()->with('error', 'Gambar tidak valid.');
                }

                // Convert palette image to true color for WebP support
                if (!imageistruecolor($img)) {
                    $w = imagesx($img);
                    $h = imagesy($img);
                    $truecolor = imagecreatetruecolor($w, $h);
                    imagecopy($truecolor, $img, 0, 0, 0, 0, $w, $h);
                    imagedestroy($img);
                    $img = $truecolor;
                }

                $filename = 'complaints/' . Str::random(40) . '.webp';
                $fullPath = storage_path('app/public/' . $filename);

                if (!is_dir(storage_path('app/public/complaints'))) {
                    mkdir(storage_path('app/public/complaints'), 0755, true);
                }

                imagewebp($img, $fullPath, 80);
                imagedestroy($img);
                $attachmentPath = $filename;
            } elseif ($mime === 'application/pdf') {
                $filename = 'complaints/' . Str::random(40) . '.pdf';
                $file->storeAs('public', $filename);
                $attachmentPath = $filename;
            } else {
                return back()->withInput()->with('error', 'Hanya file gambar atau PDF yang diizinkan.');
            }
        }

        $complaint_code = 'ADU-' . strtoupper(Str::random(8));

        $complaint = PublicComplaint::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'type' => $request->type,
            'description' => $request->description,
            'attachment' => $attachmentPath,
            'complaint_code' => $complaint_code,
            'status' => 'pending',
        ]);

        try {
            Mail::to($request->email)->send(new ComplaintCodeMail($complaint));
        } catch (\Exception $e) {
            // Log error or handle gracefully
        }

        // Send WhatsApp notification
        try {
            $waSetting = WhatsappApiSetting::first();
            if ($waSetting && $waSetting->host_url && $waSetting->api_key && $waSetting->nomor_pengirim) {
                $phoneClean = preg_replace('/[^0-9]/', '', $request->phone);
                // Convert local format (08xx) to international (628xx)
                if (strlen($phoneClean) >= 10) {
                    if (str_starts_with($phoneClean, '0')) {
                        $phoneClean = '62' . substr($phoneClean, 1);
                    }
                    $message = "Terima kasih {$request->name}, aduan/saran Anda telah kami terima.\n\n"
                        . "Kode Aduan: {$complaint_code}\n\n"
                        . "Gunakan kode tersebut untuk melacak status aduan Anda melalui website kami.\n\n"
                        . "Terima kasih.";

                    $ch = curl_init($waSetting->host_url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                        'nomor_pengirim' => $waSetting->nomor_pengirim,
                        'api_key' => $waSetting->api_key,
                        'nomor_penerima' => $phoneClean,
                        'pesan' => $message,
                    ]));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Content-Type: application/x-www-form-urlencoded'
                    ]);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    $curlError = curl_error($ch);
                    curl_close($ch);

                    if ($httpCode >= 400 || $curlError) {
                        \Log::warning('WA notification failed', [
                            'phone' => $phoneClean,
                            'http_code' => $httpCode,
                            'curl_error' => $curlError,
                            'response' => $response,
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('WA notification exception: ' . $e->getMessage());
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