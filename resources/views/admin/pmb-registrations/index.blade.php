@extends('admin.layouts.app')

@section('title', 'Data Pendaftaran PMB')
@section('page-title', 'Data Pendaftaran PMB')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--primary) !important;
        color: white !important;
        border: 1px solid var(--primary) !important;
    }

    .dataTables_wrapper .dataTables_filter input {
        border-radius: 6px;
        border: 1px solid #ddd;
        padding: 5px 10px;
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-users"></i> Daftar Calon Murid Baru</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="pmbTable">
                <thead>
                    <tr>
                        <th>No. Pendaftaran</th>
                        <th>Nama Lengkap</th>
                        <th>NISN</th>
                        <th>NIK</th>
                        <th>Tahun Pelajaran</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($registrations as $reg)
                    <tr>
                        <td><strong>{{ $reg->registration_number }}</strong></td>
                        <td>{{ $reg->nama }}</td>
                        <td>{{ $reg->nisn }}</td>
                        <td>{{ $reg->nik }}</td>
                        <td>{{ $reg->academic_year }}</td>
                        <td>
                            @if($reg->status == 'pending')
                            <span class="badge badge-warning">Pending</span>
                            @elseif($reg->status == 'approved')
                            <span class="badge badge-success">Approved</span>
                            @else
                            <span class="badge badge-danger">Rejected</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.pmb-registrations.show', $reg) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                            <a href="{{ route('admin.pmb-registrations.edit', $reg) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('admin.pmb-registrations.destroy', $reg) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pendaftaran ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
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
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#pmbTable').DataTable({
            "order": [
                [0, "desc"]
            ],
            "language": {
                "search": "Cari Calon Murid:",
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "zeroRecords": "Data tidak ditemukan",
                "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                "infoEmpty": "Tidak ada data tersedia",
                "infoFiltered": "(disaring dari _MAX_ total data)",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                }
            }
        });
    });
</script>
@endsection