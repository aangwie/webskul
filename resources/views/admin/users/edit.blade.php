@extends('admin.layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Form Edit User</h2>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Nama Lengkap *</label>
                <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
                @error('name')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Email *</label>
                <input type="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required>
                @error('email')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Role *</label>
                <select name="role" class="form-select" required>
                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="admin_komite" {{ old('role') == 'admin_komite' ? 'selected' : '' }}>Admin Komite</option>
                    <option value="teacher" {{ old('role', $user->role) == 'teacher' ? 'selected' : '' }}>Guru (Teacher)</option>
                    <option value="student" {{ old('role', $user->role) == 'student' ? 'selected' : '' }}>Siswa (Student)</option>
                </select>
                @error('role')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Password (Kosongkan jika tidak ingin mengubah)</label>
                <input type="password" name="password" class="form-input">
                @error('password')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" class="form-input">
            </div>

            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Perbarui
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn" style="background: var(--accent); color: var(--text);">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection