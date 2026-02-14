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
                    <p>Fitur ini digunakan untuk menghubungkan folder penyimpanan public dengan folder storage. Jika gambar
                        tidak muncul di website, silakan tekan tombol di bawah ini.</p>

                    <div
                        style="margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 5px; border-left: 4px solid var(--primary);">
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

                    @if(session('storage_log'))
                        <div style="margin-top: 20px;">
                            <h5>Log Symlink:</h5>
                            <pre
                                style="background: #1e1e1e; color: #0f0; padding: 15px; border-radius: 5px; font-family: monospace; max-height: 200px; overflow-y: auto; font-size: 11px;">{{ session('storage_log') }}</pre>
                        </div>
                    @endif
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
                    <p>Bersihkan cache aplikasi, route, view, dan config jika terjadi error aneh atau perubahan tidak
                        muncul.</p>
                    <form action="{{ route('admin.system.cache-clear') }}" method="POST" style="margin-top: 20px;">
                        @csrf
                        <button type="submit" class="btn btn-warning" style="color: #fff;">
                            <i class="fas fa-trash-alt"></i> Clear All Cache
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Composer Autoload Fix -->
        <div class="col-md-6" style="margin-bottom: 20px;">
            <div class="card h-100">
                <div class="card-header">
                    <h3><i class="fas fa-box"></i> Fix Package Autoload</h3>
                </div>
                <div class="card-body">
                    <p>Jalankan <strong>composer dump-autoload</strong> jika ada error "Class not found" pada library
                        seperti DomPDF, QrCode, dll.</p>

                    <div
                        style="margin-top: 15px; padding: 15px; background: #fff3cd; border-radius: 5px; border-left: 4px solid #ffc107;">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Gunakan fitur ini jika error:</strong>
                        <ul style="margin: 10px 0 0 20px; font-size: 0.9rem;">
                            <li>"Class Barryvdh\DomPDF\... not found"</li>
                            <li>"Class SimpleSoftwareIO\QrCode\... not found"</li>
                        </ul>
                    </div>

                    <form action="{{ route('admin.system.composer-dump') }}" method="POST" style="margin-top: 20px;"
                        id="composer-form">
                        @csrf
                        <button type="button" class="btn btn-info" style="color: #fff;" onclick="runComposer(this)">
                            <i class="fas fa-sync-alt"></i> Fix Autoload
                        </button>
                    </form>

                    <script>
                        function runComposer(btn) {
                            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Sedang Memproses...';
                            btn.disabled = true;
                            btn.style.opacity = '0.7';
                            document.getElementById('composer-form').submit();
                        }
                    </script>

                    @if(session('composer_log'))
                        <div style="margin-top: 20px;">
                            <h5>Log Composer:</h5>
                            <pre
                                style="background: #1e1e1e; color: #0f0; padding: 15px; border-radius: 5px; font-family: monospace; max-height: 200px; overflow-y: auto; font-size: 12px;">{{ session('composer_log') }}</pre>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Theme Selection -->
        <div class="col-12" style="margin-bottom: 30px;">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="m-0 font-weight-bold" style="color: var(--primary);"><i
                            class="fas fa-palette mr-2"></i>Pengaturan Tema</h5>
                </div>
                <div class="card-body px-0">
                    <p class="text-muted small mb-4 px-4">Pilih tema warna untuk seluruh tampilan website (Public & Admin).
                    </p>

                    <form action="{{ route('admin.system.theme') }}" method="POST">
                        @csrf
                        <div class="theme-toggle-container">
                            <div class="theme-toggle-group">
                                <label class="theme-toggle-item">
                                    <input type="radio" name="theme" value="default" {{ $theme_name == 'default' ? 'checked' : '' }} onchange="this.form.submit()">
                                    <div class="theme-toggle-btn">
                                        <div class="theme-color-dot" style="background: #1e3a5f;"></div>
                                        <span>Navy Blue</span>
                                    </div>
                                </label>
                                <label class="theme-toggle-item">
                                    <input type="radio" name="theme" value="maroon" {{ $theme_name == 'maroon' ? 'checked' : '' }} onchange="this.form.submit()">
                                    <div class="theme-toggle-btn">
                                        <div class="theme-color-dot" style="background: #800000;"></div>
                                        <span>Red Maroon</span>
                                    </div>
                                </label>
                                <label class="theme-toggle-item">
                                    <input type="radio" name="theme" value="emerald" {{ $theme_name == 'emerald' ? 'checked' : '' }} onchange="this.form.submit()">
                                    <div class="theme-toggle-btn">
                                        <div class="theme-color-dot" style="background: #10b981;"></div>
                                        <span>Green Emerald</span>
                                    </div>
                                </label>
                                <div class="theme-toggle-glider"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .theme-toggle-container {
            padding: 0 20px;
        }

        .theme-toggle-group {
            position: relative;
            display: flex;
            background-color: #f1f3f5;
            padding: 6px;
            border-radius: 12px;
            max-width: 800px;
            margin: 0 auto;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .theme-toggle-item {
            flex: 1;
            margin-bottom: 0;
            cursor: pointer;
            z-index: 2;
            position: relative;
        }

        .theme-toggle-item input {
            position: absolute;
            opacity: 0;
        }

        .theme-toggle-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 50px;
            transition: var(--transition);
            border-radius: 8px;
            gap: 12px;
        }

        .theme-color-dot {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.1);
        }

        .theme-toggle-btn span {
            font-weight: 700;
            font-size: 15px;
            color: #495057;
            transition: var(--transition);
        }

        /* Checked state text color */
        .theme-toggle-item input:checked+.theme-toggle-btn span {
            color: #fff;
        }

        .theme-toggle-glider {
            position: absolute;
            height: 50px;
            width: calc((100% - 12px) / 3);
            background: var(--primary);
            border-radius: 8px;
            z-index: 1;
            transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55), background 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        /* Glider Animation and Colors */
        .theme-toggle-group:has(.theme-toggle-item:nth-child(1) input:checked) .theme-toggle-glider {
            transform: translateX(0);
            background: #1e3a5f;
        }

        .theme-toggle-group:has(.theme-toggle-item:nth-child(2) input:checked) .theme-toggle-glider {
            transform: translateX(100%);
            background: #800000;
        }

        .theme-toggle-group:has(.theme-toggle-item:nth-child(3) input:checked) .theme-toggle-glider {
            transform: translateX(200%);
            background: #10b981;
        }

        @media (max-width: 768px) {
            .theme-toggle-group {
                flex-direction: column;
                max-width: 100%;
            }

            .theme-toggle-glider {
                display: none;
            }

            .theme-toggle-item input:checked+.theme-toggle-btn {
                background: var(--primary);
            }

            .theme-toggle-item input:checked+.theme-toggle-btn span {
                color: #fff;
            }

            .theme-toggle-btn {
                margin-bottom: 8px;
            }

            .theme-toggle-item:last-child .theme-toggle-btn {
                margin-bottom: 0;
            }
        }
    </style>

    <div class="row">
        <!-- Git Update -->
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="background: rgba(30, 58, 95, 0.05); color: var(--primary);">
                    <h3><i class="fas fa-cloud-download-alt"></i> Update Aplikasi</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Tombol ini akan mengambil kode terbaru dari
                        <strong>GitHub</strong> dan menjalankan migrasi database secara otomatis.
                    </div>

                    <form action="{{ route('admin.system.update') }}" method="POST" id="update-form">
                        @csrf
                        <button type="button" class="btn btn-success btn-lg" onclick="confirmUpdate(this)">
                            <i class="fab fa-github"></i> Update dari GitHub
                        </button>
                    </form>

                    <script>
                        function confirmUpdate(btn) {
                            Swal.fire({
                                title: 'Update Sistem?',
                                text: "Website mungkin tidak bisa diakses selama beberapa detik saat proses update berlangsung.",
                                icon: 'info',
                                showCancelButton: true,
                                confirmButtonColor: '#28a745',
                                cancelButtonColor: '#6c757d',
                                confirmButtonText: 'Ya, Lakukan Update!',
                                cancelButtonText: 'Batal'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Change button state
                                    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Sedang Memproses...';
                                    btn.disabled = true;
                                    btn.style.opacity = '0.7';
                                    btn.style.cursor = 'not-allowed';

                                    // Submit form
                                    document.getElementById('update-form').submit();
                                }
                            });
                        }
                    </script>

                    @if(session('update_log'))
                        <div style="margin-top: 20px;">
                            <h5>Log Update Terakhir:</h5>
                            <pre
                                style="background: #1e1e1e; color: #0f0; padding: 15px; border-radius: 5px; font-family: monospace; max-height: 300px; overflow-y: auto;">{{ session('update_log') }}</pre>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection