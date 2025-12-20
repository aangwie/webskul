@extends('admin.layouts.app')

@section('title', 'Edit Media Sosial')
@section('page-title', 'Edit Media Sosial')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Edit Media Sosial: {{ $socialMedia->platform }}</h2>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.social-media.update', $socialMedia) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Platform *</label>
                <input type="text" name="platform" class="form-input" value="{{ old('platform', $socialMedia->platform) }}" placeholder="Contoh: Facebook, Instagram, X" required>
                @error('platform')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">FontAwesome Icon *</label>
                <input type="text" name="icon" class="form-input" value="{{ old('icon', $socialMedia->icon) }}" placeholder="Contoh: fab fa-facebook" required>
                <small style="color: var(--text-light);">Cari icon di <a href="https://fontawesome.com/icons?d=gallery&m=free" target="_blank">FontAwesome</a></small>
                @error('icon')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">URL *</label>
                <input type="url" name="url" class="form-input" value="{{ old('url', $socialMedia->url) }}" placeholder="https://..." required>
                @error('url')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Urutan</label>
                <input type="number" name="order" class="form-input" value="{{ old('order', $socialMedia->order) }}" min="0">
            </div>

            <div class="form-group">
                <div class="form-checkbox">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $socialMedia->is_active) ? 'checked' : '' }}>
                    <label for="is_active">Aktif (ditampilkan di website)</label>
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="{{ route('admin.social-media.index') }}" class="btn" style="background: var(--accent); color: var(--text);">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection