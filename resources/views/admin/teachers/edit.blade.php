@extends('admin.layouts.app')

@section('title', 'Edit Guru')
@section('page-title', 'Edit Data Guru')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Edit: {{ $teacher->name }}</h2>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.teachers.update', $teacher) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Nama Lengkap *</label>
                <input type="text" name="name" class="form-input" value="{{ old('name', $teacher->name) }}" required>
                @error('name')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label class="form-label">NIP</label>
                    <input type="text" name="nip" class="form-input" value="{{ old('nip', $teacher->nip) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Jabatan</label>
                    <input type="text" name="position" class="form-input" value="{{ old('position', $teacher->position) }}">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label class="form-label">Pendidikan</label>
                    <input type="text" name="education" class="form-input" value="{{ old('education', $teacher->education) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Urutan Tampil</label>
                    <input type="number" name="order" class="form-input" value="{{ old('order', $teacher->order) }}" min="0">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Biografi Singkat</label>
                <textarea name="bio" class="form-textarea" rows="4">{{ old('bio', $teacher->bio) }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Foto</label>
                <input type="file" name="photo" class="form-input" accept="image/*">
                @if($teacher->photo)
                    <img src="{{ asset('storage/' . $teacher->photo) }}" alt="Current Photo" class="preview-image">
                @endif
            </div>

            <div class="form-group">
                <div class="form-checkbox">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $teacher->is_active) ? 'checked' : '' }}>
                    <label for="is_active">Aktif (ditampilkan di website)</label>
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="{{ route('admin.teachers.index') }}" class="btn" style="background: var(--accent); color: var(--text);">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
