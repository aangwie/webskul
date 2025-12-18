@extends('admin.layouts.app')

@section('title', 'Tambah Informasi')
@section('page-title', 'Tambah Informasi Baru')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Form Tambah Informasi</h2>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.information.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">Judul *</label>
                <input type="text" name="title" class="form-input" value="{{ old('title') }}" required>
                @error('title')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Konten *</label>
                <textarea name="content" class="form-textarea" rows="6" required>{{ old('content') }}</textarea>
                @error('content')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <div class="form-checkbox">
                    <input type="checkbox" name="is_important" id="is_important" value="1" {{ old('is_important') ? 'checked' : '' }}>
                    <label for="is_important">Tandai sebagai informasi penting</label>
                </div>
            </div>

            <div class="form-group">
                <div class="form-checkbox">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label for="is_active">Aktif (ditampilkan di website)</label>
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <a href="{{ route('admin.information.index') }}" class="btn" style="background: var(--accent); color: var(--text);">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
