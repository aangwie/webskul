@extends('admin.layouts.app')

@section('title', 'Import Siswa')
@section('page-title', 'Import Siswa - Excel')

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h2>Import Data Siswa</h2>
        <a href="{{ route('admin.students.index') }}" class="btn btn-warning btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        
        <div class="alert alert-success" style="background: rgba(30, 58, 95, 0.05); border-color: var(--primary); color: var(--text);">
            <h4 style="margin-bottom: 10px; color: var(--primary);"><i class="fas fa-info-circle"></i> Petunjuk Import</h4>
            <ol style="margin-left: 20px; line-height: 1.6;">
                <li>Unduh template Excel/CSV yang telah disediakan.</li>
                <li>Isi data siswa sesuai kolom (Nama, NIS, L/P, Kelas, Tahun Masuk).</li>
                <li>Pastikan nama kelas <strong>sama persis</strong> dengan data di sistem (misal: "7A", "8B").</li>
                <li>Upload file Excel atau CSV yang telah diisi.</li>
            </ol>
            <p style="margin-left: 20px; font-size: 0.9em; margin-top: 10px;">
                <strong>Catatan:</strong> Jika mengalami error "ZipArchive", silakan gunakan format <strong>.csv</strong>.
            </p>
        </div>

        <div style="text-align: center; margin-bottom: 30px; display: flex; justify-content: center; gap: 10px;">
            <a href="{{ route('admin.students.import.template', ['format' => 'xlsx']) }}" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Template Excel (.xlsx)
            </a>
            <a href="{{ route('admin.students.import.template', ['format' => 'csv']) }}" class="btn btn-info" style="color: white;">
                <i class="fas fa-file-csv"></i> Template CSV
            </a>
        </div>

        <hr style="margin: 20px 0; border: 0; border-top: 1px solid rgba(0,0,0,0.1);">

        <form id="importForm" enctype="multipart/form-data">
            @csrf
            
            <div class="form-group">
                <label for="file" class="form-label">Pilih File (.xlsx / .xls / .csv)</label>
                <div style="border: 2px dashed #ccc; padding: 40px; text-align: center; border-radius: 10px; cursor: pointer;" onclick="document.getElementById('file').click()">
                    <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: var(--text-light); margin-bottom: 15px;"></i>
                    <p id="fileName" style="margin: 0; color: var(--text-light);">Klik untuk memilih file</p>
                    <input type="file" name="file" id="file" accept=".xlsx, .xls, .csv, .txt" style="display: none;" onchange="updateFileName(this)">
                </div>
                <p style="font-size: 0.8rem; color: var(--danger); margin-top: 5px; display: none;" id="fileError"></p>
            </div>

            <div id="progressContainer" style="display: none; margin-top: 20px;">
                <label class="form-label">Memproses Data...</label>
                <div style="width: 100%; background: #eee; height: 10px; border-radius: 5px; overflow: hidden;">
                    <div id="progressBar" style="width: 0%; height: 100%; background: var(--success); transition: width 0.3s;"></div>
                </div>
                <p id="progressText" style="text-align: center; font-size: 0.85rem; margin-top: 5px; color: var(--text-light);">Mohon tunggu...</p>
            </div>

            <div style="margin-top: 30px;">
                <button type="submit" class="btn btn-primary" id="btnSubmit">
                    <i class="fas fa-upload"></i> Mulai Import
                </button>
            </div>
        </form>

        <div id="resultContainer" style="margin-top: 30px; display: none;">
            <!-- Result content will be injected here -->
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    function updateFileName(input) {
        if (input.files && input.files[0]) {
            document.getElementById('fileName').innerText = input.files[0].name;
            document.getElementById('fileName').style.color = 'var(--text)';
        }
    }

    document.getElementById('importForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const fileInput = document.getElementById('file');
        if (!fileInput.files || !fileInput.files[0]) {
            document.getElementById('fileError').innerText = 'Silakan pilih file terlebih dahulu.';
            document.getElementById('fileError').style.display = 'block';
            return;
        }

        document.getElementById('fileError').style.display = 'none';
        
        const formData = new FormData(this);
        const btnSubmit = document.getElementById('btnSubmit');
        const progressContainer = document.getElementById('progressContainer');
        const progressBar = document.getElementById('progressBar');
        const resultContainer = document.getElementById('resultContainer');
        
        // UI Loading State
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sedang Memproses...';
        progressContainer.style.display = 'block';
        resultContainer.style.display = 'none';
        progressBar.style.width = '20%'; // Fake initial progress

        // Simulate progress for better UX since we don't have websocket
        let progress = 20;
        const interval = setInterval(() => {
            if (progress < 90) {
                progress += 5;
                progressBar.style.width = progress + '%';
            }
        }, 500);

        fetch("{{ route('admin.students.import.store') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        })
        .then(response => response.json())
        .then(data => {
            clearInterval(interval);
            progressBar.style.width = '100%';
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="fas fa-upload"></i> Mulai Import';
            
            resultContainer.style.display = 'block';
            
            if (data.success) {
                resultContainer.innerHTML = `
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> ${data.message}
                    </div>
                `;
                // Reset form
                document.getElementById('importForm').reset();
                document.getElementById('fileName').innerText = 'Klik untuk memilih file';
                
                // Redirect after 2 seconds
                setTimeout(() => {
                    window.location.href = "{{ route('admin.students.index') }}";
                }, 2000);
            } else {
                let errorHtml = '<ul style="margin-left: 15px;">';
                if (data.errors) {
                    data.errors.forEach(err => {
                        errorHtml += `<li>${err}</li>`;
                    });
                }
                errorHtml += '</ul>';

                resultContainer.innerHTML = `
                    <div class="alert alert-danger" style="display: block;">
                        <strong><i class="fas fa-exclamation-triangle"></i> Gagal!</strong>
                        <p>${data.message}</p>
                        ${data.errors ? errorHtml : ''}
                    </div>
                `;
            }
        })
        .catch(error => {
            clearInterval(interval);
            progressBar.style.width = '100%';
            progressBar.style.background = 'var(--danger)';
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="fas fa-upload"></i> Mulai Import';
            
            resultContainer.style.display = 'block';
            resultContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> Terjadi kesalahan server. Silakan coba lagi.
                </div>
            `;
            console.error('Error:', error);
        });
    });
</script>
@endsection
