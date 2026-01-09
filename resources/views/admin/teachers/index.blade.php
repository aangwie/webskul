@extends('admin.layouts.app')

@section('title', 'Daftar Guru')
@section('page-title', 'Data Guru')

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Daftar Guru</h2>
            <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Guru
            </a>
        </div>
        <div class="card-body">
            @if($teachers->isEmpty())
                <p style="color: var(--text-light); text-align: center; padding: 30px;">Belum ada data guru.</p>
            @else
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th>Nama</th>
                                <th>NIP</th>
                                <th>Jabatan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teachers as $teacher)
                                <tr>
                                    <td>
                                        @if($teacher->photo)
                                            @if(Str::startsWith($teacher->photo, 'data:'))
                                                <img src="{{ $teacher->photo }}" alt="{{ $teacher->name }}"
                                                    style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                                            @else
                                                @php
                                                    $photoUrl = route('admin.storage.view', ['path' => $teacher->photo]);
                                                @endphp
                                                <img src="{{ $photoUrl }}" alt="{{ $teacher->name }}"
                                                    style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                                            @endif
                                        @else
                                            <div
                                                style="width: 50px; height: 50px; border-radius: 50%; background: var(--accent); display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-user" style="color: var(--text-light);"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td><strong>{{ $teacher->name }}</strong></td>
                                    <td>{{ $teacher->nip ?? '-' }}</td>
                                    <td>{{ $teacher->position ?? '-' }}</td>
                                    <td>
                                        @if($teacher->is_active)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 5px;">
                                            <a href="{{ route('admin.teachers.edit', $teacher) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST"
                                                onsubmit="return confirm('Hapus data guru ini?')">
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
                <div style="margin-top: 20px;">
                    {{ $teachers->withQueryString()->links('pagination::simple-default') }}
                </div>
            @endif
        </div>
    </div>
@endsection