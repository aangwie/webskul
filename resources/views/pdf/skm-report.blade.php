<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan SKM {{ $selectedYear }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.5;
            padding: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #1e3a5f;
        }
        .header h1 {
            font-size: 18px;
            color: #1e3a5f;
            margin: 0 0 5px;
        }
        .header h2 {
            font-size: 14px;
            color: #555;
            margin: 0;
            font-weight: normal;
        }
        .header p {
            font-size: 11px;
            color: #777;
            margin: 5px 0 0;
        }
        .ikm-box {
            background: #1e3a5f;
            color: white;
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .ikm-box h1 {
            font-size: 36px;
            margin: 5px 0;
        }
        .ikm-box p {
            font-size: 12px;
            margin: 3px 0;
            opacity: 0.9;
        }
        .stats-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
        }
        .stat-item {
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px 20px;
            flex: 1;
            margin: 0 5px;
        }
        .stat-item h3 {
            font-size: 20px;
            color: #1e3a5f;
            margin: 0;
        }
        .stat-item p {
            font-size: 10px;
            color: #666;
            margin: 3px 0 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th {
            background: #1e3a5f;
            color: white;
            padding: 8px 10px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }
        table td {
            padding: 7px 10px;
            border-bottom: 1px solid #eee;
            font-size: 10px;
        }
        table tr:nth-child(even) {
            background: #f9f9f9;
        }
        .section-title {
            font-size: 13px;
            color: #1e3a5f;
            border-bottom: 2px solid #1e3a5f;
            padding-bottom: 5px;
            margin: 20px 0 15px;
        }
        .footer {
            text-align: center;
            color: #999;
            font-size: 9px;
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }
        .note-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 10px 15px;
            border-radius: 6px;
            font-size: 10px;
            color: #856404;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN SURVEI KEPUASAN MASYARAKAT (SKM)</h1>
        <h2>{{ $school->name ?? 'SMP Negeri 6 Sudimoro' }}</h2>
        <p>Tahun {{ $selectedYear }}</p>
    </div>

    <div class="ikm-box">
        <p>Indeks Kepuasan Masyarakat (IKM)</p>
        <h1>{{ number_format($ikm, 2) }}</h1>
        <p>Skala 0 - 100</p>
    </div>

    <div class="stats-row">
        <div class="stat-item">
            <h3>{{ $respondentCount }}</h3>
            <p>Jumlah Responden</p>
        </div>
        <div class="stat-item">
            <h3>{{ number_format($totalCount > 0 ? ($ikm / 25) : 0, 2) }}</h3>
            <p>Rata-rata Nilai Unsur</p>
        </div>
        <div class="stat-item">
            <h3>{{ $distributions[1] + $distributions[2] + $distributions[3] + $distributions[4] }}</h3>
            <p>Total Jawaban</p>
        </div>
    </div>

    <h3 class="section-title">Distribusi Skor Penilaian</h3>
    <table>
        <thead>
            <tr>
                <th>Skor</th>
                <th>Keterangan</th>
                <th>Jumlah Jawaban</th>
                <th>Persentase</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalResponses = array_sum($distributions);
                $labels = ['1 - Sangat Buruk', '2 - Buruk', '3 - Baik', '4 - Sangat Baik'];
            @endphp
            @for($i = 1; $i <= 4; $i++)
                @php $pct = $totalResponses > 0 ? ($distributions[$i] / $totalResponses) * 100 : 0; @endphp
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ $i }}</td>
                    <td>{{ $labels[$i - 1] }}</td>
                    <td style="text-align: center;">{{ $distributions[$i] }}</td>
                    <td style="text-align: center;">{{ number_format($pct, 1) }}%</td>
                </tr>
            @endfor
            <tr style="font-weight: bold;">
                <td colspan="2" style="text-align: right;">Total</td>
                <td style="text-align: center;">{{ $totalResponses }}</td>
                <td style="text-align: center;">100%</td>
            </tr>
        </tbody>
    </table>

    <h3 class="section-title">Rata-rata Skor Per Pertanyaan</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Pertanyaan</th>
                <th>Jumlah Jawaban</th>
                <th>Rata-rata Skor</th>
                <th>Nilai IKM</th>
            </tr>
        </thead>
        <tbody>
            @foreach($questions as $q)
                @if(isset($averages[$q->id]))
                    @php
                        $avg = $averages[$q->id]['average'];
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td>{{ $q->question_text }}</td>
                        <td style="text-align: center;">{{ $averages[$q->id]['count'] }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ number_format($avg, 2) }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ number_format($avg * 25, 2) }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background: #e8f0fe; font-weight: bold;">
                <td colspan="2" style="text-align: right;">Total Rata-rata</td>
                <td style="text-align: center;">{{ $totalCount }}</td>
                <td style="text-align: center;">{{ number_format($totalCount > 0 ? ($ikm / 25) : 0, 2) }}</td>
                <td style="text-align: center; color: #1e3a5f;">{{ number_format($ikm, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    @if($includeRespondents)
        <h3 class="section-title">Daftar Responden</h3>
        <div class="note-box">
            <strong>Informasi:</strong> Laporan ini menyertakan data responden sebanyak {{ $respondentCount }} orang.
        </div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>No Telpon</th>
                    <th>Tanggal</th>
                    <th>IKM</th>
                </tr>
            </thead>
            <tbody>
                @foreach($respondents as $r)
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td>{{ $r->name }}</td>
                        <td>{{ $r->address }}</td>
                        <td>{{ $r->phone }}</td>
                        <td style="text-align: center;">{{ $r->created_at->format('d/m/Y') }}</td>
                        <td style="text-align: center;">{{ number_format($r->average_score * 25, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="note-box" style="background: #e8f0fe; border-color: #1e3a5f; color: #1e3a5f;">
            <strong>Informasi:</strong> Data responden tidak disertakan dalam laporan ini. Total responden: {{ $respondentCount }} orang.
        </div>
    @endif

    <div class="footer">
        <p>Laporan ini dihasilkan secara otomatis dari sistem e-SKM {{ $school->name ?? 'SMP Negeri 6 Sudimoro' }}</p>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>