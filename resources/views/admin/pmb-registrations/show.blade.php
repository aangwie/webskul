@extends('admin.layouts.app')

@section('title', 'Detail Pendaftaran')
@section('page-title', 'Detail Pendaftaran')

@section('content')
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px;">
    <!-- Student Information -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-id-card"></i> Data Calon Murid</h2>
        </div>
        <div class="card-body">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label class="form-label">No. Pendaftaran</label>
                    <div style="font-weight: 700; font-size: 1.2rem; color: var(--primary);">{{ $pmbRegistration->registration_number }}</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Status Saat Ini</label>
                    @if($pmbRegistration->status == 'pending')
                    <span class="badge badge-warning">Pending</span>
                    @elseif($pmbRegistration->status == 'approved')
                    <span class="badge badge-success">Approved</span>
                    @else
                    <span class="badge badge-danger">Rejected</span>
                    @endif
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <p>{{ $pmbRegistration->nama }}</p>
                </div>
                <div class="form-group">
                    <label class="form-label">NISN</label>
                    <p>{{ $pmbRegistration->nisn }}</p>
                </div>
                <div class="form-group">
                    <label class="form-label">NIK</label>
                    <p>{{ $pmbRegistration->nik }}</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Tahun Pelajaran</label>
                    <p>{{ $pmbRegistration->academic_year }}</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Tempat, Tanggal Lahir</label>
                    <p>{{ $pmbRegistration->birth_place }}, {{ $pmbRegistration->birth_date->format('d M Y') }}</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Jenis Pendaftaran</label>
                    <p>{{ ucfirst($pmbRegistration->registration_type) }}</p>
                </div>
            </div>

            <div class="form-group" style="margin-top: 20px;">
                <label class="form-label">Alamat Lengkap</label>
                <p>{{ $pmbRegistration->address }}</p>
            </div>

            <div class="sidebar-divider" style="margin: 20px 0;"></div>

            <h3><i class="fas fa-users"></i> Data Orang Tua / Wali</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px;">
                <div class="form-group">
                    <label class="form-label">Nama Ibu Kandung</label>
                    <p>{{ $pmbRegistration->mother_name }}</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Ayah Kandung</label>
                    <p>{{ $pmbRegistration->father_name }}</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Wali</label>
                    <p>{{ $pmbRegistration->guardian_name ?? '-' }}</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Nomor HP / WhatsApp</label>
                    <p>{{ $pmbRegistration->phone_number }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Attachments & Status Update -->
    <div>
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-file-alt"></i> Lampiran</h2>
            </div>
            <div class="card-body">
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <div>
                        <label class="form-label">Kartu Keluarga</label>
                        @if(Str::startsWith($pmbRegistration->kk_attachment, 'data:image'))
                        <img src="{{ $pmbRegistration->kk_attachment }}" class="preview-image" style="max-width: 100%; cursor: pointer;" onclick="window.open(this.src)">
                        @else
                        <a href="{{ $pmbRegistration->kk_attachment }}" target="_blank" class="btn btn-sm btn-outline"><i class="fas fa-file-pdf"></i> Lihat PDF</a>
                        @endif
                    </div>
                    <div>
                        <label class="form-label">Akta Kelahiran</label>
                        @if(Str::startsWith($pmbRegistration->birth_certificate_attachment, 'data:image'))
                        <img src="{{ $pmbRegistration->birth_certificate_attachment }}" class="preview-image" style="max-width: 100%; cursor: pointer;" onclick="window.open(this.src)">
                        @else
                        <a href="{{ $pmbRegistration->birth_certificate_attachment }}" target="_blank" class="btn btn-sm btn-outline"><i class="fas fa-file-pdf"></i> Lihat PDF</a>
                        @endif
                    </div>
                    <div>
                        <label class="form-label">Ijazah Sebelumnya</label>
                        @if(Str::startsWith($pmbRegistration->ijazah_attachment, 'data:image'))
                        <img src="{{ $pmbRegistration->ijazah_attachment }}" class="preview-image" style="max-width: 100%; cursor: pointer;" onclick="window.open(this.src)">
                        @else
                        <a href="{{ $pmbRegistration->ijazah_attachment }}" target="_blank" class="btn btn-sm btn-outline"><i class="fas fa-file-pdf"></i> Lihat PDF</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-edit"></i> Update Status</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.pmb-registrations.status', $pmbRegistration) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label class="form-label">Pilih Status</label>
                        <select name="status" class="form-select">
                            <option value="pending" {{ $pmbRegistration->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $pmbRegistration->status == 'approved' ? 'selected' : '' }}>Approve (Diterima)</option>
                            <option value="rejected" {{ $pmbRegistration->status == 'rejected' ? 'selected' : '' }}>Decline (Ditolak)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                        <i class="fas fa-save"></i> Perbarui Status
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection