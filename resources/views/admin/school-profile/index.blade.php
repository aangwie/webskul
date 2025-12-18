@extends('admin.layouts.app')

@section('title', 'Profil Sekolah')
@section('page-title', 'Profil Sekolah')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Data Profil Sekolah</h2>
        <a href="{{ route('admin.school-profile.edit') }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit Profil
        </a>
    </div>
    <div class="card-body">
        @if($school)
            <div style="display: grid; gap: 20px;">
                @if($school->logo)
                    <div>
                        <strong>Logo Sekolah:</strong><br>
                        <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo" style="max-width: 150px; margin-top: 10px; border-radius: 10px;">
                    </div>
                @endif
                <div><strong>Nama Sekolah:</strong> {{ $school->name }}</div>
                <div><strong>Alamat:</strong> {{ $school->address ?? '-' }}</div>
                <div><strong>Telepon:</strong> {{ $school->phone ?? '-' }}</div>
                <div><strong>Email:</strong> {{ $school->email ?? '-' }}</div>
                <div>
                    <strong>Visi:</strong><br>
                    <p style="margin-top: 5px; color: var(--text-light);">{!! nl2br(e($school->vision ?? '-')) !!}</p>
                </div>
                <div>
                    <strong>Misi:</strong><br>
                    <p style="margin-top: 5px; color: var(--text-light);">{!! nl2br(e($school->mission ?? '-')) !!}</p>
                </div>
                <div>
                    <strong>Sejarah:</strong><br>
                    <p style="margin-top: 5px; color: var(--text-light);">{!! nl2br(e($school->history ?? '-')) !!}</p>
                </div>
            </div>
        @else
            <p style="text-align: center; color: var(--text-light); padding: 30px;">
                Profil sekolah belum diatur. <a href="{{ route('admin.school-profile.edit') }}">Atur sekarang</a>
            </p>
        @endif
    </div>
</div>
@endsection
