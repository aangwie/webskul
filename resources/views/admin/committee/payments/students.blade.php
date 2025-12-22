@extends('admin.layouts.app')

@section('title', 'Siswa Kelas ' . $schoolClass->name)
@section('page-title', 'Daftar Siswa - ' . $schoolClass->name)

@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <h2><i class="fas fa-user-graduate"></i> Status Pembayaran Komite</h2>
            <p style="font-size: 0.85rem; color: var(--text-light); margin-top: 5px;">
                Tahun Ajaran: {{ $committeeFee->academicYear->year }} |
                Tagihan: <strong>Rp {{ number_format($committeeFee->amount, 0, ',', '.') }}</strong>
            </p>
        </div>
        <a href="{{ route('admin.committee.payments.index') }}" class="btn btn-sm btn-danger">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Total Bayar</th>
                        <th>Sisa Tagihan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr>
                        <td>{{ $student->nis }}</td>
                        <td><strong>{{ $student->name }}</strong></td>
                        <td>Rp {{ number_format($student->total_paid, 0, ',', '.') }}</td>
                        <td>
                            @if($student->remaining > 0)
                            <span style="color: var(--danger); font-weight: 600;">
                                Rp {{ number_format($student->remaining, 0, ',', '.') }}
                            </span>
                            @else
                            <span style="color: var(--success); font-weight: 600;">Lunas</span>
                            @endif
                        </td>
                        <td>
                            @if($student->is_paid_full)
                            <span class="badge badge-success">Lunas</span>
                            @elseif($student->total_paid > 0)
                            <span class="badge badge-warning">Dicicil</span>
                            @else
                            <span class="badge badge-danger">Belum Bayar</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.committee.payments.record', $student->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-money-check-alt"></i> Bayar / Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection