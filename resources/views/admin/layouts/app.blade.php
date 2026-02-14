<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - {{ $school->name ?? 'SMP Negeri 6 Sudimoro' }}</title>
    @if(isset($school) && $school && $school->logo)
        <link rel="icon" type="image/png" href="{{ route('public.storage.view', ['path' => $school->logo]) }}">
    @else
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @yield('styles')
    @include('partials.theme')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--body-bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--nav-bg);
            color: var(--secondary);
            padding: 20px 0;
            z-index: 1000;
            overflow-y: auto;
            transition: var(--transition);
        }

        .sidebar-brand {
            padding: 15px 25px 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .sidebar-brand h2 {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .sidebar-brand span {
            font-size: 0.75rem;
            opacity: 0.7;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0 15px;
        }

        .sidebar-menu li {
            margin-bottom: 5px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 10px;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255, 255, 255, 0.15);
            color: var(--secondary);
        }

        .sidebar-menu a i {
            width: 20px;
            text-align: center;
        }

        .sidebar-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
            margin: 20px 25px;
        }

        /* Submenu */
        .has-submenu {
            position: relative;
        }

        .submenu {
            list-style: none;
            padding-left: 15px;
            display: none;
            margin-top: 5px;
        }

        .has-submenu.active .submenu {
            display: block !important;
        }

        .submenu-toggle {
            display: flex;
            justify-content: space-between !important;
            align-items: center;
            cursor: pointer;
        }

        .submenu-toggle i:last-child {
            font-size: 0.7rem;
            transition: var(--transition);
        }

        .has-submenu.active .submenu-toggle i:last-child {
            transform: rotate(90deg);
        }

        .submenu a {
            padding: 10px 18px !important;
            font-size: 0.85rem !important;
            opacity: 0.8;
        }

        .submenu a:hover,
        .submenu a.active {
            opacity: 1;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        /* Top Bar */
        .topbar {
            background: var(--secondary);
            border-bottom: 2px solid var(--primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
            transition: var(--transition);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .topbar-left h1 {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: var(--primary);
            color: var(--secondary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .user-name {
            font-size: 0.9rem;
            font-weight: 600;
        }

        .btn-logout {
            padding: 10px 20px;
            background: transparent;
            border: 2px solid var(--danger);
            color: var(--danger);
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            transition: var(--transition);
            font-family: 'Inter', sans-serif;
        }

        .btn-logout:hover {
            background: var(--danger);
            color: var(--secondary);
        }

        /* Sidebar Overlay */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
            backdrop-filter: blur(2px);
        }

        .sidebar-overlay.active {
            display: block;
        }

        /* Content Area */
        .content {
            padding: 30px;
        }

        /* Cards */
        .card {
            background: var(--secondary);
            border-radius: 16px;
            box-shadow: var(--shadow);
            margin-bottom: 25px;
        }

        .card-header {
            padding: 20px 25px;
            border-bottom: 1px solid var(--accent);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h2 {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .card-body {
            padding: 25px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--secondary);
            border-radius: 16px;
            padding: 25px;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-icon.primary {
            background: rgba(30, 58, 95, 0.1);
            color: var(--primary);
        }

        .stat-icon.success {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success);
        }

        .stat-icon.warning {
            background: rgba(255, 193, 7, 0.1);
            color: var(--warning);
        }

        .stat-icon.gold {
            background: rgba(212, 175, 55, 0.1);
            color: var(--accent-gold);
        }

        .stat-info h3 {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text);
        }

        .stat-info p {
            color: var(--text-light);
            font-size: 0.85rem;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: var(--transition);
            font-family: 'Inter', sans-serif;
        }

        .btn-primary {
            background: var(--primary);
            color: var(--secondary);
        }

        .btn-primary:hover {
            background: var(--primary-light);
        }

        .btn-success {
            background: var(--success);
            color: var(--secondary);
        }

        .btn-danger {
            background: var(--danger);
            color: var(--secondary);
        }

        .btn-warning {
            background: var(--warning);
            color: var(--text);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.8rem;
        }

        /* Tables */
        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--accent);
        }

        table th {
            font-weight: 600;
            color: var(--text-light);
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        table td {
            font-size: 0.9rem;
        }

        table tr:hover {
            background: var(--accent);
        }

        /* Forms */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
        }

        .form-input,
        .form-textarea,
        .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--accent);
            border-radius: 10px;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            transition: var(--transition);
            background: var(--secondary);
        }

        .form-textarea {
            min-height: 150px;
            resize: vertical;
        }

        .form-input:focus,
        .form-textarea:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--primary);
        }

        .form-checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-checkbox input {
            width: 20px;
            height: 20px;
            accent-color: var(--primary);
        }

        /* Alerts */
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success);
            border: 1px solid var(--success);
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: var(--danger);
            border: 1px solid var(--danger);
        }

        /* Badge */
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-success {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success);
        }

        .badge-danger {
            background: rgba(220, 53, 69, 0.1);
            color: var(--danger);
        }

        .badge-warning {
            background: rgba(255, 193, 7, 0.1);
            color: #856404;
        }

        /* Preview Image */
        .preview-image {
            max-width: 150px;
            height: auto;
            border-radius: 10px;
            margin-top: 10px;
        }

        /* Pagination */
        .pagination {
            display: flex;
            gap: 8px;
            list-style: none;
            margin-top: 20px;
        }

        .pagination li a,
        .pagination li span {
            display: block;
            padding: 8px 14px;
            background: var(--secondary);
            color: var(--text);
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.85rem;
            border: 1px solid var(--accent);
            transition: var(--transition);
        }

        .pagination li.active span {
            background: var(--primary);
            color: var(--secondary);
            border-color: var(--primary);
        }

        .pagination li a:hover {
            background: var(--primary);
            color: var(--secondary);
            border-color: var(--primary);
        }

        /* Mobile Toggle */
        .sidebar-toggle {
            display: none;
            background: rgba(30, 58, 95, 0.1);
            color: var(--primary);
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 1.3rem;
            transition: var(--transition);
        }

        .sidebar-toggle:hover {
            background: var(--primary);
            color: var(--secondary);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .topbar {
                padding: 15px 20px;
            }

            .topbar-right .user-name {
                display: none;
            }

            .btn-logout span {
                display: none;
            }

            .btn-logout {
                width: 40px;
                height: 40px;
                padding: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 10px;
            }

            .topbar-left h1 {
                font-size: 1.1rem;
            }
        }

        @media (max-width: 576px) {
            .topbar-left h1 {
                display: none;
            }

            .content {
                padding: 15px;
            }

            .stat-card {
                padding: 20px;
            }
        }
    </style>
    @yield('styles')
</head>

<body>
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="overlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h2><i class="fas fa-school"></i> {{ $school->name ?? 'SMP N 6 Sudimoro' }}</h2>
            <span>Panel Admin</span>
        </div>

        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('admin.dashboard') }}"
                    class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            @if(auth()->user()->isAdmin() || auth()->user()->isTeacher())
                <li class="has-submenu {{ request()->routeIs('admin.settings.pmb') || request()->routeIs('admin.academic-years.*') || request()->routeIs('admin.pmb-registrations.*') || request()->routeIs('admin.subjects.*') ? 'active' : '' }}"
                    id="school-menu">
                    <a href="javascript:void(0)" onclick="toggleSubmenu('school-menu')" class="submenu-toggle">
                        <span><i class="fas fa-school"></i> Data Sekolah</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <ul class="submenu">
                        <li>
                            <a href="{{ route('admin.school-profile.index') }}"
                                class="{{ request()->routeIs('admin.school-profile.*') ? 'active' : '' }}">
                                <i class="fas fa-school"></i> Profil Sekolah
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.classes.index') }}"
                                class="{{ request()->routeIs('admin.classes.*') ? 'active' : '' }}">
                                <i class="fas fa-layer-group"></i> Kelas
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.teachers.index') }}"
                                class="{{ request()->routeIs('admin.teachers.*') ? 'active' : '' }}">
                                <i class="fas fa-chalkboard-teacher"></i> Guru
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.students.index') }}"
                                class="{{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                                <i class="fas fa-user-graduate"></i> Siswa
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.subjects.index') }}"
                                class="{{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}">
                                <i class="fas fa-book"></i> Mata Pelajaran
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.teaching-modules.index') }}"
                                class="{{ request()->routeIs('admin.teaching-modules.*') ? 'active' : '' }}">
                                <i class="fas fa-book-reader"></i> Modul Ajar
                            </a>
                        </li>

                    </ul>
                </li>

            @endif
            <li class="has-submenu {{ request()->routeIs('admin.archives.*') || request()->routeIs('admin.archive-types.*') ? 'active' : '' }}"
                id="archive-menu">
                <a href="javascript:void(0)" onclick="toggleSubmenu('archive-menu')" class="submenu-toggle">
                    <span><i class="fas fa-archive"></i> Arsip PTK</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="{{ route('admin.archives.index') }}"
                            class="{{ request()->routeIs('admin.archives.*') ? 'active' : '' }}">
                            <i class="fas fa-file-archive"></i> Daftar Arsip
                        </a>
                    </li>
                    @if(auth()->user()->isAdmin())
                        <li>
                            <a href="{{ route('admin.archive-types.index') }}"
                                class="{{ request()->routeIs('admin.archive-types.*') ? 'active' : '' }}">
                                <i class="fas fa-tags"></i> Jenis Arsip
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
            <li>
                <a href="{{ route('admin.activities.index') }}"
                    class="{{ request()->routeIs('admin.activities.*') ? 'active' : '' }}">
                    <i class="fas fa-newspaper"></i> Kegiatan
                </a>
            </li>
            <li>
                <a href="{{ route('admin.social-media.index') }}"
                    class="{{ request()->routeIs('admin.social-media.*') ? 'active' : '' }}">
                    <i class="fas fa-share-alt"></i> Media Sosial
                </a>
            </li>
            @if(auth()->user()->isAdmin() || auth()->user()->isTeacher())
                <li>
                    <a href="{{ route('admin.information.index') }}"
                        class="{{ request()->routeIs('admin.information.*') ? 'active' : '' }}">
                        <i class="fas fa-bullhorn"></i> Informasi
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.public-complaints.index') }}"
                        class="{{ request()->routeIs('admin.public-complaints.*') ? 'active' : '' }}">
                        <i class="fas fa-comments"></i> Aduan Masyarakat
                        @if(auth()->user()->isAdmin() && isset($unrespondedComplaintsCount) && $unrespondedComplaintsCount > 0)
                            <span class="badge"
                                style="margin-left: auto; background: var(--danger); color: white;">{{ $unrespondedComplaintsCount }}</span>
                        @endif
                    </a>
                </li>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->isAdminKomite())
                <div class="sidebar-divider"></div>
                <p
                    style="padding: 10px 25px; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.5;">
                    Penerimaan Murid Baru</p>
                <li class="has-submenu {{ request()->routeIs('admin.settings.pmb') || request()->routeIs('admin.academic-years.*') || request()->routeIs('admin.pmb-registrations.*') ? 'active' : '' }}"
                    id="pmb-menu">
                    <a href="javascript:void(0)" onclick="toggleSubmenu('pmb-menu')" class="submenu-toggle">
                        <span><i class="fas fa-graduation-cap"></i> PMB</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <ul class="submenu">
                        <li>
                            <a href="{{ route('admin.settings.pmb') }}"
                                class="{{ request()->routeIs('admin.settings.pmb') ? 'active' : '' }}">
                                <i class="fas fa-cog"></i> Pengaturan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.academic-years.index') }}"
                                class="{{ request()->routeIs('admin.academic-years.*') ? 'active' : '' }}">
                                <i class="fas fa-calendar-alt"></i> Tahun Pelajaran
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.pmb-registrations.index') }}"
                                class="{{ request()->routeIs('admin.pmb-registrations.*') ? 'active' : '' }}">
                                <i class="fas fa-user-plus"></i> Data Pendaftaran
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->isAdminKomite())
                <div class="sidebar-divider"></div>
                <p
                    style="padding: 10px 25px; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.5;">
                    Komite Sekolah</p>
                <li class="has-submenu {{ request()->routeIs('admin.committee.*') ? 'active' : '' }}" id="committee-menu">
                    <a href="javascript:void(0)" onclick="toggleSubmenu('committee-menu')" class="submenu-toggle">
                        <span><i class="fas fa-hand-holding-usd"></i> Komite</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <ul class="submenu">
                        <li>
                            <a href="{{ route('admin.committee.nominal.index') }}"
                                class="{{ request()->routeIs('admin.committee.nominal.*') ? 'active' : '' }}">
                                <i class="fas fa-money-bill-wave"></i> Set Nominal
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.committee.planning.index') }}"
                                class="{{ request()->routeIs('admin.committee.planning.*') ? 'active' : '' }}">
                                <i class="fas fa-tasks"></i> Perencanaan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.committee.payments.index') }}"
                                class="{{ request()->routeIs('admin.committee.payments.*') ? 'active' : '' }}">
                                <i class="fas fa-receipt"></i> Pembayaran
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.committee.report.index') }}"
                                class="{{ request()->routeIs('admin.committee.report.*') ? 'active' : '' }}">
                                <i class="fas fa-file-alt"></i> Laporan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.committee.expenditures.index') }}"
                                class="{{ request()->routeIs('admin.committee.expenditures.*') ? 'active' : '' }}">
                                <i class="fas fa-hand-holding-heart"></i> Penggunaan
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->isAdminKomite() || auth()->user()->isLibraryStaff())
                <div class="sidebar-divider"></div>
                <p
                    style="padding: 10px 25px; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.5;">
                    Perpustakaan</p>
                <li class="has-submenu {{ request()->routeIs('admin.library.*') ? 'active' : '' }}" id="library-menu">
                    <a href="javascript:void(0)" onclick="toggleSubmenu('library-menu')" class="submenu-toggle">
                        <span><i class="fas fa-book-open"></i> Perpustakaan</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <ul class="submenu">
                        <li>
                            <a href="{{ route('admin.library.book-types.index') }}"
                                class="{{ request()->routeIs('admin.library.book-types.*') ? 'active' : '' }}">
                                <i class="fas fa-tags"></i> Jenis Buku
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.library.books.index') }}"
                                class="{{ request()->routeIs('admin.library.books.*') ? 'active' : '' }}">
                                <i class="fas fa-book"></i> Pendataan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.library.conditions.index') }}"
                                class="{{ request()->routeIs('admin.library.conditions.*') ? 'active' : '' }}">
                                <i class="fas fa-clipboard-check"></i> Jumlah & Kondisi
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.library.borrowings.index') }}"
                                class="{{ request()->routeIs('admin.library.borrowings.*') ? 'active' : '' }}">
                                <i class="fas fa-hand-holding"></i> Peminjaman
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.library.reports.index') }}"
                                class="{{ request()->routeIs('admin.library.reports.*') ? 'active' : '' }}">
                                <i class="fas fa-file-alt"></i> Laporan
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
        </ul>

        @if(auth()->user()->isAdmin())
            <div class="sidebar-divider"></div>

            <ul class="sidebar-menu">
                <li class="has-submenu {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.profile.*') || request()->routeIs('admin.system.*') || request()->routeIs('admin.settings.smtp') ? 'active' : '' }}"
                    id="admin-management-menu">
                    <a href="javascript:void(0)" onclick="toggleSubmenu('admin-management-menu')" class="submenu-toggle">
                        <span><i class="fas fa-user-shield"></i> Manajemen Admin</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <ul class="submenu">
                        <li>
                            <a href="{{ route('admin.users.index') }}"
                                class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                <i class="fas fa-users"></i> Manajemen User
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.profile.index') }}"
                                class="{{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                                <i class="fas fa-user-cog"></i> Profil Admin
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.system.index') }}"
                                class="{{ request()->routeIs('admin.system.*') ? 'active' : '' }}">
                                <i class="fas fa-tools"></i> Pengaturan Sistem
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.settings.smtp') }}"
                                class="{{ request()->routeIs('admin.settings.smtp') ? 'active' : '' }}">
                                <i class="fas fa-cog"></i> Pengaturan SMTP
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        @endif

        <div class="sidebar-divider"></div>

        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('home') }}" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Lihat Website
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="topbar-right">
                <div class="user-info">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <span class="user-name">{{ Auth::user()->name }}</span>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Content -->
        <div class="content">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('overlay').classList.toggle('active');

            // Prevent scrolling when sidebar is open on mobile
            if (window.innerWidth <= 992) {
                if (document.getElementById('sidebar').classList.contains('active')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            }
        }

        function toggleSubmenu(id) {
            const menu = document.getElementById(id);
            menu.classList.toggle('active');
        }
    </script>
    @yield('scripts')
</body>

</html>