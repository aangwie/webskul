@extends('admin.layouts.app')

@section('title', 'Daftar Fasilitas Sekolah')
@section('page-title', 'Data Fasilitas')

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Daftar Fasilitas Sekolah</h2>
            <a href="{{ route('admin.school-facilities.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Fasilitas
            </a>
        </div>
        <div class="card-body">
            @if($facilities->isEmpty())
                <p style="color: var(--text-light); text-align: center; padding: 30px;">Belum ada data fasilitas sekolah.</p>
            @else
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Gambar</th>
                                <th>Deskripsi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($facilities as $index => $facility)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @if($facility->image)
                                            <img src="{{ URL::signedRoute('public.storage.view', ['path' => $facility->image]) }}" alt="Fasilitas"
                                                style="width: 120px; height: 80px; object-fit: cover; border-radius: 8px;">
                                        @else
                                            <div
                                                style="width: 120px; height: 80px; background: var(--accent); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-image" style="color: var(--text-light);"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($facility->description, 50) }}</td>
                                    <td>
                                        <div style="display: flex; gap: 5px;">
                                            <a href="{{ route('admin.school-facilities.edit', $facility) }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.school-facilities.destroy', $facility) }}" method="POST"
                                                onsubmit="return confirm('Hapus fasilitas ini?')">
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
            @endif
        </div>
    </div>
@endsection
