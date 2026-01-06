@extends('layouts.app')

@section('title', 'Cek Status Pendaftaran - ' . ($school->name ?? 'SMP Negeri 6 Sudimoro'))

@section('styles')
<style>
    .status-container {
        max-width: 850px;
        margin: 0 auto;
    }

    .search-card {
        background: var(--secondary);
        border-radius: 20px;
        padding: 35px;
        box-shadow: var(--shadow-lg);
        margin-bottom: 30px;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .search-group {
        display: flex;
        gap: 15px;
    }

    .search-input {
        flex: 1;
        padding: 14px 20px;
        border: 2px solid #eef2f7;
        border-radius: 12px;
        font-size: 1rem;
        transition: var(--transition);
        background: #fdfdfd;
    }

    .search-input:focus {
        outline: none;
        border-color: var(--primary);
        background: #fff;
        box-shadow: 0 0 0 4px rgba(30, 58, 95, 0.1);
    }

    .btn-search {
        padding: 0 35px;
        background: var(--primary);
        color: var(--secondary);
        border: none;
        border-radius: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 10px;
        height: 52px;
    }

    .btn-search:hover {
        background: var(--primary-light);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(30, 58, 95, 0.2);
    }

    .result-card {
        background: var(--secondary);
        border-radius: 20px;
        padding: 35px;
        box-shadow: var(--shadow-lg);
        border-left: 6px solid var(--primary);
    }

    .result-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 30px;
        gap: 20px;
    }

    .status-badge {
        font-size: 1rem;
        padding: 10px 25px;
        border-radius: 30px;
        font-weight: 700;
        text-transform: uppercase;
        box-shadow: var(--shadow-sm);
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        background: var(--accent);
        padding: 25px;
        border-radius: 15px;
    }

    .info-item label {
        display: block;
        font-size: 0.85rem;
        color: var(--text-light);
        margin-bottom: 5px;
        font-weight: 500;
    }

    .info-item .value {
        font-weight: 700;
        color: var(--primary);
        font-size: 1.1rem;
    }

    .status-message {
        margin-top: 35px;
        text-align: center;
        padding: 30px;
        border-radius: 15px;
        border: 1px dashed;
    }

    @media (max-width: 768px) {

        .search-card,
        .result-card {
            padding: 25px 20px;
            border-radius: 15px;
        }

        .search-group {
            flex-direction: column;
        }

        .btn-search {
            width: 100%;
            justify-content: center;
        }

        .result-header {
            flex-direction: column;
            text-align: center;
            align-items: center;
        }

        .result-header div:last-child {
            text-align: center !important;
        }

        .info-grid {
            grid-template-columns: 1fr;
            padding: 20px;
        }

        .info-item .value {
            font-size: 1rem;
        }
    }
</style>
@endsection

@section('content')
<section class="section">
    <div class="container">
        <div class="status-container animate-fade-in">
            <div class="section-title">Cek Status Pendaftaran</div>
            <p class="section-subtitle">Masukkan NISN, NIK, atau Nomor Pendaftaran untuk melihat status pendaftaran Anda.</p>

            @if(session('error'))
            <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 12px; margin-bottom: 25px; border: 1px solid #f5c6cb;">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
            @endif

            <div class="search-card">
                <form action="{{ route('pmb.status') }}" method="GET">
                    <div class="search-group">
                        <input type="text" name="search" class="search-input" value="{{ request('search', old('search', isset($registration) ? $registration->registration_number : '')) }}" placeholder="NISN / NIK / No. Pendaftaran" required>
                        <button type="submit" class="btn-search">
                            <i class="fas fa-search"></i> Cari Data
                        </button>
                    </div>
                </form>
            </div>

            @if(isset($registration))
            <div class="result-card animate-fade-in">
                <div class="result-header">
                    <div>
                        <h3 style="color: var(--primary); margin-bottom: 5px;">Hasil Pencarian</h3>
                        <p style="color: var(--text-light); font-size: 0.95rem;">Data pendaftaran ditemukan dalam sistem.</p>
                        <div style="margin-top: 15px;">
                            <a href="{{ route('pmb.downloadPdf', $registration->registration_number) }}" class="btn btn-sm btn-outline-primary" style="border-radius: 10px;">
                                <i class="fas fa-file-pdf"></i> Bukti Pendaftaran (PDF)
                            </a>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 0.8rem; text-transform: uppercase; color: var(--text-light); margin-bottom: 8px; font-weight: 600;">Status Pendaftaran</div>
                        @if($registration->status == 'pending')
                        <span class="status-badge badge-warning">PENDING</span>
                        @elseif($registration->status == 'approved')
                        <span class="status-badge badge-success">APPROVED</span>
                        @else
                        <span class="status-badge badge-danger">REJECTED</span>
                        @endif
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <label>Nomor Pendaftaran</label>
                        <div class="value">{{ $registration->registration_number }}</div>
                    </div>
                    <div class="info-item">
                        <label>Nama Lengkap</label>
                        <div class="value">{{ $registration->nama }}</div>
                    </div>
                    <div class="info-item">
                        <label>NISN</label>
                        <div class="value">{{ $registration->nisn }}</div>
                    </div>
                    <div class="info-item">
                        <label>Tahun Pelajaran</label>
                        <div class="value">{{ $registration->academic_year }}</div>
                    </div>
                </div>

                @if($registration->status == 'approved')
                <div class="status-message" style="background: rgba(40, 167, 69, 0.05); border-color: var(--success); color: #155724;">
                    <p style="margin-bottom: 20px; font-weight: 500;">
                        Selamat! Pendaftaran Anda telah disetujui. Silakan cetak kartu pendaftaran sebagai bukti untuk proses selanjutnya.
                    </p>
                    <a href="{{ route('pmb.print', $registration->registration_number) }}" target="_blank" class="btn btn-success" style="padding: 12px 40px; border-radius: 12px; font-weight: 700;">
                        <i class="fas fa-print"></i> Cetak Kartu Pendaftaran
                    </a>
                </div>
                @elseif($registration->status == 'pending')
                <div class="status-message" style="background: rgba(255, 193, 7, 0.05); border-color: var(--warning); color: #856404;">
                    <p style="font-weight: 500; margin: 0;">
                        Pendaftaran Anda sedang dalam proses peninjauan. Silakan cek kembali secara berkala atau hubungi pihak sekolah jika ada pertanyaan.
                    </p>
                </div>
                @else
                <div class="status-message" style="background: rgba(220, 53, 69, 0.05); border-color: var(--danger); color: #721c24;">
                    <p style="font-weight: 500; margin: 0;">
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