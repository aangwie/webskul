<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #eee;
            border-radius: 10px;
        }

        .header {
            background: #1e3a5f;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }

        .content {
            padding: 30px;
            background: #f9f9f9;
        }

        .code-box {
            background: #fff;
            padding: 20px;
            text-align: center;
            border: 2px dashed #1e3a5f;
            border-radius: 10px;
            margin: 20px 0;
        }

        .code {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 2px;
            color: #1e3a5f;
        }

        .footer {
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }

        .btn {
            display: inline-block;
            padding: 12px 25px;
            background: #1e3a5f;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Kode Aduan Masyarakat</h1>
        </div>
        <div class="content">
            <p>Halo <strong>{{ $complaint->name }}</strong>,</p>
            <p>Terima kasih telah menyampaikan aduan/saran kepada kami. Berikut adalah kode unik aduan Anda yang dapat digunakan untuk melihat perkembangan respon dari sekolah:</p>

            <div class="code-box">
                <div class="code">{{ $complaint->complaint_code }}</div>
            </div>

            <p>Anda dapat mengecek status aduan Anda melalui tautan di bawah ini:</p>
            <div style="text-align: center;">
                <a href="{{ route('public-complaints.status') }}" class="btn">Cek Status Aduan</a>
            </div>

            <p style="margin-top: 30px;">Terima kasih atas partisipasi Anda.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>