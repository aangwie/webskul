@extends('admin.layouts.app')

@section('title', 'Tambah Siswa')
@section('page-title', 'Tambah Siswa')

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h2>Form Tambah Siswa</h2>
        <a href="{{ route('admin.students.index') }}" class="btn btn-warning btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.students.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="name" class="form-label">Nama Lengkap <span style="color: red">*</span></label>
                <input type="text" name="name" id="name" class="form-input" placeholder="Nama Lengkap Siswa" value="{{ old('name') }}" required>
                @error('name')
                    <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="nis" class="form-label">NIS (Nomor Induk Siswa)</label>
                <input type="text" name="nis" id="nis" class="form-input" placeholder="Contoh: 12345" value="{{ old('nis') }}">
                @error('nis')
                    <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="school_class_id" class="form-label">Kelas <span style="color: red">*</span></label>
                <select name="school_class_id" id="school_class_id" class="form-select" required>
                    <option value="">Pilih Kelas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ old('school_class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }} ({{ $class->academic_year }})
                        </option>
                    @endforeach
                </select>
                @error('school_class_id')
                    <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="gender" class="form-label">Jenis Kelamin <span style="color: red">*</span></label>
                <select name="gender" id="gender" class="form-select" required>
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('gender')
                    <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="enrollment_year" class="form-label">Tahun Masuk <span style="color: red">*</span></label>
                <input type="number" name="enrollment_year" id="enrollment_year" class="form-input" min="2000" max="{{ date('Y') + 1 }}" value="{{ old('enrollment_year', $currentYear) }}" required>
                @error('enrollment_year')
                    <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                    <span>Siswa Aktif</span>
                </label>
            </div>
            
            <div style="margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Data Siswa
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
