@extends('admin.layouts.app')

@section('title', 'Atur Nominal - ' . $academicYear->year)
@section('page-title', 'Atur Nominal Komite')

@section('content')
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-money-bill-wave"></i> Set Nominal Tahun {{ $academicYear->year }}</h2>
        <a href="{{ route('admin.committee.nominal.index') }}" class="btn btn-sm btn-danger">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.committee.nominal.store', $academicYear->id) }}" method="POST">
            @csrf
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Nama Kelas</th>
                            <th>Tingkat (Grade)</th>
                            <th>Nominal Pembayaran (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classes as $class)
                        <tr>
                            <td><strong>{{ $class->name }}</strong></td>
                            <td>{{ $class->grade }}</td>
                            <td>
                                <input type="number" name="nominal[{{ $class->id }}]"
                                    value="{{ old('nominal.' . $class->id, $existingFees[$class->id] ?? 0) }}"
                                    class="form-input" placeholder="0" required min="0">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 25px; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Nominal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection