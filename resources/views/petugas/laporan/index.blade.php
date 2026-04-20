<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Petugas - Laporan</title>
  <link rel="stylesheet" href="{{ asset('template/css/styles.min.css') }}" />
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
    
    <x-navbar></x-navbar>
    <x-sidebar></x-sidebar>

    <div class="body-wrapper">
      <div class="container-fluid">

        <h4 class="mb-4">
          <iconify-icon icon="solar:chart-2-outline" width="28"></iconify-icon>
          Laporan Peminjaman & Pengembalian
        </h4>

        <!-- Alert Validasi -->
        @if ($errors->any())
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong>
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        <!-- ============================= -->
        <!-- CARD LAPORAN PEMINJAMAN -->
        <!-- ============================= -->
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">
              <iconify-icon icon="solar:document-text-outline" width="24"></iconify-icon>
              Laporan Peminjaman
            </h5>

            <form action="{{ route('petugas.laporan.peminjaman.preview') }}" method="POST">
              @csrf
              <div class="row g-3">
                <div class="col-md-3">
                  <label class="form-label">Tanggal Mulai</label>
                  <input type="date" name="tanggal_mulai" class="form-control" value="{{ old('tanggal_mulai') }}">
                  <small class="text-muted">Kosongkan untuk semua data</small>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Tanggal Selesai</label>
                  <input type="date" name="tanggal_selesai" class="form-control" value="{{ old('tanggal_selesai') }}">
                  <small class="text-muted">Kosongkan untuk semua data</small>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Status</label>
                  <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="menunggu_persetujuan" {{ old('status') == 'menunggu_persetujuan' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                    <option value="dipinjam" {{ old('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="dikembalikan" {{ old('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                    <option value="ditolak" {{ old('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="terlambat" {{ old('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                  </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                  <button type="submit" class="btn btn-primary w-100">
                    <iconify-icon icon="solar:eye-outline" width="18"></iconify-icon>
                    Tampilkan Data
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- ============================= -->
        <!-- CARD LAPORAN PENGEMBALIAN -->
        <!-- ============================= -->
        <div class="card">
          <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">
              <iconify-icon icon="solar:box-minimalistic-outline" width="24"></iconify-icon>
              Laporan Pengembalian
            </h5>

            <form action="{{ route('petugas.laporan.pengembalian.preview') }}" method="POST">
              @csrf
              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label">Tanggal Mulai</label>
                  <input type="date" name="tanggal_mulai" class="form-control" value="{{ old('tanggal_mulai') }}">
                  <small class="text-muted">Kosongkan untuk semua data</small>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Tanggal Selesai</label>
                  <input type="date" name="tanggal_selesai" class="form-control" value="{{ old('tanggal_selesai') }}">
                  <small class="text-muted">Kosongkan untuk semua data</small>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                  <button type="submit" class="btn btn-primary w-100">
                    <iconify-icon icon="solar:eye-outline" width="18"></iconify-icon>
                    Tampilkan Data
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>

  <script src="{{ asset('template/libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('template/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

  <script>
    // Auto hide alert after 5 seconds
    setTimeout(() => {
      $('.alert').fadeOut();
    }, 5000);
  </script>
</body>
</html>