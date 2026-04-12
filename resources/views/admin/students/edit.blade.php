@extends('admin.layouts.app')

@section('title', 'Edit Siswa')
@section('page-title', 'Edit Siswa')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h2>Form Edit Siswa</h2>
        <a href="{{ route('admin.students.index') }}" class="btn btn-warning btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="name" class="form-label">Nama Lengkap <span style="color: red">*</span></label>
                <input type="text" name="name" id="name" class="form-input" placeholder="Nama Lengkap Siswa" value="{{ old('name', $student->name) }}" required>
                @error('name')
                    <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="nis" class="form-label">NIS (Nomor Induk Siswa)</label>
                <input type="text" name="nis" id="nis" class="form-input" placeholder="Contoh: 12345" value="{{ old('nis', $student->nis) }}">
                @error('nis')
                    <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="nisn" class="form-label">NISN (Siswa kelulusan wajib isi) <span style="color: red">*</span></label>
                <input type="text" name="nisn" id="nisn" class="form-input" placeholder="Contoh: 0012345678" value="{{ old('nisn', $student->nisn) }}">
                @error('nisn')
                    <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="tanggal_lahir" class="form-label">Tanggal Lahir (Siswa kelulusan wajib isi) <span style="color: red">*</span></label>
                <input type="text" name="tanggal_lahir" id="tanggal_lahir" class="form-input" placeholder="Pilih Tanggal Lahir" autocomplete="off" value="{{ old('tanggal_lahir', $student->tanggal_lahir ? date('d/m/Y', strtotime($student->tanggal_lahir)) : '') }}">
                <small style="color: var(--text-light); font-size: 0.8rem; margin-top: 5px; display: block;">Format: Hari/Bulan/Tahun (Contoh: 17/08/2005)</small>
                @error('tanggal_lahir')
                    <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="school_class_id" class="form-label">Kelas <span style="color: red">*</span></label>
                <select name="school_class_id" id="school_class_id" class="form-select" required>
                    <option value="">Pilih Kelas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ old('school_class_id', $student->school_class_id) == $class->id ? 'selected' : '' }}>
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
                    <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('gender')
                    <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="enrollment_year" class="form-label">Tahun Masuk <span style="color: red">*</span></label>
                <input type="number" name="enrollment_year" id="enrollment_year" class="form-input" min="2000" max="{{ date('Y') + 1 }}" value="{{ old('enrollment_year', $student->enrollment_year) }}" required>
                @error('enrollment_year')
                    <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label class="form-label">Status Siswa <span style="color: red">*</span></label>
                <div style="display: flex; gap: 20px; align-items: center;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="radio" name="is_active" value="1" {{ old('is_active', $student->is_active) == '1' ? 'checked' : '' }} style="accent-color: var(--primary);">
                        <span>Aktif</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="radio" name="is_active" value="0" {{ old('is_active', $student->is_active) == '0' ? 'checked' : '' }} style="accent-color: var(--primary);">
                        <span>Tidak Aktif</span>
                    </label>
                </div>
            </div>

            <!-- Graduation / Ijazah Fields (Hidden if Is Active is checked) -->
            <div id="kelulusan_fields" style="display: {{ old('is_active', $student->is_active) ? 'none' : 'block' }}; padding: 15px; border: 1px solid var(--accent); border-radius: 8px; margin-bottom: 20px;">
                <div class="form-group">
                    <label for="status_lulus" class="form-label">Status Kelulusan</label>
                    <select name="status_lulus" id="status_lulus" class="form-select">
                        <option value="">Pilih Status</option>
                        <option value="lulus" {{ old('status_lulus', $student->status_lulus) == 'lulus' ? 'selected' : '' }}>Lulus</option>
                        <option value="tidak_lulus" {{ old('status_lulus', $student->status_lulus) == 'tidak_lulus' ? 'selected' : '' }}>Tidak Lulus / Pindah</option>
                    </select>
                    @error('status_lulus')
                        <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group" id="ijazah_upload_group" style="display: {{ old('status_lulus', $student->status_lulus) == 'lulus' ? 'block' : 'none' }};">
                    <label for="ijazah_file" class="form-label">Upload Ijazah Digital (PDF, max 500kb)</label>
                    @if($student->ijazah_file)
                        <div style="margin-bottom: 10px;">
                            <a href="{{ URL::signedRoute('public.storage.view', ['path' => $student->ijazah_file]) }}" target="_blank" class="btn btn-sm btn-outline">
                                <i class="fas fa-file-pdf"></i> Lihat File Ijazah Saat Ini
                            </a>
                        </div>
                    @endif
                    <input type="file" name="ijazah_file" id="ijazah_file" class="form-input" accept="application/pdf">
                    <small>Upload baru akan menimpa file lama.</small>
                    @error('ijazah_file')
                        <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            <div style="margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Perbarui Data Siswa
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const activeRadio = document.querySelector('input[name="is_active"][value="1"]');
        const inactiveRadio = document.querySelector('input[name="is_active"][value="0"]');
        const kelulusanFields = document.getElementById('kelulusan_fields');
        const statusLulusSelect = document.getElementById('status_lulus');
        const ijazahUploadGroup = document.getElementById('ijazah_upload_group');

        function toggleKelulusan() {
            if (activeRadio.checked) {
                kelulusanFields.style.display = 'none';
            } else {
                kelulusanFields.style.display = 'block';
            }
        }

        function toggleIjazah() {
            if (statusLulusSelect.value === 'lulus') {
                ijazahUploadGroup.style.display = 'block';
            } else {
                ijazahUploadGroup.style.display = 'none';
            }
        }

        activeRadio.addEventListener('change', toggleKelulusan);
        inactiveRadio.addEventListener('change', toggleKelulusan);
        statusLulusSelect.addEventListener('change', toggleIjazah);
        
        // Initial setup based on current values
        toggleKelulusan();
        toggleIjazah();

        // Initialize Flatpickr for Tanggal Lahir
        flatpickr("#tanggal_lahir", {
            dateFormat: "d/m/Y",
            allowInput: true,
            maxDate: "today",
            theme: "light"
        });
    });
</script>
@endsection
