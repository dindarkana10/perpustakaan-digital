<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Detail Pengembalian - Admin</title>
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
              <h5 class="card-title fw-semibold mb-0">Detail Pengembalian Buku</h5>
              <a href="{{ route('admin.pengembalian.index') }}" class="btn btn-outline-secondary">
                <iconify-icon icon="solar:arrow-left-outline" width="18" class="me-1"></iconify-icon> Kembali
              </a>
            </div>

            <div class="row g-4">
              <div class="col-md-4">
                <div class="p-3 border rounded h-100">
                  <h6 class="fw-bold text-primary mb-3">Informasi Peminjam</h6>
                  <p class="mb-1 text-muted fs-2">Nama Lengkap:</p>
                  <p class="fw-bold fs-4 mb-3">{{ $pengembalian->peminjaman->user->name }}</p>
                  <p class="mb-1 text-muted fs-2">NISN | Kelas:</p>
                  <p class="fs-3">{{ $pengembalian->peminjaman->user->NISN }} | {{ $pengembalian->peminjaman->user->kelas_jurusan }}</p>
                </div>
              </div>

              <div class="col-md-4">
                <div class="p-3 border rounded h-100">
                  <h6 class="fw-bold text-primary mb-3">Waktu & Status</h6>
                  <div class="row mb-2">
                    <div class="col-6 text-muted fs-2">Tgl Pinjam</div>
                    <div class="col-6 fs-3">: {{ \Carbon\Carbon::parse($pengembalian->peminjaman->tanggal_pinjam)->format('d/m/Y') }}</div>
                  </div>
                  <div class="row mb-2">
                    <div class="col-6 text-muted fs-2">Tgl Rencana Kembali</div>
                    <div class="col-6 fs-3 text-danger fw-bold">: {{ \Carbon\Carbon::parse($pengembalian->peminjaman->tanggal_kembali_rencana)->format('d/m/Y') }}</div>
                  </div>
                  <div class="row mb-2">
                    <div class="col-6 text-muted fs-2">Tgl Kembali Aktual</div>
                    <div class="col-6 fs-3 fw-bold">: {{ $pengembalian->tanggal_kembali_aktual->format('d/m/Y') }}</div>
                  </div>
                  <hr>
                  <div class="row">
                    <div class="col-6 text-muted fs-2">Status Pembayaran</div>
                    <div class="col-6">
                        @if($pengembalian->status_pengembalian == 'diajukan')
                            <span class="badge bg-secondary">-</span>
                        @elseif($pengembalian->status_pembayaran == 'lunas')
                            <span class="badge bg-success">Lunas</span>
                        @elseif($pengembalian->status_pembayaran == 'tidak_ada_denda')
                            <span class="badge bg-light text-dark border">Tidak Ada Denda</span>
                        @else
                            <span class="badge bg-danger">Belum Lunas</span>
                        @endif
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-4">
                <div class="p-3 border rounded bg-light h-100">
                  <h6 class="fw-bold text-primary mb-3">Rincian Denda</h6>
                  <div class="d-flex justify-content-between mb-2">
                    <span>Keterlambatan ({{ $pengembalian->keterlambatan_hari }} Hari)</span>
                    <span>Rp {{ number_format($pengembalian->denda_keterlambatan, 0, ',', '.') }}</span>
                  </div>
                  <div class="d-flex justify-content-between mb-2">
                    <span>Kerusakan / Hilang</span>
                    <span>Rp {{ number_format($pengembalian->denda_kerusakan, 0, ',', '.') }}</span>
                  </div>
                  <hr>
                  <div class="d-flex justify-content-between fs-4 fw-bold">
                    <span>TOTAL DENDA</span>
                    <span class="text-primary">Rp {{ number_format($pengembalian->total_denda, 0, ',', '.') }}</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-5">
              <h6 class="fw-bold text-primary mb-3">Daftar Buku yang Dikembalikan</h6>
              <div class="table-responsive">
                <table class="table table-bordered align-middle">
                  <thead class="table-light">
                    <tr>
                      <th width="50">No</th>
                      <th>Judul Buku</th>
                      <th class="text-center">Jumlah</th>
                      <th class="text-center">Kondisi Kembali</th>
                      <th class="text-end">Denda Buku</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($pengembalian->details as $detail)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $detail->buku->judul_buku }}</td>
                        <td class="text-center">{{ $detail->jumlah_kembali }}</td>
                        <td class="text-center">
                          @php
                            $color = match($detail->kondisi_kembali) {
                              'baik' => 'success',
                              'rusak_ringan' => 'warning',
                              'rusak_berat' => 'danger',
                              'hilang' => 'dark',
                              default => 'secondary'
                            };
                          @endphp
                          <span class="badge bg-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $detail->kondisi_kembali)) }}</span>
                        </td>
                        <td class="text-end">Rp {{ number_format($detail->denda_kerusakan_buku, 0, ',', '.') }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>

            @if($pengembalian->petugas)
              <div class="mt-4 text-end">
                <small class="text-muted">Dikonfirmasi oleh: <strong>{{ $pengembalian->petugas->name }}</strong></small>
                <br><small class="text-muted">Pada: {{ $pengembalian->updated_at->format('d/m/Y H:i') }}</small>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset('template/libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('template/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</body>
</html>