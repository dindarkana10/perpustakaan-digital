<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Preview Laporan Pengembalian</title>
  <link rel="stylesheet" href="{{ asset('template/css/styles.min.css') }}" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
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

            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <h5 class="card-title fw-semibold mb-1">Preview Laporan Pengembalian</h5>
                <small class="text-muted">
                  @if($tanggal_mulai && $tanggal_selesai)
                    Periode: {{ \Carbon\Carbon::parse($tanggal_mulai)->format('d/m/Y') }} - 
                    {{ \Carbon\Carbon::parse($tanggal_selesai)->format('d/m/Y') }}
                  @else
                    Periode: <strong>Semua Data</strong>
                  @endif
                </small>
              </div>

              <div>
                <a href="{{ route('petugas.laporan.index') }}" class="btn btn-secondary me-2">
                  <iconify-icon icon="solar:arrow-left-outline" width="18"></iconify-icon>
                  Kembali
                </a>

                <!-- FORM EXPORT PDF -->
                <form action="{{ route('petugas.laporan.pengembalian.export') }}" method="POST" class="d-inline">
                  @csrf
                  <input type="hidden" name="tanggal_mulai" value="{{ $tanggal_mulai }}">
                  <input type="hidden" name="tanggal_selesai" value="{{ $tanggal_selesai }}">
                  <button type="submit" class="btn btn-danger">
                    <iconify-icon icon="solar:download-outline" width="18"></iconify-icon>
                    Export PDF
                  </button>
                </form>
              </div>
            </div>

            @if($pengembalian->isEmpty())
              <div class="alert alert-warning">
                <iconify-icon icon="solar:info-circle-outline" width="20"></iconify-icon>
                Tidak ada data pengembalian dengan filter yang dipilih.
              </div>
            @else

              <div class="table-responsive">
                <table id="laporanTable" class="table table-bordered table-striped">
                  <thead class="table-light">
                    <tr>
                      <th>No</th>
                      <th>Tanggal Kembali</th>
                      <th>Peminjam</th>
                      <th>Alat</th>
                      <th>Keterlambatan</th>
                      <th>Denda Terlambat</th>
                      <th>Denda Kerusakan</th>
                      <th>Total Denda</th>
                      <th>Status Bayar</th>
                      <th>Petugas</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($pengembalian as $item)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal_kembali_aktual)->format('d/m/Y') }}</td>
                        <td>{{ $item->peminjaman->user->name }}</td>
                        <td>
                          @foreach($item->peminjaman->details as $detail)
                            <div>• {{ $detail->alat->nama_alat }}</div>
                          @endforeach
                        </td>
                        <td class="text-center">
                          @if($item->keterlambatan_hari > 0)
                            <span class="badge bg-danger">{{ $item->keterlambatan_hari }} hari</span>
                          @else
                            <span class="badge bg-success">Tepat Waktu</span>
                          @endif
                        </td>
                        <td class="text-end">Rp {{ number_format($item->denda_keterlambatan, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($item->denda_kerusakan, 0, ',', '.') }}</td>
                        <td class="text-end"><strong>Rp {{ number_format($item->total_denda, 0, ',', '.') }}</strong></td>
                        <td>
                          @if($item->status_pembayaran == 'lunas')
                            <span class="badge bg-success">Lunas</span>
                          @else
                            <span class="badge bg-danger">Belum Lunas</span>
                          @endif
                        </td>
                        <td>{{ $item->petugas->name ?? '-' }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                  <tfoot>
                    <tr class="table-secondary fw-bold">
                      <th colspan="5" class="text-end">TOTAL:</th>
                      <th class="text-end">Rp {{ number_format($pengembalian->sum('denda_keterlambatan'), 0, ',', '.') }}</th>
                      <th class="text-end">Rp {{ number_format($pengembalian->sum('denda_kerusakan'), 0, ',', '.') }}</th>
                      <th class="text-end">Rp {{ number_format($pengembalian->sum('total_denda'), 0, ',', '.') }}</th>
                      <th colspan="2"></th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            @endif

          </div>
        </div>

      </div>
    </div>
  </div>

  <script src="{{ asset('template/libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('template/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

  <script>
    $(document).ready(function() {
      $('#laporanTable').DataTable({
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        pageLength: 25,
        order: [[1, 'desc']],
        columnDefs: [
          { orderable: false, targets: [3] }
        ]
      });
    });
  </script>
</body>
</html>