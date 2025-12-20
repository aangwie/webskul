@extends('admin.layouts.app')

@section('title', 'Media Sosial')
@section('page-title', 'Manajemen Media Sosial')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Daftar Media Sosial</h2>
        <a href="{{ route('admin.social-media.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Urutan</th>
                        <th>Platform</th>
                        <th>Icon</th>
                        <th>URL</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($socials as $social)
                    <tr>
                        <td>{{ $social->order }}</td>
                        <td>{{ $social->platform }}</td>
                        <td><i class="{{ $social->icon }}"></i> ({{ $social->icon }})</td>
                        <td><a href="{{ $social->url }}" target="_blank">{{ $social->url }}</a></td>
                        <td>
                            @if($social->is_active)
                            <span class="badge badge-success">Aktif</span>
                            @else
                            <span class="badge badge-danger">Non-aktif</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <a href="{{ route('admin.social-media.edit', $social) }}" class="btn-action btn-edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.social-media.destroy', $social) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px; color: var(--text-light);">Belum ada media sosial.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection