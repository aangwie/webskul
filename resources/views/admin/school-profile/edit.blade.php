@extends('admin.layouts.app')

@section('title', 'Edit Profil Sekolah')
@section('page-title', 'Edit Profil Sekolah')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Edit Data Profil Sekolah</h2>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.school-profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Nama Sekolah *</label>
                <input type="text" name="name" class="form-input" value="{{ old('name', $school->name) }}" required>
                @error('name')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Alamat</label>
                <textarea name="address" class="form-textarea" rows="3">{{ old('address', $school->address) }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Kota</label>
                <input type="text" name="city" class="form-input" value="{{ old('city', $school->city) }}" placeholder="Contoh: Sudimoro">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label class="form-label">Telepon</label>
                    <input type="text" name="phone" class="form-input" value="{{ old('phone', $school->phone) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email', $school->email) }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Visi</label>
                <textarea name="vision" class="form-textarea" rows="4">{{ old('vision', $school->vision) }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Misi</label>
                <textarea name="mission" class="form-textarea" rows="5">{{ old('mission', $school->mission) }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Sejarah</label>
                <textarea name="history" class="form-textarea" rows="5">{{ old('history', $school->history) }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Logo Sekolah</label>
                <input type="file" name="logo" class="form-input" accept="image/*">
                @if($school->logo)
                <img src="{{ asset('storage/' . $school->logo) }}" alt="Current Logo" class="preview-image">
                @endif
            </div>

            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="{{ route('admin.school-profile.index') }}" class="btn" style="background: var(--accent); color: var(--text);">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection