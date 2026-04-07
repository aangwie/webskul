@extends('layouts.app')

@section('title', 'Fasilitas Sekolah')

@section('styles')
<style>
    .facilities-header {
        @if(isset($school) && $school->hero_image)
        background: linear-gradient(rgba(30, 58, 95, 0.9), rgba(30, 58, 95, 0.8)), url('{{ route('public.storage.view', ['path' => $school->hero_image]) }}');
        @else
        background: linear-gradient(rgba(30, 58, 95, 0.9), rgba(30, 58, 95, 0.8));
        @endif
        background-size: cover;
        background-position: center;
        color: var(--secondary);
        padding: 80px 0;
        text-align: center;
        margin-bottom: 50px;
    }

    .facilities-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 15px;
    }

    .facilities-header p {
        font-size: 1.1rem;
        opacity: 0.9;
        max-width: 600px;
        margin: 0 auto;
    }

    .facility-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-bottom: 50px;
    }

    .facility-card {
        background: var(--secondary);
        border-radius: 12px;
        box-shadow: var(--shadow);
        overflow: hidden;
        transition: var(--transition);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .facility-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }

    .facility-image-container {
        height: 150px;
        overflow: hidden;
        border-bottom: 3px solid var(--primary);
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #000;
    }

    .facility-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: var(--transition);
        opacity: 0.9;
    }

    .facility-image-container:hover .facility-image {
        opacity: 1;
    }

    .facility-card:hover .facility-image {
        transform: scale(1.05);
    }

    /* Lightbox Styles */
    .lightbox-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        padding-top: 60px;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.85);
        backdrop-filter: blur(5px);
    }

    .lightbox-content {
        margin: auto;
        display: block;
        max-width: 800px;
        max-height: 600px;
        width: 100%;
        height: auto;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.5);
        animation: zoomIn 0.3s ease;
    }

    @keyframes zoomIn {
        from {transform:scale(0.8); opacity:0} 
        to {transform:scale(1); opacity:1}
    }

    .lightbox-close {
        position: absolute;
        top: 20px;
        right: 35px;
        color: #f1f1f1;
        font-size: 40px;
        font-weight: bold;
        transition: 0.3s;
        cursor: pointer;
    }

    .lightbox-close:hover,
    .lightbox-close:focus {
        color: var(--primary);
        text-decoration: none;
        cursor: pointer;
    }

    .facility-content {
        padding: 20px;
        flex-grow: 1;
    }

    .facility-description {
        color: var(--text);
        line-height: 1.6;
    }

    .empty-state {
        text-align: center;
        padding: 50px;
        background: var(--secondary);
        border-radius: 12px;
        box-shadow: var(--shadow);
        color: var(--text-light);
    }

    .empty-state i {
        font-size: 3rem;
        color: var(--primary);
        margin-bottom: 15px;
        opacity: 0.5;
    }
</style>
@endsection

@section('content')
<div class="facilities-header">
    <div class="container animate-fade-in">
        <h1>Fasilitas Sekolah</h1>
        <p>Berbagai fasilitas pendukung yang tersedia di {{ $school->name ?? 'MTS/SMP' }} untuk menunjang kegiatan belajar mengajar.</p>
    </div>
</div>

<div class="container section">
    @if($facilities->isEmpty())
        <div class="empty-state animate-fade-in">
            <i class="fas fa-building"></i>
            <h3>Belum ada data fasilitas</h3>
            <p>Data fasilitas sekolah sedang dalam proses pembaruan.</p>
        </div>
    @else
        <div class="facility-grid">
            @foreach($facilities as $facility)
                <div class="facility-card animate-fade-in" style="animation-delay: {{ $loop->index * 0.1 }}s">
                    <div class="facility-image-container" onclick="openLightbox('{{ route('public.storage.view', ['path' => $facility->image]) }}')">
                        @if($facility->image)
                            <img src="{{ route('public.storage.view', ['path' => $facility->image]) }}" alt="Fasilitas" class="facility-image">
                        @else
                            <div style="width: 100%; height: 100%; background: var(--accent); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-image" style="font-size: 3rem; color: var(--text-light); opacity: 0.5;"></i>
                            </div>
                        @endif
                    </div>
                    <div class="facility-content">
                        <div class="facility-description">
                            {!! nl2br(e($facility->description)) !!}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Lightbox Modal -->
<div id="facilityLightbox" class="lightbox-modal" onclick="if(event.target === this || event.target.className === 'lightbox-close') closeLightbox()">
    <span class="lightbox-close">&times;</span>
    <img class="lightbox-content" id="lightboxImage">
</div>

@endsection

@section('scripts')
<script>
    function openLightbox(src) {
        if(!src || src.includes('undefined') || src.trim() === '') return;
        const lightbox = document.getElementById('facilityLightbox');
        const img = document.getElementById('lightboxImage');
        img.src = src;
        lightbox.style.display = 'flex';
    }

    function closeLightbox() {
        document.getElementById('facilityLightbox').style.display = 'none';
        document.getElementById('lightboxImage').src = '';
    }

    // Close on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            closeLightbox();
        }
    });
</script>
@endsection
