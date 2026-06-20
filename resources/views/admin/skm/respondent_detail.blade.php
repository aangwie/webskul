@extends('admin.layouts.app')

@section('title', 'Detail Responden')
@section('page-title', 'Detail Responden SKM')

@section('content')
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-user"></i> {{ $skmRespondent->name }}</h2>
        <a href="{{ route('admin.skm.reports', ['year' => $skmRespondent->year]) }}" class="btn btn-sm btn-primary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div>
                <label style="font-weight: 600; font-size: 0.85rem; color: var(--text-light);">Nama</label>
                <p style="font-size: 1rem;">{{ $skmRespondent->name }}</p>
            </div>
            <div>
                <label style="font-weight: 600; font-size: 0.85rem; color: var(--text-light);">Alamat</label>
                <p style="font-size: 1rem;">{{ $skmRespondent->address }}</p>
            </div>
            <div>
                <label style="font-weight: 600; font-size: 0.85rem; color: var(--text-light);">No. Telpon</label>
                <p style="font-size: 1rem;">{{ $skmRespondent->phone }}</p>
            </div>
            <div>
                <label style="font-weight: 600; font-size: 0.85rem; color: var(--text-light);">Tahun</label>
                <p style="font-size: 1rem;">{{ $skmRespondent->year }}</p>
            </div>
            <div>
                <label style="font-weight: 600; font-size: 0.85rem; color: var(--text-light);">Tanggal Pengisian</label>
                <p style="font-size: 1rem;">{{ $skmRespondent->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <h3 style="margin-bottom: 20px; font-size: 1.1rem;">Hasil Penilaian</h3>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pertanyaan</th>
                        <th>Skor</th>
                        <th>Kategori</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($skmRespondent->responses as $response)
                        @php
                            $labels = ['', 'Sangat Buruk', 'Buruk', 'Baik', 'Sangat Baik'];
                            $colors = ['', '#dc3545', '#ffc107', '#0d6efd', '#198754'];
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $response->question->question_text ?? 'Pertanyaan dihapus' }}</td>
                            <td style="font-weight: 700; color: {{ $colors[$response->score] ?? '#6c757d' }};">{{ $response->score }}</td>
                            <td><span class="badge" style="background: {{ $colors[$response->score] ?? '#6c757d' }}; color: white;">{{ $labels[$response->score] ?? '-' }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="font-weight: 700; background: var(--accent);">
                        <td colspan="2" style="text-align: right;">Rata-rata Skor</td>
                        <td>{{ number_format($skmRespondent->average_score, 2) }}</td>
                        <td>
                            @php
                                $ikm = $skmRespondent->ikm;
                                $category = $ikm >= 88 ? 'Sangat Baik' : ($ikm >= 76 ? 'Baik' : ($ikm >= 62 ? 'Cukup' : 'Kurang'));
                                $catColor = $ikm >= 88 ? '#198754' : ($ikm >= 76 ? '#0d6efd' : ($ikm >= 62 ? '#ffc107' : '#dc3545'));
                            @endphp
                            <span class="badge" style="background: {{ $catColor }}; color: white;">{{ $category }} (IKM: {{ number_format($ikm, 2) }})</span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection