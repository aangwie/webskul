@extends('admin.layouts.app')

@section('title', 'Pengaturan SMTP')
@section('page-title', 'Pengaturan SMTP')

@section('content')
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-envelope"></i> Konfigurasi Email SMTP</h2>
    </div>
    <div class="card-body">
        <div style="background: rgba(30, 58, 95, 0.05); padding: 15px 20px; border-radius: 10px; margin-bottom: 25px; border-left: 4px solid var(--primary);">
            <p style="color: var(--text-light); font-size: 0.9rem; margin: 0;">
                <i class="fas fa-info-circle" style="color: var(--primary);"></i>
                Konfigurasi SMTP digunakan untuk mengirim email reset password. Pastikan pengaturan sudah benar sebelum menggunakan fitur lupa password.
            </p>
        </div>

        <form action="{{ route('admin.settings.smtp.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label class="form-label">Mail Driver</label>
                    <select name="mail_mailer" class="form-select">
                        <option value="smtp" {{ $settings['mail_mailer'] == 'smtp' ? 'selected' : '' }}>SMTP</option>
                        <option value="sendmail" {{ $settings['mail_mailer'] == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                        <option value="log" {{ $settings['mail_mailer'] == 'log' ? 'selected' : '' }}>Log (Test Only)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">SMTP Host</label>
                    <input type="text" name="mail_host" class="form-input" value="{{ $settings['mail_host'] }}" placeholder="smtp.gmail.com">
                </div>

                <div class="form-group">
                    <label class="form-label">SMTP Port</label>
                    <input type="text" name="mail_port" class="form-input" value="{{ $settings['mail_port'] }}" placeholder="587">
                </div>

                <div class="form-group">
                    <label class="form-label">Encryption</label>
                    <select name="mail_encryption" class="form-select">
                        <option value="tls" {{ $settings['mail_encryption'] == 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ $settings['mail_encryption'] == 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="null" {{ $settings['mail_encryption'] == 'null' ? 'selected' : '' }}>None</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">SMTP Username</label>
                    <input type="text" name="mail_username" class="form-input" value="{{ $settings['mail_username'] }}" placeholder="email@gmail.com">
                </div>

                <div class="form-group">
                    <label class="form-label">SMTP Password</label>
                    <input type="password" name="mail_password" class="form-input" value="{{ $settings['mail_password'] }}" placeholder="••••••••">
                    <small style="color: var(--text-light); font-size: 0.8rem;">Untuk Gmail, gunakan App Password</small>
                </div>

                <div class="form-group">
                    <label class="form-label">From Email Address</label>
                    <input type="email" name="mail_from_address" class="form-input" value="{{ $settings['mail_from_address'] }}" placeholder="noreply@school.com">
                </div>

                <div class="form-group">
                    <label class="form-label">From Name</label>
                    <input type="text" name="mail_from_name" class="form-input" value="{{ $settings['mail_from_name'] }}" placeholder="SMP Negeri 6 Sudimoro">
                </div>
            </div>

            <div style="margin-top: 25px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Test Email -->
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-paper-plane"></i> Test Kirim Email</h2>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.settings.smtp.test') }}" method="POST">
            @csrf
            <div style="display: flex; gap: 15px; align-items: end; flex-wrap: wrap;">
                <div class="form-group" style="flex: 1; min-width: 250px; margin-bottom: 0;">
                    <label class="form-label">Email Tujuan Test</label>
                    <input type="email" name="test_email" class="form-input" placeholder="test@email.com" required>
                </div>
                <button type="submit" class="btn btn-success" style="height: 48px;">
                    <i class="fas fa-paper-plane"></i> Kirim Test Email
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
