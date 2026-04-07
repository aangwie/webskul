@extends('admin.layouts.app')

@section('title', 'Tambah Fasilitas')
@section('page-title', 'Tambah Fasilitas Sekolah')

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Tambah Fasilitas Baru</h2>
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

            <form action="{{ route('admin.school-facilities.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="image">Foto Fasilitas (JPG/PNG/WEBP)</label>
                    <input type="file" id="image" name="image" class="form-input" required accept="image/*">
                    <small style="color: var(--text-light); margin-top: 5px; display: block;">Gambar akan otomatis dikonversi ke format WebP.</small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Deskripsi Fasilitas</label>
                    <textarea id="description" name="description" class="form-textarea" required
                        placeholder="Contoh: Perpustakaan dengan koleksi buku lengkap..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </form>
        </div>
    </div>
@endsection
