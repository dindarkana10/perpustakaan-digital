<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Peminjaman - Perpustakaan Digital</title>
    <link rel="stylesheet" href="{{ asset('template/css/styles.min.css') }}" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
        /* Mengambil style dari Dashboard untuk konsistensi */
        .card-stats {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .card-stats:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.10) !important;
        }
        .icon-box {
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            flex-shrink: 0;
        }
        .status-badge {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 50px;
        }
        .form-label-sm {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6"
        data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">

        <x-navbar></x-navbar>
        <x-sidebar></x-sidebar>

        <div class="body-wrapper">
            <div class="container-fluid">
                
                {{-- ── Header ── --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title fw-semibold mb-0">Laporan Peminjaman Buku</h5>
                    <div>
                        <a href="{{ route('admin.laporan.pdf', request()->all()) }}" target="_blank" class="btn btn-danger btn-sm shadow-sm d-flex align-items-center">
                            <iconify-icon icon="solar:file-download-outline" width="18" class="me-1"></iconify-icon>
                            Export PDF
                        </a>
                    </div>
                </div>

                {{-- ── Summary Cards (Dashboard Style) ── --}}
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card card-stats shadow-sm h-100 border-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box bg-primary-subtle">
                                        <iconify-icon icon="solar:clipboard-list-linear" class="fs-6 text-primary"></iconify-icon>
                                    </div>
                                    <div class="ms-3">
                                        <h3 class="mb-0 fw-bold">{{ number_format($summary['total_peminjaman']) }}</h3>
                                        <span class="text-muted fs-3">Total Peminjaman</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card card-stats shadow-sm h-100 border-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box bg-success-subtle">
                                        <iconify-icon icon="solar:check-circle-linear" class="fs-6 text-success"></iconify-icon>
                                    </div>
                                    <div class="ms-3">
                                        <h3 class="mb-0 fw-bold">{{ number_format($summary['total_pengembalian']) }}</h3>
                                        <span class="text-muted fs-3">Total Pengembalian</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card card-stats shadow-sm h-100 border-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box bg-danger-subtle">
                                        <iconify-icon icon="solar:wad-of-money-linear" class="fs-6 text-danger"></iconify-icon>
                                    </div>
                                    <div class="ms-3">
                                        <h3 class="mb-0 fw-bold text-danger">Rp {{ number_format($summary['total_denda'], 0, ',', '.') }}</h3>
                                        <span class="text-muted fs-3">Total Denda Terkumpul</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Filter Section ── --}}
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <iconify-icon icon="solar:filter-linear" width="20" class="me-2 text-primary"></iconify-icon>
                            <h6 class="mb-0 fw-bold">Filter Laporan</h6>
                        </div>
                        
                        <form action="{{ route('admin.laporan.index') }}" method="GET">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label-sm">Kategori</label>
                                    <select name="kategori_id" class="form-select form-select-sm">
                                        <option value="">Semua Kategori</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ request('kategori_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-sm">Judul Buku</label>
                                    <input type="text" name="judul" value="{{ request('judul') }}" placeholder="Cari judul..." class="form-control form-control-sm">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-sm">Nama Peminjam</label>
                                    <input type="text" name="peminjam" value="{{ request('peminjam') }}" placeholder="Cari nama..." class="form-control form-control-sm">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-sm">Status</label>
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="">Semua Status</option>
                                        <option value="menunggu_persetujuan" {{ request('status') == 'menunggu_persetujuan' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                                        <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                                        <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                                        <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-sm">Pinjam (Mulai)</label>
                                    <input type="date" name="tgl_pinjam_awal" value="{{ request('tgl_pinjam_awal') }}" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-sm">Pinjam (Selesai)</label>
                                    <input type="date" name="tgl_pinjam_akhir" value="{{ request('tgl_pinjam_akhir') }}" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-6 d-flex align-items-end justify-content-end gap-2">
                                    <a href="{{ route('admin.laporan.index') }}" class="btn btn-sm btn-light-danger text-danger fw-bold">Reset</a>
                                    <button type="submit" class="btn btn-sm btn-primary px-4 d-flex align-items-center">
                                        <iconify-icon icon="solar:magnifer-linear" class="me-1"></iconify-icon> Filter Data
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- ── Table Section ── --}}
                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0">Preview Data Laporan</h6>
                            <span class="badge bg-light-primary text-primary">Total: {{ $laporan->total() }} Data</span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width:50px">No</th>
                                        <th>Nama Peminjam</th>
                                        <th>Judul Buku</th>
                                        <th>Kategori</th>
                                        <th class="text-center">Tgl Pinjam</th>
                                        <th class="text-center">Tgl Kembali</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-end">Denda</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($laporan as $item)
                                    <tr>
                                        <td class="text-center">{{ ($laporan->currentPage() - 1) * $laporan->perPage() + $loop->iteration }}</td>
                                        <td><span class="fw-bold text-dark">{{ $item->peminjaman->user->name }}</span></td>
                                        <td>{{ $item->buku->judul_buku }}</td>
                                        <td><span class="badge bg-light-info text-info">{{ $item->buku->kategoriBuku->nama_kategori }}</span></td>
                                        <td class="text-center">{{ $item->peminjaman->tanggal_pinjam->format('d/m/Y') }}</td>
                                        <td class="text-center">
                                            {{ $item->peminjaman->pengembalian ? $item->peminjaman->pengembalian->tanggal_kembali_aktual->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="text-center">
                                            <span class="status-badge {{ $item->peminjaman->status_badge }}">
                                                {{ $item->peminjaman->status_label }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold {{ ($item->peminjaman->pengembalian && $item->peminjaman->pengembalian->total_denda > 0) ? 'text-danger' : 'text-muted' }}">
                                            Rp {{ number_format($item->peminjaman->pengembalian ? $item->peminjaman->pengembalian->total_denda : 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <iconify-icon icon="solar:folder-error-outline" width="48" class="text-muted mb-2"></iconify-icon>
                                            <p class="text-muted italic">Data tidak ditemukan sesuai filter.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $laporan->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="{{ asset('template/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('template/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('template/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('template/js/app.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</body>
</html>