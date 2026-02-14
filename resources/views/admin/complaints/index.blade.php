@extends('admin.layouts.app')

@section('title', 'Aduan Masyarakat')

@section('content')
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-comments"></i> Aduan Masyarakat</h2>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check"></i> {{ session('success') }}
        </div>
        @endif

        <div class="table-responsive">
            <table class="table" id="complaintsTable">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Kode</th>
                        <th>Pengadu</th>
                        <th>Jenis</th>
                        <th>Isi Aduan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($complaints as $complaint)
                    <tr>
                        <td data-sort="{{ $complaint->created_at }}">
                            {{ $complaint->created_at->format('d/m/Y') }}<br>
                            <small>{{ $complaint->created_at->format('H:i') }}</small>
                        </td>
                        <td><code>{{ $complaint->complaint_code }}</code></td>
                        <td>
                            <strong>{{ $complaint->name }}</strong><br>
                            <small>{{ $complaint->phone }}</small>
                        </td>
                        <td>
                            <span class="badge {{ $complaint->type == 'Aduan' ? 'badge-danger' : 'badge-warning' }}">
                                {{ $complaint->type }}
                            </span>
                        </td>
                        <td>
                            <div style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $complaint->description }}">
                                {{ $complaint->description }}
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $complaint->status == 'responded' ? 'badge-success' : 'badge-warning' }}">
                                {{ $complaint->status == 'responded' ? 'Sudah Direspon' : 'Pending' }}
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <button class="btn btn-primary btn-sm" onclick="showRespondModal({{ json_encode($complaint) }})" title="Kirim Respon">
                                    <i class="fas fa-reply"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete('{{ route('admin.public-complaints.destroy', $complaint->id) }}')" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Respond Modal -->
<div id="respondModal" class="modal-custom" style="display: none;">
    <div class="modal-custom-content">
        <div class="modal-custom-header">
            <h3><i class="fas fa-reply"></i> Berikan Respon</h3>
            <span class="close-modal" onclick="closeModal()">&times;</span>
        </div>
        <form id="respondForm" method="POST">
            @csrf
            <div class="modal-custom-body">
                <div style="background: var(--accent); padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                    <p style="font-size: 0.8rem; color: var(--text-light); margin-bottom: 5px;">Aduan dari <strong id="complaintUser"></strong>:</p>
                    <p id="complaintDesc" style="font-style: italic;"></p>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggapan Sekolah</label>
                    <textarea name="response" id="respondTextarea" class="form-textarea" required placeholder="Tuliskan tanggapan atau solusi dari pihak sekolah..."></textarea>
                </div>
            </div>
            <div class="modal-custom-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Kirim Tanggapan</button>
            </div>
        </form>
    </div>
</div>

<style>
    .modal-custom {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1001;
        backdrop-filter: blur(4px);
    }

    .modal-custom-content {
        background: var(--secondary);
        width: 90%;
        max-width: 600px;
        border-radius: 16px;
        box-shadow: var(--shadow-lg);
        overflow: hidden;
    }

    .modal-custom-header {
        padding: 20px 25px;
        border-bottom: 1px solid var(--accent);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-custom-body {
        padding: 25px;
    }

    .modal-custom-footer {
        padding: 20px 25px;
        border-top: 1px solid var(--accent);
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .close-modal {
        cursor: pointer;
        font-size: 1.5rem;
        opacity: 0.5;
    }

    .close-modal:hover {
        opacity: 1;
    }
</style>

@endsection

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<style>
    .dataTables_wrapper .dataTables_length select {
        padding: 6px 30px 6px 10px;
        border-radius: 8px;
        border: 1px solid var(--accent);
        background-color: var(--secondary);
        color: var(--text);
    }

    .dataTables_wrapper .dataTables_filter input {
        padding: 6px 12px;
        border-radius: 8px;
        border: 1px solid var(--accent);
        margin-left: 10px;
        background-color: var(--secondary);
        color: var(--text);
    }

    table.dataTable.no-footer {
        border-bottom: 1px solid var(--accent);
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: var(--primary) !important;
        color: white !important;
        border: 1px solid var(--primary) !important;
        border-radius: 8px;
    }

    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        padding-top: 15px;
        color: var(--text-light) !important;
    }

    table.dataTable thead th {
        border-bottom: 1px solid var(--accent);
        color: var(--text-light);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }

    .modal-custom {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1001;
        backdrop-filter: blur(4px);
    }

    .modal-custom-content {
        background: var(--secondary);
        width: 90%;
        max-width: 600px;
        border-radius: 16px;
        box-shadow: var(--shadow-lg);
        overflow: hidden;
    }

    .modal-custom-header {
        padding: 20px 25px;
        border-bottom: 1px solid var(--accent);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-custom-body {
        padding: 25px;
    }

    .modal-custom-footer {
        padding: 20px 25px;
        border-top: 1px solid var(--accent);
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .close-modal {
        cursor: pointer;
        font-size: 1.5rem;
        opacity: 0.5;
    }

    .close-modal:hover {
        opacity: 1;
    }
</style>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#complaintsTable').DataTable({
            "order": [
                [0, "desc"]
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
            }
        });
    });

    function showRespondModal(complaint) {
        document.getElementById('complaintUser').innerText = complaint.name;
        document.getElementById('complaintDesc').innerText = complaint.description;
        document.getElementById('respondTextarea').value = complaint.response || '';
        document.getElementById('respondForm').action = `{{ url('admin/public-complaints') }}/${complaint.id}/respond`;
        document.getElementById('respondModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('respondModal').style.display = 'none';
    }

    function confirmDelete(url) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Aduan ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.innerHTML = `@csrf @method('DELETE')`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('respondModal');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>
@endsection