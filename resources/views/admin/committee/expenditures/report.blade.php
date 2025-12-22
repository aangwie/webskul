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
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-input" value="{{ $startDate }}">
            </div>
            <div class="form-group" style="flex: 1; margin-bottom: 0;">
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="end_date" class="form-input" value="{{ $endDate }}">
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Tampilkan
                </button>
                <button type="submit" name="print" value="1" class="btn btn-secondary">
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
            Periode: {{ date('d/m/Y', strtotime($startDate)) }} - {{ date('d/m/Y', strtotime($endDate)) }}
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th style="width: 150px;">Tanggal</th>
                        <th style="width: 200px;">No. Pengeluaran</th>
                        <th>Deskripsi Penggunaan</th>
                        <th style="text-align: right; width: 180px;">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenditures as $index => $exp)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $exp->date->format('d/m/Y') }}</td>
                        <td><span style="font-family: monospace;">{{ $exp->expenditure_number }}</span></td>
                        <td>{{ $exp->description }}</td>
                        <td style="text-align: right; font-weight: bold; color: var(--danger);">
                            Rp {{ number_format($exp->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 30px; color: var(--text-light);">
                            Tidak ada data dalam periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($expenditures->count() > 0)
                <tfoot style="background: var(--accent); font-weight: 700;">
                    <tr>
                        <td colspan="4" style="text-align: right;">TOTAL PENGGUNAAN</td>
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