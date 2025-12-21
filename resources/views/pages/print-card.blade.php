<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Pendaftaran - {{ $registration->registration_number }}</title>
    @if(isset($school) && $school && $school->logo)
    <link rel="icon" type="image/png" href="{{ asset('storage/' . $school->logo) }}">
    @else
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @endif
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 40px;
            color: #333;
        }

        .card-container {
            width: 800px;
            margin: 0 auto;
            border: 2px solid #1e3a5f;
            padding: 30px;
            position: relative;
        }

        .header {
            display: flex;
            align-items: center;
            border-bottom: 3px double #1e3a5f;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .logo {
            width: 80px;
            margin-right: 25px;
        }

        .school-info h1 {
            margin: 0;
            font-size: 1.5rem;
            color: #1e3a5f;
        }

        .school-info p {
            margin: 5px 0 0;
            font-size: 0.9rem;
            color: #666;
        }

        .title {
            text-align: center;
            font-size: 1.2rem;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 30px;
            text-decoration: underline;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .info-group {
            margin-bottom: 15px;
        }

        .label {
            font-size: 0.8rem;
            color: #666;
            margin-bottom: 2px;
        }

        .value {
            font-weight: 600;
            font-size: 1rem;
        }

        .registration-number {
            text-align: center;
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 30px;
            border-radius: 8px;
        }

        .registration-number .num {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e3a5f;
            letter-spacing: 2px;
        }

        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .qr-placeholder {
            width: 100px;
            height: 100px;
        }

        .signature {
            text-align: center;
            width: 250px;
        }

        .signature p {
            margin-bottom: 60px;
        }

        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #1e3a5f;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }

        @media print {
            .print-btn {
                display: none;
            }

            body {
                padding: 0;
            }

            .card-container {
                border: none;
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <button class="print-btn" onclick="window.print()">
        <i class="fas fa-print"></i> Cetak Kartu
    </button>

    <div class="card-container">
        <div class="header">
            @php $school = \App\Models\SchoolProfile::first(); @endphp
            @if($school && $school->logo)
            @if(Str::startsWith($school->logo, 'data:'))
            <img src="{{ $school->logo }}" style="width: 10%; height: 10%;">
            @else
            <img src="{{ asset('storage/' . $school->logo) }}" style="width: 10%; height: 10%;">
            @endif
            @endif
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <div class="school-info">
                <h1>{{ $school->name ?? 'SMP NEGERI 6 SUDIMORO' }}</h1>
                <p>{{ $school->address ?? 'Sudimoro, Pacitan, Jawa Timur' }}</p>
                <p>Telp: {{ $school->phone ?? '-' }} | Email: {{ $school->email ?? '-' }}</p>
            </div>
        </div>

        <div class="title">Kartu Bukti Pendaftaran Calon Murid Baru</div>

        <div class="registration-number">
            <div class="label">NOMOR PENDAFTARAN</div>
            <div class="num">{{ $registration->registration_number }}</div>
        </div>

        <div class="content-grid">
            <div class="left">
                <div class="info-group">
                    <div class="label">Nama Lengkap</div>
                    <div class="value">{{ $registration->nama }}</div>
                </div>
                <div class="info-group">
                    <div class="label">NISN</div>
                    <div class="value">{{ $registration->nisn }}</div>
                </div>
                <div class="info-group">
                    <div class="label">NIK</div>
                    <div class="value">{{ $registration->nik }}</div>
                </div>
                <div class="info-group">
                    <div class="label">Tahun Pelajaran</div>
                    <div class="value">{{ $registration->academic_year }}</div>
                </div>
            </div>
            <div class="right">
                <div class="info-group">
                    <div class="label">Tempat, Tanggal Lahir</div>
                    <div class="value">{{ $registration->birth_place }}, {{ $registration->birth_date->translatedFormat('d F Y') }}</div>
                </div>
                <div class="info-group">
                    <div class="label">Jenis Pendaftaran</div>
                    <div class="value">{{ $registration->registration_type == 'baru' ? 'Murid Baru' : 'Pindahan' }}</div>
                </div>
                <div class="info-group">
                    <div class="label">Nama Ibu</div>
                    <div class="value">{{ $registration->mother_name }}</div>
                </div>
                <div class="info-group">
                    <div class="label">Nomor HP</div>
                    <div class="value">{{ $registration->phone_number }}</div>
                </div>
            </div>
        </div>

        <div style="margin-top: 30px; font-size: 0.85rem; color: #666; line-height: 1.5; border: 1px solid #eee; padding: 15px; border-radius: 8px;">
            <strong>Catatan:</strong><br>
            1. Harap cetak dan simpan kartu ini sebagai bukti pendaftaran yang sah.<br>
            2. Bawalah kartu ini saat melakukan verifikasi berkas di sekolah sesuai jadwal yang ditentukan.<br>
            3. Pastikan data yang anda masukkan sudah benar dan sesuai dengan dokumen asli.
        </div>

        <div class="footer">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 60%; padding-left: 20px;">
                        <div class="qr-placeholder">
                            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(100)->generate('Nama: ' . $registration->nama . ' | No. Pendaftaran: ' . $registration->registration_number) !!}
                        </div>
                    </td>
                    <td style="width: 40%; text-align: left;">
                        <div class="signature">
                            <p style="text-align: left;">Sudimoro, {{ now()->translatedFormat('d F Y') }}<br><br>Panitia PMB {{ $school->name ?? '' }}</p>
                            <div style="border-bottom: 1px solid #333; width: 200px;"></div>
                            <p style="margin-top: 5px; font-size: 0.8rem; text-align: left;">(Tanda Tangan & Cap Panitia)</p>
                        </div>
                    </td>
                </tr>
            </table>

        </div>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
</body>

</html>