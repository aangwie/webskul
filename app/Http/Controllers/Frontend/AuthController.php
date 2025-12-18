<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect('/admin');
        }
        return view('pages.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/admin');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function showForgotPassword()
    {
        return view('pages.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan.']);
        }

        // Generate token
        $token = Str::random(64);

        // Delete existing tokens for this email
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Insert new token
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        // Apply SMTP settings
        $settings = Setting::getSmtpSettings();
        
        if ($settings['mail_host']) {
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
        }

        try {
            $resetUrl = route('password.reset', ['token' => $token, 'email' => $request->email]);
            
            Mail::send('emails.reset-password', ['resetUrl' => $resetUrl, 'user' => $user], function ($message) use ($request) {
                $message->to($request->email)
                    ->subject('Reset Password - SMP Negeri 6 Sudimoro');
            });

            return back()->with('success', 'Link reset password telah dikirim ke email Anda.');

        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Gagal mengirim email. Pastikan konfigurasi SMTP sudah benar.']);
        }
    }

    public function showResetPassword(Request $request, $token)
    {
        return view('pages.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Check token
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Token tidak valid atau sudah kadaluarsa.']);
        }

        // Check if token is expired (60 minutes)
        if (now()->diffInMinutes($record->created_at) > 60) {
            return back()->withErrors(['email' => 'Token sudah kadaluarsa. Silakan minta link baru.']);
        }

        // Update password
        $user = User::where('email', $request->email)->first();
        $user->update(['password' => Hash::make($request->password)]);

        // Delete token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login.');
    }
}
