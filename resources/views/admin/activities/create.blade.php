@extends('admin.layouts.app')

@section('title', 'Tambah Kegiatan')
@section('page-title', 'Tambah Kegiatan Baru')

@section('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .ql-editor {
        min-height: 200px;
        background: var(--secondary);
        color: var(--text);
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Form Tambah Kegiatan</h2>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.activities.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label">Judul *</label>
                <input type="text" name="title" class="form-input" value="{{ old('title') }}" required>
                @error('title')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Kategori *</label>
                <select name="category" class="form-select" required>
                    <option value="news" {{ old('category') == 'news' ? 'selected' : '' }}>Berita</option>
                    <option value="event" {{ old('category') == 'event' ? 'selected' : '' }}>Acara</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Konten *</label>
                <input type="hidden" name="content" id="content" value="{{ old('content') }}">
                <div id="editor" style="background: var(--secondary);">{!! old('content') !!}</div>
                @error('content')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Gambar</label>
                <input type="file" name="image" class="form-input" accept="image/*">
            </div>

            <div class="form-group">
                <div class="form-checkbox">
                    <input type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}>
                    <label for="is_published">Publikasikan sekarang</label>
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <a href="{{ route('admin.activities.index') }}" class="btn" style="background: var(--accent); color: var(--text);">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
    var toolbarOptions = [
        ['bold', 'italic', 'underline', 'strike'],        
        ['blockquote', 'code-block'],
        [{ 'header': 1 }, { 'header': 2 }],               
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        [{ 'script': 'sub'}, { 'script': 'super' }],      
        [{ 'indent': '-1'}, { 'indent': '+1' }],          
        [{ 'direction': 'rtl' }],                         
        [{ 'size': ['small', false, 'large', 'huge'] }],  
        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
        [{ 'color': [] }, { 'background': [] }],          
        [{ 'align': [] }],                                
        ['clean'],                                         
        ['link', 'image', 'video']                         
    ];

    var quill = new Quill('#editor', {
        modules: {
            toolbar: toolbarOptions
        },
        theme: 'snow'
    });
    
    var form = document.querySelector('form');
    form.onsubmit = function() {
        document.querySelector('#content').value = quill.root.innerHTML;
    };
</script>
@endsection
