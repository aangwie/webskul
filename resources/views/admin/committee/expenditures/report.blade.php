@extends('admin.layouts.app')

@section('title', 'Laporan Penggunaan Dana Komite')
@section('page-title', 'Laporan Penggunaan Dana')

@section('content')
<div class="card" style="margin-bottom: 25px;">
    <div class="card-header">
        <h2><i class="fas fa-filter"></i> Filter Laporan</h2>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.committee.expenditures.report') }}" method="GET" style="display: flex; gap: 20px; align-items: flex-end;">
            <div class="form-group" style="flex: 1; margin-bottom: 0;">
                <label class="form-label">Tahun Pelajaran</label>
                <select name="academic_year_id" class="form-input" style="color: navy;">
                    @foreach($academicYears as $year)
                    <option value="{{ $year->id }}" {{ ($selectedYear && $selectedYear->id == $year->id) ? 'selected' : '' }}>
                        {{ $year->year }} {{ $year->is_active ? '(Aktif)' : '' }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Tampilkan
                </button>
                <button type="submit" name="print" value="1" target="_blank" class="btn btn-secondary">
                    <i class="fas fa-print"></i> Cetak PDF
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h2><i class="fas fa-file-invoice-dollar"></i> Hasil Laporan</h2>
        <div style="font-weight: 600; color: var(--primary);">
            Tahun Pelajaran: {{ $selectedYear ? $selectedYear->year : 'Semua' }}
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th style="width: 150px;">No. Pengeluaran</th>
                        <th style="width: 150px;">Tanggal</th>
                        <th>Nama Program</th>
                        <th>Sub Program</th>
                        <th>Uraian Penggunaan</th>
                        <th style="text-align: right; width: 150px;">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @php $globalIndex = 1; @endphp
                    @forelse($groupedExpenditures as $activityName => $expenditures)
                    <tr style="background-color: #f0f4f8;">
                        <td colspan="7" style="font-weight: bold; color: #1e3a5f;">
                            <i class="fas fa-layer-group"></i> Kegiatan: {{ $activityName }}
                        </td>
                    </tr>
                    @foreach($expenditures as $exp)
                    <tr>
                        <td>{{ $globalIndex++ }}</td>
                        <td><span style="font-family: monospace;">{{ $exp->expenditure_number }}</span></td>
                        <td>{{ $exp->date->format('d/m/Y') }}</td>
                        <td>{{ $exp->activity->program->name ?? '-' }}</td>
                        <td>{{ $exp->activity->name ?? '-' }}</td>
                        <td>{{ $exp->description }}</td>
                        <td style="text-align: right; font-weight: bold; color: var(--danger);">
                            Rp {{ number_format($exp->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                    <tr style="background-color: #fff8e1;">
                        <td colspan="6" style="text-align: right; font-weight: 600;">Total {{ $activityName }}</td>
                        <td style="text-align: right; font-weight: 600; color: var(--danger);">
                            Rp {{ number_format($expenditures->sum('amount'), 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px; color: var(--text-light);">
                            Tidak ada data untuk tahun pelajaran ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($groupedExpenditures->count() > 0)
                <tfoot style="background: var(--accent); font-weight: 700;">
                    <tr>
                        <td colspan="6" style="text-align: right;">TOTAL KESELURUHAN</td>
                        <td style="text-align: right; font-size: 1.1rem; color: var(--danger);">
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection