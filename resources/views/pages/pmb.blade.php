@extends('layouts.app')

@section('title', 'Pendaftaran Murid Baru - ' . ($school->name ?? 'SMP Negeri 6 Sudimoro'))

@section('content')
<section class="section">
    <div class="container">
        <div class="animate-fade-in" style="max-width: 800px; margin: 0 auto;">
            <div class="section-title">Penerimaan Murid Baru</div>
            <p class="section-subtitle">Silakan isi formulir di bawah ini dengan lengkap dan benar.</p>

            @if(session('success'))
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #c3e6cb;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #f5c6cb;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="card" style="padding: 30px;">
                <form action="{{ route('pmb.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <h3 style="color: var(--primary); margin-bottom: 20px; border-bottom: 2px solid var(--accent); padding-bottom: 10px;">
                        <i class="fas fa-user-graduate"></i> Identitas Calon Murid
                    </h3>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600;">Nama Lengkap</label>
                            <input type="text" name="nama" value="{{ old('nama') }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600;">NISN</label>
                            <input type="number" name="nisn" value="{{ old('nisn') }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600;">NIK</label>
                            <input type="number" name="nik" value="{{ old('nik') }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600;">Tahun Pelajaran</label>
                            <select name="academic_year" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                                <option value="">Pilih Tahun Pelajaran</option>
                                @foreach($academicYears as $year)
                                <option value="{{ $year->year }}" {{ old('academic_year') == $year->year ? 'selected' : '' }}>{{ $year->year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600;">Jenis Pendaftaran</label>
                            <select name="registration_type" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                                <option value="" selected disabled>Pilih Jenis Pendaftaran</option>
                                <option value="baru" {{ old('registration_type') == 'baru' ? 'selected' : '' }}>Murid Baru</option>
                                <option value="pindahan" {{ old('registration_type') == 'pindahan' ? 'selected' : '' }}>Pindahan</option>
                            </select>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Tempat Lahir</label>
                                <input type="text" name="birth_place" value="{{ old('birth_place') }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Tanggal Lahir</label>
                                <input type="date" name="birth_date" value="{{ old('birth_date') }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                            </div>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600;">Alamat Lengkap</label>
                            <textarea name="address" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; min-height: 100px;">{{ old('address') }}</textarea>
                        </div>

                        <h3 style="color: var(--primary); margin-top: 30px; margin-bottom: 20px; border-bottom: 2px solid var(--accent); padding-bottom: 10px;">
                            <i class="fas fa-users"></i> Identitas Orang Tua / Wali
                        </h3>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Nama Ibu</label>
                                <input type="text" name="mother_name" value="{{ old('mother_name') }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Nama Ayah</label>
                                <input type="text" name="father_name" value="{{ old('father_name') }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Nama Wali (Opsional)</label>
                                <input type="text" name="guardian_name" value="{{ old('guardian_name') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Nomor HP</label>
                                <input type="number" name="phone_number" value="{{ old('phone_number') }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                            </div>
                        </div>


                        <div style="display: grid; grid-template-columns: 1fr; gap: 15px; margin-bottom: 30px;">

                            <h3 style="color: var(--primary); margin-top: 30px; margin-bottom: 20px; border-bottom: 2px solid var(--accent); padding-bottom: 10px;">
                                <i class="fas fa-file-upload"></i> Dokumen Lampiran
                            </h3>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Kartu Keluarga (KK)</label>
                                <input type="file" name="kk_attachment" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px; background: #f9f9f9;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Akta Kelahiran</label>
                                <input type="file" name="birth_certificate_attachment" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px; background: #f9f9f9;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Ijazah Sebelumnya (SD/MI)</label>
                                <input type="file" name="ijazah_attachment" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px; background: #f9f9f9;">
                            </div>
                            <p style="font-size: 0.85rem; color: var(--text-light); margin-bottom: 15px;">Format: PDF, JPG, PNG (Maks. 2MB per file)</p>
                            <div style="text-align: left;">
                                <button type="submit" class="btn btn-primary" style="padding: 15px 50px; font-size: 1.1rem;">
                                    <i class="fas fa-paper-plane"></i> Kirim Pendaftaran
                                </button>
                            </div>
                        </div>


                </form>
            </div>
        </div>
    </div>
</section>
@endsection