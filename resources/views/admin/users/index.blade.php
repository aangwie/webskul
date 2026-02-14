@extends('admin.layouts.app')

@section('title', 'Manajemen User')
@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <style>
        .dataTables_wrapper .dataTables_length select {
            padding: 6px 30px 6px 10px;
            border-radius: 8px;
            border: 1px solid var(--accent);
        }

        .dataTables_wrapper .dataTables_filter input {
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid var(--accent);
            margin-left: 10px;
        }

        table.dataTable.no-footer {
            border-bottom: 1px solid var(--accent);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: var(--primary) !important;
            color: white !important;
            border: 1px solid var(--primary) !important;
            border-radius: 8px;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Daftar User</h2>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah User
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td><strong>{{ $user->name }}</strong></td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->isAdmin())
                                        <span class="badge badge-success">Admin</span>
                                    @elseif($user->isAdminKomite())
                                        <span class="badge badge-primary"
                                            style="background: rgba(236, 204, 17, 0.1); color: var(--primary);">Admin Komite</span>
                                    @elseif($user->isTeacher())
                                        <span class="badge badge-primary"
                                            style="background: rgba(30, 58, 95, 0.1); color: var(--primary);">Guru</span>
                                    @elseif($user->isStudent())
                                        <span class="badge badge-warning">Siswa</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; gap: 5px;">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                                onsubmit="return confirm('Hapus user ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                }
            });
        });
    </script>
@endsection