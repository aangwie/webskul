@extends('admin.layouts.app')

@section('title', 'Laporan SKM')
@section('page-title', 'Laporan SKM (Survei Kepuasan Masyarakat)')

@section('styles')
<style>
    .ikm-card {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
        color: var(--secondary);
        border-radius: 16px;
        padding: 30px;
        text-align: center;
        margin-bottom: 25px;
    }
    .ikm-card h1 {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 5px;
    }
    .ikm-card p {
        opacity: 0.9;
        font-size: 1rem;
    }
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
    }
    .stat-box {
        background: var(--secondary);
        border: 1px solid var(--accent);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
    }
    .stat-box h3 {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--primary);
    }
    .stat-box p {
        font-size: 0.85rem;
        color: var(--text-light);
        margin-top: 5px;
    }
    .result-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 15px;
        border-bottom: 1px solid var(--accent);
    }
    .result-row:last-child {
        border-bottom: none;
    }
    .result-row .avg-score {
        font-weight: 700;
        font-size: 1rem;
    }
    .progress-bar-container {
        width: 100px;
        height: 8px;
        background: #eef2f7;
        border-radius: 10px;
        overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%;
        border-radius: 10px;
        transition: width 0.5s ease;
    }
    .category-label {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-filter"></i> Filter Tahun</h2>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.skm.reports') }}">
            <div class="form-group" style="max-width: 300px;">
                <label class="form-label">Pilih Tahun</label>
                <select name="year" class="form-select" onchange="this.form.submit()">
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ $y == $selectedYear ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                    @if($years->isEmpty())
                        <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                    @endif
                </select>
            </div>
        </form>
    </div>
</div>

@if($respondentCount > 0)
    <div class="ikm-card animate-fade-in">
        <p>Indeks Kepuasan Masyarakat (IKM)</p>
        <h1>{{ number_format($ikm, 2) }}</h1>
        <p>Skala 0 - 100</p>
    </div>

    <div class="stats-grid">
        <div class="stat-box">
            <h3>{{ $respondentCount }}</h3>
            <p>Jumlah Responden</p>
        </div>
        <div class="stat-box">
            <h3>{{ number_format($totalCount > 0 ? ($ikm / 25) : 0, 2) }}</h3>
            <p>Rata-rata Nilai Unsur</p>
        </div>
        <div class="stat-box">
            <h3>{{ $distributions[1] + $distributions[2] + $distributions[3] + $distributions[4] }}</h3>
            <p>Total Jawaban</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-chart-bar"></i> Distribusi Skor</h2>
        </div>
        <div class="card-body">
            @php
                $totalResponses = array_sum($distributions);
                $colors = ['#dc3545', '#ffc107', '#0d6efd', '#198754'];
                $labels = ['1 - Sangat Buruk', '2 - Buruk', '3 - Baik', '4 - Sangat Baik'];
            @endphp
            @for($i = 1; $i <= 4; $i++)
                @php $pct = $totalResponses > 0 ? ($distributions[$i] / $totalResponses) * 100 : 0; @endphp
                <div style="margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span style="font-size: 0.85rem; font-weight: 600;">Skor {{ $i }} - {{ $labels[$i - 1] }}</span>
                        <span style="font-size: 0.85rem;">{{ $distributions[$i] }} ({{ number_format($pct, 1) }}%)</span>
                    </div>
                    <div class="progress-bar-container" style="width: 100%;">
                        <div class="progress-bar-fill" style="width: {{ $pct }}%; background: {{ $colors[$i - 1] }};"></div>
                    </div>
                </div>
            @endfor
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-chart-line"></i> Rata-rata Skor Per Pertanyaan</h2>
        </div>
        <div class="card-body" style="padding: 0;">
            @foreach($questions as $q)
                @if(isset($averages[$q->id]))
                    @php
                        $avg = $averages[$q->id]['average'];
                        $pct = ($avg / 4) * 100;
                        $color = $avg >= 3 ? '#198754' : ($avg >= 2 ? '#ffc107' : '#dc3545');
                    @endphp
                    <div class="result-row">
                        <div style="flex: 1;">
                            <div style="font-weight: 600; font-size: 0.9rem;">{{ $loop->iteration }}. {{ $q->question_text }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-light);">Jawaban: {{ $averages[$q->id]['count'] }}</div>
                        </div>
                        <div style="text-align: right; min-width: 120px;">
                            <div class="avg-score" style="color: {{ $color }};">{{ number_format($avg, 2) }}</div>
                            <div class="progress-bar-container" style="width: 100%;">
                                <div class="progress-bar-fill" style="width: {{ $pct }}%; background: {{ $color }};"></div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-users"></i> Daftar Responden</h2>
            <span class="badge badge-success">{{ $respondentCount }} Responden</span>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Alamat</th>
                            <th>No Telpon</th>
                            <th>Tanggal</th>
                            <th>Rata-rata</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($respondents as $r)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $r->name }}</td>
                                <td>{{ Str::limit($r->address, 30) }}</td>
                                <td>{{ $r->phone }}</td>
                                <td>{{ $r->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ number_format($r->average_score, 2) }}</td>
                                <td>
                                    <a href="{{ route('admin.skm.respondent-detail', $r) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.skm.delete-respondent', $r) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data responden?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body" style="text-align: center; padding: 60px;">
            <i class="fas fa-chart-pie" style="font-size: 4rem; color: var(--text-light); margin-bottom: 20px;"></i>
            <h3 style="color: var(--text-light);">Belum ada data survei untuk tahun {{ $selectedYear }}</h3>
            <p style="color: var(--text-light); margin-top: 10px;">Data akan muncul ketika ada responden yang mengisi survei.</p>
        </div>
    </div>
@endif
@endsection