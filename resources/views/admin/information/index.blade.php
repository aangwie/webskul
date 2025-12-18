@extends('admin.layouts.app')

@section('title', 'Daftar Informasi')
@section('page-title', 'Data Informasi')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Daftar Informasi & Pengumuman</h2>
        <a href="{{ route('admin.information.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Informasi
        </a>
    </div>
    <div class="card-body">
        @if($informations->isEmpty())
            <p style="color: var(--text-light); text-align: center; padding: 30px;">Belum ada data informasi.</p>
        @else
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Penting</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($informations as $info)
                        <tr>
                            <td><strong>{{ Str::limit($info->title, 50) }}</strong></td>
                            <td>
                                @if($info->is_important)
                                    <span class="badge badge-warning">Penting</span>
                                @else
                                    <span class="badge" style="background: var(--accent); color: var(--text-light);">Normal</span>
                                @endif
                            </td>
                            <td>
                                @if($info->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td>{{ $info->created_at->format('d M Y') }}</td>
                            <td>
                                <div style="display: flex; gap: 5px;">
                                    <a href="{{ route('admin.information.edit', $info) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.information.destroy', $info) }}" method="POST" onsubmit="return confirm('Hapus informasi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $informations->links() }}
        @endif
    </div>
</div>
@endsection
