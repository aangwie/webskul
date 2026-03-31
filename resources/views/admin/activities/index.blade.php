@extends('admin.layouts.app')

@section('title', 'Daftar Kegiatan')
@section('page-title', 'Data Kegiatan')

@section('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .swal2-html-container {
        text-align: left !important;
    }
</style>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Daftar Kegiatan & Berita</h2>
            <a href="{{ route('admin.activities.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Kegiatan
            </a>
        </div>
        <div class="card-body">
            @if($activities->isEmpty())
                <p style="color: var(--text-light); text-align: center; padding: 30px;">Belum ada data kegiatan.</p>
            @else
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Gambar</th>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activities as $activity)
                                <tr>
                                    <td>
                                        @if($activity->image)
                                            @php
                                                $isBase64 = \Illuminate\Support\Str::startsWith($activity->image, 'data:');
                                                $imageUrl = $isBase64 ? $activity->image : route('admin.storage.view', ['path' => $activity->image]);
                                            @endphp
                                            <img src="{{ $imageUrl }}" alt="{{ $activity->title }}"
                                                style="width: 80px; height: 50px; object-fit: cover; border-radius: 8px;">
                                        @else
                                            <div
                                                style="width: 80px; height: 50px; background: var(--accent); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-image" style="color: var(--text-light);"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td><strong>{{ Str::limit($activity->title, 40) }}</strong></td>
                                    <td>{{ $activity->category == 'news' ? 'Berita' : 'Acara' }}</td>
                                    <td>
                                        @if($activity->is_published)
                                            <span class="badge badge-success">Dipublikasikan</span>
                                        @else
                                            <span class="badge badge-warning">Draft</span>
                                        @endif
                                    </td>
                                    <td>{{ $activity->published_at ? $activity->published_at->format('d M Y') : '-' }}</td>
                                    <td>
                                        <div style="display: flex; gap: 5px;">
                                            <button type="button" class="btn btn-info btn-sm btn-preview"
                                                data-title="{{ $activity->title }}"
                                                data-content="{{ htmlspecialchars($activity->content) }}">
                                                <i class="fas fa-eye" style="color: white;"></i>
                                            </button>
                                            <a href="{{ route('admin.activities.edit', $activity) }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.activities.destroy', $activity) }}" method="POST"
                                                onsubmit="return confirm('Hapus kegiatan ini?')">
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
                {{ $activities->links() }}
            @endif
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const previewBtns = document.querySelectorAll('.btn-preview');
        previewBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const title = this.dataset.title;
                const content = this.dataset.content;

                Swal.fire({
                    title: '<h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 0;">' + title + '</h3>',
                    html: '<div class="ql-editor" style="padding: 0;">' + content + '</div>',
                    width: '800px',
                    showCloseButton: true,
                    showConfirmButton: false,
                    customClass: {
                        container: 'preview-modal-container',
                        popup: 'preview-modal-popup',
                        content: 'preview-modal-content'
                    }
                });
            });
        });
    });
</script>
@endsection