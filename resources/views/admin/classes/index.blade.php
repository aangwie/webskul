@extends('admin.layouts.app')

@section('title', 'Manajemen Kelas')
@section('page-title', 'Manajemen Kelas')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Daftar Kelas</h2>
        <a href="{{ route('admin.classes.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah Kelas
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Kelas</th>
                        <th>Tingkat</th>
                        <th>Tahun Ajaran</th>
                        <th>Total Siswa</th>
                        <th>L / P</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $class)
                    <tr>
                        <td><strong>{{ $class->name }}</strong></td>
                        <td>{{ $class->grade }}</td>
                        <td>{{ $class->academic_year }}</td>
                        <td>
                            <span class="badge badge-success">{{ $class->total_students }} Siswa</span>
                        </td>
                        <td>
                            <span style="color: var(--primary);">{{ $class->male_count }} L</span> / 
                            <span style="color: #e83e8c;">{{ $class->female_count }} P</span>
                        </td>
                        <td>
                            @if($class->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-danger">Non-Aktif</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <a href="{{ route('admin.classes.edit', $class->id) }}" class="btn btn-warning btn-sm" style="padding: 5px 8px;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.classes.destroy', $class->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelas ini? Data siswa di dalamnya juga akan terhapus.')" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" style="padding: 5px 8px;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px; color: var(--text-light);">
                            Belum ada data kelas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 20px;">
            {{ $classes->links('pagination::simple-default') }}
        </div>
    </div>
</div>
@endsection
