<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SettingsController extends Controller
{
    public function smtp()
    {
        $settings = Setting::getSmtpSettings();
        return view('admin.settings.smtp', compact('settings'));
    }

    public function updateSmtp(Request $request)
    {
        $validated = $request->validate([
            'mail_mailer' => 'required|string|in:smtp,sendmail,log',
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|string|max:10',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|string|in:tls,ssl,null',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name' => 'nullable|string|max:255',
        ]);

        Setting::setSmtpSettings($validated);

        return redirect()->route('admin.settings.smtp')
            ->with('success', 'Pengaturan SMTP berhasil disimpan!');
    }

    public function testSmtp(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            // Apply settings temporarily
            $settings = Setting::getSmtpSettings();
            
            config([
                'mail.default' => $settings['mail_mailer'],
                'mail.mailers.smtp.host' => $settings['mail_host'],
                'mail.mailers.smtp.port' => $settings['mail_port'],
                'mail.mailers.smtp.username' => $settings['mail_username'],
                'mail.mailers.smtp.password' => $settings['mail_password'],
                'mail.mailers.smtp.encryption' => $settings['mail_encryption'] === 'null' ? null : $settings['mail_encryption'],
                'mail.from.address' => $settings['mail_from_address'],
                'mail.from.name' => $settings['mail_from_name'],
            ]);

            Mail::raw('Ini adalah email test dari SMP Negeri 6 Sudimoro. Jika Anda menerima email ini, berarti konfigurasi SMTP sudah benar.', function ($message) use ($request, $settings) {
                $message->to($request->test_email)
                    ->subject('Test Email - SMP Negeri 6 Sudimoro');
            });

            return redirect()->route('admin.settings.smtp')
                ->with('success', 'Email test berhasil dikirim ke ' . $request->test_email);

        } catch (\Exception $e) {
            return redirect()->route('admin.settings.smtp')
                ->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }
    }
}
