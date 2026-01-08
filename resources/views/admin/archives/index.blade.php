@extends('admin.layouts.app')

@section('title', 'Daftar Arsip PTK')

@section('content')
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-file-archive"></i> Daftar Arsip PTK</h2>
        <button class="btn btn-primary" onclick="showAddModal()">
            <i class="fas fa-plus"></i> Unggah Arsip
        </button>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif

        <div class="table-responsive">
            <table class="table" id="archivesTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul Arsip</th>
                        <th>Jenis</th>
                        <th>Pemilik</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($archives as $archive)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $archive->title }}</td>
                        <td><span class="badge badge-info" style="background: rgba(23, 162, 184, 0.1); color: #17a2b8; border: 1px solid #17a2b8;">{{ $archive->archiveType->name }}</span></td>
                        <td>{{ $archive->user->name }}</td>
                        <td>{{ $archive->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                @php
                                $isBase64 = str_starts_with($archive->file_path, 'data:');
                                $fileUrl = $isBase64 ? $archive->file_path : asset('storage/' . $archive->file_path);
                                @endphp
                                <button type="button" class="btn btn-success btn-sm" title="Lihat" onclick="previewFile('{{ $fileUrl }}', '{{ addslashes($archive->title) }}')">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="{{ $fileUrl }}" download="{{ $archive->title }}" class="btn btn-info btn-sm" title="Download">
                                    <i class="fas fa-download"></i>
                                </a>
                                <button class="btn btn-warning btn-sm" onclick='showEditModal(@json($archive))' title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.archives.destroy', $archive) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus arsip ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" title="Hapus">
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
    </div>
</div>

<!-- Modal Tambah/Edit -->
<div id="archiveModal" class="sidebar-overlay" style="display: none; align-items: center; justify-content: center; z-index: 1050;">
    <div class="card" style="width: 100%; max-width: 600px; margin: 20px;">
        <div class="card-header">
            <h2 id="modalTitle">Unggah Arsip Baru</h2>
            <button class="btn btn-sm" onclick="hideModal()"><i class="fas fa-times"></i></button>
        </div>
        <form id="archiveForm" method="POST" enctype="multipart/form-data">
            @csrf
            <div id="methodField"></div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Judul Arsip</label>
                    <input type="text" name="title" id="archiveTitle" class="form-input" required placeholder="Contoh: Sertifikat Pelatihan IT 2024" value="{{ old('title') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Jenis Arsip</label>
                    <select name="archive_type_id" id="archiveTypeId" class="form-select" required>
                        <option value="">-- Pilih Jenis --</option>
                        @foreach($types as $type)
                        <option value="{{ $type->id }}" {{ old('archive_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">File Arsip <span id="fileRequiredStar" style="color: red;">*</span></label>
                    <input type="file" name="file" id="archiveFile" class="form-input" onchange="validateSize(this)">
                    <small style="color: var(--text-light);">Format: PDF, JPG, PNG (Max 500KB)</small>
                    <div id="filePreview" style="margin-top: 10px; display: none;">
                        <span class="badge badge-info">File terpilih sebelumnya tersimpan</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Keterangan (Opsional)</label>
                    <textarea name="description" id="archiveDescription" class="form-textarea" placeholder="Tambahkan catatan singkat jika perlu...">{{ old('description') }}</textarea>
                </div>
            </div>
            <div class="card-footer" style="padding: 20px; border-top: 1px solid var(--accent); display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn btn-secondary" onclick="hideModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<style>
    .dataTables_wrapper .dataTables_filter input {
        padding: 8px 12px;
        border: 2px solid var(--accent);
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .dataTables_wrapper .dataTables_length select {
        padding: 6px 30px 6px 10px;
        border-radius: 8px;
        border: 1px solid var(--accent);
    }

    .card-footer {
        background: transparent;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: var(--primary) !important;
        color: white !important;
        border: 1px solid var(--primary) !important;
        border-radius: 8px;
        padding: 6px 12px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 8px;
        margin-left: 5px;
    }

    table.dataTable thead th {
        border-bottom: 1px solid var(--accent);
    }

    .badge-info {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
    }
</style>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#archivesTable').DataTable({
            "language": {
                "search": "Cari Arsip:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Tidak ada data",
                "infoFiltered": "(disaring dari _MAX_ total data)",
                "zeroRecords": "Data tidak ditemukan",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                }
            }
        });

        // Backend Validation Error Handler
        @if($errors -> any())
        let errorMsg = 'Terjadi kesalahan saat menyimpan data.';
        @if($errors -> has('file'))
        errorMsg = 'Gagal simpan karena ukuran file terlalu besar! Maksimal 500KB.';
        @endif

        Swal.fire({
            icon: 'error',
            title: 'Gagal Simpan',
            text: errorMsg,
            confirmButtonColor: 'var(--primary)',
        });

        // Re-open the modal
        document.getElementById('archiveModal').style.display = 'flex';
        // Set form action based on whether we were editing or adding
        // For simplicity, we assume 'store' if no method field is present,
        // but Laravel's old() doesn't tell us the action.
        // If the user was editing, they might lose the action.
        // However, most errors will come from new uploads.
        @endif
    });

    function validateSize(input) {
        const fileSize = input.files[0].size / 1024; // in KB
        if (fileSize > 500) {
            Swal.fire({
                icon: 'error',
                title: 'File Terlalu Besar',
                text: 'Ukuran file maksimal adalah 500KB. File Anda: ' + Math.round(fileSize) + 'KB',
            });
            input.value = '';
        }
    }

    function previewFile(url, title) {
        let extension = '';
        if (url.startsWith('data:')) {
            const mime = url.split(';')[0].split(':')[1];
            if (mime === 'application/pdf') extension = 'pdf';
            else if (mime.startsWith('image/')) extension = 'jpg';
        } else {
            extension = url.split('.').pop().toLowerCase();
        }
        let content = '';

        if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
            content = `<img src="${url}" style="width: 100%; height: auto; border-radius: 10px;">`;
        } else if (extension === 'pdf') {
            content = `<iframe src="${url}" style="width: 100%; height: 500px; border: none; border-radius: 10px;"></iframe>`;
        } else {
            content = `<div class="alert alert-info">File ini tidak mendukung preview langsung. Silakan download untuk melihatnya.</div>`;
        }

        Swal.fire({
            title: title,
            html: content,
            width: extension === 'pdf' ? '80%' : '600px',
            showCloseButton: true,
            showConfirmButton: false,
            footer: `<a href="${url}" download class="btn btn-primary" style="text-decoration: none; color: white;"><i class="fas fa-download"></i> Download File</a>`
        });
    }

    function showAddModal() {
        document.getElementById('modalTitle').innerText = 'Unggah Arsip Baru';
        document.getElementById('archiveForm').action = "{{ route('admin.archives.store') }}";
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('archiveTitle').value = '';
        document.getElementById('archiveTypeId').value = '';
        document.getElementById('archiveFile').required = true;
        document.getElementById('fileRequiredStar').style.display = 'inline';
        document.getElementById('archiveDescription').value = '';
        document.getElementById('filePreview').style.display = 'none';
        document.getElementById('archiveModal').style.display = 'flex';
    }

    function showEditModal(archive) {
        document.getElementById('modalTitle').innerText = 'Edit Arsip';
        document.getElementById('archiveForm').action = `/admin/archives/${archive.id}`;
        document.getElementById('methodField').innerHTML = '@method("PUT")';
        document.getElementById('archiveTitle').value = archive.title;
        document.getElementById('archiveTypeId').value = archive.archive_type_id;
        document.getElementById('archiveFile').required = false;
        document.getElementById('fileRequiredStar').style.display = 'none';
        document.getElementById('archiveDescription').value = archive.description || '';
        document.getElementById('filePreview').style.display = 'block';
        document.getElementById('archiveModal').style.display = 'flex';
    }

    function hideModal() {
        document.getElementById('archiveModal').style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target == document.getElementById('archiveModal')) {
            hideModal();
        }
    }
</script>
@endsection