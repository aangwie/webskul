<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Dana Komite - {{ $schoolClass->name }}</title>
    @if(isset($school) && $school && $school->logo)
    <link rel="icon" type="image/png" href="{{ asset('storage/' . $school->logo) }}">
    @else
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @endif
    <style>
        @page {
            margin: 1cm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
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

        .header h2 {
            font-size: 12pt;
            font-weight: normal;
            margin-bottom: 3px;
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
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .info-item {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            width: 150px;
            padding: 5px 0;
            font-weight: bold;
        }

        .info-value {
            display: table-cell;
            padding: 5px 0;
        }

        .summary-box {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .summary-row {
            display: table-row;
        }

        .summary-cell {
            display: table-cell;
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
        }

        .student-header strong {
            color: #1e3a5f;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }

        .signature-box {
            float: right;
            width: 200px;
            text-align: center;
        }

        .signature-box .date {
            margin-bottom: 60px;
        }

        .signature-box .name {
            border-top: 1px solid #333;
            padding-top: 5px;
        }

        .text-success {
            color: #28a745;
        }

        .text-danger {
            color: #dc3545;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    {{-- Header --}}
    <div class="header">
        <h1>{{ $school->name ?? 'SMP NEGERI 6 SUDIMORO' }}</h1>
        <p>{{ $school->address ?? '' }}</p>
    </div>

    {{-- Report Title --}}
    <div class="report-title">
        <h3>Laporan {{ $reportType == 'detail' ? 'Detail' : 'Rekapitulasi' }} Pembayaran Dana Komite</h3>
    </div>

    {{-- Info Box --}}
    <div class="info-box">
        <div class="info-item">
            <span class="info-label">Tahun Ajaran</span>
            <span class="info-value">: {{ $academicYear->year }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Kelas</span>
            <span class="info-value">: {{ $schoolClass->name }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Nominal per Siswa</span>
            <span class="info-value">: Rp {{ number_format($committeeFee->amount, 0, ',', '.') }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Tanggal Cetak</span>
            <span class="info-value">: {{ now()->format('d F Y') }}</span>
        </div>
    </div>

    {{-- Summary Box --}}
    <div class="summary-box">
        <div class="summary-row">
            <div class="summary-cell">
                <div class="value">{{ $summary['total_students'] }}</div>
                <div class="label">Total Siswa</div>
            </div>
            <div class="summary-cell">
                <div class="value">{{ $summary['lunas_count'] }}</div>
                <div class="label">Lunas</div>
            </div>
            <div class="summary-cell">
                <div class="value">{{ $summary['belum_lunas_count'] }}</div>
                <div class="label">Belum Lunas</div>
            </div>
            <div class="summary-cell">
                <div class="value">Rp {{ number_format($summary['total_terbayar'], 0, ',', '.') }}</div>
                <div class="label">Total Terbayar</div>
            </div>
        </div>
    </div>

    @if($reportType == 'recapitulation')
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
                <td>{{ $data['student']->name }}</td>
                <td>{{ $data['student']->nis ?? '-' }}</td>
                <td class="text-right">Rp {{ number_format($committeeFee->amount, 0, ',', '.') }}</td>
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
                <td class="text-right text-success">Rp {{ number_format($summary['total_terbayar'], 0, ',', '.') }}</td>
                <td class="text-right text-danger">Rp {{ number_format($summary['total_sisa'], 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    @else
    {{-- Detail Report --}}
    @foreach($reportData as $index => $data)
    <div class="student-section">
        <div class="student-header">
            <strong>{{ $index + 1 }}. {{ $data['student']->name }}</strong>
            <span style="color: #666;">(NIS: {{ $data['student']->nis ?? '-' }})</span>
            @if($data['is_paid_full'])
            <span class="badge badge-success" style="float: right;">LUNAS</span>
            @else
            <span class="badge badge-warning" style="float: right;">BELUM LUNAS</span>
            @endif
        </div>

        @if($data['payments']->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th style="width: 100px;">Tanggal</th>
                    <th class="text-right" style="width: 120px;">Nominal</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['payments'] as $pIndex => $payment)
                <tr>
                    <td class="text-center">{{ $pIndex + 1 }}</td>
                    <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                    <td class="text-right text-success">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    <td>{{ $payment->notes ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p style="color: #666; font-style: italic; padding: 10px;">Belum ada pembayaran.</p>
        @endif

        <p style="font-size: 9pt; color: #666;">
            Tagihan: <strong>Rp {{ number_format($committeeFee->amount, 0, ',', '.') }}</strong> |
            Terbayar: <strong class="text-success">Rp {{ number_format($data['total_paid'], 0, ',', '.') }}</strong> |
            Sisa: <strong class="text-danger">Rp {{ number_format($data['remaining'], 0, ',', '.') }}</strong>
        </p>
    </div>
    @endforeach
    @endif

    {{-- Footer with Signature --}}
    <div class="footer">
        <div class="signature-box">
            <div class="date">{{ $school->city ?? 'Sudimoro' }}, {{ now()->format('d F Y') }}</div>
            <div>Bendahara Komite</div>
            <div class="name">( {{ $signatory->name ?? '............................' }} )</div>
        </div>
        <div style="clear: both;"></div>
    </div>
</body>

</html>