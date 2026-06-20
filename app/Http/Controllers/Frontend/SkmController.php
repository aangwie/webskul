<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SkmQuestion;
use App\Models\SkmRespondent;
use App\Models\SkmResponse;
use App\Models\Setting;
use Illuminate\Http\Request;

class SkmController extends Controller
{
    public function index()
    {
        $questions = SkmQuestion::where('is_active', true)->orderBy('order')->get();
        $questionsExist = $questions->isNotEmpty();
        return view('frontend.skm.index', compact('questions', 'questionsExist'));
    }

    public function submitIdentity(Request $request)
    {
        // Check if questions exist first
        $questionsExist = SkmQuestion::where('is_active', true)->exists();
        if (!$questionsExist) {
            return redirect()->route('skm.index')->with('error', 'Maaf, belum ada pertanyaan survei yang tersedia. Silakan hubungi admin.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'cf-turnstile-response' => 'required|string',
            'honeypot' => 'nullable|string', // Honeypot field - will be checked manually
        ], [
            'cf-turnstile-response.required' => 'Verifikasi Turnstile diperlukan.',
        ]);

        // Check honeypot manually - must be empty
        if ($request->filled('honeypot')) {
            return back()->with('error', 'Terjadi kesalahan validasi.')->withInput();
        }

        // Validate Turnstile
        $turnstileSecret = Setting::get('turnstile_secret_key', '');
        $turnstileIsActive = Setting::get('turnstile_is_active', '0');

        if ($turnstileIsActive === '1' && $turnstileSecret) {
            $response = file_get_contents('https://challenges.cloudflare.com/turnstile/v0/siteverify', false, stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-Type: application/x-www-form-urlencoded',
                    'content' => http_build_query([
                        'secret' => $turnstileSecret,
                        'response' => $request->input('cf-turnstile-response'),
                        'remoteip' => $request->ip(),
                    ]),
                ],
            ]));

            $turnstileResult = json_decode($response, true);

            if (!$turnstileResult['success']) {
                return back()->with('error', 'Verifikasi Turnstile gagal. Silakan coba lagi.')->withInput();
            }
        }

        // Store respondent info in session
        session()->put('skm_respondent', [
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'year' => date('Y'),
        ]);

        return redirect()->route('skm.survey');
    }

    public function survey()
    {
        if (!session()->has('skm_respondent')) {
            return redirect()->route('skm.index')->with('error', 'Silakan isi data diri terlebih dahulu.');
        }

        $questions = SkmQuestion::where('is_active', true)->orderBy('order')->get();

        if ($questions->isEmpty()) {
            return redirect()->route('skm.index')->with('error', 'Belum ada pertanyaan survei.');
        }

        return view('frontend.skm.survey', compact('questions'));
    }

    public function submitSurvey(Request $request)
    {
        if (!session()->has('skm_respondent')) {
            return redirect()->route('skm.index')->with('error', 'Sesi habis. Silakan isi data diri kembali.');
        }

        $respondentData = session()->get('skm_respondent');
        $questions = SkmQuestion::where('is_active', true)->pluck('id');

        $rules = [];
        foreach ($questions as $qId) {
            $rules["score_{$qId}"] = 'required|integer|min:1|max:4';
        }
        $rules['honeypot'] = 'nullable|string';

        $request->validate($rules);

        // Check honeypot manually - must be empty
        if ($request->filled('honeypot')) {
            return back()->with('error', 'Terjadi kesalahan validasi.')->withInput();
        }

        // Check for duplicate submission in same year
        $existing = SkmRespondent::where('phone', $respondentData['phone'])
            ->where('name', $respondentData['name'])
            ->where('year', $respondentData['year'])
            ->first();

        if ($existing) {
            session()->forget('skm_respondent');
            return redirect()->route('skm.index')->with('error', 'Anda sudah mengisi survei tahun ini. Terima kasih.');
        }

        // Save respondent
        $respondent = SkmRespondent::create($respondentData);

        // Save responses
        foreach ($questions as $qId) {
            SkmResponse::create([
                'respondent_id' => $respondent->id,
                'question_id' => $qId,
                'score' => $request->input("score_{$qId}"),
            ]);
        }

        session()->forget('skm_respondent');

        return redirect()->route('skm.thankyou');
    }

    public function thankyou()
    {
        return view('frontend.skm.thankyou');
    }
}