<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappApiSetting;
use Illuminate\Http\Request;

class WhatsappApiController extends Controller
{
    public function index()
    {
        $setting = WhatsappApiSetting::first();
        return view('admin.whatsapp-api.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'host_url' => 'required|url',
            'api_key' => 'required',
            'nomor_pengirim' => 'required',
        ]);

        $setting = WhatsappApiSetting::first();
        if (!$setting) {
            $setting = new WhatsappApiSetting();
        }

        $setting->host_url = $request->host_url;
        $setting->api_key = $request->api_key;
        $setting->nomor_pengirim = $request->nomor_pengirim;
        $setting->save();

        return redirect()->route('admin.whatsapp-api.index')
            ->with('success', 'Pengaturan WhatsApp API berhasil disimpan.');
    }

    public function test(Request $request)
    {
        $request->validate([
            'nomor_penerima' => 'required',
            'pesan' => 'required',
        ]);

        $setting = WhatsappApiSetting::first();
        if (!$setting) {
            return back()->with('error', 'Konfigurasi WhatsApp API belum diatur.');
        }

        $data = [
            'nomor_pengirim' => $setting->nomor_pengirim,
            'api_key' => $setting->api_key,
            'nomor_penerima' => $request->nomor_penerima,
            'pesan' => $request->pesan,
        ];

        $ch = curl_init($setting->host_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error) {
            return back()->with('error', 'cURL Error: ' . $error);
        }

        return back()->with('test_result', $response)
            ->with('test_http_code', $httpCode);
    }
}