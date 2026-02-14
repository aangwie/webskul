<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Penggunaan Dana Komite</title>
    <style>
        @page {
            margin: 1.5cm;
            size: A4 portrait;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
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

        .header p {
            font-size: 9pt;
            color: #666;
        }

        .report-title {
            text-align: center;
            margin: 20px 0;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .period {
            text-align: center;
            margin-bottom: 20px;
            font-style: italic;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th {
            background: #1e3a5f;
            color: white;
            padding: 10px 5px;
            text-align: left;
            border: 1px solid #1e3a5f;
        }

        table td {
            padding: 8px 5px;
            border: 1px solid #ddd;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-row {
            background: #f0f0f0;
            font-weight: bold;
        }

        .footer {
            margin-top: 40px;
        }

        .signature-box {
            float: right;
            width: 250px;
            text-align: center;
        }

        .signature-box .date {
            margin-bottom: 5px;
        }

        .signature-box .name {
            border-bottom: 1px solid #333;
            padding-top: 60px;
        }
    </style>
</head>

<body onload="window.print()">
    <div class="header">
        <h1>{{ $school->name ?? 'SMP NEGERI 6 SUDIMORO' }}</h1>
        <p>{{ $school->address ?? '' }}</p>
    </div>

    <div class="report-title">LAPORAN PENGGUNAAN DANA KOMITE</div>
    <div class="period">Tahun Pelajaran: {{ $selectedYear ? $selectedYear->year : 'Semua' }}</div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="40">No</th>
                <th class="text-center" width="120">No. Pengeluaran</th>
                <th class="text-center" width="80">Tanggal</th>
                <th>Nama Program</th>
                <th>Sub Program</th>
                <th>Deskripsi Penggunaan</th>
                <th class="text-right" width="100">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @php $globalIndex = 1; @endphp
            @forelse($groupedExpenditures as $activityName => $expenditures)
            <tr style="background-color: #f0f0f0;">
                <td colspan="7" style="font-weight: bold; text-align: left; padding-left: 10px;">
                    Kegiatan: {{ $activityName }}
                </td>
            </tr>
            @foreach($expenditures as $exp)
            <tr>
                <td class="text-center">{{ $globalIndex++ }}</td>
                <td class="text-center">{{ $exp->expenditure_number }}</td>
                <td class="text-center">{{ $exp->date->format('d/m/Y') }}</td>
                <td>{{ $exp->activity->program->name ?? '-' }}</td>
                <td>{{ $exp->activity->name ?? '-' }}</td>
                <td>{{ $exp->description }}</td>
                <td class="text-right">Rp {{ number_format($exp->amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr style="background-color: #fafafa;">
                <td colspan="6" class="text-right" style="font-weight: bold;">Total {{ $activityName }}</td>
                <td class="text-right" style="font-weight: bold;">Rp {{ number_format($expenditures->sum('amount'), 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada data.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6" class="text-right">TOTAL KESELURUHAN</td>
                <td class="text-right">Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <div class="signature-box">
            <div class="date">{{ $school->city ?? 'Sudimoro' }}, {{ date('d F Y') }}</div>
            <div>Bendahara Komite</div>
            <div class="name">( {{ auth()->user()->name }} )</div>
        </div>
    </div>
</body>

</html>