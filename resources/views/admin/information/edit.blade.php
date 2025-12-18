@extends('admin.layouts.app')

@section('title', 'Edit Informasi')
@section('page-title', 'Edit Informasi')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Edit: {{ $information->title }}</h2>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.information.update', $information) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Judul *</label>
                <input type="text" name="title" class="form-input" value="{{ old('title', $information->title) }}" required>
                @error('title')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Konten *</label>
                <textarea name="content" class="form-textarea" rows="6" required>{{ old('content', $information->content) }}</textarea>
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
