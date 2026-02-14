@extends('layouts.app')

@section('title', 'Cek Pembayaran Dana Komite - ' . ($school->name ?? 'SMP Negeri 6 Sudimoro'))

@section('styles')
<style>
    .komite-container {
        max-width: 950px;
        margin: 0 auto;
    }

    .komite-card {
        background: var(--secondary);
        border-radius: 20px;
        padding: 40px;
        box-shadow: var(--shadow-lg);
        border: 1px solid rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
    }

    .komite-search-group {
        display: flex;
        gap: 15px;
    }

    .komite-search-input {
        flex: 1;
        padding: 14px 20px;
        border: 2px solid #eef2f7;
        border-radius: 12px;
        font-size: 1rem;
        transition: var(--transition);
        background: #fdfdfd;
    }

    .btn-komite-search {
        padding: 0 35px;
        background: var(--primary);
        color: var(--secondary);
        border: none;
        border-radius: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 10px;
        height: 52px;
    }

    .status-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 30px;
        gap: 20px;
    }

    .payment-badge {
        display: inline-block;
        padding: 10px 25px;
        border-radius: 30px;
        font-weight: 700;
        font-size: 0.9rem;
        color: white;
        box-shadow: var(--shadow-sm);
    }

    .komite-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 20px;
        background: var(--accent);
        padding: 25px;
        border-radius: 15px;
        margin-bottom: 30px;
    }

    .komite-info-item label {
        display: block;
        font-size: 0.85rem;
        color: var(--text-light);
        margin-bottom: 5px;
    }

    .komite-info-item .value {
        font-weight: 700;
        color: var(--primary);
        font-size: 1rem;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 35px;
    }

    .summary-card {
        padding: 25px;
        border-radius: 18px;
        text-align: center;
        color: white;
        box-shadow: var(--shadow-md);
    }

    .summary-card label {
        font-size: 0.85rem;
        opacity: 0.9;
        margin-bottom: 8px;
        display: block;
    }

    .summary-card .amount {
        font-size: 1.4rem;
        font-weight: 800;
    }

    .history-table-wrapper {
        overflow-x: auto;
        margin-top: 20px;
        border-radius: 12px;
        border: 1px solid #eef2f7;
    }

    .history-table {
        width: 100%;
        border-collapse: collapse;
    }

    .history-table th {
        background: #f8f9fa;
        padding: 15px;
        text-align: left;
        font-weight: 700;
        color: var(--primary);
        border-bottom: 2px solid #eef2f7;
    }

    .history-table td {
        padding: 15px;
        border-bottom: 1px solid #f1f4f8;
        font-size: 0.95rem;
    }

    @media (max-width: 768px) {
        .komite-card {
            padding: 25px 20px;
            border-radius: 15px;
        }

        .komite-search-group {
            flex-direction: column;
        }

        .btn-komite-search {
            width: 100%;
            justify-content: center;
        }

        .status-header {
            flex-direction: column;
            text-align: center;
            align-items: center;
        }

        .status-header div:last-child {
            text-align: center !important;
        }

        .summary-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .summary-card {
            padding: 20px;
        }
    }
</style>
@endsection

@section('content')
<section class="section">
    <div class="container">
        <div class="komite-container animate-fade-in">
            <div class="section-title">Cek Pembayaran Dana Komite</div>
            <p class="section-subtitle">Masukkan NIS (Nomor Induk Siswa) untuk melihat status pembayaran dana komite.</p>

            @if(session('error'))
            <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 12px; margin-bottom: 25px; border: 1px solid #f5c6cb;">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
            @endif

            <div class="komite-card" style="padding: 30px;">
                <form action="{{ route('komite.status') }}" method="GET">
                    <div class="komite-search-group">
                        <input type="text" name="nis" class="komite-search-input" value="{{ request('nis') }}" placeholder="Masukkan NIS Siswa" required>
                        <button type="submit" class="btn-komite-search">
                            <i class="fas fa-search"></i> Cek Status
                        </button>
                    </div>
                </form>
            </div>

            @if(isset($student) && $student)
            <div class="komite-card animate-fade-in" style="border-left: 6px solid var(--primary);">
                <div class="status-header">
                    <div>
                        <h3 style="color: var(--primary); margin-bottom: 5px;">
                            <i class="fas fa-user-graduate"></i> {{ $student->name }}
                        </h3>
                        <p style="color: var(--text-light); font-weight: 500;">{{ $student->schoolClass->name ?? 'Kelas tidak ditemukan' }}</p>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 0.8rem; text-transform: uppercase; color: var(--text-light); margin-bottom: 8px; font-weight: 600;">Status Pembayaran</div>
                        @if(isset($paymentData) && $paymentData['is_paid_full'])
                        <span class="payment-badge" style="background: linear-gradient(135deg, #28a745, #20c997);">
                            <i class="fas fa-check-circle"></i> LUNAS
                        </span>
                        @else
                        <span class="payment-badge" style="background: linear-gradient(135deg, #ffc107, #fd7e14);">
                            <i class="fas fa-clock"></i> BELUM LUNAS
                        </span>
                        @endif
                    </div>
                </div>

                <div class="komite-grid">
                    <div class="komite-info-item">
                        <label>NIS</label>
                        <div class="value">{{ $student->nis }}</div>
                    </div>
                    <div class="komite-info-item">
                        <label>Jenis Kelamin</label>
                        <div class="value">{{ $student->gender_label }}</div>
                    </div>
                    <div class="komite-info-item">
                        <label>Tahun Masuk</label>
                        <div class="value">{{ $student->enrollment_year }}</div>
                    </div>
                    @if(isset($paymentData))
                    <div class="komite-info-item">
                        <label>Tahun Ajaran</label>
                        <div class="value">{{ $paymentData['academic_year']->year }}</div>
                    </div>
                    @endif
                </div>

                @if(isset($paymentData))
                <div class="summary-grid">
                    <div class="summary-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <label>Total Tagihan</label>
                        <div class="amount">Rp {{ number_format($paymentData['committee_fee']->amount, 0, ',', '.') }}</div>
                    </div>
                    <div class="summary-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                        <label>Total Dibayar</label>
                        <div class="amount">Rp {{ number_format($paymentData['total_paid'], 0, ',', '.') }}</div>
                    </div>
                    <div class="summary-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <label>Sisa Tagihan</label>
                        <div class="amount">Rp {{ number_format($paymentData['remaining'], 0, ',', '.') }}</div>
                    </div>
                </div>

                @php
                $percentage = $paymentData['committee_fee']->amount > 0
                ? min(100, ($paymentData['total_paid'] / $paymentData['committee_fee']->amount) * 100)
                : 0;
                @endphp
                <div style="margin-bottom: 40px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span style="font-size: 0.95rem; font-weight: 600; color: var(--text);">Progres Pelunasan</span>
                        <span style="font-size: 0.95rem; font-weight: 700; color: var(--primary);">{{ number_format($percentage, 1) }}%</span>
                    </div>
                    <div style="background: #eef2f7; border-radius: 15px; height: 14px; overflow: hidden; box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);">
                        <div style="background: linear-gradient(90deg, #667eea, #764ba2); height: 100%; width: {{ $percentage }}%; border-radius: 15px; transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);"></div>
                    </div>
                </div>

                <div style="margin-top: 40px;">
                    <h4 style="color: var(--primary); margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                        <i class="fas fa-history"></i> Riwayat Pembayaran
                    </h4>

                    @if($paymentData['payments']->count() > 0)
                    <div class="history-table-wrapper">
                        <table class="history-table">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">No</th>
                                    <th>Tanggal</th>
                                    <th style="text-align: right;">Jumlah</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paymentData['payments'] as $index => $payment)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td style="font-weight: 500;">{{ $payment->payment_date->format('d M Y') }}</td>
                                    <td style="text-align: right; font-weight: 700; color: #28a745;">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                    <td style="color: var(--text-light);">{{ $payment->notes ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div style="text-align: center; padding: 40px; background: #f8fafc; border-radius: 15px; border: 2px dashed #e2e8f0;">
                        <i class="fas fa-receipt" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 15px;"></i>
                        <p style="color: #64748b; font-weight: 500; margin: 0;">Belum ada catatan riwayat pembayaran untuk siswa ini.</p>
                    </div>
                    @endif
                </div>

                @else
                <div style="text-align: center; padding: 40px; background: rgba(255, 193, 7, 0.05); border-radius: 15px; border: 2px dashed #ffc107;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 2.5rem; color: #f59e0b; margin-bottom: 15px;"></i>
                    <p style="color: #92400e; font-weight: 600; margin: 0;">Data tagihan komite untuk tahun ajaran aktif belum tersedia.</p>
                </div>
                @endif

                <div style="margin-top: 40px; padding: 20px 25px; background: rgba(30, 58, 95, 0.03); border-radius: 15px; border-left: 5px solid var(--primary); display: flex; gap: 15px; align-items: center;">
                    <i class="fas fa-info-circle" style="color: var(--primary); font-size: 1.2rem;"></i>
                    <p style="margin: 0; font-size: 0.95rem; color: var(--text-light); line-height: 1.5;">
                        Informasi pembayaran diperbarui secara otomatis setelah divalidasi oleh bendahara sekolah. Untuk pertanyaan, silakan hubungi layanan informasi sekolah.
                    </p>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection