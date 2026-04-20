<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Detail Pengembalian</title>
  <link rel="stylesheet" href="{{ asset('template/css/styles.min.css') }}" />
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">

    <x-navbar></x-navbar>
    <x-sidebar></x-sidebar>

    <div class="body-wrapper">
      <div class="container-fluid">

        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h5 class="card-title fw-semibold mb-0">Detail Pengembalian</h5>
              <a href="{{ route('admin.pengembalian.index') }}" class="btn btn-outline-secondary">
                <iconify-icon icon="solar:arrow-left-outline" width="18" class="me-1"></iconify-icon>
                Kembali
              </a>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label fw-bold">Peminjam</label>
                <input type="text" class="form-control" value="{{ $pengembalian->peminjaman->user->name }}" readonly>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold">Keperluan</label>
                <input type="text" class="form-control" value="{{ $pengembalian->peminjaman->keperluan }}" readonly>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-3">
                <label class="form-label fw-bold">Tanggal Pinjam</label>
                <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($pengembalian->peminjaman->tanggal_pinjam)->format('d/m/Y') }}" readonly>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold">Tanggal Kembali Rencana</label>
                <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($pengembalian->peminjaman->tanggal_kembali_rencana)->format('d/m/Y') }}" readonly>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold">Tanggal Kembali Aktual</label>
                <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($pengembalian->tanggal_kembali_aktual)->format('d/m/Y') }}" readonly>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold">Keterlambatan</label>
                <input type="text" class="form-control" value="{{ $pengembalian->keterlambatan_hari }} hari" readonly>
              </div>
            </div>

            @if($pengembalian->status_pengembalian == 'dikonfirmasi')
            <div class="row mb-3">
              <div class="col-md-4">
                <label class="form-label fw-bold">Denda Keterlambatan</label>
                <input type="text" class="form-control" value="Rp {{ number_format($pengembalian->denda_keterlambatan, 0, ',', '.') }}" readonly>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-bold">Denda Kerusakan</label>
                <input type="text" class="form-control" value="Rp {{ number_format($pengembalian->denda_kerusakan, 0, ',', '.') }}" readonly>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-bold">Total Denda</label>
                <input type="text" class="form-control fw-bold text-danger" value="Rp {{ number_format($pengembalian->total_denda, 0, ',', '.') }}" readonly>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label fw-bold">Status Pengembalian</label><br>
                <span class="badge bg-success fs-6">Dikonfirmasi</span>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold">Status Pembayaran</label><br>
                @if($pengembalian->total_denda > 0)
                  @if($pengembalian->status_pembayaran == 'lunas')
                    <span class="badge bg-success fs-6">Lunas</span>
                  @else
                    <span class="badge bg-danger fs-6">Belum Lunas</span>
                  @endif
                @else
                  <span class="badge bg-secondary fs-6">Tidak Ada Denda</span>
                @endif
              </div>
            </div>
            @else
            <div class="alert alert-warning">
              <iconify-icon icon="solar:clock-circle-outline" width="20"></iconify-icon>
              Status: <strong>Menunggu Validasi Petugas</strong>
            </div>
            @endif

            <hr>
            <h6 class="mb-3 fw-semibold">Detail Alat</h6>
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead class="table-light">
                  <tr>
                    <th>No</th>
                    <th>Nama Alat</th>
                    <th>Jumlah</th>
                    <th>Kondisi Pinjam</th>
                    @if($pengembalian->status_pengembalian == 'dikonfirmasi')
                    <th>Kondisi Kembali</th>
                    <th>Keterangan</th>
                    @endif
                  </tr>
                </thead>
                <tbody>
@forelse($pengembalian->peminjaman->details as $peminjamanDetail)
    @php
        $returnDetail = $pengembalian->details
            ->where('alat_id', $peminjamanDetail->alat_id)
            ->first();
    @endphp

    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $peminjamanDetail->alat->nama_alat ?? '-' }}</td>

        <td class="text-center">
            {{ $returnDetail ? $returnDetail->jumlah_kembali : $peminjamanDetail->jumlah }}
        </td>

        {{-- Kondisi pinjam --}}
        <td>
            @if($peminjamanDetail->kondisi_pinjam == 'baik')
                <span class="badge bg-success">Baik</span>
            @elseif($peminjamanDetail->kondisi_pinjam == 'rusak_ringan')
                <span class="badge bg-warning">Rusak Ringan</span>
            @else
                <span class="badge bg-danger">Rusak Berat</span>
            @endif
        </td>

        @if($pengembalian->status_pengembalian == 'dikonfirmasi')
        {{-- Kondisi kembali --}}
        <td>
            @if($returnDetail)
                @if($returnDetail->kondisi_kembali == 'baik')
                    <span class="badge bg-success">Baik</span>
                @elseif($returnDetail->kondisi_kembali == 'rusak_ringan')
                    <span class="badge bg-warning">Rusak Ringan</span>
                @elseif($returnDetail->kondisi_kembali == 'rusak_berat')
                    <span class="badge bg-danger">Rusak Berat</span>
                @else
                    <span class="badge bg-dark">Hilang</span>
                @endif
            @else
                <span class="badge bg-secondary">Belum dicatat</span>
            @endif
        </td>

        {{-- Keterangan --}}
        <td>{{ $returnDetail->keterangan_kondisi ?? '-' }}</td>
        @endif
    </tr>

@empty
<tr>
    <td colspan="6" class="text-center text-muted">
        Tidak ada data alat
    </td>
</tr>
@endforelse
</tbody>
              </table>
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