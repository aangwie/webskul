@extends('admin.layouts.app')

@section('title', 'Standar Pelayanan Publik')
@section('page-title', 'Standar Pelayanan Publik (SPP)')

@section('content')
<div class="card">
    <div class="card-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2><i class="fas fa-file-alt"></i> Daftar Dokumen SPP</h2>
            <button type="button" class="btn btn-primary" onclick="openAddModal()" style="align-self: flex-end;">
                <i class="fas fa-upload"></i> Upload Dokumen
            </button>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger">
            <ul style="margin-left: 20px;">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Judul Dokumen</th>
                        <th>Deskripsi</th>
                        <th>Tanggal Upload</th>
                        <th width="120" style="text-align: center;">File</th>
                        <th width="100" style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($standards as $index => $standard)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $standard->title }}</strong>
                        </td>
                        <td>
                            <div style="font-size: 0.85rem; color: var(--text-light);">
                                {{ Str::limit($standard->description, 80) ?? '-' }}
                            </div>
                        </td>
                        <td>{{ $standard->created_at->format('d/m/Y H:i') }}</td>
                        <td style="text-align: center;">
                            <button type="button" class="btn btn-sm btn-primary" title="Lihat PDF" onclick="openPdfModal('{{ $standard->title }}', '{{ route('spp.view', $standard->id) }}')">
                                <i class="fas fa-file-pdf"></i> View
                            </button>
                        </td>
                        <td style="text-align: center;">
                            <form action="{{ route('admin.public-service-standards.destroy', $standard->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-light); font-style: italic; padding: 30px;">
                            Belum ada dokumen SPP yang diunggah.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- PDF Modal --}}
<div id="pdfModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 2000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div style="background: white; width: 90%; height: 90%; max-width: 1000px; border-radius: 12px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.5);">
        <div style="padding: 15px 20px; background: var(--primary); color: white; display: flex; justify-content: space-between; align-items: center;">
            <h3 id="modalTitle" style="margin: 0; font-size: 1.1rem; color: white;">Lihat Dokumen</h3>
            <button onclick="closePdfModal()" style="background: none; border: none; color: white; font-size: 1.8rem; cursor: pointer; line-height: 1;">&times;</button>
        </div>
        <div style="flex-grow: 1; background: #eee;">
            <iframe id="pdfViewer" src="" style="width: 100%; height: 100%; border: none;"></iframe>
        </div>
    </div>
</div>

{{-- Modal Add --}}
<div id="addModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; width: 90%; max-width: 600px; border-radius: 12px; padding: 25px; box-shadow: 0 5px 20px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0;">Upload Dokumen SPP</h3>
            <button onclick="closeAddModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <form action="{{ route('admin.public-service-standards.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">File Dokumen (PDF, Max 5MB) <span style="color: var(--danger);">*</span></label>
                <div style="border: 2px dashed #ccc; padding: 20px; text-align: center; border-radius: 10px; cursor: pointer; position: relative;" onclick="document.getElementById('file-upload').click()">
                    <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: var(--primary);"></i>
                    <p style="margin-top: 10px; color: var(--text-light);">Klik untuk memilih file PDF</p>
                    <input type="file" name="file" id="file-upload" accept=".pdf" style="display: none;" onchange="updateFileName(this)" required>
                    <div id="file-name" style="margin-top: 10px; font-weight: bold; color: var(--success);"></div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi Singkat</label>
                <textarea name="description" class="form-textarea" style="min-height: 100px;" placeholder="Jelaskan isi singkat dokumen ini..."></textarea>
            </div>

            <div style="text-align: right; margin-top: 20px;">
                <button type="button" onclick="closeAddModal()" class="btn btn-secondary" style="margin-right: 10px; background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600;">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Upload</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPdfModal(title, url) {
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('pdfViewer').src = url;
        document.getElementById('pdfModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closePdfModal() {
        document.getElementById('pdfModal').style.display = 'none';
        document.getElementById('pdfViewer').src = '';
        document.body.style.overflow = 'auto';
    }

    function openAddModal() {
        document.getElementById('addModal').style.display = 'flex';
    }

    function closeAddModal() {
        document.getElementById('addModal').style.display = 'none';
        document.getElementById('file-name').textContent = '';
        document.getElementById('file-upload').value = '';
    }

    function updateFileName(input) {
        if (input.files && input.files[0]) {
            document.getElementById('file-name').textContent = 'File terpilih: ' + input.files[0].name;

            if (input.files[0].size > 5242880) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Terlalu Besar',
                    text: 'Ukuran file maksimal adalah 5MB',
                });
                input.value = '';
                document.getElementById('file-name').textContent = '';
            }
        }
    }

    window.onclick = function(event) {
        if (event.target == document.getElementById('addModal')) {
            closeAddModal();
        }
        if (event.target == document.getElementById('pdfModal')) {
            closePdfModal();
        }
    }
</script>
@endsection