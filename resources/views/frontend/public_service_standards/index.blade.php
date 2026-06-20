@extends('layouts.app')

@section('title', 'Standar Pelayanan Publik')

@section('styles')
    <style>
        .spp-container {
            max-width: 1100px;
            margin: 0 auto;
        }

        .spp-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .spp-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
        }

        .spp-card {
            background: var(--secondary);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .spp-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .spp-icon {
            height: 140px;
            background: rgba(30, 58, 95, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 4rem;
        }

        .spp-content {
            padding: 25px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .spp-meta {
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

        .badge-date {
            background: rgba(30, 58, 95, 0.1);
            color: var(--primary);
        }

        .spp-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .spp-desc {
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

        .btn-download {
            width: 100%;
            margin-top: 10px;
            justify-content: center;
            border-radius: 8px;
            font-size: 0.9rem;
            padding: 8px;
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
        <div class="spp-container">
            <div class="spp-header">
                <h1 class="section-title">Standar Pelayanan Publik</h1>
                <p class="section-subtitle">Dokumen Standar Pelayanan Publik (SPP) {{ $school->name ?? 'SMP Negeri 6 Sudimoro' }}.</p>
            </div>

            @if($standards->isEmpty())
                <div style="text-align: center; padding: 50px; background: var(--secondary); border-radius: 16px;">
                    <i class="fas fa-folder-open"
                        style="font-size: 4rem; color: var(--text-light); margin-bottom: 20px; opacity: 0.5;"></i>
                    <h3 style="color: var(--text-light);">Belum ada dokumen SPP tersedia.</h3>
                </div>
            @else
                <div class="spp-grid">
                    @foreach($standards as $standard)
                        <div class="spp-card">
                            <div class="spp-icon">
                                <i class="fas fa-file-contract"></i>
                            </div>
                            <div class="spp-content">
                                <div class="spp-meta">
                                    <span class="badge badge-date">
                                        <i class="fas fa-calendar-alt"></i> {{ $standard->created_at->format('d/m/Y') }}
                                    </span>
                                </div>
                                <h3 class="spp-title">{{ $standard->title }}</h3>
                                <div class="spp-desc">
                                    {{ Str::limit($standard->description, 80) ?: 'Dokumen Standar Pelayanan Publik' }}
                                </div>
                                <button onclick="openPdfModal('{{ $standard->title }}', '{{ route('spp.view', $standard->id) }}')"
                                    class="btn-view">
                                    <i class="fas fa-eye"></i> Lihat Dokumen
                                </button>
                                <a href="{{ route('spp.download', $standard->id) }}" class="btn-view btn-download" style="background: transparent; border: 2px solid var(--primary); color: var(--primary);">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- PDF Modal -->
    <div id="pdfModal" class="pdf-modal">
        <div class="pdf-modal-content">
            <div class="pdf-modal-header">
                <h3 id="modalTitle" style="margin: 0; font-size: 1.1rem;">Lihat Dokumen</h3>
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
            document.body.style.overflow = 'hidden';
        }

        function closePdfModal() {
            document.getElementById('pdfModal').style.display = 'none';
            document.getElementById('pdfViewer').src = '';
            document.body.style.overflow = 'auto';
        }

        window.onclick = function (event) {
            if (event.target == document.getElementById('pdfModal')) {
                closePdfModal();
            }
        }
    </script>
@endsection