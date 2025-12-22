@extends('admin.layouts.app')

@section('title', 'Catat Penggunaan Dana')
@section('page-title', 'Catat Penggunaan Dana')

@section('content')
<div class="card" style="max-width: 800px;">
    <div class="card-header">
        <h2><i class="fas fa-plus-circle"></i> Form Penggunaan Dana</h2>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.committee.expenditures.store') }}" method="POST">
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

            <div class="form-group">
                <label class="form-label">Nominal Pengeluaran (Rp) <span style="color: var(--danger);">*</span></label>
                <input type="number" name="amount" class="form-input" value="{{ old('amount') }}" required min="0" placeholder="Contoh: 500000">
                @error('amount')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi Penggunaan <span style="color: var(--danger);">*</span></label>
                <textarea name="description" class="form-textarea" rows="4" required placeholder="Jelaskan penggunaan dana ini secara detail...">{{ old('description') }}</textarea>
                @error('description')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
            </div>

            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Data
                </button>
                <a href="{{ route('admin.committee.expenditures.index') }}" class="btn btn-secondary">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection