@extends('layouts.app')

@section('title', 'Beranda - SMP Negeri 6 Sudimoro')

@section('styles')
<style>
    /* Hero Section */
    .hero {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: var(--secondary);
        padding: 120px 20px 100px;
        position: relative;
        overflow: hidden;
    }

    .hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .hero-container {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        align-items: center;
    }

    .hero-content h1 {
        font-size: 3rem;
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 20px;
    }

    .hero-content p {
        font-size: 1.1rem;
        opacity: 0.9;
        margin-bottom: 35px;
        line-height: 1.8;
    }

    .hero-buttons {
        display: flex;
        gap: 15px;
    }

    .btn-hero {
        padding: 15px 35px;
        border-radius: 50px;
        font-weight: 600;
        text-decoration: none;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .btn-hero-primary {
        background: var(--secondary);
        color: var(--primary);
    }

    .btn-hero-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    .btn-hero-outline {
        border: 2px solid var(--secondary);
        color: var(--secondary);
        background: transparent;
    }

    .btn-hero-outline:hover {
        background: var(--secondary);
        color: var(--primary);
    }

    .hero-image {
        text-align: center;
    }

    .hero-image i {
        font-size: 15rem;
        opacity: 0.3;
    }

    /* Stats Section */
    .stats {
        background: var(--secondary);
        padding: 40px 20px;
        margin-top: -50px;
        position: relative;
        z-index: 10;
        max-width: 1000px;
        margin-left: auto;
        margin-right: auto;
        border-radius: 20px;
        box-shadow: var(--shadow-lg);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 30px;
        text-align: center;
    }

    .stat-item {
        padding: 20px;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--primary);
        display: block;
    }

    .stat-label {
        color: var(--text-light);
        font-size: 0.9rem;
        margin-top: 5px;
    }

    /* Latest News Section */
    .news-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .news-card {
        background: var(--secondary);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: var(--shadow);
        transition: var(--transition);
    }

    .news-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--shadow-lg);
    }

    .news-image {
        height: 200px;
        background: linear-gradient(135deg, var(--primary-light), var(--primary));
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--secondary);
        font-size: 3rem;
        overflow: hidden;
    }

    .news-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .news-content {
        padding: 25px;
    }

    .news-category {
        display: inline-block;
        padding: 5px 15px;
        background: rgba(30, 58, 95, 0.1);
        color: var(--primary);
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 15px;
    }

    .news-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 10px;
        line-height: 1.4;
    }

    .news-date {
        color: var(--text-light);
        font-size: 0.85rem;
    }

    /* Info Section */
    .info-section {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: var(--secondary);
    }

    .info-section .section-title {
        color: var(--secondary);
    }

    .info-section .section-subtitle {
        color: rgba(255,255,255,0.8);
    }

    .info-list {
        max-width: 800px;
        margin: 0 auto;
    }

    .info-item {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 20px 25px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 20px;
        transition: var(--transition);
    }

    .info-item:hover {
        background: rgba(255,255,255,0.2);
        transform: translateX(10px);
    }

    .info-icon {
        width: 50px;
        height: 50px;
        background: var(--accent-gold);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .info-text h4 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .info-text p {
        font-size: 0.9rem;
        opacity: 0.8;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .hero-container {
            grid-template-columns: 1fr;
            text-align: center;
        }

        .hero-content h1 {
            font-size: 2rem;
        }

        .hero-buttons {
            justify-content: center;
            flex-wrap: wrap;
        }

        .hero-image {
            display: none;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .news-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="hero-container">
        <div class="hero-content animate-fade-in">
            <h1>Selamat Datang di<br>SMP Negeri 6 Sudimoro</h1>
            <p>Mewujudkan generasi muda yang berilmu, berakhlak mulia, dan siap menghadapi tantangan masa depan dengan pendidikan berkualitas.</p>
            <div class="hero-buttons">
                <a href="{{ route('profile.school') }}" class="btn-hero btn-hero-primary">
                    <i class="fas fa-arrow-right"></i> Profil Sekolah
                </a>
                <a href="{{ route('activities.index') }}" class="btn-hero btn-hero-outline">
                    Lihat Kegiatan
                </a>
            </div>
        </div>
        <div class="hero-image">
            <i class="fas fa-graduation-cap"></i>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats">
    <div class="stats-grid">
        <div class="stat-item">
            <span class="stat-number">{{ $featuredTeachers->count() }}+</span>
            <span class="stat-label">Guru Berpengalaman</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">500+</span>
            <span class="stat-label">Siswa Aktif</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">20+</span>
            <span class="stat-label">Tahun Berdiri</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">{{ $latestActivities->count() }}+</span>
            <span class="stat-label">Kegiatan</span>
        </div>
    </div>
</section>

<!-- Latest News Section -->
<section class="section">
    <div class="container">
        <h2 class="section-title">Berita & Kegiatan Terbaru</h2>
        <p class="section-subtitle">Ikuti perkembangan terbaru dari sekolah kami</p>

        @if($latestActivities->isEmpty())
            <p style="text-align: center; color: var(--text-light);">Belum ada kegiatan terbaru.</p>
        @else
            <div class="news-grid">
                @foreach($latestActivities as $activity)
                <a href="{{ route('activities.show', $activity->slug) }}" class="news-card" style="text-decoration: none;">
                    <div class="news-image">
                        @if($activity->image)
                            <img src="{{ asset('storage/' . $activity->image) }}" alt="{{ $activity->title }}">
                        @else
                            <i class="fas fa-newspaper"></i>
                        @endif
                    </div>
                    <div class="news-content">
                        <span class="news-category">{{ $activity->category == 'news' ? 'Berita' : 'Acara' }}</span>
                        <h3 class="news-title">{{ $activity->title }}</h3>
                        <span class="news-date">
                            <i class="far fa-calendar-alt"></i> 
                            {{ $activity->published_at ? $activity->published_at->format('d M Y') : '-' }}
                        </span>
                    </div>
                </a>
                @endforeach
            </div>
        @endif

        <div style="text-align: center; margin-top: 40px;">
            <a href="{{ route('activities.index') }}" class="btn btn-primary">
                Lihat Semua Kegiatan <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
            </a>
        </div>
    </div>
</section>

<!-- Important Info Section -->
@if($importantInfo->isNotEmpty())
<section class="section info-section">
    <div class="container">
        <h2 class="section-title">Informasi Penting</h2>
        <p class="section-subtitle">Pengumuman dan informasi terbaru untuk siswa dan orang tua</p>

        <div class="info-list">
            @foreach($importantInfo as $info)
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <div class="info-text">
                    <h4>{{ $info->title }}</h4>
                    <p>{{ Str::limit(strip_tags($info->content), 100) }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="{{ route('information.index') }}" class="btn-hero btn-hero-outline">
                Lihat Semua Informasi
            </a>
        </div>
    </div>
</section>
@endif
@endsection
