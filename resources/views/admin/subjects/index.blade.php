@extends('admin.layouts.app')

@section('title', 'Data Mata Pelajaran')
@section('page-title', 'Data Mata Pelajaran')

@section('content')
<div class="card">
    <div class="card-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2><i class="fas fa-book"></i> Daftar Mata Pelajaran</h2>
            <button type="button" class="btn btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Tambah Mapel
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
                        <th width="150">Kode Mapel</th>
                        <th>Nama Mata Pelajaran</th>
                        <th width="150" style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $index => $subject)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><span class="badge badge-warning">{{ $subject->code }}</span></td>
                        <td><strong>{{ $subject->name }}</strong></td>
                        <td style="text-align: center;">
                            <button type="button" class="btn btn-sm btn-warning" onclick="openEditModal({{ $subject->id }}, '{{ $subject->code }}', '{{ $subject->name }}')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('admin.subjects.destroy', $subject->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mata pelajaran ini? Data modul ajar terkait juga akan terhapus.')">
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
                        <td colspan="4" style="text-align: center; color: var(--text-light); font-style: italic;">
                            Belum ada data mata pelajaran.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Add --}}
<div id="addModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; width: 90%; max-width: 500px; border-radius: 12px; padding: 25px; box-shadow: 0 5px 20px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0;">Tambah Mata Pelajaran</h3>
            <button onclick="closeAddModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <form action="{{ route('admin.subjects.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Kode Mapel <span style="color: var(--danger);">*</span></label>
                <input type="text" name="code" class="form-input" placeholder="Contoh: MM-01" required maxlength="20">
            </div>
            <div class="form-group">
                <label class="form-label">Nama Mata Pelajaran <span style="color: var(--danger);">*</span></label>
                <input type="text" name="name" class="form-input" placeholder="Contoh: Matematika" required>
            </div>
            <div style="text-align: right; margin-top: 20px;">
                <button type="button" onclick="closeAddModal()" class="btn btn-secondary" style="margin-right: 10px;">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="editModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; width: 90%; max-width: 500px; border-radius: 12px; padding: 25px; box-shadow: 0 5px 20px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0;">Edit Mata Pelajaran</h3>
            <button onclick="closeEditModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Kode Mapel <span style="color: var(--danger);">*</span></label>
                <input type="text" name="code" id="edit_code" class="form-input" required maxlength="20">
            </div>
            <div class="form-group">
                <label class="form-label">Nama Mata Pelajaran <span style="color: var(--danger);">*</span></label>
                <input type="text" name="name" id="edit_name" class="form-input" required>
            </div>
            <div style="text-align: right; margin-top: 20px;">
                <button type="button" onclick="closeEditModal()" class="btn btn-secondary" style="margin-right: 10px;">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById('addModal').style.display = 'flex';
    }

    function closeAddModal() {
        document.getElementById('addModal').style.display = 'none';
    }

    function openEditModal(id, code, name) {
        document.getElementById('edit_code').value = code;
        document.getElementById('edit_name').value = name;
        document.getElementById('editForm').action = "/admin/subjects/" + id;
        document.getElementById('editModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    // Close modal slightly clicking outside
    window.onclick = function(event) {
        if (event.target == document.getElementById('addModal')) {
            closeAddModal();
        }
        if (event.target == document.getElementById('editModal')) {
            closeEditModal();
        }
    }
</script>
@endsection