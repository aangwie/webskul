@extends('admin.layouts.app')

@section('title', 'Edit Pendaftaran PMB')
@section('page-title', 'Edit Pendaftaran PMB')

@section('content')
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-edit"></i> Edit Data Calon Murid</h2>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.pmb-registrations.update', $pmbRegistration) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label class="form-label">No. Pendaftaran</label>
                    <input type="text" class="form-input" value="{{ $pmbRegistration->registration_number }}" disabled>
                    <small style="color: #666;">Nomor pendaftaran tidak dapat diubah</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="pending" {{ $pmbRegistration->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ $pmbRegistration->status == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $pmbRegistration->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Lengkap <span style="color: red;">*</span></label>
                    <input type="text" name="nama" class="form-input @error('nama') is-invalid @enderror" value="{{ old('nama', $pmbRegistration->nama) }}" required>
                    @error('nama')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">NISN <span style="color: red;">*</span></label>
                    <input type="text" name="nisn" class="form-input @error('nisn') is-invalid @enderror" value="{{ old('nisn', $pmbRegistration->nisn) }}" required>
                    @error('nisn')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">NIK <span style="color: red;">*</span></label>
                    <input type="text" name="nik" class="form-input @error('nik') is-invalid @enderror" value="{{ old('nik', $pmbRegistration->nik) }}" required>
                    @error('nik')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Tahun Pelajaran <span style="color: red;">*</span></label>
                    <input type="text" name="academic_year" class="form-input @error('academic_year') is-invalid @enderror" value="{{ old('academic_year', $pmbRegistration->academic_year) }}" required>
                    @error('academic_year')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Tempat Lahir <span style="color: red;">*</span></label>
                    <input type="text" name="birth_place" class="form-input @error('birth_place') is-invalid @enderror" value="{{ old('birth_place', $pmbRegistration->birth_place) }}" required>
                    @error('birth_place')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Tanggal Lahir <span style="color: red;">*</span></label>
                    <input type="date" name="birth_date" class="form-input @error('birth_date') is-invalid @enderror" value="{{ old('birth_date', $pmbRegistration->birth_date->format('Y-m-d')) }}" required>
                    @error('birth_date')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Jenis Pendaftaran <span style="color: red;">*</span></label>
                    <select name="registration_type" class="form-select @error('registration_type') is-invalid @enderror" required>
                        <option value="baru" {{ old('registration_type', $pmbRegistration->registration_type) == 'baru' ? 'selected' : '' }}>Baru</option>
                        <option value="pindahan" {{ old('registration_type', $pmbRegistration->registration_type) == 'pindahan' ? 'selected' : '' }}>Pindahan</option>
                    </select>
                    @error('registration_type')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor HP / WhatsApp <span style="color: red;">*</span></label>
                    <input type="text" name="phone_number" class="form-input @error('phone_number') is-invalid @enderror" value="{{ old('phone_number', $pmbRegistration->phone_number) }}" required>
                    @error('phone_number')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group" style="margin-top: 20px;">
                <label class="form-label">Alamat Lengkap <span style="color: red;">*</span></label>
                <textarea name="address" class="form-input @error('address') is-invalid @enderror" rows="3" required>{{ old('address', $pmbRegistration->address) }}</textarea>
                @error('address')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="sidebar-divider" style="margin: 25px 0;"></div>

            <h3><i class="fas fa-users"></i> Data Orang Tua / Wali</h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px;">
                <div class="form-group">
                    <label class="form-label">Nama Ibu Kandung <span style="color: red;">*</span></label>
                    <input type="text" name="mother_name" class="form-input @error('mother_name') is-invalid @enderror" value="{{ old('mother_name', $pmbRegistration->mother_name) }}" required>
                    @error('mother_name')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Ayah Kandung <span style="color: red;">*</span></label>
                    <input type="text" name="father_name" class="form-input @error('father_name') is-invalid @enderror" value="{{ old('father_name', $pmbRegistration->father_name) }}" required>
                    @error('father_name')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Wali (Opsional)</label>
                    <input type="text" name="guardian_name" class="form-input @error('guardian_name') is-invalid @enderror" value="{{ old('guardian_name', $pmbRegistration->guardian_name) }}">
                    @error('guardian_name')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div style="margin-top: 25px; display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="{{ route('admin.pmb-registrations.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
