@extends('admin.layouts.app')

@section('title', 'Profil Admin')
@section('page-title', 'Profil Admin')

@section('content')
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 25px;">
    <!-- Update Profile -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-user"></i> Ubah Profil</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
                    @error('name')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required>
                    @error('email')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </form>
        </div>
    </div>

    <!-- Update Password -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-lock"></i> Ubah Password</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.profile.password') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Password Saat Ini</label>
                    <input type="password" name="current_password" class="form-input" required>
                    @error('current_password')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password" class="form-input" required>
                    @error('password')<span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="form-input" required>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-key"></i> Ubah Password
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
