@extends('admin.layouts.app')

@section('title', 'Laporan Perpustakaan')

@section('content')
    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['total_books'] }}</h3>
                <p>Total Judul Buku</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-tags"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['total_types'] }}</h3>
                <p>Jenis Buku</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['total_laik'] }}</h3>
                <p>Buku Laik</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['total_tidak_laik'] }}</h3>
                <p>Buku Tidak Laik</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon gold">
                <i class="fas fa-hand-holding"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['total_borrowed'] }}</h3>
                <p>Buku Dipinjam</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-file-alt"></i> Laporan Data Buku</h2>
        </div>
        <div class="card-body">
            <!-- Filter Form -->
            <form method="GET" action="{{ route('admin.library.reports.index') }}"
                style="display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 25px;">
                <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
                    <label class="form-label">Jenis Buku</label>
                    <select name="book_type_id" class="form-select">
                        <option value="">Semua Jenis</option>
                        @foreach($bookTypes as $type)
                            <option value="{{ $type->id }}" {{ request('book_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
                    <label class="form-label">Tahun Perolehan</label>
                    <input type="number" name="tahun_perolehan" class="form-input" value="{{ request('tahun_perolehan') }}"
                        placeholder="Contoh: 2024" min="1900" max="{{ date('Y') }}">
                </div>
                <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
                    <label class="form-label">Kondisi</label>
                    <select name="kondisi" class="form-select">
                        <option value="">Semua Kondisi</option>
                        <option value="laik" {{ request('kondisi') == 'laik' ? 'selected' : '' }}>Laik</option>
                        <option value="tidak_laik" {{ request('kondisi') == 'tidak_laik' ? 'selected' : '' }}>Tidak Laik
                        </option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0; display: flex; align-items: flex-end;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
                @if(request()->hasAny(['book_type_id', 'tahun_perolehan', 'kondisi']))
                    <div class="form-group" style="margin-bottom: 0; display: flex; align-items: flex-end;">
                        <a href="{{ route('admin.library.reports.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    </div>
                @endif
            </form>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul Buku</th>
                            <th>Jenis</th>
                            <th>Penerbit</th>
                            <th>Pengarang</th>
                            <th>Tahun</th>
                            <th>Asal-Usul</th>
                            <th>Jumlah</th>
                            <th>Kondisi</th>
                            <th>Dipinjam</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($books as $book)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $book->judul_buku }}</td>
                                <td><span class="badge badge-success">{{ $book->bookType->name ?? '-' }}</span></td>
                                <td>{{ $book->penerbit }}</td>
                                <td>{{ $book->pengarang }}</td>
                                <td>{{ $book->tahun_perolehan }}</td>
                                <td>{{ $book->asal_usul }}</td>
                                <td>{{ $book->condition->jumlah_buku ?? 0 }}</td>
                                <td>
                                    @if($book->condition)
                                        @if($book->condition->kondisi == 'laik')
                                            <span class="badge badge-success">Laik</span>
                                        @else
                                            <span class="badge badge-danger">Tidak Laik</span>
                                        @endif
                                    @else
                                        <span class="badge badge-warning">Belum Didata</span>
                                    @endif
                                </td>
                                <td>{{ $book->borrowings->where('is_returned', false)->sum('jumlah_pinjam') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">Tidak ada data buku ditemukan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection