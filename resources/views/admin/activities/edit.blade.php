@extends('admin.layouts.app')

@section('title', 'Edit Kegiatan')
@section('page-title', 'Edit Kegiatan')

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Edit: {{ $activity->title }}</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.activities.update', $activity) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Judul *</label>
                    <input type="text" name="title" class="form-input" value="{{ old('title', $activity->title) }}"
                        required>
                    @error('title')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Kategori *</label>
                    <select name="category" class="form-select" required>
                        <option value="news" {{ old('category', $activity->category) == 'news' ? 'selected' : '' }}>Berita
                        </option>
                        <option value="event" {{ old('category', $activity->category) == 'event' ? 'selected' : '' }}>Acara
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Konten *</label>
                    <textarea name="content" class="form-textarea" rows="8"
                        required>{{ old('content', $activity->content) }}</textarea>
                    @error('content')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Gambar</label>
                    <input type="file" name="image" class="form-input" accept="image/*">
                    @if($activity->image)
                        @if(Str::startsWith($activity->image, 'data:'))
                            <img src="{{ $activity->image }}" alt="Current Image" class="preview-image" style="max-width: 200px;">
                        @else
                            @php
                                $imageUrl = route('admin.storage.view', ['path' => $activity->image]);
                            @endphp
                            <img src="{{ $imageUrl }}" alt="Current Image" class="preview-image" style="max-width: 200px;">
                        @endif
                    @endif
                </div>

                <div class="form-group">
                    <div class="form-checkbox">
                        <input type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published', $activity->is_published) ? 'checked' : '' }}>
                        <label for="is_published">Dipublikasikan</label>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 30px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.activities.index') }}" class="btn"
                        style="background: var(--accent); color: var(--text);">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection