<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $school->name ?? 'SMP Negeri 6 Sudimoro' }} - Sekolah Menengah Pertama Negeri">
    <title>@yield('title', $school->name ?? 'SMP Negeri 6 Sudimoro')</title>
    @if(isset($school) && $school && $school->logo)
    <link rel="icon" type="image/png" href="{{ asset('storage/' . $school->logo) }}">
    @else
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #1e3a5f;
            --primary-light: #2c4f7c;
            --primary-dark: #0f2340;
            --secondary: #ffffff;
            --accent: #f8f9fa;
            --accent-gold: #d4af37;
            --text: #333333;
            --text-light: #6c757d;
            --shadow: 0 4px 20px rgba(30, 58, 95, 0.15);
            --shadow-lg: 0 10px 40px rgba(30, 58, 95, 0.2);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text);
            line-height: 1.6;
            background: var(--accent);
        }

        /* Navigation */
        .navbar {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: var(--shadow);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--secondary);
        }

        .nav-brand img {
            height: 45px;
            width: auto;
        }

        .nav-brand-text {
            display: flex;
            flex-direction: column;
        }

        .nav-brand-name {
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .nav-brand-sub {
            font-size: 0.7rem;
            opacity: 0.8;
            font-weight: 400;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 5px;
        }

        .nav-item {
            position: relative;
        }

        .nav-link {
            display: block;
            padding: 12px 18px;
            color: var(--secondary);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            border-radius: 8px;
            transition: var(--transition);
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
        }

        .nav-link i {
            margin-left: 5px;
            font-size: 0.7rem;
        }

        /* Dropdown */
        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            min-width: 200px;
            background: var(--secondary);
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: var(--transition);
            overflow: hidden;
            list-style: none;
        }

        .nav-item:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            display: block;
            padding: 12px 20px;
            color: var(--text);
            text-decoration: none;
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .dropdown-item:hover {
            background: var(--primary);
            color: var(--secondary);
        }

        /* Mobile Menu Toggle */
        .nav-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--secondary);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 10px;
        }

        /* Main Content */
        main {
            margin-top: 70px;
            min-height: calc(100vh - 70px - 300px);
        }

        /* Footer */
        .footer {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            color: var(--secondary);
            padding: 60px 20px 30px;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
        }

        .footer-section h3 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }

        .footer-section h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--accent-gold);
            border-radius: 2px;
        }

        .footer-section p,
        .footer-section a {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            line-height: 1.8;
        }

        .footer-section a {
            text-decoration: none;
            display: block;
            transition: var(--transition);
        }

        .footer-section a:hover {
            color: var(--accent-gold);
            transform: translateX(5px);
        }

        .footer-contact i {
            width: 20px;
            margin-right: 10px;
            color: var(--accent-gold);
        }

        .footer-bottom {
            max-width: 1200px;
            margin: 40px auto 0;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            font-size: 0.85rem;
            opacity: 0.8;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-toggle {
                display: block;
            }

            .nav-menu {
                position: fixed;
                top: 70px;
                left: 0;
                right: 0;
                background: var(--primary);
                flex-direction: column;
                padding: 20px;
                gap: 5px;
                transform: translateX(-100%);
                transition: var(--transition);
            }

            .nav-menu.active {
                transform: translateX(0);
            }

            .dropdown-menu {
                position: static;
                opacity: 1;
                visibility: visible;
                transform: none;
                box-shadow: none;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 8px;
                margin-top: 5px;
                display: none;
            }

            .nav-item.active .dropdown-menu {
                display: block;
            }

            .dropdown-item {
                color: var(--secondary);
                padding: 10px 15px;
            }

            .dropdown-item:hover {
                background: rgba(255, 255, 255, 0.1);
            }

            .nav-brand-name {
                font-size: 0.95rem;
            }

            .nav-brand-sub {
                font-size: 0.65rem;
            }
        }

        /* Utility Classes */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .section {
            padding: 80px 20px;
        }

        .section-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            text-align: center;
            margin-bottom: 15px;
        }

        .section-subtitle {
            text-align: center;
            color: var(--text-light);
            margin-bottom: 50px;
            font-size: 1rem;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            cursor: pointer;
            border: none;
            font-size: 0.95rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: var(--secondary);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
        }

        .btn-outline:hover {
            background: var(--primary);
            color: var(--secondary);
        }

        .card {
            background: var(--secondary);
            border-radius: 16px;
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: var(--transition);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeInUp 0.6s ease forwards;
        }
    </style>
    @yield('styles')
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="{{ route('home') }}" class="nav-brand">
                @if(isset($school) && $school && $school->logo)
                <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo">
                @else
                <i class="fas fa-school" style="font-size: 2rem;"></i>
                @endif
                <div class="nav-brand-text">
                    <span class="nav-brand-name">{{ $school->name ?? 'SMP Negeri 6 Sudimoro' }}</span>
                    <span class="nav-brand-sub">Excellence in Education</span>
                </div>
            </a>

            <button class="nav-toggle" onclick="toggleMenu()">
                <i class="fas fa-bars"></i>
            </button>

            <ul class="nav-menu" id="navMenu">
                <li class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a>
                </li>
                <li class="nav-item" onclick="toggleDropdown(this)">
                    <a href="#" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        Profil <i class="fas fa-chevron-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('profile.school') }}" class="dropdown-item">Profil Sekolah</a></li>
                        <li><a href="{{ route('profile.teachers') }}" class="dropdown-item">Profil Guru</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="{{ route('activities.index') }}" class="nav-link {{ request()->routeIs('activities.*') ? 'active' : '' }}">Kegiatan</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('information.index') }}" class="nav-link {{ request()->routeIs('information.*') ? 'active' : '' }}">Informasi</a>
                </li>
                <li class="nav-item" onclick="toggleDropdown(this)">
                    <a href="#" class="nav-link {{ request()->routeIs('pmb.*') ? 'active' : '' }}">
                        PMB <i class="fas fa-chevron-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('pmb.index') }}" class="dropdown-item">Pendaftaran</a></li>
                        <li><a href="{{ route('pmb.status') }}" class="dropdown-item">Cek Status</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="{{ route('login') }}" class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3>Tentang Kami</h3>
                <p>{{ $school->name ?? 'SMP Negeri 6 Sudimoro' }} adalah sekolah menengah pertama yang berkomitmen memberikan pendidikan berkualitas untuk generasi masa depan.</p>
            </div>
            <div class="footer-section">
                <h3>Link Cepat</h3>
                <a href="{{ route('home') }}">Beranda</a>
                <a href="{{ route('profile.school') }}">Profil Sekolah</a>
                <a href="{{ route('activities.index') }}">Kegiatan</a>
                <a href="{{ route('information.index') }}">Informasi</a>
            </div>
            <div class="footer-section footer-contact">
                <h3>Kontak</h3>
                <p><i class="fas fa-map-marker-alt"></i> {{ $school->address ?? 'Sudimoro, Indonesia' }}</p>
                <p><i class="fas fa-phone"></i> {{ $school->phone ?? '-' }}</p>
                <p><i class="fas fa-envelope"></i> {{ $school->email ?? '-' }}</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} {{ $school->name ?? 'SMP Negeri 6 Sudimoro' }}. All rights reserved.</p>
        </div>
    </footer>

    @if(isset($is_pmb_open) && $is_pmb_open)
    <!-- PMB Announcement Modal -->
    <div id="pmbModal" class="modal-overlay" style="display: none;">
        <div class="modal-content animate-fade-in">
            <button class="modal-close" onclick="closePmbModal()">&times;</button>
            <div class="modal-header">
                <div class="modal-icon">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <h3>Pendaftaran Dibuka!</h3>
            </div>
            <div class="modal-body">
                <p>Kabar gembira! Penerimaan Murid Baru (PMB) tahun pelajaran ini telah resmi dibuka. Jangan lewatkan kesempatan untuk bergabung dengan {{ $school->name ?? 'SMP Negeri 6 Sudimoro' }}.</p>
                <div class="modal-features">
                    <div class="feature-item">
                        <i class="fas fa-check"></i>
                        <span>Proses Cepat & Mudah</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check"></i>
                        <span>Pendaftaran Online 24 Jam</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('pmb.index') }}" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 15px;">
                    Daftar Sekarang <i class="fas fa-arrow-right" style="margin-left: 10px;"></i>
                </a>
            </div>
        </div>
    </div>

    <style>
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .modal-content {
            background: white;
            width: 90%;
            max-width: 450px;
            border-radius: 20px;
            padding: 40px;
            position: relative;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .modal-close {
            position: absolute;
            top: 15px;
            right: 20px;
            background: none;
            border: none;
            font-size: 24px;
            color: #ccc;
            cursor: pointer;
            transition: color 0.3s;
        }

        .modal-close:hover {
            color: var(--primary);
        }

        .modal-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .modal-icon {
            width: 70px;
            height: 70px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            margin: 0 auto 15px;
            box-shadow: 0 10px 20px rgba(30, 58, 95, 0.2);
        }

        .modal-header h3 {
            color: var(--primary);
            font-size: 1.5rem;
            margin: 0;
        }

        .modal-body p {
            color: var(--text-light);
            line-height: 1.6;
            margin-bottom: 20px;
            text-align: center;
        }

        .modal-features {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
            margin-bottom: 25px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text);
            font-size: 0.9rem;
            background: #f8fafc;
            padding: 10px 15px;
            border-radius: 8px;
        }

        .feature-item i {
            color: #10b981;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Only show once per session
            if (!sessionStorage.getItem('pmb_modal_shown')) {
                setTimeout(() => {
                    document.getElementById('pmbModal').style.display = 'flex';
                }, 1500);
            }
        });

        function closePmbModal() {
            document.getElementById('pmbModal').style.display = 'none';
            sessionStorage.setItem('pmb_modal_shown', 'true');
        }

        // Close when clicking overlay
        document.getElementById('pmbModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePmbModal();
            }
        });
    </script>
    @endif

    <script>
        function toggleMenu() {
            document.getElementById('navMenu').classList.toggle('active');
        }

        function toggleDropdown(item) {
            if (window.innerWidth <= 768) {
                item.classList.toggle('active');
            }
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(e) {
            const nav = document.querySelector('.navbar');
            const toggle = document.querySelector('.nav-toggle');
            if (nav && toggle && !nav.contains(e.target) && !toggle.contains(e.target)) {
                document.getElementById('navMenu').classList.remove('active');
            }
        });
    </script>
    @yield('scripts')
</body>

</html>