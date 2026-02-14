@extends('admin.layouts.app')

@section('title', 'Pendataan Buku')

@section('content')
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-book"></i> Pendataan Buku</h2>
            <button class="btn btn-primary" onclick="showAddModal()">
                <i class="fas fa-plus"></i> Tambah Buku
            </button>
            <button class="btn btn-success" onclick="showImportModal()" style="margin-left: 10px;">
                <i class="fas fa-file-excel"></i> Import Excel
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul Buku</th>
                            <th>Penerbit</th>
                            <th>Pengarang</th>
                            <th>Tahun</th>
                            <th>Asal-Usul</th>
                            <th>Jenis</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($books as $book)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $book->judul_buku }}</td>
                                <td>{{ $book->penerbit }}</td>
                                <td>{{ $book->pengarang }}</td>
                                <td>{{ $book->tahun_perolehan }}</td>
                                <td>{{ $book->asal_usul }}</td>
                                <td><span class="badge badge-success">{{ $book->bookType->name ?? '-' }}</span></td>
                                <td>
                                    <div style="display: flex; gap: 5px;">
                                        <button class="btn btn-warning btn-sm" onclick='showEditModal(@json($book))'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.library.books.destroy', $book) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus buku ini?')">
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
                                <td colspan="8" class="text-center">Belum ada data buku</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit -->
    <div id="bookModal" class="sidebar-overlay"
        style="display: none; align-items: center; justify-content: center; z-index: 1050;">
        <div class="card" style="width: 100%; max-width: 600px; margin: 20px; max-height: 90vh; overflow-y: auto;">
            <div class="card-header">
                <h2 id="modalTitle">Tambah Buku</h2>
                <button class="btn btn-sm" onclick="hideModal()"><i class="fas fa-times"></i></button>
            </div>
            <form id="bookForm" method="POST">
                @csrf
                <div id="methodField"></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Judul Buku <span style="color: red;">*</span></label>
                        <input type="text" name="judul_buku" id="judulBuku" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Penerbit <span style="color: red;">*</span></label>
                        <input type="text" name="penerbit" id="penerbit" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pengarang <span style="color: red;">*</span></label>
                        <input type="text" name="pengarang" id="pengarang" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tahun Perolehan <span style="color: red;">*</span></label>
                        <input type="number" name="tahun_perolehan" id="tahunPerolehan" class="form-input" min="1900"
                            max="{{ date('Y') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Asal-Usul <span style="color: red;">*</span></label>
                        <input type="text" name="asal_usul" id="asalUsul" class="form-input"
                            placeholder="Contoh: Hibah, Pembelian, Sumbangan" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jenis Buku <span style="color: red;">*</span></label>
                        <select name="book_type_id" id="bookTypeId" class="form-select" required>
                            <option value="">-- Pilih Jenis Buku --</option>
                            @foreach($bookTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
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

    <!-- Modal Import -->
    <div id="importModal" class="sidebar-overlay"
        style="display: none; align-items: center; justify-content: center; z-index: 1050;">
        <div class="card" style="width: 100%; max-width: 500px; margin: 20px;">
            <div class="card-header">
                <h2>Import Data Buku</h2>
                <button class="btn btn-sm" onclick="hideImportModal()"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('admin.library.books.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Gunakan template yang telah disediakan agar format data
                        sesuai.
                        <br>
                        <a href="{{ route('admin.library.books.template') }}" class="btn btn-sm btn-info"
                            style="margin-top: 10px; text-decoration: none; color: white;">
                            <i class="fas fa-download"></i> Download Template
                        </a>
                    </div>
                    <div class="form-group">
                        <label class="form-label">File Excel (.xlsx, .xls)</label>
                        <input type="file" name="file" class="form-input" accept=".xlsx, .xls" required>
                    </div>
                </div>
                <div class="card-footer"
                    style="padding: 20px; border-top: 1px solid var(--accent); display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn btn-secondary" onclick="hideImportModal()">Batal</button>
                    <button type="submit" class="btn btn-success">Import</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function showAddModal() {
            document.getElementById('modalTitle').innerText = 'Tambah Buku';
            document.getElementById('bookForm').action = "{{ route('admin.library.books.store') }}";
            document.getElementById('methodField').innerHTML = '';
            document.getElementById('judulBuku').value = '';
            document.getElementById('penerbit').value = '';
            document.getElementById('pengarang').value = '';
            document.getElementById('tahunPerolehan').value = '';
            document.getElementById('asalUsul').value = '';
            document.getElementById('bookTypeId').value = '';
            document.getElementById('bookModal').style.display = 'flex';
        }

        function showEditModal(book) {
            document.getElementById('modalTitle').innerText = 'Edit Buku';
            document.getElementById('bookForm').action = `/admin/library/books/${book.id}`;
            document.getElementById('methodField').innerHTML = '@method("PUT")';
            document.getElementById('judulBuku').value = book.judul_buku;
            document.getElementById('penerbit').value = book.penerbit;
            document.getElementById('pengarang').value = book.pengarang;
            document.getElementById('tahunPerolehan').value = book.tahun_perolehan;
            document.getElementById('asalUsul').value = book.asal_usul;
            document.getElementById('bookTypeId').value = book.book_type_id;
            document.getElementById('bookModal').style.display = 'flex';
        }

        function hideModal() {
            document.getElementById('bookModal').style.display = 'none';
        }

        window.onclick = function (event) {
            if (event.target == document.getElementById('bookModal')) {
                hideModal();
            }
            if (event.target == document.getElementById('importModal')) {
                hideImportModal();
            }
        }

        function showImportModal() {
            document.getElementById('importModal').style.display = 'flex';
        }

        function hideImportModal() {
            document.getElementById('importModal').style.display = 'none';
        }
    </script>
@endsection