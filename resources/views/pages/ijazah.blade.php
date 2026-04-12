@extends('layouts.app')

@section('title', 'Cek Ijazah Digital')

@section('content')
<div class="container section animate-fade-in">
    <div class="row justify-content-center" style="display: flex; justify-content: center;">
        <div class="col-md-8 mx-auto" style="width: 100%; max-width: 600px;">
            <div class="section-title">Cek Ijazah Digital</div>
            <div class="section-subtitle">Masukkan NISN dan Tanggal Lahir untuk mengecek dan mengunduh ijazah digital Anda.</div>

            <div class="card" style="padding: 30px; margin-bottom: 30px;">
                <form action="{{ route('ijazah.check') }}" method="POST">
                    @csrf
                    <div style="margin-bottom: 20px;">
                        <label for="nisn" style="display:block; margin-bottom: 8px; font-weight: 600;">NISN</label>
                        <input type="text" name="nisn" id="nisn" class="form-input" required value="{{ old('nisn', request('nisn')) }}" placeholder="Contoh: 0012345678" style="width: 100%; padding: 12px; border: 2px solid var(--accent); border-radius: 8px;">
                    </div>
                    
                    <div style="margin-bottom: 25px;">
                        <label for="tanggal_lahir" style="display:block; margin-bottom: 8px; font-weight: 600;">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-input" required value="{{ old('tanggal_lahir', request('tanggal_lahir')) }}" style="width: 100%; padding: 12px; border: 2px solid var(--accent); border-radius: 8px;">
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; display: flex; justify-content: center; align-items: center; gap: 10px;">
                        <i class="fas fa-search"></i> Cek Ijazah
                    </button>
                </form>
            </div>

            @if(request()->isMethod('post'))
                @if(isset($student) && $student)
                    <div class="card animate-fade-in" style="padding: 30px; border-left: 5px solid {{ $student->status_lulus === 'lulus' ? 'var(--success)' : 'var(--warning)' }};">
                        <h3 style="color: {{ $student->status_lulus === 'lulus' ? 'var(--success)' : 'var(--warning)' }}; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                            <i class="fas {{ $student->status_lulus === 'lulus' ? 'fa-check-circle' : 'fa-info-circle' }}"></i> Data Siswa Ditemukan
                        </h3>
                        <div style="margin-bottom: 20px; background: rgba(40, 167, 69, 0.05); padding: 15px; border-radius: 8px;">
                            <p style="margin-bottom: 8px;"><strong>Nama:</strong> {{ $student->name }}</p>
                            <p style="margin-bottom: 8px;"><strong>NISN:</strong> {{ $student->nisn }}</p>
                            <p style="margin-bottom: 0;"><strong>Status:</strong> 
                                @if($student->status_lulus === 'lulus')
                                    Lulus
                                @elseif($student->status_lulus === 'tidak_lulus')
                                    Tidak Lulus / Pindah
                                @else
                                    Aktif
                                @endif
                            </p>
                        </div>
                        
                        @if($student->status_lulus === 'lulus')
                            @if($student->ijazah_file)
                                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                                    <a href="javascript:void(0)" onclick="openLightbox('{{ URL::signedRoute('public.storage.view', ['path' => $student->ijazah_file]) }}')" class="btn btn-primary" style="flex: 1; text-align: center; justify-content: center;">
                                        <i class="fas fa-eye"></i> Lihat Ijazah
                                    </a>
                                    <a href="{{ URL::signedRoute('public.storage.view', ['path' => $student->ijazah_file]) }}" target="_blank" download class="btn btn-outline" style="flex: 1; text-align: center; justify-content: center;">
                                        <i class="fas fa-download"></i> Unduh PDF
                                    </a>
                                </div>
                            @else
                                <div class="alert alert-warning" style="background: rgba(255, 193, 7, 0.1); color: #856404; padding: 15px; border-radius: 8px; border-left: 4px solid var(--warning);">
                                    <i class="fas fa-exclamation-triangle" style="margin-right: 10px;"></i> Dokumen ijazah digital belum diunggah. Silakan hubungi tata usaha atau admin sekolah untuk info lebih lanjut.
                                </div>
                            @endif
                        @else
                            <div class="alert alert-info" style="background: rgba(23, 162, 184, 0.1); color: #0c5460; padding: 15px; border-radius: 8px; border-left: 4px solid #17a2b8;">
                                <i class="fas fa-info-circle" style="margin-right: 10px;"></i> Berkas ijazah digital hanya tersedia untuk siswa dengan status kelulusan <strong>Lulus</strong>.
                            </div>
                        @endif
                    </div>
                @else
                    <div class="card animate-fade-in" style="padding: 30px; border-left: 5px solid var(--danger);">
                        <h3 style="color: var(--danger); margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-times-circle"></i> Data Tidak Ditemukan
                        </h3>
                        <p style="color: var(--text-light); line-height: 1.6;">Ijazah digital tidak ditemukan untuk kombinasi NISN dan Tanggal Lahir tersebut. Harap pastikan data yang Anda masukkan sesuai, atau hubungi pihak sekolah jika Anda merasa ini adalah kesalahan.</p>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<!-- Lightbox Modal -->
<div id="pdfLightbox" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div style="background: var(--body-bg); width: 95%; max-width: 1000px; height: 90vh; border-radius: 12px; position: relative; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
        <div style="padding: 15px 25px; background: var(--nav-bg); color: var(--secondary); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-file-pdf" style="color: #ef4444;"></i> Preview Ijazah
            </h3>
            <button onclick="closeLightbox()" style="background: rgba(255,255,255,0.1); border: none; color: white; width: 36px; height: 36px; border-radius: 50%; font-size: 1.2rem; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div style="flex-grow: 1; padding: 0; background-color: #f1f5f9;">
            <iframe id="pdfIframe" src="" style="width: 100%; height: 100%; border: none;"></iframe>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openLightbox(url) {
        document.getElementById('pdfIframe').src = url;
        const lightbox = document.getElementById('pdfLightbox');
        lightbox.style.display = 'flex';
        // Add minimal animation
        lightbox.style.opacity = '0';
        setTimeout(() => {
            lightbox.style.transition = 'opacity 0.3s ease';
            lightbox.style.opacity = '1';
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        const lightbox = document.getElementById('pdfLightbox');
        lightbox.style.opacity = '0';
        setTimeout(() => {
            lightbox.style.display = 'none';
            document.getElementById('pdfIframe').src = '';
            document.body.style.overflow = '';
        }, 300);
    }
</script>
@endsection
