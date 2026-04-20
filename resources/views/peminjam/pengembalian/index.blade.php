<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pengembalian Saya</title>
  <link rel="stylesheet" href="{{ asset ('template/css/styles.min.css') }}" />
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
              <h5 class="card-title fw-semibold">Riwayat Pengembalian</h5>
              <a href="{{ route('pengembalian.create') }}" class="btn btn-primary">
                <iconify-icon icon="solar:add-circle-outline" width="18" class="me-1"></iconify-icon>
                Ajukan Pengembalian
              </a>
            </div>

            @if (session('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            @endif

            @if (session('error'))
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
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Kembali Rencana</th>
                    <th>Tanggal Kembali Aktual</th>
                    <th>Keterlambatan</th>
                    <th>Total Denda</th>
                    <th>Status Pengembalian</th>
                    <th>Status Pembayaran</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($pengembalians as $pengembalian)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($pengembalian->peminjaman->tanggal_pinjam)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($pengembalian->peminjaman->tanggal_kembali_rencana)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($pengembalian->tanggal_kembali_aktual)->format('d/m/Y') }}</td>
                    <td class="text-center">
                      @if($pengembalian->keterlambatan_hari > 0)
                        <span class="badge bg-danger">{{ $pengembalian->keterlambatan_hari }} hari</span>
                      @else
                        <span class="badge bg-success">Tepat Waktu</span>
                      @endif
                    </td>
                    <td class="text-end">Rp {{ number_format($pengembalian->total_denda, 0, ',', '.') }}</td>
                    <td>
                      @if($pengembalian->status_pengembalian == 'diajukan')
                        <span class="badge bg-warning text-dark">Menunggu Validasi</span>
                      @else
                        <span class="badge bg-success">Dikonfirmasi</span>
                      @endif
                    </td>
                    <td>
                      @if($pengembalian->total_denda > 0)
                        @if($pengembalian->status_pembayaran == 'lunas')
                          <span class="badge bg-success">Lunas</span>
                        @else
                          <span class="badge bg-danger">Belum Lunas</span>
                        @endif
                      @else
                        <span class="badge bg-secondary">Tidak Ada Denda</span>
                      @endif
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center align-items-center gap-1">
                            <button type="button" class="btn btn-sm btn-outline-info showBtn" 
                                    data-id="{{ $pengembalian->id }}">
                                <iconify-icon icon="solar:eye-outline" width="18"></iconify-icon>
                            </button>

                            @if($pengembalian->status_pengembalian == 'diajukan')
                                <a href="{{ route('pengembalian.edit', $pengembalian->id) }}" 
                                class="btn btn-sm btn-outline-primary">
                                <iconify-icon icon="solar:pen-2-outline" width="18"></iconify-icon>
                                </a>

                                <form action="{{ route('pengembalian.destroy', $pengembalian->id) }}" 
                                    method="POST" class="d-inline deleteForm">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <iconify-icon icon="solar:trash-bin-trash-outline" width="18"></iconify-icon>
                                </button>
                                </form>
                            @endif
                        </div>
                    </td>
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

  <!-- Modal Detail -->
  <div class="modal fade" id="showPengembalianModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detail Pengembalian</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="modalContent">
          <!-- Content will be loaded via AJAX -->
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset ('template/libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset ('template/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset ('template/js/sidebarmenu.js') }}"></script>
  <script src="{{ asset ('template/js/app.min.js') }}"></script>
  <script src="{{ asset ('template/libs/simplebar/dist/simplebar.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    $(document).ready(function () {
      $('#pengembalianTable').DataTable({
        language: { 
          url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        pageLength: 10,
        order: [[3, 'desc']]
      });

      // Show Detail
      $(document).on('click', '.showBtn', function () {
        let id = $(this).data('id');
        $('#modalContent').html('<div class="text-center"><div class="spinner-border"></div></div>');
        $('#showPengembalianModal').modal('show');

        $.get('/pengembalian/' + id, function(data) {
          $('#modalContent').html(data);
        });
      });

      // Delete Confirm
      $(document).on('submit', '.deleteForm', function (e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
          title: 'Batalkan Pengajuan?',
          text: 'Pengajuan pengembalian akan dibatalkan.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#dc3545',
          cancelButtonText: 'Tidak',
          confirmButtonText: 'Ya, Batalkan'
        }).then((result) => {
          if (result.isConfirmed) form.submit();
        });
      });

      setTimeout(() => $('.alert').fadeOut(), 3000);
    });
  </script>

</body>
</html>