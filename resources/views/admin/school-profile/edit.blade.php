@extends('admin.layouts.app')

@section('title', 'Edit Profil Sekolah')
@section('page-title', 'Edit Profil Sekolah')

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Edit Data Profil Sekolah</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.school-profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Nama Sekolah *</label>
                    <input type="text" name="name" class="form-input" value="{{ old('name', $school->name) }}" required>
                    @error('name')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Sub Judul (Slogan Navbar)</label>
                    <input type="text" name="brand_subtitle" class="form-input" value="{{ old('brand_subtitle', $school->brand_subtitle) }}" placeholder="Contoh: Excellence in Education">
                    @error('brand_subtitle')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" class="form-textarea"
                        rows="3">{{ old('address', $school->address) }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Kota</label>
                    <input type="text" name="city" class="form-input" value="{{ old('city', $school->city) }}"
                        placeholder="Contoh: Sudimoro">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="phone" class="form-input" value="{{ old('phone', $school->phone) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-input" value="{{ old('email', $school->email) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Visi</label>
                    <textarea name="vision" class="form-textarea" rows="4">{{ old('vision', $school->vision) }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Misi</label>
                    <textarea name="mission" class="form-textarea"
                        rows="5">{{ old('mission', $school->mission) }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Sejarah</label>
                    <textarea name="history" class="form-textarea"
                        rows="5">{{ old('history', $school->history) }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Logo Sekolah</label>
                    <input type="file" name="logo" class="form-input" accept="image/*">
                    @if($school->logo)
                        <div style="display: flex; align-items: flex-end; gap: 12px; margin-top: 10px;">
                            @if(Str::startsWith($school->logo, 'data:'))
                                <img src="{{ $school->logo }}" alt="Current Logo" class="preview-image" style="margin-top:0;">
                            @else
                                <img src="{{ route('admin.storage.view', ['path' => $school->logo]) }}" alt="Current Logo" class="preview-image" style="margin-top:0;">
                            @endif
                            <button type="button" class="btn" style="background: var(--danger); color: white; padding: 8px 14px; font-size: 0.8rem;" onclick="deleteLogo('logo')">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <label class="form-label">Logo SSN</label>
                    <input type="file" name="logo_ssn" class="form-input" accept="image/*">
                    @if($school->logo_ssn)
                        <div style="display: flex; align-items: flex-end; gap: 12px; margin-top: 10px;">
                            @if(Str::startsWith($school->logo_ssn, 'data:'))
                                <img src="{{ $school->logo_ssn }}" alt="Current Logo SSN" class="preview-image" style="margin-top:0;">
                            @else
                                <img src="{{ route('admin.storage.view', ['path' => $school->logo_ssn]) }}" alt="Current Logo SSN" class="preview-image" style="margin-top:0;">
                            @endif
                            <button type="button" class="btn" style="background: var(--danger); color: white; padding: 8px 14px; font-size: 0.8rem;" onclick="deleteLogo('ssn')">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </div>
                    @endif
                </div>

                <div style="display: flex; gap: 10px; margin-top: 30px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.school-profile.index') }}" class="btn"
                        style="background: var(--accent); color: var(--text);">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
function deleteLogo(type) {
    const label = type === 'ssn' ? 'Logo SSN' : 'Logo Sekolah';
    if (!confirm('Yakin ingin menghapus ' + label + '?')) return;

    fetch('{{ url("admin/school-profile/delete-logo") }}/' + type, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    }).then(response => response.json())
    .then(data => {
        if (data && data.success) {
            window.location.reload();
        } else {
            alert('Gagal: ' + (data.message || 'Terjadi kesalahan pada server'));
        }
    }).catch(error => {
        console.error(error);
        alert('Terjadi kesalahan jaringan atau server merespon tidak valid.');
    });
}
</script>
@endsection