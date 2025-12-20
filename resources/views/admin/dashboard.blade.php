@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="stats-grid">
    @if(auth()->user()->isAdmin() || auth()->user()->isTeacher())
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <div class="stat-info">
            <h3>{{ $stats['teachers'] }}</h3>
            <p>Total Guru</p>
        </div>
    </div>
    @endif
    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-newspaper"></i>
        </div>
        <div class="stat-info">
            <h3>{{ $stats['activities'] }}</h3>
            <p>Total Kegiatan</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-info">
            <h3>{{ $stats['published_activities'] }}</h3>
            <p>Dipublikasikan</p>
        </div>
    </div>
    @if(auth()->user()->isAdmin() || auth()->user()->isTeacher())
    <div class="stat-card">
        <div class="stat-icon gold">
            <i class="fas fa-bullhorn"></i>
        </div>
        <div class="stat-info">
            <h3>{{ $stats['informations'] }}</h3>
            <p>Total Informasi</p>
        </div>
    </div>
    @endif
</div>

<div class="card">
    <div class="card-header">
        <h2>Kegiatan Terbaru</h2>
        <a href="{{ route('admin.activities.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah
        </a>
    </div>
    <div class="card-body">
        @if($latestActivities->isEmpty())
        <p style="color: var(--text-light); text-align: center; padding: 30px;">Belum ada kegiatan.</p>
        @else
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($latestActivities as $activity)
                    <tr>
                        <td>{{ $activity->title }}</td>
                        <td>{{ $activity->category == 'news' ? 'Berita' : 'Acara' }}</td>
                        <td>
                            @if($activity->is_published)
                            <span class="badge badge-success">Dipublikasikan</span>
                            @else
                            <span class="badge badge-warning">Draft</span>
                            @endif
                        </td>
                        <td>{{ $activity->created_at->format('d M Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection