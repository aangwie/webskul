@extends('layouts.app')

@section('title', 'PMB Ditutup - ' . ($school->name ?? 'SMP Negeri 6 Sudimoro'))

@section('content')
<section class="section" style="min-height: 60vh; display: flex; align-items: center;">
    <div class="container">
        <div class="animate-fade-in" style="max-width: 600px; margin: 0 auto; text-align: center;">
            <div style="font-size: 5rem; color: var(--primary); margin-bottom: 20px; opacity: 0.5;">
                <i class="fas fa-lock"></i>
            </div>
            <div class="section-title">Pendaftaran Ditutup</div>
            <p class="section-subtitle">
                Mohon maaf, Penerimaan Murid Baru (PMB) saat ini belum dibuka atau sudah ditutup.
            </p>

            @if($startDate || $endDate)
            <div style="background: rgba(30, 58, 95, 0.05); padding: 20px; border-radius: 12px; margin: 25px 0; border: 1px solid rgba(30, 58, 95, 0.1);">
                <p style="font-weight: 600; color: var(--primary); margin-bottom: 10px;">Jadwal Pendaftaran:</p>
                <div style="display: flex; justify-content: center; gap: 20px; font-size: 0.95rem;">
                    <div>
                        <small style="display: block; color: var(--text-light); text-transform: uppercase; font-size: 0.7rem; letter-spacing: 1px;">Mulai</small>
                        <strong>{{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M Y') : '-' }}</strong>
                    </div>
                    <div style="border-right: 1px solid #ddd;"></div>
                    <div>
                        <small style="display: block; color: var(--text-light); text-transform: uppercase; font-size: 0.7rem; letter-spacing: 1px;">Berakhir</small>
                        <strong>{{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d M Y') : '-' }}</strong>
                    </div>
                </div>
            </div>
            @endif

            <p class="section-subtitle" style="font-size: 0.9rem;">
                Silakan pantau halaman informasi kami untuk update terbaru.
            </p>
            <div style="margin-top: 30px;">
                <a href="{{ route('home') }}" class="btn btn-primary">
                    <i class="fas fa-home"></i> Kembali ke Beranda
                </a>
                <a href="{{ route('information.index') }}" class="btn btn-outline" style="margin-left: 10px;">
                    <i class="fas fa-info-circle"></i> Lihat Informasi
                </a>
            </div>
        </div>
    </div>
</section>
@endsection