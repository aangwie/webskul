@extends('admin.layouts.app')

@section('title', 'Pembayaran Dana Komite')
@section('page-title', 'Pembayaran Dana Komite')

@section('content')
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-layer-group"></i> Pilih Kelas</h2>
        </div>
        <div class="card-body">
            <!-- Filter Tahun Ajaran -->
            <div class="mb-4">
                <form method="GET" action="{{ route('admin.committee.payments.index') }}" class="d-flex align-items-center gap-3">
                    <label for="academic_year_id" class="mb-0" style="font-weight: 500;">Tahun Ajaran:</label>
                    <select name="academic_year_id" id="academic_year_id" class="form-control" style="width: 200px;" onchange="this.form.submit()">
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ $selectedYear && $selectedYear->id == $year->id ? 'selected' : '' }}>
                                {{ $year->year }} {{ $year->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @if($selectedYear)
                        <span class="badge" style="background: var(--primary); color: white; padding: 8px 12px;">
                            <i class="fas fa-calendar"></i> {{ $selectedYear->year }}
                        </span>
                    @endif
                </form>
            </div>

            <p class="mb-4" style="color: var(--text-light); margin-bottom: 20px;">Pilih kelas untuk melihat daftar siswa
                dan mengelola sumbangan dana komite.</p>

            @if(!$selectedYear)
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> Silakan pilih tahun ajaran terlebih dahulu untuk melihat data pembayaran.
                </div>
            @else
                <div class="stats-grid">
                    @foreach($classes as $class)
                        <div class="stat-card" style="cursor: pointer; position: relative;"
                            onclick="window.location='{{ route('admin.committee.payments.students', ['schoolClass' => $class->id, 'academic_year_id' => $selectedYear->id]) }}';">
                            <div class="stat-icon primary">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-info">
                                <h3>{{ $class->name }}</h3>
                                <p>Kelas {{ $class->grade }}</p>
                            </div>
                            <div style="position: absolute; right: 20px; color: var(--text-light); opacity: 0.5;">
                                <i class="fas fa-arrow-down"></i>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($classes->isEmpty())
                    <div style="text-align: center; padding: 40px;">
                        <div style="color: var(--text-light); font-size: 3rem; margin-bottom: 20px; opacity: 0.3;">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <p>Belum ada data kelas.</p>
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
