@extends('admin.layouts.app')

@section('title', 'Manajemen Siswa')
@section('page-title', 'Manajemen Siswa')

@section('content')
<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <h3>{{ $stats['total'] }}</h3>
            <p>Total Siswa Aktif</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-male"></i>
        </div>
        <div class="stat-info">
            <h3>{{ $stats['male'] }}</h3>
            <p>Siswa Laki-laki</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(232, 62, 140, 0.1); color: #e83e8c;">
            <i class="fas fa-female"></i>
        </div>
        <div class="stat-info">
            <h3>{{ $stats['female'] }}</h3>
            <p>Siswa Perempuan</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Daftar Siswa</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.students.import') }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel"></i> Import Excel
            </a>
            <a href="{{ route('admin.students.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Siswa
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Filter Form -->
        <form action="{{ route('admin.students.index') }}" method="GET" style="margin-bottom: 25px; padding: 20px; background: var(--accent); border-radius: 10px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; align-items: end;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="status" class="form-label">Filter Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="class_id" class="form-label">Filter Kelas</label>
                    <select name="class_id" id="class_id" class="form-select">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="gender" class="form-label">Filter Jenis Kelamin</label>
                    <select name="gender" id="gender" class="form-select">
                        <option value="">Semua</option>
                        <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="height: 45px;">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
        </form>

        @if(auth()->user()->isAdmin())
        <!-- Bulk Action Bar (Hidden by default) -->
        <div id="bulk-action-bar" style="display: none; background: var(--primary); color: white; padding: 15px 25px; border-radius: 10px; margin-bottom: 20px; align-items: center; justify-content: space-between; position: sticky; top: 100px; z-index: 90; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; gap: 15px;">
                <span id="selected-count" style="font-weight: 600;">0 Siswa Terpilih</span>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="button" class="btn btn-success btn-sm" onclick="bulkUpdateStatus('active')">
                    <i class="fas fa-check-circle"></i> Aktifkan
                </button>
                <button type="button" class="btn btn-danger btn-sm" onclick="bulkUpdateStatus('inactive')">
                    <i class="fas fa-times-circle"></i> Non-Aktifkan
                </button>
                <button type="button" class="btn btn-sm" style="background: rgba(255,255,255,0.2); color: white;" onclick="clearSelection()">
                    Batal
                </button>
            </div>
        </div>
        @endif

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        @if(auth()->user()->isAdmin())
                        <th style="width: 40px;">
                            <input type="checkbox" id="select-all" style="width: 18px; height: 18px; cursor: pointer;">
                        </th>
                        @endif
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>L/P</th>
                        <th>Tahun Masuk</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr>
                        @if(auth()->user()->isAdmin())
                        <td>
                            <input type="checkbox" class="student-checkbox" value="{{ $student->id }}" style="width: 18px; height: 18px; cursor: pointer;">
                        </td>
                        @endif
                        <td>{{ $student->nis ?? '-' }}</td>
                        <td><strong>{{ $student->name }}</strong></td>
                        <td>
                            @if($student->schoolClass)
                                <span class="badge" style="background: rgba(30, 58, 95, 0.1); color: var(--primary);">
                                    {{ $student->schoolClass->name }}
                                </span>
                            @else
                                <span class="badge badge-warning">Tidak ada kelas</span>
                            @endif
                        </td>
                        <td>
                            @if($student->gender == 'male')
                                <span style="color: var(--primary);"><i class="fas fa-male"></i> L</span>
                            @else
                                <span style="color: #e83e8c;"><i class="fas fa-female"></i> P</span>
                            @endif
                        </td>
                        <td>{{ $student->enrollment_year }}</td>
                        <td>
                            @if($student->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-danger">Non-Aktif</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-warning btn-sm" style="padding: 5px 8px;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data siswa ini?')" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" style="padding: 5px 8px;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isAdmin() ? '8' : '7' }}" style="text-align: center; padding: 20px; color: var(--text-light);">
                            Data siswa tidak ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 20px;">
            {{ $students->withQueryString()->links('pagination::simple-default') }}
        </div>
    </div>
</div>
@section('scripts')
@if(auth()->user()->isAdmin())
<script>
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.student-checkbox');
    const bulkBar = document.getElementById('bulk-action-bar');
    const selectedCountSpan = document.getElementById('selected-count');

    function updateBulkBar() {
        const checkedCount = document.querySelectorAll('.student-checkbox:checked').length;
        if (checkedCount > 0) {
            bulkBar.style.display = 'flex';
            selectedCountSpan.textContent = `${checkedCount} Siswa Terpilih`;
        } else {
            bulkBar.style.display = 'none';
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = selectAll.checked;
            });
            updateBulkBar();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            if (!this.checked) {
                selectAll.checked = false;
            } else if (document.querySelectorAll('.student-checkbox:checked').length === checkboxes.length) {
                selectAll.checked = true;
            }
            updateBulkBar();
        });
    });

    function clearSelection() {
        checkboxes.forEach(cb => cb.checked = false);
        if (selectAll) selectAll.checked = false;
        updateBulkBar();
    }

    function bulkUpdateStatus(status) {
        const selectedIds = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb => cb.value);
        
        if (selectedIds.length === 0) return;

        const statusLabel = status === 'active' ? 'Mengaktifkan' : 'Menonaktifkan';
        
        Swal.fire({
            title: 'Konfirmasi',
            text: `Apakah Anda yakin ingin ${statusLabel} ${selectedIds.length} siswa yang dipilih?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'var(--primary)',
            cancelButtonColor: 'var(--danger)',
            confirmButtonText: 'Ya, Lanjutkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Memproses...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch("{{ route('admin.students.bulk-status') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        student_ids: selectedIds,
                        status: status
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: data.message
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: data.message || 'Terjadi kesalahan saat memperbarui status.'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan sistem.'
                    });
                });
            }
        });
    }
</script>
@endif
@endsection
@endsection
