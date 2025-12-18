@extends('layouts.app')

@section('title', 'Profil Sekolah - SMP Negeri 6 Sudimoro')

@section('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: var(--secondary);
        padding: 100px 20px 60px;
        text-align: center;
    }

    .page-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 15px;
    }

    .page-header p {
        opacity: 0.9;
        font-size: 1.1rem;
    }

    .profile-content {
        max-width: 900px;
        margin: -40px auto 0;
        padding: 0 20px 80px;
        position: relative;
        z-index: 10;
    }

    .profile-card {
        background: var(--secondary);
        border-radius: 20px;
        box-shadow: var(--shadow-lg);
        padding: 50px;
        margin-bottom: 30px;
    }

    .profile-section {
        margin-bottom: 40px;
    }

    .profile-section:last-child {
        margin-bottom: 0;
    }

    .profile-section h2 {
        font-size: 1.5rem;
        color: var(--primary);
        font-weight: 700;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .profile-section h2 i {
        width: 45px;
        height: 45px;
        background: rgba(30, 58, 95, 0.1);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
    }

    .profile-section p,
    .profile-section li {
        color: var(--text);
        line-height: 1.8;
        font-size: 1rem;
    }

    .profile-section ul {
        list-style: none;
        padding: 0;
    }

    .profile-section ul li {
        padding: 10px 0;
        padding-left: 30px;
        position: relative;
    }

    .profile-section ul li::before {
        content: '✓';
        position: absolute;
        left: 0;
        color: var(--primary);
        font-weight: bold;
    }

    .school-logo {
        text-align: center;
        margin-bottom: 30px;
    }

    .school-logo img {
        max-width: 150px;
        height: auto;
    }

    .school-logo i {
        font-size: 5rem;
        color: var(--primary);
    }

    @media (max-width: 768px) {
        .profile-card {
            padding: 30px 20px;
        }

        .page-header h1 {
            font-size: 1.8rem;
        }
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <h1>Profil Sekolah</h1>
    <p>Mengenal lebih dekat SMP Negeri 6 Sudimoro</p>
</div>

<div class="profile-content">
    <div class="profile-card">
        <div class="school-logo">
            @if($school && $school->logo)
                <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo Sekolah">
            @else
                <i class="fas fa-school"></i>
            @endif
        </div>

        @if($school)
            <div class="profile-section">
                <h2><i class="fas fa-eye"></i> Visi</h2>
                <p>{!! nl2br(e($school->vision ?? 'Menjadi sekolah unggulan yang menghasilkan lulusan berilmu, berakhlak mulia, dan berdaya saing tinggi.')) !!}</p>
            </div>

            <div class="profile-section">
                <h2><i class="fas fa-bullseye"></i> Misi</h2>
                <div>{!! nl2br(e($school->mission ?? 'Menyelenggarakan pendidikan berkualitas yang mengembangkan potensi peserta didik secara optimal.')) !!}</div>
            </div>

            <div class="profile-section">
                <h2><i class="fas fa-history"></i> Sejarah</h2>
                <p>{!! nl2br(e($school->history ?? 'SMP Negeri 6 Sudimoro didirikan dengan tekad untuk memberikan pendidikan terbaik bagi generasi muda. Sejak berdiri, sekolah ini telah menghasilkan banyak alumni yang sukses di berbagai bidang.')) !!}</p>
            </div>

            <div class="profile-section">
                <h2><i class="fas fa-map-marker-alt"></i> Alamat</h2>
                <p>{{ $school->address ?? 'Sudimoro, Indonesia' }}</p>
            </div>

            <div class="profile-section">
                <h2><i class="fas fa-phone-alt"></i> Kontak</h2>
                <p>
                    <strong>Telepon:</strong> {{ $school->phone ?? '-' }}<br>
                    <strong>Email:</strong> {{ $school->email ?? '-' }}
                </p>
            </div>
        @else
            <p style="text-align: center; color: var(--text-light);">Profil sekolah belum tersedia.</p>
        @endif
    </div>
</div>
@endsection
