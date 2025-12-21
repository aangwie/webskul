@extends('admin.layouts.app')

@section('title', 'Tahun Pelajaran')
@section('page-title', 'Tahun Pelajaran')

@section('content')
<div style="display: grid; grid-template-columns: 1fr 300px; gap: 25px;">
    <!-- List of Academic Years -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-calendar-alt"></i> Daftar Tahun Pelajaran</h2>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Tahun</th>
                            <th>Status</th>
                            <th>Dibuat Pada</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($academicYears as $year)
                        <tr>
                            <td><strong>{{ $year->year }}</strong></td>
                            <td>
                                @if($year->is_active)
                                <span class="badge badge-success">Aktif</span>
                                @else
                                <span class="badge badge-danger">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>{{ $year->created_at->format('d M Y') }}</td>
                            <td>
                                <div style="display: flex; gap: 10px;">
                                    <form action="{{ route('admin.academic-years.update', $year) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="is_active" value="{{ $year->is_active ? 0 : 1 }}">
                                        <button type="submit" class="btn btn-sm {{ $year->is_active ? 'btn-danger' : 'btn-success' }}">
                                            <i class="fas {{ $year->is_active ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
                                            {{ $year->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.academic-years.destroy', $year) }}" method="POST" onsubmit="return confirm('Hapus tahun pelajaran ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 40px;">
                                <div style="color: var(--text-light); font-size: 3rem; margin-bottom: 20px; opacity: 0.3;">
                                    <i class="fas fa-calendar-times"></i>
                                </div>
                                <p>Belum ada data tahun pelajaran.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add New Academic Year -->
    <div class="card" style="height: fit-content;">
        <div class="card-header">
            <h2><i class="fas fa-plus"></i> Tambah Baru</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.academic-years.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Tahun Pelajaran</label>
                    <input type="text" name="year" class="form-input" placeholder="Contoh: 2025/2026" required>
                    <small style="color: var(--text-light);">Format: 2025/2026</small>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection