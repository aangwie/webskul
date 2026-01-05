@extends('admin.layouts.app')

@section('title', 'Pembayaran Dana Komite')
@section('page-title', 'Pembayaran Dana Komite')

@section('content')
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-layer-group"></i> Pilih Kelas</h2>
    </div>
    <div class="card-body">
        <p class="mb-4" style="color: var(--text-light); margin-bottom: 20px;">Pilih kelas untuk melihat daftar siswa dan mengelola pembayaran dana komite.</p>

        <div class="stats-grid">
            @foreach($classes as $class)
            <div class="stat-card" style="cursor: pointer; position: relative;" onclick="window.location='{{ route('admin.committee.payments.students', $class->id) }}';">
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
    </div>
</div>
@endsection