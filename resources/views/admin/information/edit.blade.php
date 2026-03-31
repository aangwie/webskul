@extends('admin.layouts.app')

@section('title', 'Edit Informasi')
@section('page-title', 'Edit Informasi')

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
        <h2>Edit: {{ $information->title }}</h2>
    </div>
    <div class="card-body">
        <form id="information-form" action="{{ route('admin.information.update', $information) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Judul *</label>
                <input type="text" name="title" class="form-input" value="{{ old('title', $information->title) }}" required>
                @error('title')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Konten *</label>
                <input type="hidden" name="content" id="content" value="{{ old('content', $information->content) }}">
                <div id="editor" style="background: var(--secondary);">{!! old('content', $information->content) !!}</div>
                @error('content')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <div class="form-checkbox">
                    <input type="checkbox" name="is_important" id="is_important" value="1" {{ old('is_important', $information->is_important) ? 'checked' : '' }}>
                    <label for="is_important">Tandai sebagai informasi penting</label>
                </div>
            </div>

            <div class="form-group">
                <div class="form-checkbox">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $information->is_active) ? 'checked' : '' }}>
                    <label for="is_active">Aktif (ditampilkan di website)</label>
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="{{ route('admin.information.index') }}" class="btn" style="background: var(--accent); color: var(--text);">
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
    
    var form = document.getElementById('information-form');
    form.onsubmit = function() {
        document.querySelector('#content').value = quill.root.innerHTML;
    };
</script>
@endsection
