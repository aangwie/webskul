@extends('admin.layouts.app')

@section('title', 'Pengaturan Sistem')
@section('page-title', 'Pengaturan Sistem')

@section('content')
<div class="row">
    <!-- Storage Storage Link -->
    <div class="col-md-6" style="margin-bottom: 20px;">
        <div class="card h-100">
            <div class="card-header">
                <h3><i class="fas fa-link"></i> Storage Link</h3>
            </div>
            <div class="card-body">
                <p>Fitur ini digunakan untuk menghubungkan folder penyimpanan public dengan folder storage. Jika gambar tidak muncul di website, silakan tekan tombol di bawah ini.</p>
                
                <div style="margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 5px; border-left: 4px solid var(--primary);">
                    <strong>Status: </strong>
                    @if($hasStorageLink)
                        <span class="badge badge-success">Terhubung</span>
                    @else
                        <span class="badge badge-danger">Tidak Terhubung</span>
                    @endif
                </div>

                <form action="{{ route('admin.system.storage-link') }}" method="POST" style="margin-top: 20px;">
                    @csrf
                    <button type="submit" class="btn btn-primary" {{ $hasStorageLink ? 'disabled' : '' }}>
                        <i class="fas fa-hammer"></i> Perbaiki Storage Link
                    </button>
                    @if($hasStorageLink)
                         <button type="submit" class="btn btn-warning" onclick="return confirm('Paksa buat ulang link?')">
                            <i class="fas fa-sync"></i> Re-Create Link (Paksa)
                        </button>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Optimization -->
    <div class="col-md-6" style="margin-bottom: 20px;">
        <div class="card h-100">
            <div class="card-header">
                <h3><i class="fas fa-broom"></i> Cache System</h3>
            </div>
            <div class="card-body">
                <p>Bersihkan cache aplikasi, route, view, dan config jika terjadi error aneh atau perubahan tidak muncul.</p>
                <form action="{{ route('admin.system.cache-clear') }}" method="POST" style="margin-top: 20px;">
                    @csrf
                    <button type="submit" class="btn btn-warning" style="color: #fff;">
                        <i class="fas fa-trash-alt"></i> Clear All Cache
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Git Update -->
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="background: rgba(30, 58, 95, 0.05); color: var(--primary);">
                <h3><i class="fas fa-cloud-download-alt"></i> Update Aplikasi</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Tombol ini akan mengambil kode terbaru dari <strong>GitHub</strong> dan menjalankan migrasi database secara otomatis.
                </div>

                <form action="{{ route('admin.system.update') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin melakukan update sistem? Website mungkin tidak bisa diakses selama beberapa detik.')">
                    @csrf
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fab fa-github"></i> Update dari GitHub
                    </button>
                </form>

                @if(session('update_log'))
                <div style="margin-top: 20px;">
                    <h5>Log Update Terakhir:</h5>
                    <pre style="background: #1e1e1e; color: #0f0; padding: 15px; border-radius: 5px; font-family: monospace; max-height: 300px; overflow-y: auto;">{{ session('update_log') }}</pre>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
