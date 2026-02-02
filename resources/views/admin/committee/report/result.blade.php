@extends('admin.layouts.app')

@section('title', 'Hasil Laporan Dana Komite')
@section('page-title', 'Laporan Dana Komite: ' . $schoolClass->name)

@section('content')
    {{-- Report Header --}}
    <div class="card" style="margin-bottom: 25px;">
        <div class="card-body">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                <div>
                    <h3 style="margin-bottom: 10px;">
                        <i class="fas fa-file-alt" style="color: var(--primary);"></i>
                        @if($reportType == 'class_summary')
                            Laporan Rekapitulasi Per Kelas
                        @elseif($reportType == 'all_summary')
                            Laporan Rekapitulasi Semua Kelas
                        @else
                            Laporan {{ $reportType == 'detail' ? 'Detail' : 'Rekapitulasi' }} Pembayaran Dana Komite
                        @endif
                    </h3>
                    <div style="display: flex; gap: 30px; flex-wrap: wrap; color: var(--text-light);">
                        <span><i class="fas fa-graduation-cap"></i> {{ $schoolClass->name }}</span>
                        @if($filterType === 'academic_year' && $academicYear)
                            <span><i class="fas fa-calendar"></i> Tahun Ajaran {{ $academicYear->year }}</span>
                        @else
                            <span><i class="fas fa-calendar-alt"></i> Periode: {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</span>
                        @endif
                        @if($committeeFee)
                            <span><i class="fas fa-money-bill-wave"></i> Nominal: Rp
                                {{ number_format($committeeFee->amount, 0, ',', '.') }}</span>
                        @endif
                    </div>
                </div>
                <div style="display: flex; gap: 10px;">
                    <form action="{{ route('admin.committee.report.pdf') }}" method="POST" target="_blank"
                        style="display: inline;">
                        @csrf
                        <input type="hidden" name="filter_type" value="{{ $filterType }}">
                        @if($filterType === 'academic_year' && $academicYear)
                            <input type="hidden" name="academic_year_id" value="{{ $academicYear->id }}">
                        @else
                            <input type="hidden" name="date_from" value="{{ $dateFrom }}">
                            <input type="hidden" name="date_to" value="{{ $dateTo }}">
                        @endif
                        <input type="hidden" name="school_class_id" value="{{ request('school_class_id') }}">
                        <input type="hidden" name="report_type" value="{{ $reportType }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-print"></i> Cetak
                        </button>
                    </form>
                    <a href="{{ route('admin.committee.report.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($reportType == 'detail' || $reportType == 'recapitulation')
        {{-- Summary Cards --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $summary['total_students'] }}</h3>
                    <p>Total Siswa</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div class="stat-info">
                    <h3>Rp {{ number_format($summary['total_tagihan'], 0, ',', '.') }}</h3>
                    <p>Total Tagihan</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>Rp {{ number_format($summary['total_terbayar'], 0, ',', '.') }}</h3>
                    <p>Total Terbayar</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(220, 53, 69, 0.1); color: var(--danger);">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>Rp {{ number_format($summary['total_sisa'], 0, ',', '.') }}</h3>
                    <p>Total Sisa</p>
                </div>
            </div>
        </div>

        {{-- Status Summary --}}
        <div style="display: flex; gap: 20px; margin-bottom: 25px;">
            <div
                style="flex: 1; background: rgba(40, 167, 69, 0.1); padding: 20px; border-radius: 12px; text-align: center; border: 2px solid var(--success);">
                <div style="font-size: 2rem; font-weight: 700; color: var(--success);">{{ $summary['lunas_count'] ?? 0 }}</div>
                <div style="color: var(--success); font-weight: 600;">Siswa Lunas</div>
            </div>
            <div
                style="flex: 1; background: rgba(255, 193, 7, 0.1); padding: 20px; border-radius: 12px; text-align: center; border: 2px solid var(--warning);">
                <div style="font-size: 2rem; font-weight: 700; color: #856404;">{{ $summary['belum_lunas_count'] ?? 0 }}</div>
                <div style="color: #856404; font-weight: 600;">Siswa Belum Lunas</div>
            </div>
        </div>
    @else
        {{-- Grand Summary for Multi-class Reports --}}
        <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr);">
            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div class="stat-info">
                    <h3>Rp {{ number_format($summary['total_tagihan'], 0, ',', '.') }}</h3>
                    <p>Total Tagihan (Semua Kelas)</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>Rp {{ number_format($summary['total_terbayar'], 0, ',', '.') }}</h3>
                    <p>Total Terbayar (Semua Kelas)</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(220, 53, 69, 0.1); color: var(--danger);">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>Rp {{ number_format($summary['total_sisa'], 0, ',', '.') }}</h3>
                    <p>Total Sisa (Semua Kelas)</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Report Table --}}
    <div class="card">
        <div class="card-header">
            <h2>
                <i class="fas fa-table"></i>
                @if($reportType == 'class_summary') Recap Per Kelas @elseif($reportType == 'all_summary') Recap Semua Kelas
                @else Data {{ $reportType == 'detail' ? 'Detail' : 'Rekapitulasi' }} @endif
            </h2>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if($reportType == 'class_summary')
                    {{-- Class Summary Table --}}
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kelas</th>
                                <th style="text-align: center;">Total Siswa</th>
                                <th style="text-align: right;">Total Tagihan</th>
                                <th style="text-align: right;">Total Terbayar</th>
                                <th style="text-align: right;">Total Sisa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportData as $index => $data)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $data['class']->name }}</strong></td>
                                    <td style="text-align: center;">{{ $data['total_students'] }}</td>
                                    <td style="text-align: right;">Rp {{ number_format($data['total_target'], 0, ',', '.') }}</td>
                                    <td style="text-align: right; color: var(--success); font-weight: 600;">Rp
                                        {{ number_format($data['total_paid'], 0, ',', '.') }}</td>
                                    <td style="text-align: right; color: var(--danger); font-weight: 600;">Rp
                                        {{ number_format($data['remaining'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="background: var(--accent); font-weight: 700;">
                            <tr>
                                <td colspan="2" style="text-align: right;">TOTAL GABUNGAN</td>
                                <td style="text-align: center;">{{ $summary['total_students'] }}</td>
                                <td style="text-align: right;">Rp {{ number_format($summary['total_tagihan'], 0, ',', '.') }}
                                </td>
                                <td style="text-align: right; color: var(--success);">Rp
                                    {{ number_format($summary['total_terbayar'], 0, ',', '.') }}</td>
                                <td style="text-align: right; color: var(--danger);">Rp
                                    {{ number_format($summary['total_sisa'], 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                @elseif($reportType == 'all_summary')
                    {{-- Grand Total Summary Card View --}}
                    <div style="padding: 30px; text-align: center; background: var(--accent); border-radius: 15px;">
                        <h3 style="margin-bottom: 25px; color: var(--primary);">Ringkasan Dana Komite TA
                            {{ $academicYear->year }}</h3>
                        <div
                            style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; max-width: 600px; margin: 0 auto; text-align: left;">
                            <div style="background: white; padding: 15px; border-radius: 10px;">
                                <span style="color: var(--text-light); display: block; font-size: 0.9rem;">Total Siswa</span>
                                <strong style="font-size: 1.2rem;">{{ $summary['total_students'] }} Siswa</strong>
                            </div>
                            <div style="background: white; padding: 15px; border-radius: 10px;">
                                <span style="color: var(--text-light); display: block; font-size: 0.9rem;">Total Tagihan</span>
                                <strong style="font-size: 1.2rem;">Rp
                                    {{ number_format($summary['total_tagihan'], 0, ',', '.') }}</strong>
                            </div>
                            <div style="background: white; padding: 15px; border-radius: 10px;">
                                <span style="color: var(--text-light); display: block; font-size: 0.9rem;">Total Terbayar</span>
                                <strong style="font-size: 1.2rem; color: var(--success);">Rp
                                    {{ number_format($summary['total_terbayar'], 0, ',', '.') }}</strong>
                            </div>
                            <div style="background: white; padding: 15px; border-radius: 10px;">
                                <span style="color: var(--text-light); display: block; font-size: 0.9rem;">Total Sisa
                                    Tagihan</span>
                                <strong style="font-size: 1.2rem; color: var(--danger);">Rp
                                    {{ number_format($summary['total_sisa'], 0, ',', '.') }}</strong>
                            </div>
                        </div>
                        @php $totalPercent = $summary['total_tagihan'] > 0 ? ($summary['total_terbayar'] / $summary['total_tagihan']) * 100 : 0; @endphp
                        <div style="margin-top: 30px; max-width: 600px; margin-left: auto; margin-right: auto;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span style="font-weight: 600;">Persentase Pelunasan</span>
                                <span
                                    style="color: var(--success); font-weight: 700;">{{ number_format($totalPercent, 1) }}%</span>
                            </div>
                            <div
                                style="width: 100%; background: white; border-radius: 15px; height: 15px; overflow: hidden; border: 1px solid #ddd;">
                                <div style="width: {{ $totalPercent }}%; background: var(--success); height: 100%;"></div>
                            </div>
                        </div>
                    </div>
                @elseif($reportType == 'recapitulation')
                    {{-- Recapitulation Table --}}
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Siswa</th>
                                <th>NIS</th>
                                <th style="text-align: right;">Tagihan</th>
                                <th style="text-align: right;">Terbayar</th>
                                <th style="text-align: right;">Sisa</th>
                                <th style="text-align: center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportData as $index => $data)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $data['student']->name }}</strong>
                                        @if(isset($data['class_name']))
                                            <div style="font-size: 0.8rem; color: #666;">{{ $data['class_name'] }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $data['student']->nis ?? '-' }}</td>
                                    <td style="text-align: right;">Rp {{ number_format($data['fee_amount'], 0, ',', '.') }}</td>
                                    <td style="text-align: right; color: var(--success); font-weight: 600;">Rp
                                        {{ number_format($data['total_paid'], 0, ',', '.') }}</td>
                                    <td style="text-align: right; color: var(--danger); font-weight: 600;">Rp
                                        {{ number_format($data['remaining'], 0, ',', '.') }}</td>
                                    <td style="text-align: center;">
                                        @if($data['is_paid_full'])
                                            <span class="badge badge-success">LUNAS</span>
                                        @else
                                            <span class="badge badge-warning">BELUM LUNAS</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="background: var(--accent); font-weight: 700;">
                            <tr>
                                <td colspan="3" style="text-align: right;">TOTAL</td>
                                <td style="text-align: right;">Rp {{ number_format($summary['total_tagihan'], 0, ',', '.') }}
                                </td>
                                <td style="text-align: right; color: var(--success);">Rp
                                    {{ number_format($summary['total_terbayar'], 0, ',', '.') }}</td>
                                <td style="text-align: right; color: var(--danger);">Rp
                                    {{ number_format($summary['total_sisa'], 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                @else
                    {{-- Detail Table --}}
                    @foreach($reportData as $index => $data)
                        <div style="margin-bottom: 30px; padding: 20px; background: var(--accent); border-radius: 12px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                <div>
                                    <strong style="font-size: 1.1rem;">{{ $index + 1 }}. {{ $data['student']->name }}</strong>
                                    <span style="color: var(--text-light); margin-left: 10px;">NIS:
                                        {{ $data['student']->nis ?? '-' }}</span>
                                </div>
                                @if($data['is_paid_full'])
                                    <span class="badge badge-success">LUNAS</span>
                                @else
                                    <span class="badge badge-warning">BELUM LUNAS</span>
                                @endif
                            </div>

                            @if($data['payments']->count() > 0)
                                <table style="margin-bottom: 10px;">
                                    <thead>
                                        <tr style="background: white;">
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th style="text-align: right;">Nominal</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['payments'] as $pIndex => $payment)
                                            <tr style="background: white;">
                                                <td>{{ $pIndex + 1 }}</td>
                                                <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                                <td style="text-align: right; color: var(--success); font-weight: 600;">Rp
                                                    {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                                <td>{{ $payment->notes ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p style="color: var(--text-light); font-style: italic;">Belum ada pembayaran.</p>
                            @endif

                            <div
                                style="display: flex; gap: 30px; padding-top: 10px; border-top: 1px dashed #ddd; margin-top: 10px;">
                                <span>Tagihan: <strong>Rp {{ number_format($data['fee_amount'], 0, ',', '.') }}</strong></span>
                                <span>Terbayar: <strong style="color: var(--success);">Rp
                                        {{ number_format($data['total_paid'], 0, ',', '.') }}</strong></span>
                                <span>Sisa: <strong style="color: var(--danger);">Rp
                                        {{ number_format($data['remaining'], 0, ',', '.') }}</strong></span>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@endsection