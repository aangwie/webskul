@extends('admin.layouts.app')

@section('title', 'Tambah User')
@section('page-title', 'Tambah User Baru')

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Form Tambah User</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">Nama Lengkap *</label>
                    <input type="text" name="name" class="form-input" value="{{ old('name') }}" required>
                    @error('name')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email') }}" required>
                    @error('email')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Role *</label>
                    <select name="role" class="form-select" required>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="admin_komite" {{ old('role') == 'admin_komite' ? 'selected' : '' }}>Admin Komite
                        </option>
                        <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>Guru (Teacher)</option>
                        <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Siswa (Student)</option>
                        <option value="library_staff" {{ old('role') == 'library_staff' ? 'selected' : '' }}>Petugas
                            Perpustakaan</option>
                    </select>
                    @error('role')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Password *</label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="password" class="form-input" required>
                        <i class="fas fa-eye" id="togglePassword"
                            style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-light);"></i>
                    </div>
                    @error('password')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Konfirmasi Password *</label>
                    <div style="position: relative;">
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-input"
                            required>
                        <i class="fas fa-eye" id="togglePasswordConfirm"
                            style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-light);"></i>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 30px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn"
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
        document.getElementById('togglePassword').addEventListener('click', function () {
            const password = document.getElementById('password');
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });

        document.getElementById('togglePasswordConfirm').addEventListener('click', function () {
            const password = document.getElementById('password_confirmation');
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
    </script>
@endsection