<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan SKM {{ $selectedYear }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.5;
            padding: 25px;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #1e3a5f;
        }
        .header h1 {
            font-size: 16px;
            color: #1e3a5f;
            margin: 0 0 5px;
        }
        .header h2 {
            font-size: 12px;
            color: #555;
            margin: 0;
            font-weight: normal;
        }
        .header p {
            font-size: 10px;
            color: #777;
            margin: 5px 0 0;
        }

        /* Summary Table Style */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .summary-table th {
            background: #1e3a5f;
            color: white;
            padding: 10px 12px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }
        .summary-table td {
            padding: 12px 12px;
            border-bottom: 1px solid #dee2e6;
            font-size: 10px;
            vertical-align: middle;
        }
        .summary-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        .summary-table .no-col {
            text-align: center;
            font-weight: bold;
            width: 50px;
        }
        .summary-table .value-col {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            color: #1e3a5f;
            width: 140px;
        }
        .summary-table .desc-col {
            color: #666;
            font-size: 9px;
        }
        .category-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            color: white;
        }

        /* Existing styles */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table th {
            background: #1e3a5f;
            color: white;
            padding: 7px 8px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
        }
        table.data-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #eee;
            font-size: 9px;
        }
        table.data-table tr:nth-child(even) {
            background: #f9f9f9;
        }

        .section-title {
            font-size: 12px;
            color: #1e3a5f;
            border-bottom: 2px solid #1e3a5f;
            padding-bottom: 5px;
            margin: 18px 0 12px;
        }
        .footer {
            text-align: center;
            color: #999;
            font-size: 8px;
            margin-top: 35px;
            padding-top: 12px;
            border-top: 1px solid #ddd;
        }
        .note-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 9px;
            color: #856404;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN SURVEI KEPUASAN MASYARAKAT (SKM)</h1>
        <h2>{{ $school->name ?? 'SMP Negeri 6 Sudimoro' }}</h2>
        <p>Tahun {{ $selectedYear }}</p>
    </div>

    <!-- Summary Table: IKM, Respondents, Avg, Total -->
    @php
        $ikmCategory = $ikm >= 88 ? 'Sangat Baik' : ($ikm >= 76 ? 'Baik' : ($ikm >= 62 ? 'Cukup' : 'Kurang'));
        $ikmColor = $ikm >= 88 ? '#198754' : ($ikm >= 76 ? '#0d6efd' : ($ikm >= 62 ? '#ffc107' : '#dc3545'));
    @endphp
    <table class="summary-table">
        <thead>
            <tr>
                <th style="width: 50px; text-align: center;">No</th>
                <th>Indikator</th>
                <th style="width: 140px; text-align: center;">Nilai</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="no-col">1</td>
                <td><strong>Indeks Kepuasan Masyarakat (IKM)</strong></td>
                <td class="value-col">{{ number_format($ikm, 2) }}</td>
                <td class="desc-col">
                    <span class="category-badge" style="background: {{ $ikmColor }};">
                        {{ $ikmCategory }}
                    </span>
                    &nbsp;(Skala 0 - 100)
                </td>
            </tr>
            <tr>
                <td class="no-col">2</td>
                <td><strong>Jumlah Responden</strong></td>
                <td class="value-col" style="color: #198754;">{{ $respondentCount }}</td>
                <td class="desc-col">Orang yang telah berpartisipasi dalam survei</td>
            </tr>
            <tr>
                <td class="no-col">3</td>
                <td><strong>Rata-rata Nilai Unsur</strong></td>
                <td class="value-col" style="color: #d4af37;">{{ number_format($totalCount > 0 ? ($ikm / 25) : 0, 2) }}</td>
                <td class="desc-col">Rata-rata dari seluruh skor penilaian (Skala 1 - 4)</td>
            </tr>
            <tr>
                <td class="no-col">4</td>
                <td><strong>Total Jawaban</strong></td>
                <td class="value-col" style="color: #ffc107;">{{ $distributions[1] + $distributions[2] + $distributions[3] + $distributions[4] }}</td>
                <td class="desc-col">Total seluruh jawaban dari semua responden</td>
            </tr>
        </tbody>
    </table>

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