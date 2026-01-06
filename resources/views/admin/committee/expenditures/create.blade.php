@extends('admin.layouts.app')

@section('title', 'Catat Penggunaan Dana')
@section('page-title', 'Catat Penggunaan Dana')

@section('content')
<div class="card" style="max-width: 800px;">
    <div class="card-header">
        <h2><i class="fas fa-plus-circle"></i> Form Penggunaan Dana</h2>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.committee.expenditures.store') }}" method="POST" id="expenditureForm">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label class="form-label">Nomor Pengeluaran</label>
                    <input type="text" name="expenditure_number" class="form-input" value="{{ old('expenditure_number', $expNumber) }}" required readonly style="background: var(--accent); font-family: monospace;">
                    @error('expenditure_number')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Pengeluaran <span style="color: var(--danger);">*</span></label>
                    <input type="date" name="date" class="form-input" value="{{ old('date', date('Y-m-d')) }}" required>
                    @error('date')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
                </div>
            </div>

            <!-- New Logic: Year -> Program -> Activity -->
            <div class="form-group">
                <label class="form-label">Tahun Pelajaran <span style="color: var(--danger);">*</span></label>
                <select name="academic_year_id" id="yearSelect" class="form-input" required>
                    <option value="">Pilih Tahun Pelajaran</option>
                    @foreach($academicYears as $year)
                    <option value="{{ $year->id }}" {{ ($activeYear && $activeYear->id == $year->id) ? 'selected' : '' }}>
                        {{ $year->year }} {{ $year->is_active ? '' : '' }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Program Kegiatan <span style="color: var(--danger);">*</span></label>
                <select name="committee_program_id" id="programSelect" class="form-input" required disabled>
                    <option value="">Pilih Program</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Sub Program (Kegiatan) <span style="color: var(--danger);">*</span></label>
                <select name="committee_activity_id" id="activitySelect" class="form-input" required disabled>
                    <option value="">Pilih Sub Program</option>
                </select>
            </div>

            <div class="form-group" id="budgetInfo" style="display: none; background: aliceblue; padding: 15px; border-radius: 8px; border: 1px solid #b6d4fe;">
                <p style="margin: 0; font-weight: 600; color: #084298;">
                    <i class="fas fa-info-circle"></i> Sisa Anggaran: <span id="remainingBudgetDisplay">Rp 0</span>
                </p>
                <input type="hidden" id="maxBudget" value="0">
            </div>

            <div class="form-group">
                <label class="form-label">Nominal Pengeluaran (Rp) <span style="color: var(--danger);">*</span></label>
                <input type="number" name="amount" id="amountInput" class="form-input" value="{{ old('amount') }}" required min="0" placeholder="Contoh: 500000">
                <span id="amountError" style="color: var(--danger); font-size: 0.8rem; display: none;">Nominal melebihi sisa anggaran!</span>
                @error('amount')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi Penggunaan <span style="color: var(--danger);">*</span></label>
                <textarea name="description" class="form-textarea" rows="4" required placeholder="Jelaskan penggunaan dana ini secara detail...">{{ old('description') }}</textarea>
                @error('description')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
            </div>

            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-save"></i> Simpan Data
                </button>
                <a href="{{ route('admin.committee.expenditures.index') }}" class="btn btn-secondary">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<style>
    /* Fix for invisible options in some browsers/themes */
    select.form-input,
    select.form-input option {
        color: navy !important;
        background-color: #fff !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const yearSelect = document.getElementById('yearSelect');
        const programSelect = document.getElementById('programSelect');
        const activitySelect = document.getElementById('activitySelect');
        const budgetInfo = document.getElementById('budgetInfo');
        const remainingBudgetDisplay = document.getElementById('remainingBudgetDisplay');
        const maxBudgetInput = document.getElementById('maxBudget');
        const amountInput = document.getElementById('amountInput');
        const amountError = document.getElementById('amountError');
        const submitBtn = document.getElementById('submitBtn');

        // Load programs if year is selected initially
        if (yearSelect.value) {
            loadPrograms(yearSelect.value);
        }

        yearSelect.addEventListener('change', function() {
            if (this.value) {
                loadPrograms(this.value);
            } else {
                programSelect.innerHTML = '<option value="">Pilih Program</option>';
                programSelect.disabled = true;
                resetActivity();
            }
        });

        programSelect.addEventListener('change', function() {
            if (this.value) {
                loadActivities(this.value);
            } else {
                resetActivity();
            }
        });

        activitySelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (this.value) {
                const remaining = parseFloat(selectedOption.getAttribute('data-remaining'));
                const formatted = selectedOption.getAttribute('data-formatted');

                maxBudgetInput.value = remaining;
                remainingBudgetDisplay.textContent = 'Rp ' + formatted;
                budgetInfo.style.display = 'block';
                validateAmount();
            } else {
                budgetInfo.style.display = 'none';
                maxBudgetInput.value = 0;
            }
        });

        amountInput.addEventListener('input', validateAmount);

        function loadPrograms(yearId) {
            programSelect.disabled = true;
            programSelect.innerHTML = '<option>Memuat...</option>';

            fetch(`/admin/committee/expenditures/get-programs?year_id=${yearId}`)
                .then(response => response.json())
                .then(data => {
                    let html = '<option value="">Pilih Program</option>';
                    data.forEach(program => {
                        html += `<option value="${program.id}">${program.name}</option>`;
                    });
                    programSelect.innerHTML = html;
                    programSelect.disabled = false;
                })
                .catch(err => {
                    programSelect.innerHTML = '<option value="">Gagal memuat program</option>';
                });
        }

        function loadActivities(programId) {
            activitySelect.disabled = true;
            activitySelect.innerHTML = '<option>Memuat...</option>';

            fetch(`/admin/committee/expenditures/get-activities?program_id=${programId}`)
                .then(response => response.json())
                .then(data => {
                    let html = '<option value="">Pilih Sub Program</option>';
                    data.forEach(activity => {
                        html += `<option value="${activity.id}" data-remaining="${activity.remaining_budget}" data-formatted="${activity.formatted_remaining_budget}">${activity.name}</option>`;
                    });
                    activitySelect.innerHTML = html;
                    activitySelect.disabled = false;
                })
                .catch(err => {
                    activitySelect.innerHTML = '<option value="">Gagal memuat kegiatan</option>';
                });
        }

        function resetActivity() {
            activitySelect.innerHTML = '<option value="">Pilih Sub Program</option>';
            activitySelect.disabled = true;
            budgetInfo.style.display = 'none';
            maxBudgetInput.value = 0;
        }

        function validateAmount() {
            const amount = parseFloat(amountInput.value) || 0;
            const max = parseFloat(maxBudgetInput.value) || 0;

            if (max > 0 && amount > max) {
                amountError.style.display = 'block';
                submitBtn.disabled = true;
                amountInput.style.borderColor = 'var(--danger)';
            } else {
                amountError.style.display = 'none';
                submitBtn.disabled = false;
                amountInput.style.borderColor = '#ddd';
            }
        }
    });
</script>
</div>
</div>
@endsection