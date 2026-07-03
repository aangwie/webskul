<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PublicComplaint;
use App\Models\WhatsappApiSetting;
use Illuminate\Http\Request;

class PublicComplaintController extends Controller
{
    public function index()
    {
        $complaints = PublicComplaint::orderBy('created_at', 'desc')->get();
        return view('admin.complaints.index', compact('complaints'));
    }

    public function respond(Request $request, PublicComplaint $publicComplaint)
    {
        $request->validate([
            'response' => 'required',
        ]);

        $publicComplaint->update([
            'response' => $request->response,
            'status' => 'responded',
        ]);

        // Send WhatsApp notification
        try {
            $waSetting = WhatsappApiSetting::first();
            if ($waSetting && $waSetting->host_url && $waSetting->api_key && $waSetting->nomor_pengirim) {
                $phoneClean = preg_replace('/[^0-9]/', '', $publicComplaint->phone);
                if (strlen($phoneClean) >= 10) {
                    if (str_starts_with($phoneClean, '0')) {
                        $phoneClean = '62' . substr($phoneClean, 1);
                    }
                    $message = "Halo {$publicComplaint->name},\n\n"
                        . "Aduan Anda dengan kode {$publicComplaint->complaint_code} telah mendapat respon:\n\n"
                        . "\"{$request->response}\"\n\n"
                        . "Gunakan kode aduan tersebut untuk mengecek status di website kami.\n\n"
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
                        \Log::warning('WA admin response notification failed', [
                            'complaint_code' => $publicComplaint->complaint_code,
                            'phone' => $phoneClean,
                            'http_code' => $httpCode,
                            'curl_error' => $curlError,
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('WA admin response notification exception: ' . $e->getMessage());
        }

        return back()->with('success', 'Tanggapan berhasil dikirim!');
    }

    public function destroy(PublicComplaint $publicComplaint)
    {
        $publicComplaint->delete();
        return back()->with('success', 'Aduan berhasil dihapus!');
    }
}