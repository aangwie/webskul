@extends('admin.layouts.app')

@section('title', 'Catat Pembayaran - ' . $student->name)
@section('page-title', 'Pembayaran: ' . $student->name)

@section('content')
<div style="display: grid; grid-template-columns: 1fr 350px; gap: 25px;">
    <!-- Payment History -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-history"></i> Riwayat Pembayaran</h2>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nominal</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                            <td><strong>Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong></td>
                            <td>{{ $payment->notes ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.committee.payments.receipt', $payment->id) }}" class="btn btn-sm btn-success" target="_blank">
                                    <i class="fas fa-print"></i> Kwitansi
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 40px; color: var(--text-light);">
                                Belum ada riwayat pembayaran.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($totalPaid >= $committeeFee->amount)
            <div style="margin-top: 30px; text-align: center; padding: 20px; background: rgba(40, 167, 69, 0.1); border: 2px dashed var(--success); border-radius: 12px;">
                <i class="fas fa-check-circle" style="font-size: 2rem; color: var(--success); margin-bottom: 10px;"></i>
                <h3 style="color: var(--success);">Pembayaran Lunas</h3>
                <p>Seluruh nominal dana komite telah dibayarkan.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Payment Form & Status -->
    <div>
        <div class="card" style="margin-bottom: 25px;">
            <div class="card-header">
                <h2><i class="fas fa-info-circle"></i> Ringkasan</h2>
            </div>
            <div class="card-body">
                <div style="margin-bottom: 15px;">
                    <span style="font-size: 0.8rem; color: var(--text-light); text-transform: uppercase;">Total Tagihan</span>
                    <h3 style="font-size: 1.4rem; color: var(--text);">Rp {{ number_format($committeeFee->amount, 0, ',', '.') }}</h3>
                </div>
                <div style="margin-bottom: 15px;">
                    <span style="font-size: 0.8rem; color: var(--text-light); text-transform: uppercase;">Sudah Dibayar</span>
                    <h3 style="font-size: 1.4rem; color: var(--success);">Rp {{ number_format($totalPaid, 0, ',', '.') }}</h3>
                </div>
                <div style="border-top: 1px solid var(--accent); padding-top: 15px;">
                    <span style="font-size: 0.8rem; color: var(--text-light); text-transform: uppercase;">Sisa Tagihan</span>
                    <h3 style="font-size: 1.4rem; color: var(--danger);">Rp {{ number_format($remaining, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        @if($remaining > 0)
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-plus"></i> Form Pembayaran</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.committee.payments.store', $student->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="committee_fee_id" value="{{ $committeeFee->id }}">

                    <div class="form-group">
                        <label class="form-label">Nominal Bayar (Rp)</label>
                        <input type="number" name="amount" class="form-input" max="{{ $remaining }}" min="1" required placeholder="Masukkan nominal...">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal Pembayaran</label>
                        <input type="date" name="payment_date" class="form-input" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea name="notes" class="form-textarea" style="min-height: 80px;" placeholder="Contoh: Cicilan ke-1"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                        <i class="fas fa-save"></i> Simpan Pembayaran
                    </button>
                    <a href="{{ route('admin.committee.payments.students', $student->school_class_id) }}" class="btn btn-sm" style="width: 100%; justify-content: center; margin-top: 10px;">
                        Batal
                    </a>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection