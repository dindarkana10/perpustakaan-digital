<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Petugas - Pengembalian</title>
  <link rel="stylesheet" href="{{ asset('template/css/styles.min.css') }}" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6"
    data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">

    <x-navbar></x-navbar>
    <x-sidebar></x-sidebar>

    <div class="body-wrapper">
      <div class="container-fluid">
        <div class="card">
          <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="card-title fw-semibold">Kelola Pengembalian</h5>
            </div>

            @if($denda)
            <div class="alert alert-info d-flex gap-4 flex-wrap py-2 mb-3">
              <small><i class="ti ti-info-circle me-1"></i><strong>Tarif Denda Aktif:</strong></small>
              <small>Keterlambatan: <strong>Rp {{ number_format($denda->denda_per_hari, 0, ',', '.') }}/hari</strong></small>
              <small>Rusak Ringan: <strong>{{ $denda->denda_rusak_ringan }}% harga alat</strong></small>
              <small>Rusak Berat: <strong>{{ $denda->denda_rusak_berat }}% harga alat</strong></small>
              <small>Hilang: <strong>100% harga alat (ganti rugi penuh)</strong></small>
            </div>
            @endif

            @if(session('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            @endif
            @if(session('error'))
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            @endif

            <div class="table-responsive">
              <table id="pengembalianTable" class="table table-bordered table-striped">
                <thead class="table-light">
                  <tr>
                    <th>No</th>
                    <th>Peminjam</th>
                    <th>Tanggal Kembali</th>
                    <th>Terlambat</th>
                    <th>Total Denda</th>
                    <th>Status Pembayaran</th>
                    <th>Status Pengembalian</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($pengembalians as $item)
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>{{ $item->peminjaman->user->name }}</td>
                      <td>{{ $item->tanggal_kembali_aktual->format('d-m-Y') }}</td>
                      <td>
                        @if($item->keterlambatan_hari > 0)
                          <span class="badge bg-danger">{{ $item->keterlambatan_hari }} hari</span>
                        @else
                          <span class="badge bg-success">Tepat waktu</span>
                        @endif
                      </td>
                      <td>Rp {{ number_format($item->total_denda, 0, ',', '.') }}</td>
                      <td>
                        @if($item->status_pembayaran == 'lunas')
                          <span class="badge bg-success">Lunas</span>
                        @else
                          <span class="badge bg-warning text-dark">Belum Lunas</span>
                        @endif
                      </td>
                      <td>
                        @if($item->status_pengembalian == 'diajukan')
                          <span class="badge bg-info text-dark">Diajukan</span>
                        @else
                          <span class="badge bg-success">Dikonfirmasi</span>
                        @endif
                      </td>

                      {{-- ============ KOLOM AKSI ============ --}}
                      <td class="text-center">

                        @if($item->status_pengembalian == 'diajukan')

                          <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#konfirmasiModal{{ $item->id }}">
                            <i class="ti ti-check"></i> Konfirmasi
                          </button>

                          {{-- ===== MODAL KONFIRMASI ===== --}}
                          <div class="modal fade" id="konfirmasiModal{{ $item->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                              <form action="{{ route('petugas.pengembalian.konfirmasi', $item->id) }}" method="POST">
                                @csrf
                                <div class="modal-content">
                                  <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">Konfirmasi Pengembalian</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                  </div>

                                  <div class="modal-body">

                                    {{-- Info Peminjam --}}
                                    <div class="mb-3 p-3 bg-light rounded">
                                      <h6 class="fw-semibold mb-2">Informasi Peminjaman</h6>
                                      <div class="row">
                                        <div class="col-6">
                                          <small class="text-muted">Peminjam</small>
                                          <p class="mb-1 fw-semibold">{{ $item->peminjaman->user->name }}</p>
                                        </div>
                                        <div class="col-6">
                                          <small class="text-muted">Tanggal Kembali</small>
                                          <p class="mb-1 fw-semibold">{{ $item->tanggal_kembali_aktual->format('d-m-Y') }}</p>
                                        </div>
                                        <div class="col-6">
                                          <small class="text-muted">Keterlambatan</small>
                                          <p class="mb-0 fw-semibold {{ $item->keterlambatan_hari > 0 ? 'text-danger' : 'text-success' }}">
                                            {{ $item->keterlambatan_hari > 0 ? $item->keterlambatan_hari . ' hari' : 'Tepat waktu' }}
                                          </p>
                                        </div>
                                        <div class="col-6">
                                          <small class="text-muted">Total Harga Alat</small>
                                          <p class="mb-0 fw-semibold">Rp {{ number_format($item->total_harga_alat, 0, ',', '.') }}</p>
                                        </div>
                                      </div>
                                    </div>

                                    {{-- ── Daftar Alat + Kondisi Kembali (dari peminjam) ── --}}
                                    <div class="mb-3">
                                      <h6 class="fw-semibold mb-2">Kondisi Alat Saat Dikembalikan</h6>
                                      <table class="table table-sm table-bordered align-middle">
                                        <thead class="table-light">
                                          <tr>
                                            <th>Alat</th>
                                            <th class="text-center">Jml</th>
                                            <th class="text-center">Kondisi Kembali</th>
                                            <th>Keterangan</th>
                                            <th class="text-end">Harga Satuan</th>
                                            <th class="text-end">Denda Item</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          @foreach($item->breakdown_kondisi as $bk)
                                          <tr>
                                            <td>{{ $bk['nama_alat'] }}</td>
                                            <td class="text-center">{{ $bk['jumlah'] }}</td>
                                            <td class="text-center">
                                              @php
                                                $badgeMap = [
                                                  'baik'         => 'bg-success',
                                                  'rusak_ringan' => 'bg-warning text-dark',
                                                  'rusak_berat'  => 'bg-danger',
                                                  'hilang'       => 'bg-secondary',
                                                ];
                                                $labelMap = [
                                                  'baik'         => 'Baik',
                                                  'rusak_ringan' => 'Rusak Ringan',
                                                  'rusak_berat'  => 'Rusak Berat',
                                                  'hilang'       => 'Hilang',
                                                ];
                                              @endphp
                                              <span class="badge {{ $badgeMap[$bk['kondisi_kembali']] ?? 'bg-light text-dark' }}">
                                                {{ $labelMap[$bk['kondisi_kembali']] ?? $bk['kondisi_kembali'] }}
                                              </span>
                                            </td>
                                            <td>
                                              <small class="text-muted">{{ $bk['keterangan'] ?? '-' }}</small>
                                            </td>
                                            <td class="text-end">Rp {{ number_format($bk['harga_satuan'], 0, ',', '.') }}</td>
                                            <td class="text-end {{ $bk['denda_item'] > 0 ? 'text-danger fw-semibold' : '' }}">
                                              Rp {{ number_format($bk['denda_item'], 0, ',', '.') }}
                                            </td>
                                          </tr>
                                          @endforeach
                                        </tbody>
                                        <tfoot class="table-light">
                                          <tr>
                                            <td colspan="5" class="text-end fw-semibold">Total Denda Kerusakan:</td>
                                            <td class="text-end fw-bold text-danger">
                                              Rp {{ number_format($item->denda_kerusakan_otomatis, 0, ',', '.') }}
                                            </td>
                                          </tr>
                                        </tfoot>
                                      </table>
                                    </div>

                                    {{-- ── Input Denda ── --}}
                                    <div class="row">
                                      <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Denda Keterlambatan</label>
                                        <div class="input-group">
                                          <span class="input-group-text">Rp</span>
                                          <input type="number"
                                            name="denda_keterlambatan"
                                            id="denda_keterlambatan_{{ $item->id }}"
                                            value="{{ $item->denda_keterlambatan_otomatis }}"
                                            class="form-control denda-input"
                                            data-modal="{{ $item->id }}"
                                            min="0">
                                        </div>
                                        <small class="text-muted">
                                          Otomatis: {{ $item->keterlambatan_hari }} hari × Rp {{ number_format($denda->denda_per_hari ?? 5000, 0, ',', '.') }}
                                        </small>
                                      </div>

                                      <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Denda Kerusakan</label>
                                        <div class="input-group">
                                          <span class="input-group-text">Rp</span>
                                          <input type="number"
                                            name="denda_kerusakan"
                                            id="denda_kerusakan_{{ $item->id }}"
                                            value="{{ $item->denda_kerusakan_otomatis }}"
                                            class="form-control denda-input"
                                            data-modal="{{ $item->id }}"
                                            min="0">
                                        </div>
                                        <small class="text-muted">
                                          Terisi otomatis dari kondisi kembali alat. Dapat disesuaikan.
                                        </small>
                                      </div>
                                    </div>

                                    {{-- Preview Total Denda --}}
                                    <div class="p-3 bg-light rounded border">
                                      <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-semibold">Total Denda:</span>
                                        <span class="fw-bold text-danger fs-5" id="total_preview_{{ $item->id }}">
                                          Rp {{ number_format($item->denda_keterlambatan_otomatis + $item->denda_kerusakan_otomatis, 0, ',', '.') }}
                                        </span>
                                      </div>
                                    </div>

                                  </div>{{-- /modal-body --}}

                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-success">
                                      <i class="ti ti-check"></i> Konfirmasi Pengembalian
                                    </button>
                                  </div>
                                </div>
                              </form>
                            </div>
                          </div>
                          {{-- End Modal --}}

                        @elseif($item->status_pengembalian == 'dikonfirmasi' && $item->status_pembayaran == 'belum_lunas')

                          <form action="{{ route('petugas.pengembalian.lunasi', $item->id) }}" method="POST"
                            class="d-inline form-lunasi">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm btn-lunasi">
                              <i class="ti ti-cash"></i> Tandai Lunas
                            </button>
                          </form>

                        @endif
                      </td>
                      {{-- ============ END KOLOM AKSI ============ --}}

                    </tr>
                  @endforeach
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
  <script src="{{ asset('template/libs/simplebar/dist/simplebar.js') }}"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    $(document).ready(function () {
      $('#pengembalianTable').DataTable({
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
          emptyTable: 'Tidak ada pengembalian yang perlu ditangani',
          zeroRecords: 'Tidak ada data yang cocok'
        },
        pageLength: 10,
        order: [[0, 'asc']],
        columnDefs: [{ orderable: false, targets: [7] }]
      });

      setTimeout(() => $('.alert').fadeOut(), 4000);

      // Live preview total denda di modal
      $(document).on('input', '.denda-input', function () {
        const modalId       = $(this).data('modal');
        const keterlambatan = parseFloat($('#denda_keterlambatan_' + modalId).val()) || 0;
        const kerusakan     = parseFloat($('#denda_kerusakan_'     + modalId).val()) || 0;
        const total         = keterlambatan + kerusakan;
        $('#total_preview_' + modalId).text('Rp ' + total.toLocaleString('id-ID'));
      });

      // SweetAlert konfirmasi sebelum tandai lunas
      $(document).on('click', '.btn-lunasi', function (e) {
        e.preventDefault();
        const form = $(this).closest('form');
        Swal.fire({
          title: 'Konfirmasi Pelunasan?',
          text: 'Pastikan denda telah dibayar oleh peminjam.',
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#13deb9',
          cancelButtonColor: '#fa896b',
          confirmButtonText: 'Ya, tandai lunas',
          cancelButtonText: 'Batal'
        }).then((result) => {
          if (result.isConfirmed) form.submit();
        });
      });
    });
  </script>

</body>
</html>