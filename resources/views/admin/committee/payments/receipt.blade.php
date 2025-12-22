<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Pembayaran - {{ $committeePayment->student->name }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            background: #f0f0f0;
            padding: 20px;
            color: #333;
        }

        .receipt {
            background: white;
            width: 800px;
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
            font-size: 24px;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0;
            font-size: 14px;
        }

        .row {
            display: flex;
            margin-bottom: 15px;
        }

        .label {
            width: 200px;
            font-weight: bold;
        }

        .value {
            flex: 1;
            border-bottom: 1px dotted #999;
        }

        .amount-box {
            border: 2px solid #333;
            padding: 15px;
            font-size: 20px;
            font-weight: bold;
            display: inline-block;
            margin-top: 30px;
        }

        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: flex-end;
        }

        .signature {
            text-align: center;
            width: 200px;
        }

        .signature p {
            margin-top: 80px;
            border-top: 1px solid #333;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .receipt {
                box-shadow: none;
                width: 100%;
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
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(0, 0, 0, 0.05);
            pointer-events: none;
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #1e3a5f; color: white; border: none; border-radius: 5px;">
            <i class="fas fa-print"></i> Cetak Kwitansi
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer; background: #6c757d; color: white; border: none; border-radius: 5px;">
            Tutup
        </button>
    </div>

    <div class="receipt">
        <div class="watermark">KWITANSI</div>

        <div class="header">
            <h1>BUKTI PEMBAYARAN KOMITE</h1>
            <p>{{ $school->name ?? 'SMP NEGERI 6 SUDIMORO' }}</p>
            <p>{{ $school->address ?? 'Kec. Sudimoro, Kab. Pacitan' }}</p>
        </div>

        <div class="row">
            <div class="label">Nomor Transaksi</div>
            <div class="value">#KM-{{ str_pad($committeePayment->id, 5, '0', STR_PAD_LEFT) }}</div>
        </div>
        <div class="row">
            <div class="label">Tanggal</div>
            <div class="value">{{ $committeePayment->payment_date->format('d F Y') }}</div>
        </div>
        <div class="row">
            <div class="label">Telah Terima Dari</div>
            <div class="value"><strong>{{ $committeePayment->student->name }}</strong> (NIS: {{ $committeePayment->student->nis }})</div>
        </div>
        <div class="row">
            <div class="label">Kelas</div>
            <div class="value">{{ $committeePayment->student->schoolClass->name }} (Grade {{ $committeePayment->student->schoolClass->grade }})</div>
        </div>
        <div class="row">
            <div class="label">Tahun Ajaran</div>
            <div class="value">{{ $committeePayment->committeeFee->academicYear->year }}</div>
        </div>
        <div class="row">
            <div class="label">Untuk Pembayaran</div>
            <div class="value">Dana Komite Sekolah ({{ $committeePayment->notes ?? 'Angsuran' }})</div>
        </div>
        <div class="row">
            <div class="label">Status</div>
            <div class="value"><strong>{{ $isPaidFull ? 'LUNAS' : 'BELUM LUNAS' }}</strong></div>
        </div>

        <div class="amount-box">
            TERBILANG: Rp {{ number_format($committeePayment->amount, 0, ',', '.') }},-
        </div>

        <div class="footer">
            <div>
                <p>Sudimoro, {{ $committeePayment->payment_date->format('d/m/Y') }}</p>
                <p style="margin-top: 5px;">Bendahara Komite,</p>
                <div style="margin-top: 60px;"><strong>( {{ Auth::user()->name }} )</strong></div>
            </div>
        </div>

        <div style="margin-top: 30px; font-size: 10px; color: #666; font-style: italic;">
            * Simpan bukti pembayaran ini sebagai tanda bukti yang sah.
            <br>Dicetak pada: {{ date('d/m/Y H:i:s') }}
        </div>
    </div>
</body>

</html>