@extends('admin.layouts.app')

@section('title', 'Riwayat Perubahan Siswa')
@section('page-title', 'Riwayat Perubahan Siswa')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<style>
    .dataTables_wrapper .dataTables_length select {
        min-width: 70px;
    }
    .dataTables_wrapper .dataTables_filter input {
        min-width: 200px;
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-history"></i> Riwayat Perubahan Kelas/Status Siswa</h2>
    </div>
    <div class="card-body">
        <!-- Filter Form (server-side pre-filter) -->
        <form action="{{ route('admin.students.history') }}" method="GET" style="margin-bottom: 25px; padding: 20px; background: var(--accent); border-radius: 10px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="academic_year" class="form-label">Filter Tahun Pelajaran</label>
                    <select name="academic_year" id="academic_year" class="form-select">
                        <option value="">Semua Tahun</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->year }}" {{ request('academic_year') == $year->year ? 'selected' : '' }}>
                                {{ $year->year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="school_class_id" class="form-label">Filter Kelas</label>
                    <select name="school_class_id" id="school_class_id" class="form-select">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('school_class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="height: 45px;">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
        </form>

        <div class="table-responsive">
            <table id="history-table" class="table table-striped">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Tahun Pelajaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($histories as $history)
                        <tr>
                            <td>{{ $history->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $history->student->name ?? '-' }}</td>
                            <td>{{ $history->schoolClass->name ?? '-' }}</td>
                            <td>{{ $history->academic_year ?? '-' }}</td>
                            <td>
                                @if($history->action === 'registered')
                                    <span class="badge badge-info">Mendaftar</span>
                                @elseif($history->action === 'moved')
                                    <span class="badge badge-warning">Dipindah</span>
                                @elseif($history->action === 'activated')
                                    <span class="badge badge-success">Diaktifkan</span>
                                @elseif($history->action === 'deactivated')
                                    <span class="badge badge-danger">Dinonaktifkan</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada riwayat perubahan.</td>
                        </tr>
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
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#history-table').DataTable({
            lengthMenu: [10, 20, 30, 40, 50],
            pageLength: 10,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 data",
                infoFiltered: "(difilter dari _MAX_ total data)",
                zeroRecords: "Tidak ada data yang cocok",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "&raquo;",
                    previous: "&laquo;"
                }
            }
        });
    });
</script>
@endsection