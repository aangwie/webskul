@extends('admin.layouts.app')

@section('title', 'Set Nominal Komite')
@section('page-title', 'Set Nominal Komite')

@section('content')
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-calendar-alt"></i> Pilih Tahun Pelajaran</h2>
    </div>
    <div class="card-body">
        <p class="mb-4" style="color: var(--text-light); margin-bottom: 20px;">Pilih tahun pelajaran untuk mengatur nominal dana komite per kelas.</p>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Tahun Pelajaran</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($academicYears as $year)
                    <tr>
                        <td><strong>{{ $year->year }}</strong></td>
                        <td>
                            @if($year->is_active)
                            <span class="badge badge-success">Aktif</span>
                            @else
                            <span class="badge badge-danger">Tidak Aktif</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.committee.nominal.set', $year->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-money-bill-wave"></i> Set Nominal
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 40px;">
                            <div style="color: var(--text-light); font-size: 3rem; margin-bottom: 20px; opacity: 0.3;">
                                <i class="fas fa-calendar-times"></i>
                            </div>
                            <p>Belum ada data tahun pelajaran.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection