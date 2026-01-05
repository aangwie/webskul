@extends('admin.layouts.app')

@section('title', 'Modul Ajar')
@section('page-title', 'Modul Ajar')

@section('content')
<div class="card">
    <div class="card-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2><i class="fas fa-book-reader"></i> Daftar Modul Ajar</h2>
            <button type="button" class="btn btn-primary" onclick="openAddModal()">
                <i class="fas fa-upload"></i> Upload Modul
            </button>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger">
            <ul style="margin-left: 20px;">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Judul Modul</th>
                        <th>Mata Pelajaran</th>
                        <th>Kelas</th>
                        <th>Tahun Ajaran</th>
                        <th width="120" style="text-align: center;">File</th>
                        <th width="100" style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($modules as $index => $module)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $module->title }}</strong>
                            <div style="font-size: 0.85rem; color: var(--text-light); margin-top: 4px;">
                                {{ Str::limit($module->description, 50) }}
                            </div>
                        </td>
                        <td>{{ $module->subject->name }} ({{ $module->subject->code }})</td>
                        <td>
                            @if($module->schoolClass)
                            <span class="badge badge-warning">{{ $module->schoolClass->name }}</span>
                            @else
                            <span class="badge badge-secondary">Umum</span>
                            @endif
                        </td>
                        <td><span class="badge badge-success">{{ $module->academicYear->year }}</span></td>
                        <td style="text-align: center;">
                            <a href="{{ asset('storage/' . $module->file_path) }}" target="_blank" class="btn btn-sm btn-primary" title="Lihat PDF">
                                <i class="fas fa-file-pdf"></i> View
                            </a>
                        </td>
                        <td style="text-align: center;">
                            <form action="{{ route('admin.teaching-modules.destroy', $module->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus modul ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-light); font-style: italic; padding: 30px;">
                            Belum ada modul ajar yang diunggah.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Add --}}
<div id="addModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; width: 90%; max-width: 600px; border-radius: 12px; padding: 25px; box-shadow: 0 5px 20px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0;">Upload Modul Ajar</h3>
            <button onclick="closeAddModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <form action="{{ route('admin.teaching-modules.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Tahun Ajaran <span style="color: var(--danger);">*</span></label>
                <select name="academic_year_id" class="form-select" required>
                    <option value="">-- Pilih Tahun Ajaran --</option>
                    @foreach($academicYears as $year)
                    <option value="{{ $year->id }}" {{ $year->is_active ? 'selected' : '' }}>{{ $year->year }} {{ $year->is_active ? '(Aktif)' : '' }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <select name="subject_id" class="form-select" required>
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->code }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Kelas (Opsional)</label>
                <select name="school_class_id" class="form-select">
                    <option value="">-- Untuk Semua Kelas (Umum) --</option>
                    @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">File Modul (PDF, Max 5MB) <span style="color: var(--danger);">*</span></label>
                <div style="border: 2px dashed #ccc; padding: 20px; text-align: center; border-radius: 10px; cursor: pointer; position: relative;" onclick="document.getElementById('file-upload').click()">
                    <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: var(--primary);"></i>
                    <p style="margin-top: 10px; color: var(--text-light);">Klik untuk memilih file PDF</p>
                    <input type="file" name="file" id="file-upload" accept=".pdf" style="display: none;" onchange="updateFileName(this)" required>
                    <div id="file-name" style="margin-top: 10px; font-weight: bold; color: var(--success);"></div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi Singkat</label>
                <textarea name="description" class="form-textarea" style="min-height: 100px;" placeholder="Jelaskan isi singkat modul ini..."></textarea>
            </div>

            <div style="text-align: right; margin-top: 20px;">
                <button type="button" onclick="closeAddModal()" class="btn btn-secondary" style="margin-right: 10px;">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Upload</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById('addModal').style.display = 'flex';
    }

    function closeAddModal() {
        document.getElementById('addModal').style.display = 'none';
        document.getElementById('file-name').textContent = '';
        document.getElementById('file-upload').value = '';
    }

    function updateFileName(input) {
        if (input.files && input.files[0]) {
            document.getElementById('file-name').textContent = 'File terpilih: ' + input.files[0].name;

            // Check file size (5MB = 5 * 1024 * 1024 bytes)
            if (input.files[0].size > 5242880) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Terlalu Besar',
                    text: 'Ukuran file maksimal adalah 5MB',
                });
                input.value = '';
                document.getElementById('file-name').textContent = '';
            }
        }
    }

    // Close modal slightly clicking outside
    window.onclick = function(event) {
        if (event.target == document.getElementById('addModal')) {
            closeAddModal();
        }
    }
</script>
@endsection