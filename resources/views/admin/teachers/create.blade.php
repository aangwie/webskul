@extends('admin.layouts.app')

@section('title', 'Tambah Guru')
@section('page-title', 'Tambah Guru Baru')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Form Tambah Guru</h2>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.teachers.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label">Nama Lengkap *</label>
                <input type="text" name="name" class="form-input" value="{{ old('name') }}" required>
                @error('name')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label class="form-label">NIP</label>
                    <input type="text" name="nip" class="form-input" value="{{ old('nip') }}" maxlength="18" pattern="[0-9-]+" placeholder="Hanya angka dan tanda hubung (-) yang diperbolehkan" oninput="this.value = this.value.replace(/[^0-9-]/g, '');">
                    @error('nip')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Jabatan</label>
                    <input type="text" name="position" class="form-input" value="{{ old('position') }}" placeholder="Contoh: Guru Matematika">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label class="form-label">Pendidikan</label>
                    <input type="text" name="education" class="form-input" value="{{ old('education') }}" placeholder="Contoh: S1 Pendidikan">
                </div>
                <div class="form-group">
                    <label class="form-label">Urutan Tampil</label>
                    <input type="number" name="order" class="form-input" value="{{ old('order', 0) }}" min="0">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Biografi Singkat</label>
                <textarea name="bio" class="form-textarea" rows="4">{{ old('bio') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Foto</label>
                <input type="file" name="photo" class="form-input" accept="image/*">
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
                <a href="{{ route('admin.teachers.index') }}" class="btn" style="background: var(--accent); color: var(--text);">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection