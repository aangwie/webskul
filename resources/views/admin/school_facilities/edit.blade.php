@extends('admin.layouts.app')

@section('title', 'Edit Fasilitas')
@section('page-title', 'Edit Fasilitas Sekolah')

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Edit Fasilitas</h2>
            <a href="{{ route('admin.school-facilities.index') }}" class="btn btn-outline" style="padding: 6px 12px;">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.school-facilities.update', $schoolFacility) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label class="form-label" for="image">Foto Fasilitas (Opsional)</label>
                    <input type="file" id="image" name="image" class="form-input" accept="image/*">
                    <small style="color: var(--text-light); margin-top: 5px; display: block;">Kosongkan jika tidak ingin mengubah foto. Gambar akan otomatis dikonversi ke format WebP.</small>
                    @if($schoolFacility->image)
                        <div style="margin-top: 10px;">
                            <img src="{{ URL::signedRoute('public.storage.view', ['path' => $schoolFacility->image]) }}" alt="Preview" style="max-height: 150px; border-radius: 8px;">
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Deskripsi Fasilitas</label>
                    <textarea id="description" name="description" class="form-textarea" required>{{ old('description', $schoolFacility->description) }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Perbarui
                </button>
            </form>
        </div>
    </div>
@endsection
