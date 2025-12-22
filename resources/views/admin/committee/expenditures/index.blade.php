@extends('admin.layouts.app')

@section('title', 'Manajemen Penggunaan Dana Komite')
@section('page-title', 'Penggunaan Dana Komite')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <div>
        <p style="color: var(--text-light);">Kelola data pengeluaran dan penggunaan dana komite sekolah.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('admin.committee.expenditures.report') }}" class="btn btn-secondary">
            <i class="fas fa-file-alt"></i> Laporan Penggunaan
        </a>
        <a href="{{ route('admin.committee.expenditures.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Catat Penggunaan
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-list"></i> Daftar Penggunaan Dana</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor Pengeluaran</th>
                        <th>Tanggal</th>
                        <th>Deskripsi Penggunaan</th>
                        <th style="text-align: right;">Nominal</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenditures as $index => $exp)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><span class="badge badge-info" style="font-family: monospace;">{{ $exp->expenditure_number }}</span></td>
                        <td>{{ $exp->date->format('d/m/Y') }}</td>
                        <td>{{ $exp->description }}</td>
                        <td style="text-align: right; font-weight: 600; color: var(--danger);">
                            Rp {{ number_format($exp->amount, 0, ',', '.') }}
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 5px; justify-content: center;">
                                <a href="{{ route('admin.committee.expenditures.print', $exp) }}" target="_blank" class="btn btn-sm btn-info" title="Cetak Bukti">
                                    <i class="fas fa-print"></i>
                                </a>
                                <a href="{{ route('admin.committee.expenditures.edit', $exp) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.committee.expenditures.destroy', $exp) }}" method="POST" id="delete-form-{{ $exp->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger" title="Hapus" onclick="confirmDelete({{ $exp->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px; color: var(--text-light);">
                            Belum ada data penggunaan dana.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@section('scripts')
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data penggunaan dana ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endsection
@endsection