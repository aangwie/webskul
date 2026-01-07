@extends('layouts.app')

@section('title', 'Cek Status Aduan - ' . ($school->name ?? 'SMP Negeri 6 Sudimoro'))

@section('styles')
<style>
    .complaint-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .complaint-card {
        background: var(--secondary);
        border-radius: 20px;
        box-shadow: var(--shadow-lg);
        padding: 40px;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .status-form-grid {
        display: flex;
        gap: 15px;
        margin-bottom: 30px;
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

    .btn-check {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 12px 30px;
        background: var(--primary);
        color: var(--secondary);
        border: none;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition);
        white-space: nowrap;
    }

    .btn-check:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(30, 58, 95, 0.2);
        background: var(--primary-light);
    }

    .result-card {
        padding: 30px;
        background: white;
        border-radius: 15px;
        border: 1px solid #eef2f7;
        margin-top: 30px;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 16px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-responded {
        background: #d4edda;
        color: #155724;
    }

    @media (max-width: 600px) {
        .complaint-card {
            padding: 30px 20px;
        }

        .status-form-grid {
            flex-direction: column;
        }

        .btn-check {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
<section class="section" style="background: var(--body-bg);">
    <div class="container">
        <div class="complaint-container animate-fade-in">
            <div style="text-align: center; margin-bottom: 40px;">
                <h1 class="section-title">Cek Status Aduan</h1>
                <p class="section-subtitle">Masukkan kode unik aduan Anda untuk melihat respon dari sekolah.</p>
            </div>

            <div class="complaint-card">
                @if(session('error'))
                <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 10px; margin-bottom: 25px; border: 1px solid #f5c6cb;">
                    <i class="fas fa-exclamation-circle" style="margin-right: 10px;"></i> {{ session('error') }}
                </div>
                @endif

                <form action="{{ route('public-complaints.check') }}" method="POST">
                    @csrf
                    <div class="status-form-grid">
                        <input type="text" name="complaint_code" class="form-control" placeholder="Contoh: ADU-XXXXXXXX" value="{{ old('complaint_code', request('complaint_code')) }}" required style="font-size: 1.1rem; text-transform: uppercase; text-align: center; letter-spacing: 2px;">
                        <button type="submit" class="btn-check">
                            Cek Status <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

                @if(isset($complaint))
                <div class="result-card">
                    <div style="margin-bottom: 25px;">
                        <span class="status-badge {{ $complaint->status == 'responded' ? 'status-responded' : 'status-pending' }}">
                            <i class="fas {{ $complaint->status == 'responded' ? 'fa-check-circle' : 'fa-clock' }}" style="margin-right: 5px;"></i>
                            {{ $complaint->status == 'responded' ? 'Sudah Direspon' : 'Menunggu Respon' }}
                        </span>
                        <h3 style="margin: 0; color: var(--text); font-size: 1.4rem;">Kode: {{ $complaint->complaint_code }}</h3>
                        <p style="color: var(--text-light); font-size: 0.85rem; margin-top: 5px;">Dikirim pada: {{ $complaint->created_at->format('d M Y, H:i') }}</p>
                    </div>

                    <div style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px dashed #eef2f7;">
                        <h4 style="font-size: 0.85rem; color: var(--text-light); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 1px; font-weight: 700;">Aduan/Saran Anda:</h4>
                        <div style="padding: 20px; background: #f8fafc; border-radius: 12px; color: var(--text); border: 1px solid rgba(0,0,0,0.02);">
                            <p style="white-space: pre-line; line-height: 1.7;">{{ $complaint->description }}</p>
                        </div>
                    </div>

                    @if($complaint->response)
                    <div>
                        <h4 style="font-size: 0.85rem; color: var(--primary); margin-bottom: 15px; text-transform: uppercase; letter-spacing: 1px; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                            <i class="fas fa-reply-all"></i> Respon Sekolah:
                        </h4>
                        <div style="padding: 25px; background: rgba(30, 58, 95, 0.05); border-left: 4px solid var(--primary); border-radius: 0 15px 15px 0; color: var(--text);">
                            <p style="white-space: pre-line; font-style: italic; font-size: 1.05rem; line-height: 1.8;">"{{ $complaint->response }}"</p>
                            <p style="margin-top: 15px; font-size: 0.8rem; color: var(--text-light); font-weight: 600;">
                                <i class="far fa-clock" style="margin-right: 5px;"></i> Dibalas pada: {{ $complaint->updated_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>
                    @else
                    <div style="text-align: center; padding: 40px 20px; color: var(--text-light); background: #fffcf5; border-radius: 15px; border: 1px solid #fff3cd;">
                        <i class="fas fa-hourglass-half" style="font-size: 2.5rem; margin-bottom: 15px; opacity: 0.6; display: block; color: #856404;"></i>
                        <p style="font-weight: 500;">Terima kasih atas masukannya. Mohon bersabar, pihak sekolah akan segera memberikan respon terbaik.</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ route('public-complaints.create') }}" style="color: var(--primary); text-decoration: none; font-size: 0.95rem; font-weight: 600;">
                    <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Kembali ke Form Aduan
                </a>
            </div>
        </div>
    </div>
</section>
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('success'))
    const complaintCode = "{{ session('complaint_code') }}";

    Swal.fire({
        title: 'Berhasil Terkirim!',
        html: `
                <div style="margin-top: 20px;">
                    <p style="margin-bottom: 15px;">Aduan Anda telah kami terima. Simpan kode di bawah ini untuk mengecek status aduan Anda:</p>
                    <div style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 2px dashed var(--primary); margin-bottom: 20px;">
                        <span id="copyCode" style="font-size: 2rem; font-weight: 800; letter-spacing: 3px; font-family: monospace; color: var(--primary); cursor: pointer;" title="Klik untuk menyalin">
                            ${complaintCode}
                        </span>
                        <p style="font-size: 0.8rem; color: var(--text-light); margin-top: 10px;">(Klik kode di atas untuk menyalin)</p>
                    </div>
                </div>
            `,
        icon: 'success',
        confirmButtonText: 'Tutup',
        confirmButtonColor: '#1e3a5f'
    });

    // Copy mechanism
    document.getElementById('copyCode').addEventListener('click', function() {
        const el = document.createElement('textarea');
        el.value = complaintCode;
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);

        Swal.fire({
            title: 'Disalin!',
            text: 'Kode aduan berhasil disalin ke clipboard.',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
    });
    @endif
</script>
@endsection
@endsection