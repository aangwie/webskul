<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Bukti Penggunaan Dana - {{ $expenditure->expenditure_number }}</title>
    <style>
        @page {
            margin: 1cm;
            size: A5 landscape;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #333;
        }

        .receipt-container {
            border: 2px solid #1e3a5f;
            padding: 20px;
            position: relative;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #1e3a5f;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 14pt;
            margin: 0;
            color: #1e3a5f;
        }

        .header p {
            font-size: 8pt;
            margin: 5px 0 0;
        }

        .receipt-title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            text-decoration: underline;
            margin-bottom: 20px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 5px;
            vertical-align: top;
        }

        .label {
            width: 150px;
            font-weight: bold;
        }

        .amount-box {
            background: #f0f0f0;
            border: 1px solid #ccc;
            padding: 10px;
            font-weight: bold;
            font-size: 12pt;
            display: inline-block;
            margin-top: 10px;
        }

        .footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }

        .signature {
            text-align: center;
            width: 150px;
        }

        .signature .name {
            margin-top: 50px;
            border-top: 1px solid #333;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="receipt-container">
        <div class="header">
            <h1>{{ $school->name ?? 'SMP NEGERI 6 SUDIMORO' }}</h1>
            <p>{{ $school->address ?? '' }}</p>
        </div>

        <div class="receipt-title">BUKTI PENGGUNAAN DANA KOMITE</div>

        <table class="info-table">
            <tr>
                <td class="label">No. Pengeluaran</td>
                <td>: {{ $expenditure->expenditure_number }}</td>
                <td align="right" style="font-weight: bold;">Tanggal: {{ $expenditure->date->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="label">Deskripsi Penggunaan</td>
                <td colspan="2">: {{ $expenditure->description }}</td>
            </tr>
            <tr>
                <td class="label">Jumlah Nominal</td>
                <td colspan="2">
                    <div class="amount-box">Rp {{ number_format($expenditure->amount, 0, ',', '.') }}</div>
                </td>
            </tr>
        </table>

        <div class="footer">
            <div class="signature">
                <p>Penerima,</p>
                <div class="name">( ............................ )</div>
            </div>
            <div class="signature">
                <p>{{ $school->city ?? 'Sudimoro' }}, {{ date('d/m/Y') }}</p>
                <p>Bendahara,</p>
                <div class="name">( {{ auth()->user()->name }} )</div>
            </div>
        </div>
    </div>
</body>

</html>