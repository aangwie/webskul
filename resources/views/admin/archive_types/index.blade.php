@extends('admin.layouts.app')

@section('title', 'Jenis Arsip')

@section('content')
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-tags"></i> Manajemen Jenis Arsip</h2>
        <button class="btn btn-primary" onclick="showAddModal()">
            <i class="fas fa-plus"></i> Tambah Jenis Arsip
        </button>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif

        <div class="table-responsive">
            <table class="table" id="archiveTypesTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Jenis Arsip</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($types as $type)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $type->name }}</td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <button class="btn btn-warning btn-sm" onclick='showEditModal(@json($type))'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.archive-types.destroy', $type) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus jenis arsip ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">
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
<div id="typeModal" class="sidebar-overlay" style="display: none; align-items: center; justify-content: center; z-index: 1050;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 20px;">
        <div class="card-header">
            <h2 id="modalTitle">Tambah Jenis Arsip</h2>
            <button class="btn btn-sm" onclick="hideModal()"><i class="fas fa-times"></i></button>
        </div>
        <form id="typeForm" method="POST">
            @csrf
            <div id="methodField"></div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Nama Jenis Arsip</label>
                    <input type="text" name="name" id="typeName" class="form-input" required>
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
<style>
    .card-footer {
        background: transparent;
    }
</style>
@endsection

@section('scripts')
<script>
    function showAddModal() {
        document.getElementById('modalTitle').innerText = 'Tambah Jenis Arsip';
        document.getElementById('typeForm').action = "{{ route('admin.archive-types.store') }}";
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('typeName').value = '';
        document.getElementById('typeModal').style.display = 'flex';
    }

    function showEditModal(type) {
        document.getElementById('modalTitle').innerText = 'Edit Jenis Arsip';
        document.getElementById('typeForm').action = `/admin/archive-types/${type.id}`;
        document.getElementById('methodField').innerHTML = '@method("PUT")';
        document.getElementById('typeName').value = type.name;
        document.getElementById('typeModal').style.display = 'flex';
    }

    function hideModal() {
        document.getElementById('typeModal').style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target == document.getElementById('typeModal')) {
            hideModal();
        }
    }
</script>
@endsection