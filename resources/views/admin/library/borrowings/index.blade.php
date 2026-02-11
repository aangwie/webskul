@extends('admin.layouts.app')

@section('title', 'Pemiinjaman Buku')

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        .tabs-nav {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--accent);
            padding-bottom: 10px;
        }

        .tab-btn {
            padding: 10px 20px;
            border: none;
            background: none;
            cursor: pointer;
            font-weight: 600;
            color: var(--text-muted);
            transition: var(--transition);
            border-radius: 8px;
        }

        .tab-btn:hover {
            background: var(--accent);
            color: var(--text);
        }

        .tab-btn.active {
            background: var(--primary);
            color: white;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary) !important;
            color: white !important;
            border: none !important;
            border-radius: 4px;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid var(--accent) !important;
            border-radius: 4px !important;
            padding: 5px 10px !important;
            background: var(--card-bg) !important;
            color: var(--text) !important;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-hand-holding"></i> Manajemen Peminjaman Buku</h2>
            <button class="btn btn-primary" onclick="showAddModal()">
                <i class="fas fa-plus"></i> Tambah Peminjaman
            </button>
        </div>
        <div class="card-body">
            <!-- Tabs Navigation -->
            <div class="tabs-nav">
                <button class="tab-btn active" onclick="showTab('siswa-tab', this)">
                    <i class="fas fa-user-graduate"></i> Siswa
                </button>
                <button class="tab-btn" onclick="showTab('guru-tab', this)">
                    <i class="fas fa-chalkboard-teacher"></i> Guru
                </button>
                @if($otherBorrowings->count() > 0)
                    <button class="tab-btn" onclick="showTab('others-tab', this)">
                        <i class="fas fa-users"></i> Lainnya
                    </button>
                @endif
            </div>

            <!-- Siswa Tab Content -->
            <div id="siswa-tab" class="tab-content active">
                <div class="table-responsive">
                    <table class="table datatable" id="studentTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul Buku</th>
                                <th>No Buku</th>
                                <th>Nama Siswa</th>
                                <th>NIS</th>
                                <th>Kelas</th>
                                <th>Tgl Pinjam</th>
                                <th>Jml</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($studentBorrowings as $borrowing)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $borrowing->book->judul_buku }}</td>
                                    <td>{{ $borrowing->nomor_buku ?? '-' }}</td>
                                    <td><strong>{{ $borrowing->peminjam }}</strong></td>
                                    <td>{{ $borrowing->identitas_peminjam ?? '-' }}</td>
                                    <td>{{ $borrowing->kelas_peminjam ?? '-' }}</td>
                                    <td>{{ $borrowing->tanggal_pinjam->format('d/m/Y') }}</td>
                                    <td>{{ $borrowing->jumlah_pinjam }}</td>
                                    <td>
                                        @if($borrowing->is_returned)
                                            <span class="badge badge-success">Dikembalikan</span>
                                        @else
                                            <span class="badge badge-warning">Dipinjam</span>
                                        @endif
                                    </td>
                                    <td>
                                        @include('admin.library.borrowings._actions', ['borrowing' => $borrowing])
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Guru Tab Content -->
            <div id="guru-tab" class="tab-content">
                <div class="table-responsive">
                    <table class="table datatable" id="teacherTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul Buku</th>
                                <th>No Buku</th>
                                <th>Nama Guru</th>
                                <th>NIP</th>
                                <th>Tgl Pinjam</th>
                                <th>Jml</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teacherBorrowings as $borrowing)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $borrowing->book->judul_buku }}</td>
                                    <td>{{ $borrowing->nomor_buku ?? '-' }}</td>
                                    <td><strong>{{ $borrowing->peminjam }}</strong></td>
                                    <td>{{ $borrowing->identitas_peminjam ?? '-' }}</td>
                                    <td>{{ $borrowing->tanggal_pinjam->format('d/m/Y') }}</td>
                                    <td>{{ $borrowing->jumlah_pinjam }}</td>
                                    <td>
                                        @if($borrowing->is_returned)
                                            <span class="badge badge-success">Dikembalikan</span>
                                        @else
                                            <span class="badge badge-warning">Dipinjam</span>
                                        @endif
                                    </td>
                                    <td>
                                        @include('admin.library.borrowings._actions', ['borrowing' => $borrowing])
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Others/Legacy Tab Content -->
            @if($otherBorrowings->count() > 0)
                <div id="others-tab" class="tab-content">
                    <div class="table-responsive">
                        <table class="table datatable" id="othersTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Judul Buku</th>
                                    <th>No Buku</th>
                                    <th>Peminjam</th>
                                    <th>Tgl Pinjam</th>
                                    <th>Jml</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($otherBorrowings as $borrowing)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $borrowing->book->judul_buku }}</td>
                                        <td>{{ $borrowing->nomor_buku ?? '-' }}</td>
                                        <td><strong>{{ $borrowing->peminjam }}</strong></td>
                                        <td>{{ $borrowing->tanggal_pinjam->format('d/m/Y') }}</td>
                                        <td>{{ $borrowing->jumlah_pinjam }}</td>
                                        <td>
                                            @if($borrowing->is_returned)
                                                <span class="badge badge-success">Dikembalikan</span>
                                            @else
                                                <span class="badge badge-warning">Dipinjam</span>
                                            @endif
                                        </td>
                                        <td>
                                            @include('admin.library.borrowings._actions', ['borrowing' => $borrowing])
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Tambah/Edit -->
    <div id="borrowingModal" class="sidebar-overlay"
        style="display: none; align-items: center; justify-content: center; z-index: 1050;">
        <div class="card" style="width: 100%; max-width: 600px; margin: 20px; max-height: 90vh; overflow-y: auto;">
            <div class="card-header">
                <h2 id="modalTitle">Tambah Peminjaman</h2>
                <button class="btn btn-sm" onclick="hideModal()"><i class="fas fa-times"></i></button>
            </div>
            <form id="borrowingForm" method="POST">
                @csrf
                <div id="methodField"></div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="form-group">
                        <label class="form-label">Pilih Buku <span style="color: red;">*</span></label>
                        <select name="book_id" id="bookId" class="form-select" required>
                            <option value="">-- Pilih Buku --</option>
                            @foreach($books as $book)
                                <option value="{{ $book->id }}">{{ $book->judul_buku }} ({{ $book->bookType->name ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nomor Buku</label>
                        <input type="text" name="nomor_buku" id="nomorBuku" class="form-input"
                            placeholder="Masukkan nomor/kode buku (jika ada)">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tipe Peminjam <span style="color: red;">*</span></label>
                        <div style="display: flex; gap: 20px; margin-top: 10px;">
                            <label style="cursor: pointer; display: flex; align-items: center; gap: 8px;">
                                <input type="radio" name="borrower_type" value="student" id="typeStudent"
                                    onchange="toggleBorrowerFields()" checked> Siswa
                            </label>
                            <label style="cursor: pointer; display: flex; align-items: center; gap: 8px;">
                                <input type="radio" name="borrower_type" value="teacher" id="typeTeacher"
                                    onchange="toggleBorrowerFields()"> Guru
                            </label>
                        </div>
                    </div>

                    <div id="studentField" class="form-group">
                        <label class="form-label">Pilih Siswa <span style="color: red;">*</span></label>
                        <select name="student_id" id="studentId" class="form-select">
                            <option value="">-- Pilih Siswa --</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" data-nis="{{ $student->nis }}"
                                    data-class="{{ $student->schoolClass->name ?? '-' }}">
                                    {{ $student->name }} (NIS: {{ $student->nis }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="teacherField" class="form-group" style="display: none;">
                        <label class="form-label">Pilih Guru <span style="color: red;">*</span></label>
                        <select name="teacher_id" id="teacherId" class="form-select">
                            <option value="">-- Pilih Guru --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" data-nip="{{ $teacher->nip }}">
                                    {{ $teacher->name }} (NIP: {{ $teacher->nip ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal Pinjam <span style="color: red;">*</span></label>
                        <input type="date" name="tanggal_pinjam" id="tanggalPinjam" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jumlah Pinjam <span style="color: red;">*</span></label>
                        <input type="number" name="jumlah_pinjam" id="jumlahPinjam" class="form-input" min="1" value="1"
                            required>
                    </div>
                    <div id="returnSection" style="display: none;">
                        <div class="form-group">
                            <label class="form-checkbox">
                                <input type="checkbox" name="is_returned" id="isReturned" value="1"
                                    onchange="toggleReturnDate()">
                                <span>Sudah Dikembalikan</span>
                            </label>
                        </div>
                        <div class="form-group" id="returnDateGroup" style="display: none;">
                            <label class="form-label">Tanggal Kembali</label>
                            <input type="date" name="tanggal_kembali" id="tanggalKembali" class="form-input">
                        </div>
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

    <!-- Modal Return -->
    <div id="returnModal" class="sidebar-overlay"
        style="display: none; align-items: center; justify-content: center; z-index: 1050;">
        <div class="card" style="width: 100%; max-width: 400px; margin: 20px;">
            <div class="card-header">
                <h2>Konfirmasi Pengembalian</h2>
                <button class="btn btn-sm" onclick="hideReturnModal()"><i class="fas fa-times"></i></button>
            </div>
            <form id="returnForm" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <p id="returnBookTitle" style="margin-bottom: 15px;"></p>
                    <div class="form-group">
                        <label class="form-label">Tanggal Kembali <span style="color: red;">*</span></label>
                        <input type="date" name="tanggal_kembali" id="returnDate" class="form-input" required>
                    </div>
                </div>
                <div class="card-footer"
                    style="padding: 20px; border-top: 1px solid var(--accent); display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn btn-secondary" onclick="hideReturnModal()">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Konfirmasi Kembali
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.datatable').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.13.4/i18n/id.json"
                },
                "pageLength": 10,
                "order": [[0, "asc"]]
            });
        });

        function showTab(tabId, btn) {
            $('.tab-content').removeClass('active');
            $('.tab-btn').removeClass('active');
            $('#' + tabId).addClass('active');
            $(btn).addClass('active');
        }

        function toggleBorrowerFields() {
            const type = $('input[name="borrower_type"]:checked').val();
            if (type === 'student') {
                $('#studentField').show();
                $('#teacherField').hide();
                $('#studentId').prop('disabled', false).attr('required', true);
                $('#teacherId').prop('disabled', true).attr('required', false).val('');
            } else {
                $('#studentField').hide();
                $('#teacherField').show();
                $('#studentId').prop('disabled', true).attr('required', false).val('');
                $('#teacherId').prop('disabled', false).attr('required', true);
            }
        }

        function showAddModal() {
            $('#modalTitle').text('Tambah Peminjaman');
            $('#borrowingForm').attr('action', "{{ route('admin.library.borrowings.store') }}");
            $('#methodField').empty();
            $('#bookId').val('');
            $('#nomorBuku').val('');
            $('#typeStudent').prop('checked', true);
            $('#studentId').val('');
            $('#teacherId').val('');
            $('#tanggalPinjam').val(new Date().toISOString().split('T')[0]);
            $('#jumlahPinjam').val(1);
            $('#returnSection').hide();
            toggleBorrowerFields();
            $('#borrowingModal').css('display', 'flex');
        }

        function showEditModal(borrowing) {
            $('#modalTitle').text('Edit Peminjaman');
            $('#borrowingForm').attr('action', `/admin/library/borrowings/${borrowing.id}`);
            $('#methodField').html('@method("PUT")');
            $('#bookId').val(borrowing.book_id);
            $('#nomorBuku').val(borrowing.nomor_buku || '');

            if (borrowing.borrower_type === 'teacher') {
                $('#typeTeacher').prop('checked', true);
                $('#teacherId').val(borrowing.teacher_id);
                $('#studentId').val('');
            } else {
                $('#typeStudent').prop('checked', true);
                $('#studentId').val(borrowing.student_id || '');
                $('#teacherId').val('');
            }

            $('#tanggalPinjam').val(borrowing.tanggal_pinjam.split('T')[0]);
            $('#jumlahPinjam').val(borrowing.jumlah_pinjam);
            $('#returnSection').show();
            $('#isReturned').prop('checked', borrowing.is_returned == 1);

            if (borrowing.tanggal_kembali) {
                $('#tanggalKembali').val(borrowing.tanggal_kembali.split('T')[0]);
            } else {
                $('#tanggalKembali').val('');
            }
            toggleReturnDate();
            toggleBorrowerFields();
            $('#borrowingModal').css('display', 'flex');
        }

        function toggleReturnDate() {
            const isChecked = $('#isReturned').is(':checked');
            $('#returnDateGroup').toggle(isChecked);
            if (isChecked && !$('#tanggalKembali').val()) {
                $('#tanggalKembali').val(new Date().toISOString().split('T')[0]);
            }
        }

        function hideModal() {
            $('#borrowingModal').hide();
        }

        function showReturnModal(borrowing) {
            $('#returnForm').attr('action', `/admin/library/borrowings/${borrowing.id}/return`);
            $('#returnBookTitle').html(`<strong>Buku:</strong> ${borrowing.book.judul_buku}<br><strong>Peminjam:</strong> ${borrowing.peminjam}`);
            $('#returnDate').val(new Date().toISOString().split('T')[0]);
            $('#returnModal').css('display', 'flex');
        }

        function hideReturnModal() {
            $('#returnModal').hide();
        }

        $(window).on('click', function (event) {
            if ($(event.target).is('#borrowingModal')) hideModal();
            if ($(event.target).is('#returnModal')) hideReturnModal();
        });
    </script>
@endsection