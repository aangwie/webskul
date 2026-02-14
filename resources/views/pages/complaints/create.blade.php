@extends('layouts.app')

@section('title', 'Aduan Masyarakat - ' . ($school->name ?? 'SMP Negeri 6 Sudimoro'))

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

    .complaint-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
        margin-bottom: 25px;
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

    @media (max-width: 768px) {
        .complaint-card {
            padding: 30px 20px;
            border-radius: 15px;
        }

        .complaint-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
    }
</style>
@endsection

@section('content')
<section class="section" style="background: var(--body-bg);">
    <div class="container">
        <div class="complaint-container animate-fade-in">
            <div style="text-align: center; margin-bottom: 40px;">
                <h1 class="section-title">Aduan Masyarakat</h1>
                <p class="section-subtitle">Sampaikan aduan atau saran Anda untuk kemajuan sekolah kami.</p>
            </div>

            <div class="complaint-card">
                <form action="{{ route('public-complaints.store') }}" method="POST">
                    @csrf
                    <div class="complaint-grid">
                        <div class="form-group">
                            <label class="form-label">Nama Pengadu</label>
                            <input type="text" name="name" class="form-control" placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="number" name="phone" class="form-control" placeholder="Contoh: 08123456789" required>
                        </div>
                    </div>

                    <div class="complaint-grid">
                        <div class="form-group">
                            <label class="form-label">Alamat Email</label>
                            <input type="email" name="email" class="form-control" placeholder="alamat@email.com" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Jenis Aduan</label>
                            <select name="type" class="form-control" required>
                                <option value="" disabled selected>Pilih Jenis</option>
                                <option value="Aduan">Aduan</option>
                                <option value="Saran">Saran</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Deskripsi Aduan / Saran</label>
                        <textarea name="description" class="form-control" style="min-height: 150px; resize: vertical;" placeholder="Tuliskan detail aduan atau saran Anda di sini..." required></textarea>
                    </div>

                    <div style="margin-top: 30px;">
                        <button type="submit" class="btn-submit">
                            Kirim Aduan <i class="fas fa-paper-plane" style="margin-left: 10px;"></i>
                        </button>
                    </div>

                    <div style="text-align: center; margin-top: 25px;">
                        <a href="{{ route('public-complaints.status') }}" style="color: var(--primary); text-decoration: none; font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-search" style="margin-right: 5px;"></i> Sudah kirim aduan? Cek status di sini
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection