@extends('admin.layouts.app')

@section('title', 'Carousel')

@section('styles')
<style>
    .carousel-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 20px;
    }

    .carousel-card {
        background: var(--secondary);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: var(--shadow);
        transition: var(--transition);
        position: relative;
    }

    .carousel-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .carousel-thumb {
        width: 100%;
        height: 160px;
        object-fit: cover;
        display: block;
        background: var(--accent);
    }

    .carousel-thumb-placeholder {
        width: 100%;
        height: 160px;
        background: linear-gradient(135deg, var(--primary-light), var(--primary));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2.5rem;
    }

    .carousel-card-body {
        padding: 14px 16px;
    }

    .carousel-card-title {
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--text);
        margin-bottom: 6px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .carousel-card-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.78rem;
        color: var(--text-light);
        margin-bottom: 10px;
    }

    .carousel-card-actions {
        display: flex;
        gap: 8px;
    }

    .carousel-card-actions .btn {
        flex: 1;
        justify-content: center;
    }

    /* Upload Modal */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.55);
        z-index: 3000;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
        opacity: 0;
        pointer-events: none;
        transition: opacity .25s;
    }

    .modal-overlay.open {
        opacity: 1;
        pointer-events: all;
    }

    .modal-box {
        background: var(--secondary);
        border-radius: 18px;
        width: 100%;
        max-width: 480px;
        padding: 30px;
        box-shadow: 0 20px 60px rgba(0,0,0,.3);
        transform: translateY(30px);
        transition: transform .25s;
    }

    .modal-overlay.open .modal-box {
        transform: translateY(0);
    }

    .modal-title {
        font-size: 1.15rem;
        font-weight: 700;
        margin-bottom: 20px;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-title i {
        color: var(--primary);
    }

    .modal-footer {
        display: flex;
        gap: 10px;
        margin-top: 20px;
        justify-content: flex-end;
    }

    /* Image drop zone */
    .drop-zone {
        border: 2px dashed var(--accent);
        border-radius: 12px;
        padding: 30px;
        text-align: center;
        cursor: pointer;
        transition: var(--transition);
        margin-bottom: 16px;
        color: var(--text-light);
    }

    .drop-zone:hover, .drop-zone.drag-over {
        border-color: var(--primary);
        background: rgba(30,58,95,.04);
        color: var(--primary);
    }

    .drop-zone i {
        font-size: 2rem;
        margin-bottom: 8px;
        display: block;
    }

    .drop-zone .file-info {
        font-size: 0.8rem;
        margin-top: 6px;
        color: var(--text-light);
    }

    #addPreview, #editPreview {
        width: 300px;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
        margin: 10px auto 0;
        display: none;
        box-shadow: var(--shadow);
    }

    .order-badge {
        background: rgba(30,58,95,.1);
        color: var(--primary);
        padding: 2px 9px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-light);
    }

    .empty-state i {
        font-size: 4rem;
        opacity: .25;
        margin-bottom: 16px;
        display: block;
    }
</style>
@endsection

@section('content')
<div class="topbar">
    <div class="topbar-left">
        <button class="sidebar-toggle" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
        <h1><i class="fas fa-images" style="color:var(--primary);margin-right:8px;"></i> Carousel</h1>
    </div>
    <div class="topbar-right">
        <button class="btn btn-primary" id="btnTambah" onclick="openAddModal()">
            <i class="fas fa-plus"></i> Tambah Gambar
        </button>
    </div>
</div>

<div class="content">
    {{-- Alert --}}
    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif

    {{-- Info --}}
    <div class="alert" style="background:rgba(30,58,95,.07);border:1px solid rgba(30,58,95,.15);color:var(--text);margin-bottom:20px;">
        <i class="fas fa-info-circle" style="color:var(--primary)"></i>
        <span>Gambar yang diunggah akan otomatis dikompres ke format <strong>WebP</strong>. Ukuran file maksimal <strong>500 KB</strong>. Di halaman depan, 5 gambar ditampilkan sejajar di desktop dan 1 gambar di mobile.</span>
    </div>

    {{-- Hero Settings --}}
    <div class="card" style="margin-bottom: 30px; padding: 25px; border-radius: 16px; background: var(--secondary); box-shadow: var(--shadow);">
        <h3 style="margin-top:0; margin-bottom: 15px; color: var(--primary); display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-desktop"></i> Pengaturan Latar Belakang Hero Section
        </h3>
        <p style="color: var(--text-light); margin-bottom: 20px;">Berikan tampilan utama yang menarik. Ukuran gambar yang direkomendasikan adalah <strong>1920x800 pixel</strong>. Gambar dengan rasio yang berbeda akan dipotong (crop) otomatis agar sesuai dengan ukuran tersebut tanpa mengurangi kualitas, kemudian diubah ke format WebP.</p>
        
        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 300px;">
                <form action="{{ route('admin.carousel.hero.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="drop-zone" id="heroDropZone" onclick="document.getElementById('heroFileInput').click()" style="padding: 40px 20px; margin-bottom: 15px;">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>Klik atau seret gambar Hero ke sini</span>
                        <span class="file-info">Format: JPG, PNG, GIF, WebP — Maksimal 2 MB</span>
                    </div>
                    <input type="file" id="heroFileInput" name="hero_image" accept="image/*" style="display:none" onchange="previewFile(this,'heroPreviewActual','heroDropZone')" required>
                    <button type="submit" class="btn btn-primary" style="width: 100%;"><i class="fas fa-save"></i> Upload Gambar Hero</button>
                </form>
            </div>
            
            <div style="flex: 1; min-width: 300px; text-align: center;">
                <h4 style="margin-top: 0;">Preview Saat Ini:</h4>
                @if(\Illuminate\Support\Facades\Storage::disk('public')->exists('hero/hero_bg.webp'))
                    <img id="heroPreviewActual" src="{{ URL::signedRoute('public.storage.view', ['path' => 'hero/hero_bg.webp', 'v' => time()]) }}" style="width: 100%; max-width: 400px; height: 166px; object-fit: cover; border-radius: 8px; box-shadow: var(--shadow); margin-bottom: 15px; display: block; margin-left: auto; margin-right: auto;">
                    <form action="{{ route('admin.carousel.hero.destroy') }}" method="POST" onsubmit="return confirm('Hapus gambar Hero dan kembali ke default?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="width: 100%; max-width: 400px;"><i class="fas fa-trash"></i> Hapus & Gunakan Default</button>
                    </form>
                @else
                    <div id="heroPreviewDefault" style="width: 100%; max-width: 400px; height: 166px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); border-radius: 8px; margin: 0 auto; display: flex; align-items: center; justify-content: center; color: white;">
                        <span>Default (Gradiasi)</span>
                    </div>
                    <img id="heroPreviewActual" src="#" style="display:none; width: 100%; max-width: 400px; height: 166px; object-fit: cover; border-radius: 8px; box-shadow: var(--shadow); margin: 0 auto;">
                @endif
            </div>
        </div>
    </div>

    {{-- Grid --}}
    @if($images->isEmpty())
        <div class="card">
            <div class="empty-state">
                <i class="fas fa-images"></i>
                <p style="font-size:1.1rem;font-weight:600;margin-bottom:8px;">Belum ada gambar carousel</p>
                <p>Klik <strong>Tambah Gambar</strong> di pojok kanan atas untuk memulai.</p>
            </div>
        </div>
    @else
        <div class="carousel-grid">
            @foreach($images as $img)
            <div class="carousel-card">
                @if($img->image_path)
                    <img class="carousel-thumb"
                         src="{{ URL::signedRoute('public.storage.view', ['path' => $img->image_path]) }}"
                         alt="{{ $img->title ?? 'Carousel' }}"
                         loading="lazy">
                @else
                    <div class="carousel-thumb-placeholder">
                        <i class="fas fa-image"></i>
                    </div>
                @endif

                <div class="carousel-card-body">
                    <div class="carousel-card-title">{{ $img->title ?: '(Tanpa Judul)' }}</div>
                    <div class="carousel-card-meta">
                        <span class="order-badge"><i class="fas fa-sort"></i> Urutan: {{ $img->order }}</span>
                        <span>
                            @if($img->is_active)
                                <span class="badge badge-success"><i class="fas fa-eye"></i> Aktif</span>
                            @else
                                <span class="badge badge-danger"><i class="fas fa-eye-slash"></i> Nonaktif</span>
                            @endif
                        </span>
                    </div>
                    <div class="carousel-card-actions">
                        <button class="btn btn-warning btn-sm" onclick="openEditModal({{ $img->id }}, '{{ addslashes($img->title ?? '') }}', {{ $img->order }}, {{ $img->is_active ? 'true' : 'false' }})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <form action="{{ route('admin.carousel.destroy', $img) }}" method="POST"
                              onsubmit="return confirmDelete(event, this)">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

{{-- ===== ADD MODAL ===== --}}
<div class="modal-overlay" id="addModal" onclick="closeOnBackdrop(event,'addModal')">
    <div class="modal-box">
        <div class="modal-title"><i class="fas fa-plus-circle"></i> Tambah Gambar Carousel</div>
        <form action="{{ route('admin.carousel.store') }}" method="POST" enctype="multipart/form-data" id="addForm">
            @csrf
            <div class="form-group">
                <label class="form-label">Judul <span style="color:var(--text-light);font-weight:400;">(opsional)</span></label>
                <input type="text" name="title" class="form-input" placeholder="Contoh: Foto Kegiatan Upacara">
            </div>

            {{-- Drop zone --}}
            <div class="drop-zone" id="addDropZone" onclick="document.getElementById('addFileInput').click()">
                <i class="fas fa-cloud-upload-alt"></i>
                <span>Klik atau seret gambar ke sini</span>
                <span class="file-info">Format: JPG, PNG, GIF, WebP — Maks. <strong>500 KB</strong></span>
            </div>
            <input type="file" id="addFileInput" name="image" accept="image/*" style="display:none" onchange="previewFile(this,'addPreview','addDropZone')">
            <div style="text-align: center;">
                <img id="addPreview" src="#" alt="Preview" style="display:none; width: 300px; height: 150px; object-fit: cover; border-radius: 8px; margin: 10px auto 0; box-shadow: var(--shadow);">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:16px;">
                <div class="form-group">
                    <label class="form-label">Urutan</label>
                    <input type="number" name="order" class="form-input" value="0" min="0">
                </div>
                <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:4px;">
                    <label class="form-checkbox">
                        <input type="checkbox" name="is_active" value="1" checked>
                        <span>Aktifkan</span>
                    </label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn" style="border:2px solid var(--accent);background:transparent;color:var(--text);" onclick="closeModal('addModal')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ===== EDIT MODAL ===== --}}
<div class="modal-overlay" id="editModal" onclick="closeOnBackdrop(event,'editModal')">
    <div class="modal-box">
        <div class="modal-title"><i class="fas fa-edit"></i> Edit Gambar Carousel</div>
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Judul <span style="color:var(--text-light);font-weight:400;">(opsional)</span></label>
                <input type="text" name="title" id="editTitle" class="form-input" placeholder="Judull gambar">
            </div>

            <div class="drop-zone" id="editDropZone" onclick="document.getElementById('editFileInput').click()">
                <i class="fas fa-cloud-upload-alt"></i>
                <span>Klik untuk ganti gambar</span>
                <span class="file-info">Kosongkan jika tidak ingin mengganti — Maks. <strong>500 KB</strong></span>
            </div>
            <input type="file" id="editFileInput" name="image" accept="image/*" style="display:none" onchange="previewFile(this,'editPreview','editDropZone')">
            <div style="text-align: center;">
                <img id="editPreview" src="#" alt="Preview" style="display:none; width: 300px; height: 150px; object-fit: cover; border-radius: 8px; margin: 10px auto 0; box-shadow: var(--shadow);">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:16px;">
                <div class="form-group">
                    <label class="form-label">Urutan</label>
                    <input type="number" name="order" id="editOrder" class="form-input" value="0" min="0">
                </div>
                <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:4px;">
                    <label class="form-checkbox">
                        <input type="checkbox" name="is_active" id="editActive" value="1">
                        <span>Aktifkan</span>
                    </label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn" style="border:2px solid var(--accent);background:transparent;color:var(--text);" onclick="closeModal('editModal')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Perbarui</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openAddModal() {
        document.getElementById('addModal').classList.add('open');
    }

    function openEditModal(id, title, order, isActive) {
        const form = document.getElementById('editForm');
        form.action = `/admin/carousel/${id}`;
        document.getElementById('editTitle').value = title;
        document.getElementById('editOrder').value = order;
        document.getElementById('editActive').checked = isActive;
        document.getElementById('editPreview').style.display = 'none';
        document.getElementById('editModal').classList.add('open');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
    }

    function closeOnBackdrop(e, id) {
        if (e.target.id === id) closeModal(id);
    }

    function previewFile(input, previewId, dropZoneId) {
        const file = input.files[0];
        if (!file) return;

        // Client-side size check (500 KB = 512000 bytes)
        if (file.size > 512000) {
            Swal.fire({
                icon: 'error',
                title: 'File Terlalu Besar',
                text: `Ukuran file ${(file.size/1024).toFixed(1)} KB melebihi batas 500 KB.`,
                confirmButtonColor: 'var(--primary)'
            });
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const prev = document.getElementById(previewId);
            prev.src = e.target.result;
            prev.style.display = 'block';
            const dz = document.getElementById(dropZoneId);
            dz.style.borderColor = 'var(--success)';
            dz.querySelector('span').textContent = file.name;
        };
        reader.readAsDataURL(file);
    }

    // Drag & drop
    ['addDropZone', 'editDropZone', 'heroDropZone'].forEach(id => {
        const dz = document.getElementById(id);
        if (!dz) return;
        dz.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('drag-over'); });
        dz.addEventListener('dragleave', () => dz.classList.remove('drag-over'));
        dz.addEventListener('drop', e => {
            e.preventDefault();
            dz.classList.remove('drag-over');
            
            let inputId = 'addFileInput';
            let prevId = 'addPreview';
            if (id === 'editDropZone') {
                inputId = 'editFileInput';
                prevId = 'editPreview';
            } else if (id === 'heroDropZone') {
                inputId = 'heroFileInput';
                prevId = 'heroPreviewActual';
                const def = document.getElementById('heroPreviewDefault');
                if(def) def.style.display = 'none';
            }

            const inp = document.getElementById(inputId);
            inp.files = e.dataTransfer.files;
            previewFile(inp, prevId, id);
        });
    });

    function confirmDelete(e, form) {
        e.preventDefault();
        Swal.fire({
            title: 'Hapus Gambar?',
            text: 'Gambar ini akan dihapus permanen dan tidak dapat dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (result.isConfirmed) form.submit();
        });
        return false;
    }

    // Auto-close alert
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(a => {
            a.style.transition = 'opacity .5s';
            a.style.opacity = '0';
            setTimeout(() => a.remove(), 500);
        });
    }, 4000);
</script>
@endsection
