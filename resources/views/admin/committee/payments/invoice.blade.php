<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Sumbangan Komite - {{ $student->name }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            background: #f0f0f0;
            padding: 20px;
            color: #333;
        }

        .receipt {
            background: white;
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            margin: 0;
            font-size: 22px;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0;
            font-size: 14px;
        }

        .row {
            display: flex;
            margin-bottom: 12px;
        }

        .label {
            width: 200px;
            font-weight: bold;
        }

        .value {
            flex: 1;
            border-bottom: 1px dotted #999;
        }

        .payments-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            margin-bottom: 25px;
        }

        .payments-table th, .payments-table td {
            border: 1px solid #333;
            padding: 8px 12px;
            text-align: left;
        }

        .payments-table th {
            background-color: #f2f2f2;
        }

        .amount-box {
            border: 2px solid #333;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
            display: inline-block;
            margin-top: 20px;
        }

        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: flex-end;
        }

        .signature {
            text-align: center;
            width: 250px;
        }

        .signature p {
            margin: 0;
            line-height: 1.5;
        }

        @media print {
            @page {
                size: portrait;
                margin: 15mm;
            }

            body {
                background: white;
                padding: 0;
                margin: 0;
            }

            .receipt {
                box-shadow: none;
                width: 100%;
                max-width: 100%;
                padding: 0;
                border: none;
            }

            .no-print {
                display: none;
            }
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 90px;
            color: rgba(40, 167, 69, 0.08);
            pointer-events: none;
            text-transform: uppercase;
            font-weight: bold;
            border: 8px dashed rgba(40, 167, 69, 0.08);
            padding: 10px 30px;
            border-radius: 15px;
        }
    </style>
</head>

<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()"
            style="padding: 10px 20px; cursor: pointer; background: #28a745; color: white; border: none; border-radius: 5px; font-weight: bold; margin-right: 10px;">
            Cetak Invoice Sumbangan Komite
        </button>
        <button onclick="window.close()"
            style="padding: 10px 20px; cursor: pointer; background: #6c757d; color: white; border: none; border-radius: 5px;">
            Tutup
        </button>
    </div>

    <div class="receipt">
        <div class="watermark">KWITANSI</div>

        <div class="header">
            <h1>BUKTI SUMBANGAN DANA KOMITE</h1>
            <p>{{ $school->name ?? 'SMP NEGERI 6 SUDIMORO' }}</p>
            <p>{{ $school->address ?? 'Kec. Sudimoro, Kab. Pacitan' }}</p>
        </div>

        <div class="row">
            <div class="label">Nama Siswa</div>
            <div class="value"><strong>{{ $student->name }}</strong></div>
        </div>
        <div class="row">
            <div class="label">NIS</div>
            <div class="value">{{ $student->nis }}</div>
        </div>
        <div class="row">
            <div class="label">Kelas</div>
            <div class="value">{{ $student->schoolClass->name }} (Grade {{ $student->schoolClass->grade }})</div>
        </div>
        <div class="row">
            <div class="label">Tahun Ajaran</div>
            <div class="value">{{ $committeeFee->academicYear->year }}</div>
        </div>
        <div class="row">
            <div class="label">Total Kewajiban</div>
            <div class="value"><strong>Rp {{ number_format($committeeFee->amount, 0, ',', '.') }}</strong></div>
        </div>

        <h3 style="margin-top: 30px; margin-bottom: 10px; font-size: 14px; text-transform: uppercase;">Rincian Pembayaran (Cicilan)</h3>
        <table class="payments-table">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">No</th>
                    <th style="width: 150px;">Tanggal Bayar</th>
                    <th>Keterangan</th>
                    <th style="width: 200px; text-align: right;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $index => $payment)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                        <td>{{ $payment->notes ?? 'Cicilan Komite' }}</td>
                        <td style="text-align: right;">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr style="font-weight: bold; background-color: #f9f9f9;">
                    <td colspan="3" style="text-align: right;">Total Terbayar</td>
                    <td style="text-align: right;">Rp {{ number_format($totalPaid, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="row">
            <div class="label">Status Pembayaran</div>
            <div class="value" style="color: #28a745; font-weight: bold;">SUDAH MEMBAYAR</div>
        </div>

        <div class="amount-box">
            TERBILANG: Rp {{ number_format($totalPaid, 0, ',', '.') }},-
        </div>

        <div class="footer">
            <div class="signature">
                <p>Sudimoro, {{ date('d/m/Y') }}</p>
                <p style="margin-top: 5px;">Bendahara Komite,</p>
                <div style="margin-top: 60px;"><strong>( {{ Auth::user()->name }} )</strong></div>
            </div>
        </div>

        <div style="margin-top: 30px; font-size: 10px; color: #666; font-style: italic;">
            * Dokumen ini merupakan bukti pembayaran sumbangan dana komite yang sah.
            <br>Dicetak pada: {{ date('d/m/Y H:i:s') }} oleh {{ Auth::user()->name }}
        </div>
    </div>
</body>

</html>
