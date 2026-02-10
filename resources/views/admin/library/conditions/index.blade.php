@extends('admin.layouts.app')

@section('title', 'Jumlah & Kondisi Buku')

@section('content')
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-clipboard-check"></i> Jumlah & Kondisi Buku</h2>
            @if($books->count() > 0)
                <button class="btn btn-primary" onclick="showAddModal()">
                    <i class="fas fa-plus"></i> Tambah Data
                </button>
            @endif
        </div>
        <div class="card-body">
            @if($books->count() == 0 && $conditions->count() == 0)
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> Silakan tambahkan data buku terlebih dahulu di menu <a
                        href="{{ route('admin.library.books.index') }}">Pendataan</a>.
                </div>
            @endif

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul Buku</th>
                            <th>Jenis Buku</th>
                            <th>Tahun Perolehan</th>
                            <th>Jumlah Buku</th>
                            <th>Kondisi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($conditions as $condition)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $condition->book->judul_buku }}</td>
                                <td><span class="badge badge-success">{{ $condition->book->bookType->name ?? '-' }}</span></td>
                                <td>{{ $condition->book->tahun_perolehan }}</td>
                                <td>{{ $condition->jumlah_buku }}</td>
                                <td>
                                    @if($condition->kondisi == 'laik')
                                        <span class="badge badge-success">Laik</span>
                                    @else
                                        <span class="badge badge-danger">Tidak Laik</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; gap: 5px;">
                                        <button class="btn btn-warning btn-sm" onclick='showEditModal(@json($condition))'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.library.conditions.destroy', $condition) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Belum ada data kondisi buku</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit -->
    <div id="conditionModal" class="sidebar-overlay"
        style="display: none; align-items: center; justify-content: center; z-index: 1050;">
        <div class="card" style="width: 100%; max-width: 500px; margin: 20px;">
            <div class="card-header">
                <h2 id="modalTitle">Tambah Data Kondisi</h2>
                <button class="btn btn-sm" onclick="hideModal()"><i class="fas fa-times"></i></button>
            </div>
            <form id="conditionForm" method="POST">
                @csrf
                <div id="methodField"></div>
                <div class="card-body">
                    <div class="form-group" id="bookSelectGroup">
                        <label class="form-label">Pilih Buku <span style="color: red;">*</span></label>
                        <select name="book_id" id="bookId" class="form-select" required>
                            <option value="">-- Pilih Buku --</option>
                            @foreach($books as $book)
                                <option value="{{ $book->id }}">{{ $book->judul_buku }} ({{ $book->bookType->name ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" id="bookSelectEditGroup" style="display: none;">
                        <label class="form-label">Buku</label>
                        <select name="book_id_edit" id="bookIdEdit" class="form-select">
                            @foreach($allBooks as $book)
                                <option value="{{ $book->id }}">{{ $book->judul_buku }} ({{ $book->bookType->name ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jumlah Buku <span style="color: red;">*</span></label>
                        <input type="number" name="jumlah_buku" id="jumlahBuku" class="form-input" min="0" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kondisi <span style="color: red;">*</span></label>
                        <select name="kondisi" id="kondisi" class="form-select" required>
                            <option value="laik">Laik</option>
                            <option value="tidak_laik">Tidak Laik</option>
                        </select>
                    </div>
                </div>
                <div class="card-footer"
                    style="padding: 20px; border-top: 1px solid var(--accent); display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn btn-secondary" onclick="hideModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function showAddModal() {
            document.getElementById('modalTitle').innerText = 'Tambah Data Kondisi';
            document.getElementById('conditionForm').action = "{{ route('admin.library.conditions.store') }}";
            document.getElementById('methodField').innerHTML = '';
            document.getElementById('bookSelectGroup').style.display = 'block';
            document.getElementById('bookSelectEditGroup').style.display = 'none';
            document.getElementById('bookId').name = 'book_id';
            document.getElementById('bookIdEdit').name = 'book_id_disabled';
            document.getElementById('bookId').value = '';
            document.getElementById('jumlahBuku').value = '';
            document.getElementById('kondisi').value = 'laik';
            document.getElementById('conditionModal').style.display = 'flex';
        }

        function showEditModal(condition) {
            document.getElementById('modalTitle').innerText = 'Edit Data Kondisi';
            document.getElementById('conditionForm').action = `/admin/library/conditions/${condition.id}`;
            document.getElementById('methodField').innerHTML = '@method("PUT")';
            document.getElementById('bookSelectGroup').style.display = 'none';
            document.getElementById('bookSelectEditGroup').style.display = 'block';
            document.getElementById('bookId').name = 'book_id_disabled';
            document.getElementById('bookIdEdit').name = 'book_id';
            document.getElementById('bookIdEdit').value = condition.book_id;
            document.getElementById('jumlahBuku').value = condition.jumlah_buku;
            document.getElementById('kondisi').value = condition.kondisi;
            document.getElementById('conditionModal').style.display = 'flex';
        }

        function hideModal() {
            document.getElementById('conditionModal').style.display = 'none';
        }

        window.onclick = function (event) {
            if (event.target == document.getElementById('conditionModal')) {
                hideModal();
            }
        }
    </script>
@endsection