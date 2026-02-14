@extends('admin.layouts.app')

@section('title', 'Manajemen Penggunaan Dana Komite')
@section('page-title', 'Penggunaan Dana Komite')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <div>
        <p style="color: var(--text-light);">Kelola data pengeluaran dan penggunaan dana komite sekolah.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('admin.committee.expenditures.report') }}" class="btn btn-secondary">
            <i class="fas fa-file-alt"></i> Laporan Penggunaan
        </a>
        <a href="{{ route('admin.committee.expenditures.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Catat Penggunaan
        </a>
    </div>
</div>

<!-- Academic Year Filter -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body">
        <form action="{{ route('admin.committee.expenditures.index') }}" method="GET" style="display: flex; align-items: center; gap: 1rem;">
            <label style="white-space: nowrap; font-weight: 500;">Pilih Tahun Pelajaran:</label>
            <select name="academic_year_id" class="form-input" style="width: auto; flex-grow: 1;" onchange="this.form.submit()">
                @foreach($academicYears as $year)
                <option value="{{ $year->id }}" {{ ($selectedYear && $selectedYear->id == $year->id) ? 'selected' : '' }}>
                    {{ $year->year }} {{ $year->is_active ? '' : '' }}
                </option>
                @endforeach
            </select>
            <noscript><button type="submit" class="btn btn-primary">Filter</button></noscript>
        </form>
    </div>
</div>

<!-- Program Budget Summary -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <h2><i class="fas fa-chart-pie"></i> Ringkasan Penggunaan Dana Tahun {{ $selectedYear ? $selectedYear->year : '' }}</h2>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Nama Program</th>
                    <th style="text-align: right;">Kebutuhan Biaya (Rencana)</th>
                    <th style="text-align: right;">Realisasi Penggunaan</th>
                    <th style="text-align: right;">Sisa Anggaran</th>
                    <th style="text-align: center;">Persentase</th>
                </tr>
            </thead>
            <tbody>
                @forelse($programs as $program)
                <tr>
                    <td style="font-weight: 500;">{{ $program->name }}</td>
                    <td style="text-align: right;">Rp {{ number_format($program->total_budget, 0, ',', '.') }}</td>
                    <td style="text-align: right; color: var(--danger);">Rp {{ number_format($program->total_used, 0, ',', '.') }}</td>
                    <td style="text-align: right; color: {{ $program->balance < 0 ? 'var(--danger)' : 'var(--success)' }}">
                        Rp {{ number_format($program->balance, 0, ',', '.') }}
                    </td>
                    <td style="text-align: center; width: 150px;">
                        @php
                        $percentage = $program->total_budget > 0 ? ($program->total_used / $program->total_budget) * 100 : 0;
                        $percentage = min($percentage, 100);
                        @endphp
                        <div style="background: #e9ecef; border-radius: 4px; height: 8px; width: 100%; overflow: hidden;">
                            <div style="background: {{ $percentage > 90 ? 'var(--danger)' : 'var(--primary)' }}; height: 100%; width: {{ $percentage }}%;"></div>
                        </div>
                        <span style="font-size: 0.8rem; color: var(--text-light);">{{ number_format($percentage, 1) }}%</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px; color: var(--text-light);">
                        Tidak ada program ditemukan untuk tahun pelajaran ini.
                    </td>
                </tr>
                @endforelse
                @if(count($programs) > 0)
                <tr style="background-color: #f8f9fa; font-weight: 600;">
                    <td>Total Keseluruhan</td>
                    <td style="text-align: right;">Rp {{ number_format($programs->sum('total_budget'), 0, ',', '.') }}</td>
                    <td style="text-align: right; color: var(--danger);">Rp {{ number_format($programs->sum('total_used'), 0, ',', '.') }}</td>
                    <td style="text-align: right;">Rp {{ number_format($programs->sum('balance'), 0, ',', '.') }}</td>
                    <td></td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<div class="card-header">
    <h2><i class="fas fa-list"></i> Daftar Penggunaan Dana</h2>
</div>
<div class="card-body">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor Pengeluaran</th>
                    <th>Tanggal</th>
                    <th>Deskripsi Penggunaan</th>
                    <th style="text-align: right;">Nominal</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenditures as $index => $exp)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><span class="badge badge-info" style="font-family: monospace;">{{ $exp->expenditure_number }}</span></td>
                    <td>{{ $exp->date->format('d/m/Y') }}</td>
                    <td>{{ $exp->description }}</td>
                    <td style="text-align: right; font-weight: 600; color: var(--danger);">
                        Rp {{ number_format($exp->amount, 0, ',', '.') }}
                    </td>
                    <td style="text-align: center;">
                        <div style="display: flex; gap: 5px; justify-content: center;">
                            <a href="{{ route('admin.committee.expenditures.print', $exp) }}" target="_blank" class="btn btn-sm btn-info" title="Cetak Bukti">
                                <i class="fas fa-print"></i>
                            </a>
                            <a href="{{ route('admin.committee.expenditures.edit', $exp) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.committee.expenditures.destroy', $exp) }}" method="POST" id="delete-form-{{ $exp->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger" title="Hapus" onclick="confirmDelete({{ $exp->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px; color: var(--text-light);">
                        Belum ada data penggunaan dana.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>
@section('scripts')
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data penggunaan dana ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endsection
@endsection