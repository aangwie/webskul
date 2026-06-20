@extends('layouts.app')

@section('title', 'Survei Kepuasan Masyarakat - ' . ($school->name ?? 'SMP Negeri 6 Sudimoro'))

@section('styles')
<style>
    .skm-container {
        max-width: 700px;
        margin: 0 auto;
    }
    .skm-card {
        background: var(--secondary);
        border-radius: 20px;
        box-shadow: var(--shadow-lg);
        padding: 40px;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--text);
        font-size: 0.95rem;
    }
    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #eef2f7;
        border-radius: 12px;
        font-size: 1rem;
        transition: var(--transition);
        background: #fdfdfd;
        font-family: 'Inter', sans-serif;
    }
    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        background: #fff;
        box-shadow: 0 0 0 4px rgba(30, 58, 95, 0.1);
    }
    textarea.form-control {
        min-height: 100px;
        resize: vertical;
    }
    .btn-submit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 16px 40px;
        background: var(--primary);
        color: var(--secondary);
        border: none;
        border-radius: 14px;
        font-size: 1.1rem;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition);
        width: 100%;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(30, 58, 95, 0.2);
        background: var(--primary-light);
    }
    .info-box {
        background: rgba(30, 58, 95, 0.05);
        border-left: 4px solid var(--primary);
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 30px;
        font-size: 0.9rem;
        color: var(--text-light);
    }
    .cf-turnstile {
        margin-bottom: 20px;
        display: flex;
        justify-content: center;
    }
    /* Honeypot - hidden from users */
    .honeypot-field {
        position: absolute;
        left: -9999px;
        opacity: 0;
        height: 0;
        overflow: hidden;
    }
    @media (max-width: 768px) {
        .skm-card {
            padding: 30px 20px;
            border-radius: 15px;
        }
    }
</style>
@endsection

@section('content')
<section class="section" style="background: var(--body-bg);">
    <div class="container">
        <div class="skm-container animate-fade-in">
            <div style="text-align: center; margin-bottom: 40px;">
                <h1 class="section-title">Survei Kepuasan Masyarakat</h1>
                <p class="section-subtitle">Isi data diri Anda untuk memulai survei kepuasan terhadap pelayanan {{ $school->name ?? 'SMP Negeri 6 Sudimoro' }}.</p>
            </div>

            <div class="skm-card">
                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    Data diri Anda hanya digunakan untuk keperluan identifikasi responden dan dijamin kerahasiaannya.
                </div>

                <form action="{{ route('skm.submit-identity') }}" method="POST" id="identityForm">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" placeholder="Masukkan nama lengkap" value="{{ old('name') }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" class="form-control" placeholder="Masukkan alamat lengkap" required>{{ old('address') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" name="phone" class="form-control" placeholder="Contoh: 08123456789" value="{{ old('phone') }}" required>
                    </div>

                    <!-- Honeypot Protection -->
                    <div class="honeypot-field">
                        <input type="text" name="honeypot" value="" tabindex="-1" autocomplete="off">
                    </div>

                    <!-- Cloudflare Turnstile -->
                    @php
                        $turnstileSiteKey = \App\Models\Setting::get('turnstile_site_key', '');
                        $turnstileIsActive = \App\Models\Setting::get('turnstile_is_active', '0');
                    @endphp
                    @if($turnstileIsActive === '1' && $turnstileSiteKey)
                        <div class="cf-turnstile" data-sitekey="{{ $turnstileSiteKey }}" data-theme="light"></div>
                        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
                    @else
                        <input type="hidden" name="cf-turnstile-response" value="bypass">
                    @endif

                    <div style="margin-top: 30px;">
                        <button type="submit" class="btn-submit">
                            Lanjutkan ke Survei <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection