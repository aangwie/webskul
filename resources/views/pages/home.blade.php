@extends('layouts.app')

@section('title', 'Beranda - ' . ($school->name ?? 'SMP Negeri 6 Sudimoro'))

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
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
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

        .hero-heading-wrapper {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .hero-logo-ssn {
            width: 90px;
            height: 90px;
            object-fit: contain;
            flex-shrink: 0;
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.3));
            transition: var(--transition);
        }

        .hero-logo-ssn:hover {
            transform: scale(1.05);
        }

        .hero-logo-ssn-placeholder {
            width: 90px;
            height: 90px;
            flex-shrink: 0;
        }

        .hero-content h1 {
            font-size: clamp(1.5rem, 3.5vw, 2.4rem);
            font-weight: 800;
            line-height: 1.2;
            color: #ffffff;
            white-space: nowrap;
        }

        .hero-content p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 35px;
            line-height: 1.8;
            color: #ffffff;
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
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
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
            color: rgba(255, 255, 255, 0.8);
        }

        .info-list {
            max-width: 800px;
            margin: 0 auto;
        }

        .info-item {
            background: rgba(255, 255, 255, 0.1);
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
            background: rgba(255, 255, 255, 0.2);
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
                font-size: clamp(1.2rem, 5vw, 1.8rem);
                white-space: normal;
            }

            .hero-buttons {
                justify-content: center;
                flex-wrap: wrap;
            }

            .hero-heading-wrapper {
                justify-content: center;
            }

            .hero-logo-ssn,
            .hero-logo-ssn-placeholder {
                width: 60px;
                height: 60px;
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

        /* Social Media Section */
        .social-section {
            padding: 60px 20px;
            background: var(--secondary);
            text-align: center;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .social-link {
            width: 60px;
            height: 60px;
            background: var(--primary);
            color: var(--secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 1.5rem;
            transition: var(--transition);
            text-decoration: none;
        }

        .social-link:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            background: var(--accent-gold);
            color: var(--primary);
        }

        /* ====================== CAROUSEL ====================== */
        .carousel-section {
            padding: 70px 20px;
            background: var(--secondary);
            overflow: hidden;
        }

        .carousel-wrapper {
            position: relative;
            max-width: 1200px;
            margin: 40px auto 0;
            overflow: hidden;
            border-radius: 18px;
        }

        .carousel-track {
            display: flex;
            transition: transform 0.55s cubic-bezier(.4, 0, .2, 1);
            will-change: transform;
        }

        /* Desktop: 5 visible, Mobile: 1 visible */
        .carousel-slide {
            min-width: calc(100% / 5);
            flex-shrink: 0;
            padding: 0 8px;
            box-sizing: border-box;
        }

        .carousel-slide img {
            width: 100%;
            max-width: 300px;
            height: 150px;
            margin: 0 auto;
            object-fit: cover;
            border-radius: 12px;
            display: block;
            box-shadow: var(--shadow);
            transition: transform .3s, box-shadow .3s;
        }

        .carousel-slide img:hover {
            transform: scale(1.03);
            box-shadow: var(--shadow-lg);
        }

        .carousel-slide-caption {
            text-align: center;
            font-size: 0.8rem;
            color: var(--text-light);
            margin-top: 8px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Navigation */
        .carousel-nav {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 14px;
            margin-top: 24px;
        }

        .carousel-btn {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            flex-shrink: 0;
        }

        .carousel-btn:hover {
            background: var(--primary-light);
            transform: scale(1.1);
        }

        .carousel-dots {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .carousel-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent);
            border: none;
            cursor: pointer;
            transition: var(--transition);
            padding: 0;
        }

        .carousel-dot.active {
            background: var(--primary);
            width: 24px;
            border-radius: 4px;
        }

        @media (max-width: 768px) {
            .carousel-slide {
                min-width: 100%;
            }

            .carousel-slide img {
                height: 200px;
            }
        }

        /* ============= LIGHTBOX ============= */
        .lightbox-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            /* blurred transparent backdrop – web still visible behind */
            background: rgba(0, 0, 0, 0.45);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .lightbox-overlay.open {
            opacity: 1;
            pointer-events: all;
        }

        .lightbox-img-wrap {
            position: relative;
            max-width: 90vw;
            max-height: 90vh;
            transform: scale(0.88);
            transition: transform 0.3s ease;
        }

        .lightbox-overlay.open .lightbox-img-wrap {
            transform: scale(1);
        }

        .lightbox-img-wrap img {
            display: block;
            max-width: 90vw;
            max-height: 85vh;
            border-radius: 14px;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.5);
            object-fit: contain;
        }

        .lightbox-caption {
            text-align: center;
            color: #fff;
            margin-top: 12px;
            font-size: 0.95rem;
            font-weight: 500;
            text-shadow: 0 2px 6px rgba(0, 0, 0, 0.6);
        }

        /* Close button – top-right of the overlay (not relative to image) */
        .lightbox-close {
            position: fixed;
            top: 18px;
            right: 20px;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            border: 2px solid rgba(255, 255, 255, 0.5);
            color: #fff;
            font-size: 1.3rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s, transform 0.2s;
            z-index: 10000;
            line-height: 1;
            backdrop-filter: blur(4px);
        }

        .lightbox-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1) rotate(90deg);
        }

        .carousel-slide img {
            cursor: zoom-in;
        }
    </style>
@endsection

@section('content')
    <!-- Hero Section -->
    @if(\Illuminate\Support\Facades\Storage::disk('public')->exists('hero/hero_bg.webp'))
        <style>
            @media (min-width: 769px) {
                .hero {
                    background: url('{{ URL::signedRoute('public.storage.view', ['path' => 'hero/hero_bg.webp']) }}?v={{ time() }}') center center / cover no-repeat !important;
                }

                .hero::before {
                    display: none !important;
                }

                .hero-image {
                    display: none !important;
                }

                .hero-content h1,
                .hero-content p {
                    background: var(--nav-bg) !important;
                    -webkit-background-clip: text !important;
                    -webkit-text-fill-color: transparent !important;
                    color: var(--primary) !important;
                    /* Fallback */
                    text-shadow: none !important;
                }

                .btn-hero-outline {
                    background: rgba(0, 0, 0, 0.3) !important;
                    border-color: transparent !important;
                    color: white !important;
                }
            }
        </style>
    @endif
    <section class="hero">
        <div class="hero-container">
            <div class="hero-content animate-fade-in">
                <div class="hero-heading-wrapper">
                    @if(isset($school) && $school && $school->logo_ssn)
                        <img class="hero-logo-ssn" src="{{ URL::signedRoute('public.storage.view', ['path' => $school->logo_ssn]) }}"
                            alt="Logo SSN">
                    @else
                        <div class="hero-logo-ssn-placeholder"></div>
                    @endif
                    <h1>Selamat Datang di<br>{{ $school->name ?? 'SMP Negeri 6 Sudimoro' }}</h1>
                </div>
                <p>Mewujudkan generasi muda yang berilmu, berakhlak mulia, dan siap menghadapi tantangan masa depan dengan
                    pendidikan berkualitas.</p>
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
                <span class="stat-number">{{ $featuredTeachers }}+</span>
                <span class="stat-label">Guru Berpengalaman</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $studentStats['total_students'] }}+</span>
                <span class="stat-label">Siswa Aktif</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $studentStats['total_classes'] }}</span>
                <span class="stat-label">Rombongan Belajar</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $latestActivities->count() }}+</span>
                <span class="stat-label">Kegiatan Sekolah</span>
            </div>
        </div>
    </section>

    <!-- Student Statistics Details -->
    <section class="section" style="background: var(--secondary);">
        <div class="container">
            <h2 class="section-title">Data Statistik Siswa</h2>
            <p class="section-subtitle">Komposisi siswa berdasarkan kelas dan gender</p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px;">
                <!-- Chart -->
                <div>
                    <canvas id="enrollmentChart" data-years='@json($enrollmentData->pluck("enrollment_year") ?? [])'
                        data-counts='@json($enrollmentData->pluck("total") ?? [])'
                        style="width: 100%; height: 300px;"></canvas>
                </div>

                <!-- Class Breakdown -->
                <div style="background: var(--accent); padding: 25px; border-radius: 16px;">
                    <h3 style="margin-bottom: 20px; color: var(--primary);">Detail Sisiwa Per Kelas</h3>
                    <div style="max-height: 400px; overflow-y: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 2px solid rgba(0,0,0,0.1);">
                                    <th style="text-align: left; padding: 10px; color: var(--text-light);">Kelas</th>
                                    <th style="text-align: center; padding: 10px; color: var(--primary);"><i
                                            class="fas fa-male"></i></th>
                                    <th style="text-align: center; padding: 10px; color: #e83e8c;"><i
                                            class="fas fa-female"></i></th>
                                    <th style="text-align: center; padding: 10px; font-weight: bold;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($classStats as $class)
                                    <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                                        <td style="padding: 12px 10px; font-weight: 600;">{{ $class->name }}</td>
                                        <td style="text-align: center;">{{ $class->male }}</td>
                                        <td style="text-align: center;">{{ $class->female }}</td>
                                        <td style="text-align: center; font-weight: bold;">{{ $class->total }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @section('scripts')
        <script>
            (function () {
                const initChart = function () {
                    const canvas = document.getElementById('enrollmentChart');
                    if (!canvas) {
                        console.error('Canvas enrollmentChart not found');
                        return;
                    }

                    const ctx = canvas.getContext('2d');

                    // Data from data-attributes
                    let years = JSON.parse(canvas.dataset.years || '[]');
                    let counts = JSON.parse(canvas.dataset.counts || '[]');

                    // Fallback for demo/testing if data is empty
                    if (!years || years.length === 0) {
                        years = ['2020', '2021', '2022', '2023', '2024'];
                        counts = [100, 150, 200, 180, 220];
                    }

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: years,
                            datasets: [{
                                label: 'Jumlah Siswa Masuk per Tahun',
                                data: counts,
                                backgroundColor: 'rgba(30, 58, 95, 0.7)',
                                borderColor: 'rgba(30, 58, 95, 1)',
                                borderWidth: 1,
                                borderRadius: 5
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Grafik Enrollment Siswa',
                                    font: {
                                        size: 16,
                                        family: 'Inter',
                                        weight: '600'
                                    }
                                },
                                legend: {
                                    position: 'bottom'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(0,0,0,0.05)'
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                };

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initChart);
                } else {
                    initChart();
                }
            })();
        </script>

        {{-- ===== CAROUSEL JS ===== --}}
        <script>
            (function () {
                const track = document.getElementById('carouselTrack');
                const dotsBox = document.getElementById('carouselDots');
                if (!track) return; // no carousel images

                const slides = track.querySelectorAll('.carousel-slide');
                const total = slides.length;

                // Determine how many slides are visible based on viewport
                function visibleCount() {
                    return window.innerWidth <= 768 ? 1 : 5;
                }

                let current = 0;
                let autoTimer;

                function pageCount() {
                    return Math.ceil(total / visibleCount());
                }

                // Build dot buttons
                function buildDots() {
                    dotsBox.innerHTML = '';
                    const pages = pageCount();
                    for (let i = 0; i < pages; i++) {
                        const btn = document.createElement('button');
                        btn.className = 'carousel-dot' + (i === 0 ? ' active' : '');
                        btn.setAttribute('aria-label', 'Halaman ' + (i + 1));
                        btn.addEventListener('click', () => goTo(i));
                        dotsBox.appendChild(btn);
                    }
                }

                function updateDots() {
                    dotsBox.querySelectorAll('.carousel-dot').forEach((d, i) => {
                        d.classList.toggle('active', i === current);
                    });
                }

                function goTo(page) {
                    const pages = pageCount();
                    current = ((page % pages) + pages) % pages;
                    const offset = current * visibleCount() * (100 / total);
                    track.style.transform = `translateX(-${offset}%)`;
                    updateDots();
                }

                function next() { goTo(current + 1); }
                function prev() { goTo(current - 1); }

                function startAuto() {
                    stopAuto();
                    autoTimer = setInterval(next, 4000);
                }

                function stopAuto() {
                    clearInterval(autoTimer);
                }

                // Set each slide width based on total slides count (track scrolls by page)
                function setSlideSizes() {
                    const pct = 100 / total + '%';
                    slides.forEach(s => s.style.minWidth = pct);
                }

                function init() {
                    setSlideSizes();
                    buildDots();
                    goTo(0);
                    startAuto();
                }

                document.getElementById('carouselNext').addEventListener('click', () => { stopAuto(); next(); startAuto(); });
                document.getElementById('carouselPrev').addEventListener('click', () => { stopAuto(); prev(); startAuto(); });

                // Touch / swipe support
                let touchStartX = 0;
                track.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; stopAuto(); }, { passive: true });
                track.addEventListener('touchend', e => {
                    const diff = touchStartX - e.changedTouches[0].clientX;
                    if (Math.abs(diff) > 40) diff > 0 ? next() : prev();
                    startAuto();
                }, { passive: true });

                // Rebuild on resize (mobile ↔ desktop switch)
                let resizeTimer;
                window.addEventListener('resize', () => {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(() => {
                        buildDots();
                        goTo(0);
                    }, 200);
                });

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', init);
                } else {
                    init();
                }
            })();
        </script>

        {{-- ===== LIGHTBOX JS ===== --}}
        <script>
            (function () {
                const overlay = document.getElementById('lightboxOverlay');
                const lbImg = document.getElementById('lightboxImg');
                const lbCap = document.getElementById('lightboxCaption');

                if (!overlay) return;

                window.openLightbox = function (imgEl) {
                    lbImg.src = imgEl.src;
                    lbImg.alt = imgEl.alt;
                    lbCap.textContent = imgEl.dataset.caption || '';
                    lbCap.style.display = imgEl.dataset.caption ? 'block' : 'none';
                    overlay.classList.add('open');
                    document.body.style.overflow = 'hidden'; // prevent background scroll
                };

                window.closeLightbox = function () {
                    overlay.classList.remove('open');
                    document.body.style.overflow = '';
                    // clear src after transition ends to avoid flicker
                    setTimeout(() => { lbImg.src = ''; }, 300);
                };

                // Close when clicking the dark backdrop (not the image/caption)
                window.closeLightboxOnBackdrop = function (e) {
                    if (e.target === overlay) closeLightbox();
                };

                // Close with ESC key
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape' && overlay.classList.contains('open')) {
                        closeLightbox();
                    }
                });
            })();
        </script>
    @endsection

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
                                    @if(Str::startsWith($activity->image, 'data:'))
                                        <img src="{{ $activity->image }}" alt="{{ $activity->title }}">
                                    @else
                                        <img src="{{ URL::signedRoute('public.storage.view', ['path' => $activity->image]) }}"
                                            alt="{{ $activity->title }}">
                                    @endif
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

    {{-- ======================== CAROUSEL SECTION ======================== --}}
    @if(isset($carouselImages) && $carouselImages->isNotEmpty())
        <section class="carousel-section" id="section-carousel">
            <div class="container" style="text-align:center;">
                <h2 class="section-title">Galeri Foto</h2>
                <p class="section-subtitle">Momen dan kegiatan sekolah kami</p>
            </div>

            <div class="carousel-wrapper" id="carouselWrapper">
                <div class="carousel-track" id="carouselTrack">
                    @foreach($carouselImages as $img)
                        <div class="carousel-slide">
                            <img src="{{ URL::signedRoute('public.storage.view', ['path' => $img->image_path]) }}"
                                alt="{{ $img->title ?? 'Galeri' }}" loading="lazy" data-caption="{{ $img->title ?? '' }}"
                                onclick="openLightbox(this)">
                            @if($img->title)
                                <div class="carousel-slide-caption">{{ $img->title }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="carousel-nav">
                <button class="carousel-btn" id="carouselPrev" aria-label="Sebelumnya">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="carousel-dots" id="carouselDots"></div>
                <button class="carousel-btn" id="carouselNext" aria-label="Selanjutnya">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </section>
    @endif

    {{-- ===== LIGHTBOX ===== --}}
    <div class="lightbox-overlay" id="lightboxOverlay" onclick="closeLightboxOnBackdrop(event)">
        <button class="lightbox-close" id="lightboxClose" onclick="closeLightbox()" aria-label="Tutup">&times;</button>
        <div class="lightbox-img-wrap" id="lightboxImgWrap">
            <img id="lightboxImg" src="" alt="">
            <div class="lightbox-caption" id="lightboxCaption"></div>
        </div>
    </div>

    {{-- Social Media Section --}}
    @if($socials->isNotEmpty())
        <section class="social-section">
            <div class="container text-center">
                <h2 class="section-title">Terhubung Dengan Kami</h2>
                <p class="section-subtitle">Ikuti media sosial resmi kami untuk informasi terkini</p>

                <div class="social-links">
                    @foreach($socials as $social)
                        <a href="{{ $social->url }}" target="_blank" class="social-link" title="{{ $social->platform }}">
                            <i class="{{ $social->icon }}"></i>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection