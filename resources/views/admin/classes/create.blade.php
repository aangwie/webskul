@extends('admin.layouts.app')

@section('title', 'Tambah Kelas')
@section('page-title', 'Tambah Kelas')

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h2>Form Tambah Kelas</h2>
        <a href="{{ route('admin.classes.index') }}" class="btn btn-warning btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.classes.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="name" class="form-label">Nama Kelas <span style="color: red">*</span></label>
                <input type="text" name="name" id="name" class="form-input" placeholder="Contoh: 7A, 8B" value="{{ old('name') }}" required>
                @error('name')
                    <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="grade" class="form-label">Tingkat <span style="color: red">*</span></label>
                <select name="grade" id="grade" class="form-select" required>
                    <option value="">Pilih Tingkat</option>
                    <option value="7" {{ old('grade') == '7' ? 'selected' : '' }}>Kelas 7</option>
                    <option value="8" {{ old('grade') == '8' ? 'selected' : '' }}>Kelas 8</option>
                    <option value="9" {{ old('grade') == '9' ? 'selected' : '' }}>Kelas 9</option>
                </select>
                @error('grade')
                    <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="academic_year" class="form-label">Tahun Ajaran <span style="color: red">*</span></label>
                <input type="text" name="academic_year" id="academic_year" class="form-input" placeholder="Contoh: 2023/2024" value="{{ old('academic_year', date('Y') . '/' . (date('Y') + 1)) }}" required>
                @error('academic_year')
                    <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                    <span>Kelas Aktif</span>
                </label>
            </div>
            
            <div style="margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Kelas
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
