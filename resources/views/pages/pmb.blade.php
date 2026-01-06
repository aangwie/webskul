@extends('layouts.app')

@section('title', 'Pendaftaran Murid Baru - ' . ($school->name ?? 'SMP Negeri 6 Sudimoro'))

@section('styles')
<style>
    .pmb-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .pmb-card {
        background: var(--secondary);
        border-radius: 20px;
        box-shadow: var(--shadow-lg);
        padding: 40px;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .pmb-section-title {
        color: var(--primary);
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--accent);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .pmb-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
        margin-bottom: 25px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--text);
        font-size: 0.95rem;
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #eef2f7;
        border-radius: 12px;
        font-size: 1rem;
        transition: var(--transition);
        background: #fdfdfd;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        background: #fff;
        box-shadow: 0 0 0 4px rgba(30, 58, 95, 0.1);
    }

    .form-note {
        font-size: 0.85rem;
        color: var(--text-light);
        margin-top: 10px;
        display: block;
    }

    .btn-submit {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 16px 40px;
        background: var(--primary);
        color: var(--secondary);
        border: none;
        border-radius: 14px;
        font-size: 1.1rem;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition);
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(30, 58, 95, 0.2);
        background: var(--primary-light);
    }

    @media (max-width: 768px) {
        .pmb-card {
            padding: 25px 20px;
            border-radius: 15px;
        }

        .pmb-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .pmb-section-title {
            font-size: 1.3rem;
        }

        .btn-submit {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
<section class="section">
    <div class="container">
        <div class="pmb-container animate-fade-in">
            <div class="section-title">Penerimaan Murid Baru</div>
            <p class="section-subtitle">Silakan isi formulir di bawah ini dengan lengkap dan benar untuk pendaftaran murid baru.</p>

            @if(session('success'))
            <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 20px; border-radius: 15px; margin-bottom: 25px; border: 1px solid #c3e6cb; display: flex; align-items: center; gap: 15px;">
                <i class="fas fa-check-circle" style="font-size: 1.5rem;"></i>
                <div>
                    <strong style="display: block; margin-bottom: 5px;">Pendaftaran Berhasil!</strong>
                    {{ session('success') }}
                </div>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 20px; border-radius: 15px; margin-bottom: 25px; border: 1px solid #f5c6cb;">
                <div style="font-weight: 700; margin-bottom: 10px;"><i class="fas fa-exclamation-triangle"></i> Mohon perhatikan kesalahan berikut:</div>
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="pmb-card">
                <form action="{{ route('pmb.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <h3 class="pmb-section-title">
                        <i class="fas fa-user-graduate"></i> Identitas Calon Murid
                    </h3>

                    <div class="pmb-grid">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" value="{{ old('nama') }}" required class="form-control" placeholder="Contoh: Ahmad Wijaya">
                        </div>
                        <div class="form-group">
                            <label class="form-label">NISN</label>
                            <input type="number" name="nisn" value="{{ old('nisn') }}" required class="form-control" placeholder="10 Digit NISN">
                        </div>
                    </div>

                    <div class="pmb-grid">
                        <div class="form-group">
                            <label class="form-label">NIK</label>
                            <input type="number" name="nik" value="{{ old('nik') }}" required class="form-control" placeholder="16 Digit NIK">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tahun Pelajaran</label>
                            <select name="academic_year" required class="form-control">
                                <option value="">Pilih Tahun Pelajaran</option>
                                @foreach($academicYears as $year)
                                <option value="{{ $year->year }}" {{ old('academic_year') == $year->year ? 'selected' : '' }}>{{ $year->year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="pmb-grid">
                        <div class="form-group">
                            <label class="form-label">Jenis Pendaftaran</label>
                            <select name="registration_type" required class="form-control">
                                <option value="" selected disabled>Pilih Jenis Pendaftaran</option>
                                <option value="baru" {{ old('registration_type') == 'baru' ? 'selected' : '' }}>Murid Baru</option>
                                <option value="pindahan" {{ old('registration_type') == 'pindahan' ? 'selected' : '' }}>Pindahan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="pmb-grid" style="grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 0;">
                                <div>
                                    <label class="form-label">Tempat Lahir</label>
                                    <input type="text" name="birth_place" value="{{ old('birth_place') }}" required class="form-control" placeholder="Kota">
                                </div>
                                <div>
                                    <label class="form-label">Tanggal Lahir</label>
                                    <input type="date" name="birth_date" value="{{ old('birth_date') }}" required class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea name="address" required class="form-control" style="min-height: 100px;" placeholder="Dusun, RT/RW, Desa, Kecamatan">{{ old('address') }}</textarea>
                    </div>

                    <h3 class="pmb-section-title" style="margin-top: 40px;">
                        <i class="fas fa-users"></i> Identitas Orang Tua / Wali
                    </h3>

                    <div class="pmb-grid">
                        <div class="form-group">
                            <label class="form-label">Nama Ibu Kandung</label>
                            <input type="text" name="mother_name" value="{{ old('mother_name') }}" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nama Ayah Kandung</label>
                            <input type="text" name="father_name" value="{{ old('father_name') }}" required class="form-control">
                        </div>
                    </div>

                    <div class="pmb-grid">
                        <div class="form-group">
                            <label class="form-label">Nama Wali (Jika ada)</label>
                            <input type="text" name="guardian_name" value="{{ old('guardian_name') }}" class="form-control" placeholder="Opsional">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nomor WhatsApp / HP</label>
                            <input type="number" name="phone_number" value="{{ old('phone_number') }}" required class="form-control" placeholder="08xxxxxxxxxx">
                        </div>
                    </div>

                    <h3 class="pmb-section-title" style="margin-top: 40px;">
                        <i class="fas fa-file-upload"></i> Dokumen Lampiran
                    </h3>

                    <div class="pmb-grid">
                        <div class="form-group">
                            <label class="form-label">Kartu Keluarga (KK)</label>
                            <input type="file" name="kk_attachment" required class="form-control" style="padding: 10px;">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Akta Kelahiran</label>
                            <input type="file" name="birth_certificate_attachment" required class="form-control" style="padding: 10px;">
                        </div>
                        <div class="form-group" style="grid-column: span 1;">
                            <label class="form-label">Ijazah Terakhir / SKL</label>
                            <input type="file" name="ijazah_attachment" required class="form-control" style="padding: 10px;">
                        </div>
                    </div>

                    <span class="form-note"><i class="fas fa-info-circle"></i> Format file: JPG, PNG, PDF (Maks. 2MB per file)</span>

                    <div style="margin-top: 40px; border-top: 1px solid #eef2f7; padding-top: 30px; text-align: center;">
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-paper-plane"></i> Kirim Form Pendaftaran
                        </button>
                        <p style="margin-top: 15px; font-size: 0.9rem; color: var(--text-light);">Pastikan data yang Anda masukkan sudah benar sebelum mengirim.</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection