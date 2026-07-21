@extends('admin.layouts.app')

@section('title', 'Rekap Komite')
@section('page-title', 'Rekap Komite')

@section('content')
<div style="margin-bottom: 25px;">
    <p style="color: var(--text-light);">Ringkasan penerimaan dan pengeluaran dana komite.</p>
</div>

<!-- Academic Year Filter -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body">
        <form action="{{ route('admin.committee.expenditures.rekap') }}" method="GET" style="display: flex; align-items: center; gap: 1rem;">
            <label style="white-space: nowrap; font-weight: 500;">Pilih Tahun Pelajaran:</label>
            <select name="academic_year_id" class="form-input" style="width: auto; flex-grow: 1;" onchange="this.form.submit()">
                @foreach($academicYears as $year)
                <option value="{{ $year->id }}" {{ ($selectedYear && $selectedYear->id == $year->id) ? 'selected' : '' }}>
                    {{ $year->year }}
                </option>
                @endforeach
            </select>
            <noscript><button type="submit" class="btn btn-primary">Filter</button></noscript>
        </form>
    </div>
</div>

@if($selectedYear)
<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="stat-info">
            <h3>Rp {{ number_format($totalIncome, 0, ',', '.') }}</h3>
            <p>Pembayaran Diterima</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-piggy-bank"></i>
        </div>
        <div class="stat-info">
            <h3>Rp {{ number_format($previousBalance, 0, ',', '.') }}</h3>
            <p>Sisa Saldo Tahun Lalu</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-calculator"></i>
        </div>
        <div class="stat-info">
            <h3>Rp {{ number_format($accumulation, 0, ',', '.') }}</h3>
            <p>Akumulasi</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(220, 53, 69, 0.1); color: var(--danger);">
            <i class="fas fa-hand-holding-heart"></i>
        </div>
        <div class="stat-info">
            <h3>Rp {{ number_format($totalExpenditure, 0, ',', '.') }}</h3>
            <p>Total Pengeluaran</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon gold">
            <i class="fas fa-coins"></i>
        </div>
        <div class="stat-info">
            <h3 style="color: {{ $remainingFunds < 0 ? 'var(--danger)' : 'var(--success)' }};">
                Rp {{ number_format($remainingFunds, 0, ',', '.') }}
            </h3>
            <p>Sisa Dana</p>
        </div>
    </div>
</div>

<!-- Detail Table -->
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-list"></i> Rincian Rekap Tahun {{ $selectedYear->year }}</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Uraian</th>
                        <th style="text-align: right;">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Pembayaran Diterima</td>
                        <td style="text-align: right;">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Sisa Saldo Tahun Lalu</td>
                        <td style="text-align: right;">Rp {{ number_format($previousBalance, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="font-weight: 600; background: #f8f9fa;">
                        <td>Akumulasi (Pembayaran + Sisa Saldo)</td>
                        <td style="text-align: right;">Rp {{ number_format($accumulation, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Total Pengeluaran</td>
                        <td style="text-align: right; color: var(--danger);">Rp {{ number_format($totalExpenditure, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="font-weight: 700; background: {{ $remainingFunds < 0 ? '#fff5f5' : '#f0fdf4' }};">
                        <td>Sisa Dana</td>
                        <td style="text-align: right; color: {{ $remainingFunds < 0 ? 'var(--danger)' : 'var(--success)' }};">
                            Rp {{ number_format($remainingFunds, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="card">
    <div class="card-body" style="text-align: center; padding: 40px;">
        <i class="fas fa-calendar-alt" style="font-size: 3rem; color: var(--text-light); margin-bottom: 15px;"></i>
        <p style="color: var(--text-light);">Pilih tahun pelajaran untuk menampilkan rekap.</p>
    </div>
</div>
@endif
@endsection