@extends('admin.layouts.app')

@section('title', 'Catat Pembayaran - ' . $student->name)
@section('page-title', 'Pembayaran: ' . $student->name)

@section('content')
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.committee.payments.students', ['schoolClass' => $student->school_class_id, 'academic_year_id' => $committeeFee->academic_year_id]) }}"
            class="btn btn-sm" style="display: inline-flex; align-items: center; gap: 6px;">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Siswa
        </a>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 350px; gap: 25px;">
        <!-- Left: Payment History -->
        <div>
            <!-- Current Year Payments -->
            <div class="card" style="margin-bottom: 25px;">
                <div class="card-header">
                    <h2><i class="fas fa-history"></i> Riwayat Sumbangan ({{ $committeeFee->academicYear->year }})</h2>
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
                                            <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                                <a href="{{ route('admin.committee.payments.receipt', $payment->id) }}"
                                                    class="btn btn-sm btn-success" target="_blank" title="Cetak Kwitansi">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                <a href="{{ route('admin.committee.payments.edit', $payment->id) }}"
                                                    class="btn btn-sm btn-warning" title="Edit Pembayaran">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.committee.payments.destroy', $payment->id) }}"
                                                    method="POST" id="delete-form-{{ $payment->id }}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger" title="Hapus Pembayaran"
                                                        onclick="confirmDelete({{ $payment->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
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
                        <div
                            style="margin-top: 30px; text-align: center; padding: 20px; background: rgba(40, 167, 69, 0.1); border: 2px dashed var(--success); border-radius: 12px;">
                            <i class="fas fa-check-circle" style="font-size: 2rem; color: var(--success); margin-bottom: 10px;"></i>
                            <h3 style="color: var(--success);">Sumbangan Komite</h3>
                            <p style="margin-bottom: 15px;">Seluruh nominal dana komite telah dibayarkan.</p>
                            <a href="{{ route('admin.committee.payments.invoice', $student->id) }}"
                                class="btn btn-success" target="_blank" style="display: inline-flex; align-items: center; gap: 8px;">
                                <i class="fas fa-print"></i> Cetak Bukti Sumbangan
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- All Years Payment History -->
            @if($allPayments->count() > 0)
                @foreach($allPayments as $yearLabel => $yearPayments)
                    <div class="card" style="margin-bottom: 25px;">
                        <div class="card-header">
                            <h2><i class="fas fa-calendar-alt"></i> Riwayat Pembayaran {{ $yearLabel }}</h2>
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
                                        @foreach($yearPayments as $payment)
                                            <tr>
                                                <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                                <td><strong>Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong></td>
                                                <td>{{ $payment->notes ?? '-' }}</td>
                                                <td>
                                                    <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                                        <a href="{{ route('admin.committee.payments.receipt', $payment->id) }}"
                                                            class="btn btn-sm btn-success" target="_blank" title="Cetak Kwitansi">
                                                            <i class="fas fa-print"></i>
                                                        </a>
                                                        <a href="{{ route('admin.committee.payments.edit', $payment->id) }}"
                                                            class="btn btn-sm btn-warning" title="Edit Pembayaran">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('admin.committee.payments.destroy', $payment->id) }}"
                                                            method="POST" id="delete-form-all-{{ $payment->id }}" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-danger" title="Hapus Pembayaran"
                                                                onclick="confirmDelete({{ $payment->id }})">
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
                @endforeach
            @endif
        </div>

        <!-- Right: Summary & Form -->
        <div>
            <div class="card" style="margin-bottom: 25px;">
                <div class="card-header">
                    <h2><i class="fas fa-info-circle"></i> Ringkasan Tahun Ini</h2>
                </div>
                <div class="card-body">
                    <div style="margin-bottom: 15px;">
                        <span style="font-size: 0.8rem; color: var(--text-light); text-transform: uppercase;">Tahun Ajaran</span>
                        <h3 style="font-size: 1.2rem; color: var(--text);">{{ $committeeFee->academicYear->year }}</h3>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <span style="font-size: 0.8rem; color: var(--text-light); text-transform: uppercase;">Total Sumbangan</span>
                        <h3 style="font-size: 1.4rem; color: var(--text);">Rp {{ number_format($committeeFee->amount, 0, ',', '.') }}</h3>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <span style="font-size: 0.8rem; color: var(--text-light); text-transform: uppercase;">Sudah Dibayar</span>
                        <h3 style="font-size: 1.4rem; color: var(--success);">Rp {{ number_format($totalPaid, 0, ',', '.') }}</h3>
                    </div>
                    <div style="border-top: 1px solid var(--accent); padding-top: 15px;">
                        <span style="font-size: 0.8rem; color: var(--text-light); text-transform: uppercase;">Sisa Sumbangan</span>
                        <h3 style="font-size: 1.4rem; color: var(--danger);">Rp {{ number_format($remaining, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>

            <!-- Yearly Summaries -->
            <div class="card" style="margin-bottom: 25px;">
                <div class="card-header">
                    <h2><i class="fas fa-chart-bar"></i> Riwayat per Tahun Ajaran</h2>
                </div>
                <div class="card-body" style="padding: 0;">
                    @forelse($yearlySummaries as $summary)
                        <div style="padding: 15px; border-bottom: 1px solid var(--accent);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                                <strong>{{ $summary['academic_year']->year }}</strong>
                                @if($summary['is_paid_full'])
                                    <span class="badge badge-success">Lunas</span>
                                @elseif($summary['total_paid'] > 0)
                                    <span class="badge badge-warning">Cicil</span>
                                @else
                                    <span class="badge badge-danger">Belum</span>
                                @endif
                            </div>
                            <div style="font-size: 0.85rem; color: var(--text-light);">
                                <div>Target: Rp {{ number_format($summary['fee_amount'], 0, ',', '.') }}</div>
                                <div>Bayar: Rp {{ number_format($summary['total_paid'], 0, ',', '.') }}</div>
                                <div>Sisa: Rp {{ number_format($summary['remaining'], 0, ',', '.') }}</div>
                            </div>
                            <a href="{{ route('admin.committee.payments.record', ['student' => $student->id, 'academic_year_id' => $summary['academic_year']->id]) }}"
                                style="font-size: 0.8rem; margin-top: 5px; display: inline-block;">
                                <i class="fas fa-external-link-alt"></i> Detail
                            </a>
                        </div>
                    @empty
                        <div style="padding: 20px; text-align: center; color: var(--text-light);">
                            Belum ada data.
                        </div>
                    @endforelse
                </div>
            </div>

            @if($remaining > 0)
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-plus"></i> Form Pembayaran</h2>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.committee.payments.store', ['student' => $student->id, 'academic_year_id' => $committeeFee->academic_year_id]) }}" method="POST">
                            @csrf
                            <input type="hidden" name="committee_fee_id" value="{{ $committeeFee->id }}">

                            <div class="form-group">
                                <label class="form-label">Nominal Bayar (Rp)</label>
                                <input type="number" name="amount" class="form-input" max="{{ $remaining }}" min="1" required
                                    placeholder="Masukkan nominal...">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Tanggal Pembayaran</label>
                                <input type="date" name="payment_date" class="form-input" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Catatan (Opsional)</label>
                                <textarea name="notes" class="form-textarea" style="min-height: 80px;"
                                    placeholder="Contoh: Cicilan ke-1"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                                <i class="fas fa-save"></i> Simpan Pembayaran
                            </button>
                            <a href="{{ route('admin.committee.payments.students', ['schoolClass' => $student->school_class_id, 'academic_year_id' => $committeeFee->academic_year_id]) }}"
                                class="btn btn-sm" style="width: 100%; justify-content: center; margin-top: 10px;">
                                Batal
                            </a>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @section('scripts')
        <script>
            function confirmDelete(id) {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data pembayaran ini akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + id).submit();
                    }
                });
            }
        </script>
    @endsection
@endsection