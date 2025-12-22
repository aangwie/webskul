@extends('layouts.app')

@section('title', 'Cek Pembayaran Dana Komite - ' . ($school->name ?? 'SMP Negeri 6 Sudimoro'))

@section('content')
<section class="section">
    <div class="container">
        <div class="animate-fade-in" style="max-width: 900px; margin: 0 auto;">
            <div class="section-title">Cek Pembayaran Dana Komite</div>
            <p class="section-subtitle">Masukkan NIS (Nomor Induk Siswa) untuk melihat status pembayaran dana komite.</p>

            @if(session('error'))
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #f5c6cb;">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
            @endif

            <div class="card" style="padding: 30px; margin-bottom: 30px;">
                <form action="{{ route('komite.status') }}" method="GET">
                    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 250px;">
                            <input type="text" name="nis" value="{{ request('nis') }}" placeholder="Masukkan NIS Siswa" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;">
                        </div>
                        <button type="submit" class="btn btn-primary" style="padding: 0 30px;">
                            <i class="fas fa-search"></i> Cek Status
                        </button>
                    </div>
                </form>
            </div>

            @if(isset($student) && $student)
            <div class="card animate-fade-in" style="padding: 30px; border-left: 5px solid var(--primary);">
                {{-- Student Information Header --}}
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 25px; flex-wrap: wrap; gap: 20px;">
                    <div>
                        <h3 style="color: var(--primary); margin-bottom: 5px;">
                            <i class="fas fa-user-graduate"></i> {{ $student->name }}
                        </h3>
                        <p style="color: var(--text-light);">{{ $student->schoolClass->name ?? 'Kelas tidak ditemukan' }}</p>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 0.8rem; text-transform: uppercase; color: var(--text-light); margin-bottom: 5px;">Status Pembayaran</div>
                        @if(isset($paymentData) && $paymentData['is_paid_full'])
                        <span style="display: inline-block; background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 8px 20px; border-radius: 25px; font-weight: 600; font-size: 0.9rem;">
                            <i class="fas fa-check-circle"></i> LUNAS
                        </span>
                        @else
                        <span style="display: inline-block; background: linear-gradient(135deg, #ffc107, #fd7e14); color: white; padding: 8px 20px; border-radius: 25px; font-weight: 600; font-size: 0.9rem;">
                            <i class="fas fa-clock"></i> BELUM LUNAS
                        </span>
                        @endif
                    </div>
                </div>

                {{-- Student Details --}}
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; background: var(--accent); padding: 20px; border-radius: 12px; margin-bottom: 25px;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; color: var(--text-light);">NIS</label>
                        <div style="font-weight: 700; color: var(--primary);">{{ $student->nis }}</div>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; color: var(--text-light);">Jenis Kelamin</label>
                        <div style="font-weight: 600;">{{ $student->gender_label }}</div>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; color: var(--text-light);">Tahun Masuk</label>
                        <div style="font-weight: 600;">{{ $student->enrollment_year }}</div>
                    </div>
                    @if(isset($paymentData))
                    <div>
                        <label style="display: block; font-size: 0.85rem; color: var(--text-light);">Tahun Ajaran</label>
                        <div style="font-weight: 600;">{{ $paymentData['academic_year']->year }}</div>
                    </div>
                    @endif
                </div>

                @if(isset($paymentData))
                {{-- Payment Summary Cards --}}
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 25px; border-radius: 16px; text-align: center;">
                        <div style="font-size: 0.85rem; opacity: 0.9; margin-bottom: 5px;">Total Tagihan</div>
                        <div style="font-size: 1.5rem; font-weight: 700;">Rp {{ number_format($paymentData['committee_fee']->amount, 0, ',', '.') }}</div>
                    </div>
                    <div style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; padding: 25px; border-radius: 16px; text-align: center;">
                        <div style="font-size: 0.85rem; opacity: 0.9; margin-bottom: 5px;">Total Dibayar</div>
                        <div style="font-size: 1.5rem; font-weight: 700;">Rp {{ number_format($paymentData['total_paid'], 0, ',', '.') }}</div>
                    </div>
                    <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 25px; border-radius: 16px; text-align: center;">
                        <div style="font-size: 0.85rem; opacity: 0.9; margin-bottom: 5px;">Sisa Tagihan</div>
                        <div style="font-size: 1.5rem; font-weight: 700;">Rp {{ number_format($paymentData['remaining'], 0, ',', '.') }}</div>
                    </div>
                </div>

                {{-- Progress Bar --}}
                @php
                $percentage = $paymentData['committee_fee']->amount > 0
                ? min(100, ($paymentData['total_paid'] / $paymentData['committee_fee']->amount) * 100)
                : 0;
                @endphp
                <div style="margin-bottom: 30px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span style="font-size: 0.9rem; color: var(--text-light);">Progress Pembayaran</span>
                        <span style="font-size: 0.9rem; font-weight: 600;">{{ number_format($percentage, 1) }}%</span>
                    </div>
                    <div style="background: #e9ecef; border-radius: 10px; height: 12px; overflow: hidden;">
                        <div style="background: linear-gradient(90deg, #667eea, #764ba2); height: 100%; width: {{ $percentage }}%; border-radius: 10px; transition: width 0.5s ease;"></div>
                    </div>
                </div>

                {{-- Payment History --}}
                @if($paymentData['payments']->count() > 0)
                <h4 style="color: var(--primary); margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-history"></i> Riwayat Pembayaran
                </h4>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: var(--accent);">
                                <th style="padding: 12px 15px; text-align: left; font-weight: 600; color: var(--text); border-radius: 8px 0 0 8px;">No</th>
                                <th style="padding: 12px 15px; text-align: left; font-weight: 600; color: var(--text);">Tanggal</th>
                                <th style="padding: 12px 15px; text-align: right; font-weight: 600; color: var(--text);">Jumlah</th>
                                <th style="padding: 12px 15px; text-align: left; font-weight: 600; color: var(--text); border-radius: 0 8px 8px 0;">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paymentData['payments'] as $index => $payment)
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 12px 15px;">{{ $index + 1 }}</td>
                                <td style="padding: 12px 15px;">{{ $payment->payment_date->format('d M Y') }}</td>
                                <td style="padding: 12px 15px; text-align: right; font-weight: 600; color: #28a745;">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                <td style="padding: 12px 15px; color: var(--text-light);">{{ $payment->notes ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div style="text-align: center; padding: 30px; background: var(--accent); border-radius: 12px;">
                    <i class="fas fa-inbox" style="font-size: 2rem; color: var(--text-light); margin-bottom: 10px;"></i>
                    <p style="color: var(--text-light); margin: 0;">Belum ada riwayat pembayaran.</p>
                </div>
                @endif

                @else
                {{-- No Committee Fee Data --}}
                <div style="text-align: center; padding: 30px; background: rgba(255, 193, 7, 0.1); border-radius: 12px; border: 1px dashed #ffc107;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 2rem; color: #856404; margin-bottom: 10px;"></i>
                    <p style="color: #856404; margin: 0;">Data tagihan komite untuk tahun ajaran aktif belum tersedia untuk kelas ini.</p>
                </div>
                @endif

                {{-- Info Note --}}
                <div style="margin-top: 25px; padding: 15px 20px; background: rgba(30, 58, 95, 0.05); border-radius: 10px; border-left: 4px solid var(--primary);">
                    <p style="margin: 0; font-size: 0.9rem; color: var(--text-light);">
                        <i class="fas fa-info-circle" style="color: var(--primary);"></i>
                        Untuk informasi lebih lanjut atau pertanyaan terkait pembayaran, silakan hubungi pihak sekolah.
                    </p>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection