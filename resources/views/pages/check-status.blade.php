@extends('layouts.app')

@section('title', 'Cek Status Pendaftaran - ' . ($school->name ?? 'SMP Negeri 6 Sudimoro'))

@section('content')
<section class="section">
    <div class="container">
        <div class="animate-fade-in" style="max-width: 800px; margin: 0 auto;">
            <div class="section-title">Cek Status Pendaftaran</div>
            <p class="section-subtitle">Masukkan NISN, NIK, atau Nomor Pendaftaran untuk melihat status pendaftaran Anda.</p>

            @if(session('error'))
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #f5c6cb;">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
            @endif

            @if(session('success'))
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #c3e6cb;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
            @endif

            <div class="card" style="padding: 30px; margin-bottom: 30px;">
                <form action="{{ route('pmb.status') }}" method="GET">
                    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 250px;">
                            <input type="text" name="search" value="{{ request('search', old('search', isset($registration) ? $registration->registration_number : '')) }}" placeholder="NISN / NIK / No. Pendaftaran" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;">
                        </div>
                        <button type="submit" class="btn btn-primary" style="padding: 0 30px;">
                            <i class="fas fa-search"></i> Cari Data
                        </button>
                    </div>
                </form>
            </div>

            @if(isset($registration))
            <div class="card animate-fade-in" style="padding: 30px; border-left: 5px solid var(--primary);">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 25px;">
                    <div>
                        <h3 style="color: var(--primary); margin-bottom: 5px;">Hasil Pencarian</h3>
                        <p style="color: var(--text-light);">Data pendaftaran ditemukan dalam sistem kami.</p>
                        <div style="margin-top: 10px;">
                            <a href="{{ route('pmb.downloadPdf', $registration->registration_number) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-file-pdf"></i> Unduh Bukti Pendaftaran (PDF)
                            </a>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 0.8rem; text-transform: uppercase; color: var(--text-light); margin-bottom: 5px;">Status Pendaftaran</div>
                        @if($registration->status == 'pending')
                        <span class="badge badge-warning" style="font-size: 1rem; padding: 8px 20px;">PENDING</span>
                        @elseif($registration->status == 'approved')
                        <span class="badge badge-success" style="font-size: 1rem; padding: 8px 20px;">APPROVED</span>
                        @else
                        <span class="badge badge-danger" style="font-size: 1rem; padding: 8px 20px;">REJECTED</span>
                        @endif
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; background: var(--accent); padding: 20px; border-radius: 12px;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; color: var(--text-light);">Nomor Pendaftaran</label>
                        <div style="font-weight: 700; color: var(--primary);">{{ $registration->registration_number }}</div>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; color: var(--text-light);">Nama Lengkap</label>
                        <div style="font-weight: 600;">{{ $registration->nama }}</div>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; color: var(--text-light);">NISN</label>
                        <div style="font-weight: 600;">{{ $registration->nisn }}</div>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; color: var(--text-light);">Tahun Pelajaran</label>
                        <div style="font-weight: 600;">{{ $registration->academic_year }}</div>
                    </div>
                </div>

                @if($registration->status == 'approved')
                <div style="margin-top: 30px; text-align: center; background: rgba(40, 167, 69, 0.05); padding: 25px; border-radius: 12px; border: 1px dashed var(--success);">
                    <p style="margin-bottom: 20px; color: var(--success); font-weight: 500;">
                        Selamat! Pendaftaran Anda telah disetujui. Silakan cetak kartu pendaftaran sebagai bukti untuk proses selanjutnya.
                    </p>
                    <a href="{{ route('pmb.print', $registration->registration_number) }}" target="_blank" class="btn btn-success" style="padding: 12px 40px;">
                        <i class="fas fa-print"></i> Cetak Kartu Pendaftaran
                    </a>
                </div>
                @elseif($registration->status == 'pending')
                <div style="margin-top: 30px; text-align: center; background: rgba(255, 193, 7, 0.05); padding: 25px; border-radius: 12px; border: 1px dashed var(--warning);">
                    <p style="color: #856404; font-weight: 500;">
                        Pendaftaran Anda sedang dalam proses peninjauan. Silakan cek kembali secara berkala atau hubungi pihak sekolah jika ada pertanyaan.
                    </p>
                </div>
                @else
                <div style="margin-top: 30px; text-align: center; background: rgba(220, 53, 69, 0.05); padding: 25px; border-radius: 12px; border: 1px dashed var(--danger);">
                    <p style="color: var(--danger); font-weight: 500;">
                        Mohon maaf, pendaftaran Anda belum dapat disetujui saat ini. Silakan hubungi pihak sekolah untuk informasi lebih lanjut.
                    </p>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</section>
@endsection