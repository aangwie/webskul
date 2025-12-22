@extends('admin.layouts.app')

@section('title', 'Edit Pembayaran')
@section('page-title', 'Edit Pembayaran: ' . $committeePayment->student->name)

@section('content')
<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h2><i class="fas fa-edit"></i> Edit Data Pembayaran</h2>
    </div>
    <div class="card-body">
        <div style="background: var(--accent); padding: 15px; border-radius: 8px; margin-bottom: 25px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <span style="font-size: 0.8rem; color: var(--text-light);">Nama Siswa</span>
                    <div style="font-weight: 600;">{{ $committeePayment->student->name }}</div>
                </div>
                <div>
                    <span style="font-size: 0.8rem; color: var(--text-light);">Kelas</span>
                    <div style="font-weight: 600;">{{ $committeePayment->student->schoolClass->name ?? '-' }}</div>
                </div>
                <div>
                    <span style="font-size: 0.8rem; color: var(--text-light);">Total Tagihan</span>
                    <div style="font-weight: 600;">Rp {{ number_format($committeePayment->committeeFee->amount, 0, ',', '.') }}</div>
                </div>
                <div>
                    <span style="font-size: 0.8rem; color: var(--text-light);">Maksimal Pembayaran</span>
                    <div style="font-weight: 600; color: var(--success);">Rp {{ number_format($maxAmount, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.committee.payments.update', $committeePayment->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Nominal Bayar (Rp) <span style="color: var(--danger);">*</span></label>
                <input type="number" name="amount" class="form-input @error('amount') is-invalid @enderror"
                    value="{{ old('amount', $committeePayment->amount) }}"
                    max="{{ $maxAmount }}" min="1" required>
                @error('amount')
                <span style="color: var(--danger); font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Tanggal Pembayaran <span style="color: var(--danger);">*</span></label>
                <input type="date" name="payment_date" class="form-input @error('payment_date') is-invalid @enderror"
                    value="{{ old('payment_date', $committeePayment->payment_date->format('Y-m-d')) }}" required>
                @error('payment_date')
                <span style="color: var(--danger); font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Catatan (Opsional)</label>
                <textarea name="notes" class="form-textarea" style="min-height: 80px;"
                    placeholder="Contoh: Cicilan ke-1">{{ old('notes', $committeePayment->notes) }}</textarea>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="{{ route('admin.committee.payments.record', $committeePayment->student_id) }}" class="btn btn-secondary" style="flex: 1; justify-content: center;">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection