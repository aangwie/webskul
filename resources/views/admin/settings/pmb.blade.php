@extends('admin.layouts.app')

@section('title', 'Pengaturan PMB')
@section('page-title', 'Pengaturan PMB')

@section('content')
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-user-plus"></i> Status Penerimaan Murid Baru (PMB)</h2>
    </div>
    <div class="card-body">
        <div style="background: rgba(30, 58, 95, 0.05); padding: 15px 20px; border-radius: 10px; margin-bottom: 25px; border-left: 4px solid var(--primary);">
            <p style="color: var(--text-light); font-size: 0.9rem; margin: 0;">
                <i class="fas fa-info-circle" style="color: var(--primary);"></i>
                Gunakan pengaturan ini untuk membuka atau menutup formulir pendaftaran murid baru di halaman depan.
            </p>
        </div>

        <form action="{{ route('admin.settings.pmb.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px; max-width: 800px;">
                <div class="form-group">
                    <label class="form-label">Status Pendaftaran</label>
                    <div style="display: flex; gap: 20px; margin-top: 10px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="radio" name="pmb_status" value="open" {{ $pmb_status == 'open' ? 'checked' : '' }} style="width: 18px; height: 18px;">
                            <span style="font-weight: 500; color: #10b981;">Buka Pendaftaran</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="radio" name="pmb_status" value="closed" {{ $pmb_status == 'closed' ? 'checked' : '' }} style="width: 18px; height: 18px;">
                            <span style="font-weight: 500; color: #ef4444;">Tutup Pendaftaran</span>
                        </label>
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px; max-width: 800px;">
                <div class="form-group">
                    <label class="form-label">Tanggal Buka Pendaftaran</label>
                    <input type="date" name="pmb_start_date" value="{{ $pmb_start_date }}" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                    <small style="color: var(--text-light);">Formulir akan terbuka otomatis pada tanggal ini.</small>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Tutup Pendaftaran</label>
                    <input type="date" name="pmb_end_date" value="{{ $pmb_end_date }}" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                    <small style="color: var(--text-light);">Formulir akan tertutup otomatis setelah tanggal ini.</small>
                </div>
            </div>

            <div style="background: #f8fafc; padding: 20px; border-radius: 10px; border: 1px solid #e2e8f0; margin-bottom: 25px; max-width: 800px;">
                <h4 style="margin-bottom: 15px; font-size: 1rem; color: var(--primary);"><i class="fas fa-history"></i> Status Jadwal Saat Ini</h4>
                <div style="font-size: 0.9rem;">
                    @php
                    $now = now()->startOfDay();
                    $start = $pmb_start_date ? \Carbon\Carbon::parse($pmb_start_date)->startOfDay() : null;
                    $end = $pmb_end_date ? \Carbon\Carbon::parse($pmb_end_date)->startOfDay() : null;

                    $is_active_by_date = true;
                    if ($start && $now->lt($start)) $is_active_by_date = false;
                    if ($end && $now->gt($end)) $is_active_by_date = false;
                    @endphp

                    @if($pmb_status == 'open')
                    @if(!$start && !$end)
                    <p style="color: #10b981; margin: 5px 0;"><i class="fas fa-check-circle"></i> Pendaftaran <strong>AKTIF</strong> (Manual)</p>
                    @elseif($is_active_by_date)
                    <p style="color: #10b981; margin: 5px 0;"><i class="fas fa-check-circle"></i> Pendaftaran <strong>AKTIF</strong> berdasarkan jadwal.</p>
                    @else
                    <p style="color: #ef4444; margin: 5px 0;"><i class="fas fa-exclamation-triangle"></i> Status Manual <strong>BUKA</strong>, namun <strong>TERTUTUP</strong> oleh jadwal atau belum masuk masa pendaftaran.</p>
                    @endif
                    @else
                    <p style="color: #ef4444; margin: 5px 0;"><i class="fas fa-times-circle"></i> Pendaftaran <strong>NONAKTIF</strong> (Manual)</p>
                    @endif

                    <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #e2e8f0; color: var(--text-light);">
                        <strong>Jadwal:</strong> {{ $pmb_start_date ? \Carbon\Carbon::parse($pmb_start_date)->format('d M Y') : 'Tidak diatur' }}
                        s/d
                        {{ $pmb_end_date ? \Carbon\Carbon::parse($pmb_end_date)->format('d M Y') : 'Tidak diatur' }}
                    </div>
                </div>
            </div>

            <div style="margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection