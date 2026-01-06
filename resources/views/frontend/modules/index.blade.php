@extends('layouts.app')

@section('title', 'Modul Ajar')

@section('styles')
    <style>
        .filter-section {
            background: var(--secondary);
            padding: 30px;
            border-radius: 16px;
            box-shadow: var(--shadow);
            margin-bottom: 40px;
        }

        .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--accent);
            border-radius: 10px;
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-select:focus {
            outline: none;
            border-color: var(--primary);
        }

        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
        }

        .module-card {
            background: var(--secondary);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .module-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .module-icon {
            height: 140px;
            background: rgba(220, 53, 69, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--danger);
            font-size: 4rem;
        }

        .module-content {
            padding: 25px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .module-meta {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            font-size: 0.8rem;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            background: var(--accent);
            color: var(--text-light);
        }

        .badge-year {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success);
        }

        .module-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .module-desc {
            color: var(--text-light);
            font-size: 0.9rem;
            margin-bottom: 20px;
            flex-grow: 1;
        }

        .btn-view {
            width: 100%;
            padding: 12px;
            background: var(--primary);
            color: var(--secondary);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-view:hover {
            background: var(--primary-light);
        }

        /* PDF Modal */
        .pdf-modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(5px);
        }

        .pdf-modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90%;
            height: 90%;
            background-color: white;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .pdf-modal-header {
            padding: 15px 20px;
            background: var(--primary);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .pdf-modal-body {
            flex-grow: 1;
            background: #eee;
        }

        .close-modal {
            color: white;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
            line-height: 1;
        }
    </style>
@endsection

@section('content')
    <div class="container section">
        <div style="text-align: center; margin-bottom: 50px;">
            <h1 class="section-title">Modul Ajar</h1>
            <p class="section-subtitle">Kumpulan materi pembelajaran digital untuk siswa.</p>
        </div>

        <!-- Filter -->
        <div class="filter-section">
            <form action="{{ route('modules.index') }}" method="GET"
                style="display: flex; gap: 20px; align-items: center; flex-wrap: wrap;">
                <div style="flex-grow: 1;">
                    <label for="year" class="form-label">Tahun Ajaran</label>
                    <select name="academic_year_id" id="year" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Semua Tahun Ajaran --</option>
                        @foreach($years as $year)
                            <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                                {{ $year->year }} {{ $year->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="align-self: flex-end;">
                    <a href="{{ route('modules.index') }}" class="btn-hero btn-hero-outline"
                        style="padding: 10px 20px; border-radius: 10px; font-size: 0.9rem;">
                        Reset Filter
                    </a>
                </div>
            </form>
        </div>

        <!-- Modules Grid -->
        @if($modules->isEmpty())
            <div style="text-align: center; padding: 50px; background: var(--secondary); border-radius: 16px;">
                <i class="fas fa-folder-open"
                    style="font-size: 4rem; color: var(--text-light); margin-bottom: 20px; opacity: 0.5;"></i>
                <h3 style="color: var(--text-light);">Tidak ada modul ajar ditemukan.</h3>
            </div>
        @else
            <div class="modules-grid">
                @foreach($modules as $module)
                    <div class="module-card">
                        <div class="module-icon">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div class="module-content">
                            <div class="module-meta">
                                <span class="badge">{{ $module->subject->name }}</span>
                                @if($module->schoolClass)
                                    <span class="badge badge-warning">{{ $module->schoolClass->name }}</span>
                                @endif
                                <span class="badge badge-year">{{ $module->academicYear->year }}</span>
                            </div>
                            <h3 class="module-title">{{ $module->title }}</h3>
                            <div class="module-desc">
                                {{ Str::limit($module->description, 80) }}
                            </div>
                            <button onclick="openPdfModal('{{ $module->title }}', '{{ route('modules.view', $module->id) }}')"
                                class="btn-view">
                                <i class="fas fa-eye"></i> Lihat Materi
                            </button>
                            <a href="{{ route('modules.download', $module->id) }}" class="btn-hero btn-hero-outline"
                                style="width: 100%; margin-top: 10px; justify-content: center; border-radius: 8px; font-size: 0.9rem; padding: 8px;">
                                <i class="fas fa-download" style="margin-right: 5px;"></i> Download
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- PDF Modal -->
    <div id="pdfModal" class="pdf-modal">
        <div class="pdf-modal-content">
            <div class="pdf-modal-header">
                <h3 id="modalTitle" style="margin: 0; font-size: 1.1rem;">Lihat Modul</h3>
                <button class="close-modal" onclick="closePdfModal()">&times;</button>
            </div>
            <div class="pdf-modal-body">
                <iframe id="pdfViewer" src="" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        function openPdfModal(title, url) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('pdfViewer').src = url;
            document.getElementById('pdfModal').style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        }

        function closePdfModal() {
            document.getElementById('pdfModal').style.display = 'none';
            document.getElementById('pdfViewer').src = '';
            document.body.style.overflow = 'auto'; // Restore scrolling
        }

        // Close when clicking outside content
        window.onclick = function (event) {
            if (event.target == document.getElementById('pdfModal')) {
                closePdfModal();
            }
        }
    </script>
@endsection