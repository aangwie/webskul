<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Dana Komite - {{ $schoolClass->name }}</title>
    @if(isset($school) && $school && $school->logo)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $school->logo) }}">
    @else
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @endif
    <style>
        @page {
            margin: 1cm;
            size: A4 portrait;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print {
                display: none !important;
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
            background: #f5f5f5;
        }

        .print-container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            padding: 1cm;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .print-actions {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }

        .print-actions button {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-print {
            background: #1e3a5f;
            color: white;
        }

        .btn-back {
            background: #6c757d;
            color: white;
        }

        .header {
            text-align: center;
            border-bottom: 3px double #1e3a5f;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 14pt;
            color: #1e3a5f;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 9pt;
            color: #666;
        }

        .report-title {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .report-title h3 {
            font-size: 12pt;
            color: #1e3a5f;
            text-transform: uppercase;
        }

        .info-box {
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            margin-bottom: 5px;
        }

        .info-label {
            width: 150px;
            font-weight: bold;
        }

        .info-value {
            flex: 1;
        }

        .summary-box {
            display: flex;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
        }

        .summary-cell {
            flex: 1;
            padding: 10px;
            text-align: center;
            border-right: 1px solid #ddd;
        }

        .summary-cell:last-child {
            border-right: none;
        }

        .summary-cell .value {
            font-size: 14pt;
            font-weight: bold;
            color: #1e3a5f;
        }

        .summary-cell .label {
            font-size: 8pt;
            color: #666;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th {
            background: #1e3a5f;
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-size: 9pt;
        }

        table td {
            padding: 8px 5px;
            border-bottom: 1px solid #ddd;
            font-size: 9pt;
        }

        table tr:nth-child(even) {
            background: #f8f9fa;
        }

        table tfoot td {
            background: #e9ecef;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 8pt;
            font-weight: bold;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .student-section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .student-header {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .student-header strong {
            color: #1e3a5f;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            overflow: hidden;
        }

        .signature-box {
            float: right;
            width: 200px;
            text-align: center;
        }

        .signature-box .date {
            margin-bottom: 5px;
        }

        .signature-box .name {
            border-bottom: 1px solid #333;
            padding-top: 60px;
        }

        .text-success {
            color: #28a745;
        }

        .text-danger {
            color: #dc3545;
        }
    </style>
</head>

<body>
    {{-- Print Actions --}}
    <div class="print-actions no-print">
        <button class="btn-back" onclick="history.back()">
            <i>←</i> Kembali
        </button>
        <button class="btn-print" onclick="window.print()">
            <i>🖨</i> Cetak
        </button>
    </div>

    <div class="print-container">
        {{-- Header --}}
        <div class="header">
            <h1>{{ $school->name ?? 'SMP NEGERI 6 SUDIMORO' }}</h1>
            <p>{{ $school->address ?? '' }}</p>
        </div>

        {{-- Report Title --}}
        <div class="report-title">
            <h3>LAPORAN
                {{ strtoupper($reportType == 'detail' ? 'Detail' : ($reportType == 'class_summary' ? 'Rekap Per Kelas' : ($reportType == 'all_summary' ? 'Rekap Semua Kelas' : 'Rekapitulasi'))) }}
                PEMBAYARAN DANA KOMITE
            </h3>
        </div>

        {{-- Info Box --}}
        <div class="info-box">
            @if($filterType === 'academic_year' && $academicYear)
                <div class="info-item">
                    <span class="info-label">Tahun Ajaran</span>
                    <span class="info-value">: {{ $academicYear->year }}</span>
                </div>
            @else
                <div class="info-item">
                    <span class="info-label">Periode Laporan</span>
                    <span class="info-value">: {{ \Carbon\Carbon::parse($dateFrom)->format('d F Y') }} -
                        {{ \Carbon\Carbon::parse($dateTo)->format('d F Y') }}</span>
                </div>
            @endif
            <div class="info-item">
                <span class="info-label">Kelas</span>
                <span class="info-value">: {{ $schoolClass->name }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Tanggal Cetak</span>
                <span class="info-value">: {{ now()->format('d F Y') }}</span>
            </div>
            @if($committeeFee)
                <div class="info-item">
                    <span class="info-label">Nominal per Siswa</span>
                    <span class="info-value">: Rp {{ number_format($committeeFee->amount, 0, ',', '.') }}</span>
                </div>
            @endif
        </div>

        {{-- Summary Box --}}
        @if($reportType == 'detail' || $reportType == 'recapitulation')
            <div class="summary-box">
                <div class="summary-cell">
                    <div class="value">{{ $summary['total_students'] }}</div>
                    <div class="label">Total Siswa</div>
                </div>
                <div class="summary-cell">
                    <div class="value">{{ $summary['lunas_count'] ?? 0 }}</div>
                    <div class="label">Lunas</div>
                </div>
                <div class="summary-cell">
                    <div class="value">{{ $summary['belum_lunas_count'] ?? 0 }}</div>
                    <div class="label">Belum Lunas</div>
                </div>
                <div class="summary-cell">
                    <div class="value">Rp {{ number_format($summary['total_terbayar'], 0, ',', '.') }}</div>
                    <div class="label">Total Terbayar</div>
                </div>
            </div>
        @endif

        @if($reportType == 'class_summary')
            {{-- Class Summary Table --}}
            <table>
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th>Kelas</th>
                        <th class="text-center" style="width: 80px;">Siswa</th>
                        <th class="text-right" style="width: 100px;">Tagihan</th>
                        <th class="text-right" style="width: 100px;">Terbayar</th>
                        <th class="text-right" style="width: 100px;">Sisa</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData as $index => $data)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $data['class']->name }}</td>
                            <td class="text-center">{{ $data['total_students'] }}</td>
                            <td class="text-right">Rp {{ number_format($data['total_target'], 0, ',', '.') }}</td>
                            <td class="text-right text-success">Rp {{ number_format($data['total_paid'], 0, ',', '.') }}</td>
                            <td class="text-right text-danger">Rp {{ number_format($data['remaining'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-right"><strong>TOTAL GABUNGAN</strong></td>
                        <td class="text-center">{{ $summary['total_students'] }}</td>
                        <td class="text-right">Rp {{ number_format($summary['total_tagihan'], 0, ',', '.') }}</td>
                        <td class="text-right text-success">Rp {{ number_format($summary['total_terbayar'], 0, ',', '.') }}
                        </td>
                        <td class="text-right text-danger">Rp {{ number_format($summary['total_sisa'], 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        @elseif($reportType == 'all_summary')
            {{-- Global Summary Card --}}
            <div style="margin: 30px auto; max-width: 450px; border: 2px solid #333; padding: 25px; border-radius: 10px;">
                <h3
                    style="text-align: center; margin-top: 0; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px;">
                    RINGKASAN GLOBAL @if($filterType === 'academic_year' && $academicYear) TA {{ $academicYear->year }}
                    @else Periode {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} -
                    {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }} @endif</h3>
                <table style="width: 100%; border: none;">
                    <tr style="border: none;">
                        <td style="border: none; padding: 10px 0;">Total Siswa (Semua Kelas)</td>
                        <td style="border: none; padding: 10px 0;" class="text-right">
                            <strong>{{ $summary['total_students'] }} Siswa</strong>
                        </td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none; padding: 10px 0;">Total Target Tagihan</td>
                        <td style="border: none; padding: 10px 0;" class="text-right"><strong>Rp
                                {{ number_format($summary['total_tagihan'], 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none; padding: 10px 0;">Total Pembayaran Masuk</td>
                        <td style="border: none; padding: 10px 0;" class="text-right"><strong class="text-success">Rp
                                {{ number_format($summary['total_terbayar'], 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr style="border: none; border-top: 1px dashed #333;">
                        <td style="border: none; padding: 10px 0;">Total Sisa Tagihan</td>
                        <td style="border: none; padding: 10px 0;" class="text-right"><strong class="text-danger">Rp
                                {{ number_format($summary['total_sisa'], 0, ',', '.') }}</strong></td>
                    </tr>
                    @php $totalPercent = $summary['total_tagihan'] > 0 ? ($summary['total_terbayar'] / $summary['total_tagihan']) * 100 : 0; @endphp
                    <tr style="border: none;">
                        <td style="border: none; padding: 15px 0 0 0;">Persentase Pelunasan</td>
                        <td style="border: none; padding: 15px 0 0 0;" class="text-right"><strong
                                style="font-size: 1.2rem;">{{ number_format($totalPercent, 1) }}%</strong></td>
                    </tr>
                </table>
            </div>
        @elseif($reportType == 'recapitulation')
            {{-- Recapitulation Table --}}
            <table>
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th>Nama Siswa</th>
                        <th style="width: 70px;">NIS</th>
                        <th class="text-right" style="width: 90px;">Tagihan</th>
                        <th class="text-right" style="width: 90px;">Terbayar</th>
                        <th class="text-right" style="width: 90px;">Sisa</th>
                        <th class="text-center" style="width: 70px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData as $index => $data)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                {{ $data['student']->name }}
                                @if(isset($data['class_name']))
                                    <div style="font-size: 8pt; color: #666;">{{ $data['class_name'] }}</div>
                                @endif
                            </td>
                            <td>{{ $data['student']->nis ?? '-' }}</td>
                            <td class="text-right">Rp {{ number_format($data['fee_amount'], 0, ',', '.') }}</td>
                            <td class="text-right text-success">Rp {{ number_format($data['total_paid'], 0, ',', '.') }}</td>
                            <td class="text-right text-danger">Rp {{ number_format($data['remaining'], 0, ',', '.') }}</td>
                            <td class="text-center">
                                @if($data['is_paid_full'])
                                    <span class="badge badge-success">LUNAS</span>
                                @else
                                    <span class="badge badge-warning">BELUM</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right"><strong>TOTAL</strong></td>
                        <td class="text-right">Rp {{ number_format($summary['total_tagihan'], 0, ',', '.') }}</td>
                        <td class="text-right text-success">Rp {{ number_format($summary['total_terbayar'], 0, ',', '.') }}
                        </td>
                        <td class="text-right text-danger">Rp {{ number_format($summary['total_sisa'], 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        @else
            {{-- Detail Report - Excel-like Table Format --}}
            <table style="border: 1px solid #333;">
                <thead>
                    <tr>
                        <th style="width: 30px; border: 1px solid #333;">No</th>
                        <th style="width: 70px; border: 1px solid #333;">NIS</th>
                        <th style="border: 1px solid #333;">Nama</th>
                        <th style="width: 70px; border: 1px solid #333;">Kelas</th>
                        <th style="width: 130px; border: 1px solid #333;">Tgl Bayar</th>
                        <th class="text-right" style="width: 90px; border: 1px solid #333;">Total Bayar</th>
                        <th class="text-right" style="width: 90px; border: 1px solid #333;">Sisa Bayar</th>
                        <th class="text-center" style="width: 70px; border: 1px solid #333;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData as $index => $data)
                        <tr>
                            <td class="text-center" style="border: 1px solid #ddd;">{{ $index + 1 }}</td>
                            <td style="border: 1px solid #ddd;">{{ $data['student']->nis ?? '-' }}</td>
                            <td style="border: 1px solid #ddd;">{{ $data['student']->name }}</td>
                            <td style="border: 1px solid #ddd;">{{ $data['class_name'] ?? '-' }}</td>
                            <td style="border: 1px solid #ddd; font-size: 8pt;">
                                @if($data['payments']->count() > 0)
                                    {{ $data['payments']->pluck('payment_date')->map(fn($d) => $d->format('d/m/Y'))->implode(', ') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-right text-success" style="border: 1px solid #ddd;">Rp
                                {{ number_format($data['total_paid'], 0, ',', '.') }}</td>
                            <td class="text-right text-danger" style="border: 1px solid #ddd;">Rp
                                {{ number_format($data['remaining'], 0, ',', '.') }}</td>
                            <td class="text-center" style="border: 1px solid #ddd;">
                                @if($data['is_paid_full'])
                                    <span class="badge badge-success">LUNAS</span>
                                @else
                                    <span class="badge badge-warning">BELUM</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-right" style="border: 1px solid #333;"><strong>TOTAL</strong></td>
                        <td class="text-right text-success" style="border: 1px solid #333;"><strong>Rp
                                {{ number_format($summary['total_terbayar'], 0, ',', '.') }}</strong></td>
                        <td class="text-right text-danger" style="border: 1px solid #333;"><strong>Rp
                                {{ number_format($summary['total_sisa'], 0, ',', '.') }}</strong></td>
                        <td style="border: 1px solid #333;"></td>
                    </tr>
                </tfoot>
            </table>
        @endif

        {{-- Footer with Signature --}}
        <div class="footer">
            <div class="signature-box">
                <div class="date">{{ $school->city ?? 'Sudimoro' }}, {{ now()->format('d F Y') }}</div>
                <div>Bendahara Komite</div>
                <div class="name">( {{ $signatory->name ?? '............................' }} )</div>
            </div>
        </div>
    </div>

    <script>
        // Auto-focus on print when page loads
        window.onload = function () {
            // Small delay to ensure page is fully rendered
            setTimeout(function () {
                window.print();
            }, 500);
        };
    </script>
</body>

</html>