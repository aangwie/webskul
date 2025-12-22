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
    <div class="period">Periode: {{ date('d F Y', strtotime($startDate)) }} - {{ date('d F Y', strtotime($endDate)) }}</div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="40">No</th>
                <th class="text-center" width="100">Tanggal</th>
                <th class="text-center" width="150">No. Pengeluaran</th>
                <th>Deskripsi Penggunaan</th>
                <th class="text-right" width="130">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenditures as $index => $exp)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $exp->date->format('d/m/Y') }}</td>
                <td class="text-center">{{ $exp->expenditure_number }}</td>
                <td>{{ $exp->description }}</td>
                <td class="text-right">Rp {{ number_format($exp->amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-right">TOTAL PENGGUNAAN</td>
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