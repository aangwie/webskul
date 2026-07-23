@extends('admin.layouts.app')

@section('title', 'Manajemen Siswa')
@section('page-title', 'Manajemen Siswa')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    .dataTables_wrapper .dataTables_length select {
        padding: 6px 24px 6px 12px;
        border-radius: 8px;
        border: 2px solid var(--accent);
        background: var(--secondary);
        font-family: 'Inter', sans-serif;
        font-size: 0.85rem;
    }
    .dataTables_wrapper .dataTables_filter input {
        padding: 8px 14px;
        border-radius: 8px;
        border: 2px solid var(--accent);
        background: var(--secondary);
        font-family: 'Inter', sans-serif;
        font-size: 0.85rem;
        margin-left: 8px;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: var(--primary);
        outline: none;
    }
    .dataTables_wrapper .dataTables_info {
        font-size: 0.85rem;
        color: var(--text-light);
        padding-top: 15px;
    }
    .dataTables_wrapper .dataTables_paginate {
        padding-top: 15px;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 8px !important;
        padding: 6px 14px !important;
        font-size: 0.85rem !important;
        margin: 0 2px !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--primary) !important;
        color: var(--secondary) !important;
        border: none !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: rgba(30, 58, 95, 0.1) !important;
    }
    #students-table_wrapper {
        margin-top: 0;
    }
</style>
@endsection

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
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <button type="button" class="btn btn-success btn-sm" onclick="bulkUpdateStatus('active')">
                    <i class="fas fa-check-circle"></i> Aktifkan
                </button>
                <button type="button" class="btn btn-danger btn-sm" onclick="bulkUpdateStatus('inactive')">
                    <i class="fas fa-times-circle"></i> Non-Aktifkan
                </button>
                <button type="button" class="btn btn-danger btn-sm" style="background: #dc3545; border-color: #dc3545;" onclick="bulkDelete()">
                    <i class="fas fa-trash"></i> Hapus
                </button>
                <div style="display: flex; gap: 5px; align-items: center;">
                    <select id="bulk-class-select" class="form-select" style="width: auto; min-width: 150px; padding: 6px 10px; font-size: 0.85rem; border-radius: 8px; background: white; color: #333; border: 1px solid #ddd;">
                        <option value="">Pindah ke Kelas...</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-sm" style="background: rgba(255,255,255,0.2); color: white;" onclick="bulkMoveClass()">
                        <i class="fas fa-arrows-alt"></i> Pindahkan
                    </button>
                </div>
                <button type="button" class="btn btn-sm" style="background: rgba(255,255,255,0.3); color: white;" onclick="clearSelection()">
                    Batal
                </button>
            </div>
        </div>
        @endif

        <div class="table-responsive">
            <table id="students-table" class="table">
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
                        <th>Tanggal Lahir</th>
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
                        <td>{{ $student->tanggal_lahir ? \Carbon\Carbon::parse($student->tanggal_lahir)->format('d/m/Y') : '-' }}</td>
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
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#students-table').DataTable({
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            zeroRecords: "Data tidak ditemukan",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "›",
                previous: "‹"
            },
        },
        lengthMenu: [[10, 20, 30, 40, 50, -1], [10, 20, 30, 40, 50, "Semua"]],
        pageLength: 10,
        order: [],
        columnDefs: [
            @if(auth()->user()->isAdmin())
            { orderable: false, targets: 0 },
            { orderable: false, targets: 7 },
            @else
            { orderable: false, targets: 6 },
            @endif
        ],
        drawCallback: function() {
            // Rebind checkbox events after DataTables redraws
            if (typeof bindCheckboxEvents === 'function') {
                bindCheckboxEvents();
            }
        }
    });
});

@if(auth()->user()->isAdmin())
function bindCheckboxEvents() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.student-checkbox');
    const bulkBar = document.getElementById('bulk-action-bar');
    const selectedCountSpan = document.getElementById('selected-count');

    function updateBulkBar() {
        const checkedCount = document.querySelectorAll('.student-checkbox:checked').length;
        if (checkedCount > 0) {
            bulkBar.style.display = 'flex';
            selectedCountSpan.textContent = checkedCount + ' Siswa Terpilih';
        } else {
            bulkBar.style.display = 'none';
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('.student-checkbox').forEach(cb => {
                cb.checked = selectAll.checked;
            });
            updateBulkBar();
        });
    }

    document.querySelectorAll('.student-checkbox').forEach(cb => {
        cb.removeEventListener('change', updateBulkBar);
        cb.addEventListener('change', function() {
            if (!this.checked && selectAll) {
                selectAll.checked = false;
            } else if (selectAll && document.querySelectorAll('.student-checkbox:checked').length === document.querySelectorAll('.student-checkbox').length) {
                selectAll.checked = true;
            }
            updateBulkBar();
        });
    });
}

// Initial bind
bindCheckboxEvents();

function clearSelection() {
    document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = false);
    const selectAll = document.getElementById('select-all');
    if (selectAll) selectAll.checked = false;
    const bulkBar = document.getElementById('bulk-action-bar');
    const selectedCountSpan = document.getElementById('selected-count');
    if (bulkBar) bulkBar.style.display = 'none';
    if (selectedCountSpan) selectedCountSpan.textContent = '0 Siswa Terpilih';
}

function bulkUpdateStatus(status) {
    const selectedIds = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb => cb.value);
    if (selectedIds.length === 0) return;

    const statusLabel = status === 'active' ? 'Mengaktifkan' : 'Menonaktifkan';

    Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin ingin ' + statusLabel + ' ' + selectedIds.length + ' siswa yang dipilih?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: 'var(--primary)',
        cancelButtonColor: 'var(--danger)',
        confirmButtonText: 'Ya, Lanjutkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch("{{ route('admin.students.bulk-status') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ student_ids: selectedIds, status: status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Terjadi kesalahan.' });
                }
            })
            .catch(error => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan sistem.' });
            });
        }
    });
}

function bulkMoveClass() {
    const selectedIds = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb => cb.value);
    const classId = document.getElementById('bulk-class-select').value;

    if (selectedIds.length === 0) return;
    if (!classId) {
        Swal.fire({ icon: 'warning', title: 'Pilih Kelas', text: 'Silakan pilih kelas tujuan terlebih dahulu.' });
        return;
    }

    Swal.fire({
        title: 'Konfirmasi',
        text: 'Pindahkan ' + selectedIds.length + ' siswa ke kelas yang dipilih?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: 'var(--primary)',
        cancelButtonColor: 'var(--danger)',
        confirmButtonText: 'Ya, Pindahkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            fetch("{{ route('admin.students.bulk-move-class') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ student_ids: selectedIds, class_id: classId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message }).then(() => window.location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Terjadi kesalahan.' });
                }
            })
            .catch(error => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan sistem.' });
            });
        }
    });
}

function bulkDelete() {
    const selectedIds = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb => cb.value);
    if (selectedIds.length === 0) return;

    Swal.fire({
        title: 'Hapus Data Siswa',
        text: 'Apakah Anda yakin ingin menghapus ' + selectedIds.length + ' siswa yang dipilih? Data yang sudah dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            fetch("{{ route('admin.students.bulk-destroy') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ student_ids: selectedIds })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message }).then(() => window.location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Terjadi kesalahan.' });
                }
            })
            .catch(error => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan sistem.' });
            });
        }
    });
}
@endif
</script>
@endsection