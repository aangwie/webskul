@extends('admin.layouts.app')

@section('title', 'Laporan Dana Komite')
@section('page-title', 'Laporan Dana Komite')

@section('content')
    <div class="card" style="max-width: 900px;">
        <div class="card-header">
            <h2><i class="fas fa-file-alt"></i> Generate Laporan</h2>
        </div>
        <div class="card-body">
            @if($activeYear)
                <div style="margin-bottom: 30px;">
                    <h3 style="margin-bottom: 15px; color: var(--primary);">
                        <i class="fas fa-chart-pie"></i> Ringkasan Pembayaran TA {{ $activeYear->year }}
                    </h3>
                    <div class="table-responsive"
                        style="background: white; border-radius: 12px; border: 1px solid var(--accent);">
                        <table>
                            <thead>
                                <tr style="background: var(--accent);">
                                    <th>Kelas</th>
                                    <th style="text-align: center;">Siswa</th>
                                    <th style="text-align: right;">Target</th>
                                    <th style="text-align: right;">Terbayar</th>
                                    <th style="text-align: right;">Sisa</th>
                                    <th style="text-align: center;">Progres</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $grandTarget = 0;
                                    $grandPaid = 0;
                                $grandStudents = 0; @endphp
                                @foreach($classSummaries as $summary)
                                    @php
                                        $grandTarget += $summary['total_target'];
                                        $grandPaid += $summary['total_paid'];
                                        $grandStudents += $summary['total_students'];
                                        $percent = $summary['total_target'] > 0 ? ($summary['total_paid'] / $summary['total_target']) * 100 : 0;
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $summary['class']->name }}</strong></td>
                                        <td style="text-align: center;">{{ $summary['total_students'] }}</td>
                                        <td style="text-align: right;">Rp {{ number_format($summary['total_target'], 0, ',', '.') }}
                                        </td>
                                        <td style="text-align: right; color: var(--success); font-weight: 600;">Rp
                                            {{ number_format($summary['total_paid'], 0, ',', '.') }}</td>
                                        <td style="text-align: right; color: var(--danger);">Rp
                                            {{ number_format($summary['remaining'], 0, ',', '.') }}</td>
                                        <td style="text-align: center; width: 100px;">
                                            <div
                                                style="width: 100%; background: #eee; border-radius: 10px; height: 8px; overflow: hidden;">
                                                <div style="width: {{ $percent }}%; background: var(--success); height: 100%;">
                                                </div>
                                            </div>
                                            <span
                                                style="font-size: 0.75rem; color: var(--success); font-weight: bold;">{{ number_format($percent, 1) }}%</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot style="background: #f8f9fa; font-weight: bold; border-top: 2px solid #ddd;">
                                <tr>
                                    <td>TOTAL SEMUA</td>
                                    <td style="text-align: center;">{{ $grandStudents }}</td>
                                    <td style="text-align: right;">Rp {{ number_format($grandTarget, 0, ',', '.') }}</td>
                                    <td style="text-align: right; color: var(--success);">Rp
                                        {{ number_format($grandPaid, 0, ',', '.') }}</td>
                                    <td style="text-align: right; color: var(--danger);">Rp
                                        {{ number_format(max(0, $grandTarget - $grandPaid), 0, ',', '.') }}</td>
                                    <td style="text-align: center;">
                                        @php $totalPercent = $grandTarget > 0 ? ($grandPaid / $grandTarget) * 100 : 0; @endphp
                                        <span style="color: var(--primary);">{{ number_format($totalPercent, 1) }}%</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif

            <div class="card" style="max-width: 100%; border: 1px solid var(--accent);">
                <div class="card-header" style="background: var(--accent);">
                    <h2 style="font-size: 1.1rem; margin-bottom: 0;"><i class="fas fa-file-export"></i> Generate Laporan
                        Detail / Per Siswa</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.committee.report.generate') }}" method="POST">
                        @csrf

                        {{-- Filter Type Selection --}}
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label class="form-label">Jenis Filter <span style="color: var(--danger);">*</span></label>
                            <div style="display: flex; gap: 20px; margin-top: 10px;">
                                <label
                                    style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 10px 15px; border: 2px solid var(--accent); border-radius: 8px;"
                                    id="filter-academic-label">
                                    <input type="radio" name="filter_type" value="academic_year" checked
                                        style="accent-color: var(--primary);" id="filter-academic">
                                    <div>
                                        <strong style="font-size: 0.9rem;">Tahun Ajaran</strong>
                                        <span style="font-size: 0.75rem; color: var(--text-light); display: block;">Filter
                                            berdasarkan tahun ajaran</span>
                                    </div>
                                </label>
                                <label
                                    style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 10px 15px; border: 2px solid var(--accent); border-radius: 8px;"
                                    id="filter-date-label">
                                    <input type="radio" name="filter_type" value="date_period"
                                        style="accent-color: var(--primary);" id="filter-date">
                                    <div>
                                        <strong style="font-size: 0.9rem;">Periode Tanggal</strong>
                                        <span style="font-size: 0.75rem; color: var(--text-light); display: block;">Filter
                                            berdasarkan rentang tanggal</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            {{-- Academic Year Filter --}}
                            <div class="form-group" id="academic-year-group">
                                <label class="form-label">Tahun Ajaran <span style="color: var(--danger);">*</span></label>
                                <select name="academic_year_id" id="academic_year_id" class="form-select">
                                    <option value="">-- Pilih Tahun Ajaran --</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ $year->is_active ? 'selected' : '' }}>
                                            {{ $year->year }} {{ $year->is_active ? '' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Date Period Filter (Hidden by default) --}}
                            <div class="form-group" id="date-from-group" style="display: none;">
                                <label class="form-label">Dari Tanggal <span style="color: var(--danger);">*</span></label>
                                <input type="date" name="date_from" id="date_from" class="form-control"
                                    value="{{ date('Y-01-01') }}">
                            </div>
                            <div class="form-group" id="date-to-group" style="display: none;">
                                <label class="form-label">Sampai Tanggal <span
                                        style="color: var(--danger);">*</span></label>
                                <input type="date" name="date_to" id="date_to" class="form-control"
                                    value="{{ date('Y-12-31') }}">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Kelas <span style="color: var(--danger);">*</span></label>
                                <select name="school_class_id" id="school_class_id" class="form-select" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    <option value="all">-- Semua Kelas --</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }} (Kelas {{ $class->grade }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Jenis Laporan <span style="color: var(--danger);">*</span></label>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 10px;">
                                <label
                                    style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 12px 15px; border: 2px solid var(--accent); border-radius: 10px;">
                                    <input type="radio" name="report_type" value="recapitulation" checked
                                        style="accent-color: var(--primary);">
                                    <div>
                                        <strong style="display: block; font-size: 0.9rem;">Rekapitulasi</strong>
                                        <span style="font-size: 0.75rem; color: var(--text-light);">Ringkasan per
                                            siswa</span>
                                    </div>
                                </label>
                                <label
                                    style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 12px 15px; border: 2px solid var(--accent); border-radius: 10px;">
                                    <input type="radio" name="report_type" value="detail"
                                        style="accent-color: var(--primary);">
                                    <div>
                                        <strong style="display: block; font-size: 0.9rem;">Detail</strong>
                                        <span style="font-size: 0.75rem; color: var(--text-light);">Rincian tiap
                                            pembayaran</span>
                                    </div>
                                </label>
                                <label
                                    style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 12px 15px; border: 2px solid var(--accent); border-radius: 10px;">
                                    <input type="radio" name="report_type" value="class_summary"
                                        style="accent-color: var(--primary);">
                                    <div>
                                        <strong style="display: block; font-size: 0.9rem;">Rekapitulasi Per Kelas</strong>
                                        <span style="font-size: 0.75rem; color: var(--text-light);">Total per kelas (Pilih
                                            Semua Kelas)</span>
                                    </div>
                                </label>
                                <label
                                    style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 12px 15px; border: 2px solid var(--accent); border-radius: 10px;">
                                    <input type="radio" name="report_type" value="all_summary"
                                        style="accent-color: var(--primary);">
                                    <div>
                                        <strong style="display: block; font-size: 0.9rem;">Rekap Semua Kelas</strong>
                                        <span style="font-size: 0.75rem; color: var(--text-light);">Total gabungan (Pilih
                                            Semua Kelas)</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div style="display: flex; gap: 10px; margin-top: 25px;">
                            <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">
                                <i class="fas fa-search"></i> Tampilkan Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Filter type toggle
        function toggleFilterType() {
            const filterType = document.querySelector('input[name="filter_type"]:checked').value;
            const academicYearGroup = document.getElementById('academic-year-group');
            const dateFromGroup = document.getElementById('date-from-group');
            const dateToGroup = document.getElementById('date-to-group');
            const academicYearSelect = document.getElementById('academic_year_id');
            const filterAcademicLabel = document.getElementById('filter-academic-label');
            const filterDateLabel = document.getElementById('filter-date-label');

            if (filterType === 'academic_year') {
                academicYearGroup.style.display = 'block';
                dateFromGroup.style.display = 'none';
                dateToGroup.style.display = 'none';
                academicYearSelect.setAttribute('required', 'required');
                document.getElementById('date_from').removeAttribute('required');
                document.getElementById('date_to').removeAttribute('required');
                filterAcademicLabel.style.borderColor = 'var(--primary)';
                filterAcademicLabel.style.background = 'rgba(30, 58, 95, 0.05)';
                filterDateLabel.style.borderColor = 'var(--accent)';
                filterDateLabel.style.background = 'transparent';
            } else {
                academicYearGroup.style.display = 'none';
                dateFromGroup.style.display = 'block';
                dateToGroup.style.display = 'block';
                academicYearSelect.removeAttribute('required');
                document.getElementById('date_from').setAttribute('required', 'required');
                document.getElementById('date_to').setAttribute('required', 'required');
                filterDateLabel.style.borderColor = 'var(--primary)';
                filterDateLabel.style.background = 'rgba(30, 58, 95, 0.05)';
                filterAcademicLabel.style.borderColor = 'var(--accent)';
                filterAcademicLabel.style.background = 'transparent';
            }
        }

        document.getElementsByName('filter_type').forEach(radio => {
            radio.addEventListener('change', toggleFilterType);
        });

        // Initialize on page load
        toggleFilterType();

        // Report type toggle
        document.getElementsByName('report_type').forEach(radio => {
            radio.addEventListener('change', function () {
                const classSelect = document.getElementById('school_class_id');
                if (this.value === 'class_summary' || this.value === 'all_summary') {
                    classSelect.value = 'all';
                    classSelect.setAttribute('readonly', true);
                    classSelect.style.background = '#f8f9fa';
                } else {
                    if (classSelect.value === 'all') classSelect.value = '';
                    classSelect.removeAttribute('readonly');
                    classSelect.style.background = 'white';
                }
            });
        });
    </script>
    </div>
    </div>
@endsection