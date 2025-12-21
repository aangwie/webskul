<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Bukti Pendaftaran - {{ $registration->registration_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #1e3a5f;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .school-name {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a5f;
            margin: 0;
        }

        .school-info {
            font-size: 10px;
            color: #666;
            margin: 5px 0;
        }

        .title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 20px;
            text-decoration: underline;
        }

        .reg-number-box {
            background: #f4f4f4;
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            margin-bottom: 30px;
        }

        .reg-number-label {
            font-size: 10px;
            color: #666;
            margin-bottom: 5px;
        }

        .reg-number-value {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a5f;
            letter-spacing: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            text-align: left;
            padding: 8px;
            vertical-align: top;
        }

        .label {
            width: 30%;
            font-weight: bold;
            color: #666;
        }

        .divider {
            border-bottom: 1px solid #eee;
            margin: 10px 0;
        }

        .section-title {
            font-weight: bold;
            color: #1e3a5f;
            border-left: 3px solid #1e3a5f;
            padding-left: 10px;
            margin: 20px 0 10px 0;
            background: #f9f9f9;
        }

        .footer {
            margin-top: 50px;
        }

        .signature-table {
            width: 100%;
        }

        .signature-space {
            height: 60px;
        }

        .note {
            font-size: 10px;
            color: #666;
            border: 1px dashed #ccc;
            padding: 10px;
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1 class="school-name">{{ $school->name ?? 'SMP NEGERI 6 SUDIMORO' }}</h1>
        <p class="school-info">{{ $school->address ?? 'Sudimoro, Pacitan, Jawa Timur' }}</p>
        <p class="school-info">Telp: {{ $school->phone ?? '-' }} | Email: {{ $school->email ?? '-' }}</p>
    </div>

    <div class="title">Bukti Pendaftaran Calon Murid Baru</div>

    <div class="reg-number-box">
        <div class="reg-number-label">NOMOR PENDAFTARAN</div>
        <div class="reg-number-value">{{ $registration->registration_number }}</div>
    </div>

    <div class="section-title">Data Calon Murid</div>
    <table>
        <tr>
            <td class="label">Nama Lengkap</td>
            <td>: {{ $registration->nama }}</td>
        </tr>
        <tr>
            <td class="label">NISN</td>
            <td>: {{ $registration->nisn }}</td>
        </tr>
        <tr>
            <td class="label">NIK</td>
            <td>: {{ $registration->nik }}</td>
        </tr>
        <tr>
            <td class="label">Tahun Pelajaran</td>
            <td>: {{ $registration->academic_year }}</td>
        </tr>
        <tr>
            <td class="label">Tempat, Tgl Lahir</td>
            <td>: {{ $registration->birth_place }}, {{ $registration->birth_date->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Jenis Pendaftaran</td>
            <td>: {{ $registration->registration_type == 'baru' ? 'Murid Baru' : 'Pindahan' }}</td>
        </tr>
        <tr>
            <td class="label">Alamat</td>
            <td>: {{ $registration->address }}</td>
        </tr>
    </table>

    <div class="section-title">Data Orang Tua / Wali</div>
    <table>
        <tr>
            <td class="label">Nama Ibu</td>
            <td>: {{ $registration->mother_name }}</td>
        </tr>
        <tr>
            <td class="label">Nama Ayah</td>
            <td>: {{ $registration->father_name }}</td>
        </tr>
        <tr>
            <td class="label">Nomor HP</td>
            <td>: {{ $registration->phone_number }}</td>
        </tr>
    </table>

    <div class="note">
        <strong>Catatan Penting:</strong><br>
        1. Simpan dokumen ini sebagai bukti pendaftaran yang sah.<br>
        2. Gunakan nomor pendaftaran di atas untuk mengecek status pendaftaran melalui website.<br>
        3. Bawalah dokumen fisik (Asli & Fotocopy) saat melakukan verifikasi di sekolah.
    </div>

    <div class="footer">
        <table class="signature-table">
            <tr>
                <td style="width: 60%;">
                    <img src="data:image/svg+xml;base64,{{ base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(80)->generate('Nama: ' . $registration->nama . ' | No. Pendaftaran: ' . $registration->registration_number)) }}">
                </td>
                <td style="text-align: center;">
                    Sudimoro, {{ now()->translatedFormat('d F Y') }}<br>
                    Panitia PMB
                    <div class="signature-space"></div>
                    (________________________)
                </td>
            </tr>
        </table>
    </div>
</body>

</html>