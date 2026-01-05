@extends('admin.layouts.app')

@section('title', 'Perencanaan Program Komite')
@section('page-title', 'Perencanaan Program Komite')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<style>
    .year-selector {
        background: var(--secondary);
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: var(--shadow);
    }

    .year-selector .form-group {
        margin-bottom: 0;
    }

    .action-icons {
        display: flex;
        gap: 5px;
        justify-content: center;
    }

    .action-icons .btn {
        width: 34px;
        height: 34px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-info {
        background: #17a2b8;
        color: #fff;
    }

    .btn-info:hover {
        background: #138496;
    }

    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 2000;
        justify-content: center;
        align-items: center;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-content {
        background: var(--secondary);
        border-radius: 16px;
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: var(--shadow-lg);
    }

    .modal-header {
        padding: 20px 25px;
        border-bottom: 1px solid var(--accent);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 1.1rem;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: var(--text-light);
    }

    .modal-body {
        padding: 25px;
    }

    .budget-info {
        background: var(--accent);
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .budget-info p {
        margin: 5px 0;
        font-size: 0.9rem;
    }

    .budget-info .remaining {
        font-weight: 600;
        color: var(--success);
    }

    .budget-info .remaining.warning {
        color: var(--warning);
    }

    .budget-info .remaining.danger {
        color: var(--danger);
    }

    .activity-list {
        margin-top: 20px;
    }

    .activity-item {
        background: var(--accent);
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .activity-item-info h4 {
        margin: 0 0 5px 0;
        font-size: 0.95rem;
    }

    .activity-item-info p {
        margin: 0;
        font-size: 0.85rem;
        color: var(--text-light);
    }

    .activity-item-cost {
        font-weight: 600;
        color: var(--primary);
    }

    .activity-actions {
        display: flex;
        gap: 5px;
    }

    #programsTable_wrapper .dataTables_filter input {
        padding: 8px 12px;
        border: 2px solid var(--accent);
        border-radius: 8px;
    }

    #programsTable_wrapper .dataTables_length select {
        padding: 8px;
        border: 2px solid var(--accent);
        border-radius: 8px;
    }

    .swal2-container {
        z-index: 3000 !important;
    }
</style>
@endsection

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <div>
        <p style="color: var(--text-light);">Kelola rencana program dan kegiatan komite sekolah.</p>
    </div>
</div>

<!-- Year Selector -->
<div class="year-selector">
    <form method="GET" action="{{ route('admin.committee.planning.index') }}" style="display: flex; gap: 15px; align-items: end;">
        <div class="form-group" style="flex: 1; max-width: 300px;">
            <label class="form-label">Tahun Pelajaran</label>
            <select name="year_id" class="form-select" onchange="this.form.submit()">
                @foreach($academicYears as $year)
                <option value="{{ $year->id }}" {{ ($selectedYear && $selectedYear->id == $year->id) ? 'selected' : '' }}>
                    {{ $year->year }} {{ $year->is_active ? '(Aktif)' : '' }}
                </option>
                @endforeach
            </select>
        </div>
    </form>
</div>

@if($selectedYear)
<!-- Add Program Form -->
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-plus-circle"></i> Tambah Rencana Program</h2>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.committee.planning.store') }}" method="POST">
            @csrf
            <input type="hidden" name="academic_year_id" value="{{ $selectedYear->id }}">
            <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                <div class="form-group" style="flex: 2; min-width: 250px;">
                    <label class="form-label">Nama Rencana Program <span style="color: var(--danger);">*</span></label>
                    <input type="text" name="name" class="form-input" required placeholder="Contoh: Pengembangan Diri">
                </div>
                <div class="form-group" style="flex: 1; min-width: 200px;">
                    <label class="form-label">Kebutuhan Biaya <span style="color: var(--danger);">*</span></label>
                    <input type="number" name="budget" class="form-input" required min="0" step="1000" placeholder="Contoh: 5000000">
                </div>
                <div class="form-group" style="flex: 2; min-width: 250px;">
                    <label class="form-label">Keterangan</label>
                    <input type="text" name="description" class="form-input" placeholder="Opsional">
                </div>
                <div class="form-group" style="display: flex; align-items: flex-end;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Programs List -->
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-list"></i> Daftar Rencana Program - {{ $selectedYear->year }}</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="programsTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Program</th>
                        <th style="text-align: right;">Kebutuhan Biaya</th>
                        <th style="text-align: right;">Total Terpakai</th>
                        <th style="text-align: right;">Sisa Anggaran</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($programs as $index => $program)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $program->name }}</strong>
                            @if($program->description)
                            <br><small style="color: var(--text-light);">{{ $program->description }}</small>
                            @endif
                        </td>
                        <td style="text-align: right;">Rp {{ number_format($program->budget, 0, ',', '.') }}</td>
                        <td style="text-align: right; color: var(--danger);">Rp {{ number_format($program->total_cost, 0, ',', '.') }}</td>
                        <td style="text-align: right; color: var(--success); font-weight: 600;">Rp {{ number_format($program->remaining_budget, 0, ',', '.') }}</td>
                        <td>
                            <div class="action-icons">
                                <button type="button" class="btn btn-sm btn-success" title="Tambah Kegiatan" onclick="openAddActivityModal({{ $program->id }}, '{{ $program->name }}', {{ $program->remaining_budget }})">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-info" title="Lihat Detail Kegiatan" onclick="openDetailModal({{ $program->id }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-warning" title="Edit Program" onclick="openEditModal({{ $program->id }}, '{{ addslashes($program->name) }}', {{ $program->budget }}, '{{ addslashes($program->description ?? '') }}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.committee.planning.destroy', $program) }}" method="POST" id="delete-program-{{ $program->id }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger" title="Hapus Program" onclick="confirmDeleteProgram({{ $program->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px; color: var(--text-light);">
                            Belum ada rencana program untuk tahun ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="card">
    <div class="card-body" style="text-align: center; padding: 50px;">
        <i class="fas fa-calendar-times" style="font-size: 3rem; color: var(--text-light); margin-bottom: 20px;"></i>
        <p style="color: var(--text-light);">Silakan pilih tahun pelajaran terlebih dahulu.</p>
    </div>
</div>
@endif

<!-- Modal: Add Activity -->
<div class="modal-overlay" id="addActivityModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-plus-circle"></i> Tambah Kegiatan</h3>
            <button class="modal-close" onclick="closeModal('addActivityModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="budget-info">
                <p>Program: <strong id="addActivityProgramName"></strong></p>
                <p class="remaining">Sisa Anggaran: <span id="addActivityRemainingBudget"></span></p>
            </div>
            <form id="addActivityForm">
                <input type="hidden" id="addActivityProgramId">
                <div class="form-group">
                    <label class="form-label">Uraian Kegiatan <span style="color: var(--danger);">*</span></label>
                    <input type="text" id="addActivityName" class="form-input" required placeholder="Contoh: Pembelian bola sepak">
                </div>
                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">Harga Satuan <span style="color: var(--danger);">*</span></label>
                        <input type="number" id="addActivityUnitPrice" class="form-input" required min="0" step="1000" placeholder="Contoh: 100000" oninput="calculateAddActivityTotal()">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">Quantity <span style="color: var(--danger);">*</span></label>
                        <input type="number" id="addActivityQuantity" class="form-input" required min="1" step="1" placeholder="Contoh: 5" oninput="calculateAddActivityTotal()">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Total Biaya</label>
                    <input type="text" id="addActivityCostDisplay" class="form-input" readonly style="background: var(--accent); font-weight: 600; color: var(--primary);">
                    <input type="hidden" id="addActivityCost">
                </div>
                <div class="form-group">
                    <label class="form-label">Keterangan</label>
                    <textarea id="addActivityDescription" class="form-textarea" rows="2" placeholder="Opsional"></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-save"></i> Simpan Kegiatan
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Detail Activities -->
<div class="modal-overlay" id="detailModal">
    <div class="modal-content" style="max-width: 900px;">
        <div class="modal-header">
            <h3><i class="fas fa-list-alt"></i> Detail Kegiatan</h3>
            <button class="modal-close" onclick="closeModal('detailModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="budget-info">
                <p>Program: <strong id="detailProgramName"></strong></p>
                <p>Anggaran: <span id="detailBudget"></span></p>
                <p>Total Terpakai: <span id="detailTotalCost" style="color: var(--danger);"></span></p>
                <p class="remaining">Sisa Anggaran: <span id="detailRemainingBudget"></span></p>
            </div>
            <div class="table-responsive" id="activityListContainer">
                <p style="text-align: center; color: var(--text-light);">Memuat data...</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Delete Activity Confirmation -->
<div class="modal-overlay" id="deleteActivityModal" style="z-index: 2100;">
    <div class="modal-content" style="max-width: 400px;">
        <div class="modal-header" style="border-bottom-color: var(--danger);">
            <h3 style="color: var(--danger);"><i class="fas fa-exclamation-triangle"></i> Konfirmasi Hapus</h3>
            <button class="modal-close" onclick="closeDeleteActivityModal()">&times;</button>
        </div>
        <div class="modal-body" style="text-align: center;">
            <p style="margin-bottom: 20px;">Apakah Anda yakin ingin menghapus kegiatan ini?</p>
            <p style="font-weight: 600; margin-bottom: 20px;" id="deleteActivityName"></p>
            <input type="hidden" id="deleteActivityId">
            <input type="hidden" id="deleteActivityProgramId">
            <div style="display: flex; gap: 10px; justify-content: center;">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteActivityModal()">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="button" class="btn btn-danger" onclick="confirmDeleteActivity()">
                    <i class="fas fa-trash"></i> Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Edit Program -->
<div class="modal-overlay" id="editProgramModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit Rencana Program</h3>
            <button class="modal-close" onclick="closeModal('editProgramModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editProgramForm" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label class="form-label">Nama Rencana Program <span style="color: var(--danger);">*</span></label>
                    <input type="text" name="name" id="editProgramName" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kebutuhan Biaya <span style="color: var(--danger);">*</span></label>
                    <input type="number" name="budget" id="editProgramBudget" class="form-input" required min="0" step="1000">
                </div>
                <div class="form-group">
                    <label class="form-label">Keterangan</label>
                    <textarea name="description" id="editProgramDescription" class="form-textarea" rows="2"></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Activity -->
<div class="modal-overlay" id="editActivityModal" style="z-index: 2100;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit Kegiatan</h3>
            <button class="modal-close" onclick="closeEditActivityModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editActivityForm">
                <input type="hidden" id="editActivityId">
                <input type="hidden" id="editActivityProgramId">
                <div class="form-group">
                    <label class="form-label">Uraian Kegiatan <span style="color: var(--danger);">*</span></label>
                    <input type="text" id="editActivityName" class="form-input" required>
                </div>
                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">Harga Satuan <span style="color: var(--danger);">*</span></label>
                        <input type="number" id="editActivityUnitPrice" class="form-input" required min="0" step="1000" oninput="calculateEditActivityTotal()">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">Quantity <span style="color: var(--danger);">*</span></label>
                        <input type="number" id="editActivityQuantity" class="form-input" required min="1" step="1" oninput="calculateEditActivityTotal()">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Total Biaya</label>
                    <input type="text" id="editActivityCostDisplay" class="form-input" readonly style="background: var(--accent); font-weight: 600; color: var(--primary);">
                    <input type="hidden" id="editActivityCost">
                </div>
                <div class="form-group">
                    <label class="form-label">Keterangan</label>
                    <textarea id="editActivityDescription" class="form-textarea" rows="2"></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        @if(count($programs ?? []) > 0)
        $('#programsTable').DataTable({
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            },
            order: [
                [0, 'asc']
            ],
            columnDefs: [{
                orderable: false,
                targets: 5
            }]
        });
        @endif
    });

    function formatRupiah(number) {
        return 'Rp ' + number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function calculateAddActivityTotal() {
        const unitPrice = parseFloat(document.getElementById('addActivityUnitPrice').value) || 0;
        const quantity = parseFloat(document.getElementById('addActivityQuantity').value) || 0;
        const total = unitPrice * quantity;
        document.getElementById('addActivityCost').value = total;
        document.getElementById('addActivityCostDisplay').value = total > 0 ? formatRupiah(total) : '';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
    }

    function openAddActivityModal(programId, programName, remainingBudget) {
        document.getElementById('addActivityProgramId').value = programId;
        document.getElementById('addActivityProgramName').textContent = programName;
        document.getElementById('addActivityRemainingBudget').textContent = formatRupiah(remainingBudget);
        document.getElementById('addActivityName').value = '';
        document.getElementById('addActivityUnitPrice').value = '';
        document.getElementById('addActivityQuantity').value = '';
        document.getElementById('addActivityCost').value = '';
        document.getElementById('addActivityCostDisplay').value = '';
        document.getElementById('addActivityDescription').value = '';
        document.getElementById('addActivityModal').classList.add('active');
    }

    document.getElementById('addActivityForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const programId = document.getElementById('addActivityProgramId').value;
        const data = {
            name: document.getElementById('addActivityName').value,
            unit_price: document.getElementById('addActivityUnitPrice').value,
            quantity: document.getElementById('addActivityQuantity').value,
            cost: document.getElementById('addActivityCost').value,
            description: document.getElementById('addActivityDescription').value,
            _token: '{{ csrf_token() }}'
        };

        fetch(`/admin/committee/planning/${programId}/activities`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    Swal.fire('Berhasil!', result.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Gagal!', result.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'Terjadi kesalahan. Silakan coba lagi.', 'error');
            });
    });

    function openDetailModal(programId) {
        document.getElementById('detailModal').classList.add('active');
        document.getElementById('activityListContainer').innerHTML = '<p style="text-align: center; color: var(--text-light);">Memuat data...</p>';

        fetch(`/admin/committee/planning/${programId}/activities`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('detailProgramName').textContent = data.program.name;
                document.getElementById('detailBudget').textContent = formatRupiah(data.program.budget);
                document.getElementById('detailTotalCost').textContent = formatRupiah(data.total_cost);
                document.getElementById('detailRemainingBudget').textContent = formatRupiah(data.remaining_budget);

                let html = '';
                if (data.activities.length === 0) {
                    html = '<p style="text-align: center; color: var(--text-light); padding: 20px;">Belum ada kegiatan untuk program ini.</p>';
                } else {
                    html = `
                    <table style="width: 100%;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Uraian</th>
                                <th style="text-align: right;">Harga Satuan</th>
                                <th style="text-align: center;">Qty</th>
                                <th style="text-align: right;">Total Biaya</th>
                                <th style="text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>`;
                    data.activities.forEach(function(activity, index) {
                        const unitPrice = activity.unit_price || 0;
                        const quantity = activity.quantity || 1;
                        html += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>
                                    <strong>${activity.name}</strong>
                                    ${activity.description ? '<br><small style="color: var(--text-light);">' + activity.description + '</small>' : ''}
                                </td>
                                <td style="text-align: right;">${formatRupiah(unitPrice)}</td>
                                <td style="text-align: center;">${quantity}</td>
                                <td style="text-align: right; font-weight: 600; color: var(--primary);">${formatRupiah(activity.cost)}</td>
                                <td style="text-align: center;">
                                    <div class="action-icons">
                                        <button class="btn btn-sm btn-warning" title="Edit" onclick="openEditActivityModal(${activity.id}, ${programId}, '${activity.name.replace(/'/g, "\\'")}', ${unitPrice}, ${quantity}, '${(activity.description || '').replace(/'/g, "\\'")}')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" title="Hapus" onclick="openDeleteActivityModal(${activity.id}, ${programId}, '${activity.name.replace(/'/g, "\\'")}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                    html += '</tbody></table>';
                }
                document.getElementById('activityListContainer').innerHTML = html;
            });
    }

    function openEditModal(programId, name, budget, description) {
        document.getElementById('editProgramForm').action = `/admin/committee/planning/${programId}`;
        document.getElementById('editProgramName').value = name;
        document.getElementById('editProgramBudget').value = budget;
        document.getElementById('editProgramDescription').value = description;
        document.getElementById('editProgramModal').classList.add('active');
    }

    function openEditActivityModal(activityId, programId, name, unitPrice, quantity, description) {
        document.getElementById('editActivityId').value = activityId;
        document.getElementById('editActivityProgramId').value = programId;
        document.getElementById('editActivityName').value = name;
        document.getElementById('editActivityUnitPrice').value = unitPrice;
        document.getElementById('editActivityQuantity').value = quantity;
        document.getElementById('editActivityDescription').value = description;
        calculateEditActivityTotal();
        document.getElementById('editActivityModal').classList.add('active');
    }

    function closeEditActivityModal() {
        document.getElementById('editActivityModal').classList.remove('active');
    }

    function calculateEditActivityTotal() {
        const unitPrice = parseFloat(document.getElementById('editActivityUnitPrice').value) || 0;
        const quantity = parseFloat(document.getElementById('editActivityQuantity').value) || 0;
        const total = unitPrice * quantity;
        document.getElementById('editActivityCost').value = total;
        document.getElementById('editActivityCostDisplay').value = total > 0 ? formatRupiah(total) : '';
    }

    document.getElementById('editActivityForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const activityId = document.getElementById('editActivityId').value;
        const programId = document.getElementById('editActivityProgramId').value;
        const data = {
            name: document.getElementById('editActivityName').value,
            unit_price: document.getElementById('editActivityUnitPrice').value,
            quantity: document.getElementById('editActivityQuantity').value,
            cost: document.getElementById('editActivityCost').value,
            description: document.getElementById('editActivityDescription').value,
            _token: '{{ csrf_token() }}',
            _method: 'PUT'
        };

        fetch(`/admin/committee/planning/activities/${activityId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    Swal.fire('Berhasil!', result.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Gagal!', result.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'Terjadi kesalahan. Silakan coba lagi.', 'error');
            });
    });

    function openDeleteActivityModal(activityId, programId, activityName) {
        document.getElementById('deleteActivityId').value = activityId;
        document.getElementById('deleteActivityProgramId').value = programId;
        document.getElementById('deleteActivityName').textContent = activityName;
        document.getElementById('deleteActivityModal').classList.add('active');
    }

    function closeDeleteActivityModal() {
        document.getElementById('deleteActivityModal').classList.remove('active');
    }

    function confirmDeleteActivity() {
        const activityId = document.getElementById('deleteActivityId').value;
        const programId = document.getElementById('deleteActivityProgramId').value;

        fetch(`/admin/committee/planning/activities/${activityId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeDeleteActivityModal();
                    Swal.fire('Berhasil!', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Gagal!', data.message || 'Gagal menghapus kegiatan.', 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'Terjadi kesalahan. Silakan coba lagi.', 'error');
            });
    }

    function confirmDeleteProgram(programId) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Program beserta semua kegiatannya akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-program-' + programId).submit();
            }
        });
    }

    // Close modal when clicking outside
    document.querySelectorAll('.modal-overlay').forEach(function(modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        });
    });
</script>
@endsection