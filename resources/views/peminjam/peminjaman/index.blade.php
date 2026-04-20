<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Riwayat Peminjaman - Peminjam</title>
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
              <h5 class="card-title fw-semibold">Riwayat Peminjaman Saya</h5>
              <div>
                <a href="{{ route('alat.index') }}" class="btn btn-outline-secondary me-2">
                  <iconify-icon icon="solar:box-outline" width="18" class="me-1"></iconify-icon>
                  Daftar Alat
                </a>
                <a href="{{ route('peminjaman.create') }}" class="btn btn-primary">
                  <iconify-icon icon="solar:add-circle-outline" width="18" class="me-1"></iconify-icon>
                  Ajukan Peminjaman
                </a>
              </div>
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
              <table id="peminjamanTable" class="table table-bordered table-striped">
                <thead class="table-light">
                  <tr>
                    <th>No</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Kembali</th>
                    {{-- <th>Keperluan</th> --}}
                    <th>Disetujui Oleh</th>
                    <th>Status</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($peminjaman as $item)
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}</td>
                      <td>{{ \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->format('d/m/Y') }}</td>
                      {{-- <td>{{ Str::limit($item->keperluan, 40) }}</td> --}}
                      <td>
                        @if($item->petugas)
                          {{ $item->petugas->name }}
                        @else
                          <span class="text-muted">-</span>
                        @endif
                      </td>
                      <td>
                        @if($item->status == 'menunggu_persetujuan')
                          <span class="badge bg-info text-white">Menunggu Persetujuan</span>
                        @elseif($item->status == 'disetujui')
                          <span class="badge bg-primary">Disetujui</span>
                        @elseif($item->status == 'dipinjam')
                          <span class="badge bg-warning text-dark">Dipinjam</span>
                        @elseif($item->status == 'dikembalikan')
                          <span class="badge bg-success">Dikembalikan</span>
                        @elseif($item->status == 'ditolak')
                          <span class="badge bg-danger">Ditolak</span>
                        @else
                          <span class="badge bg-dark">Terlambat</span>
                        @endif
                      </td>
                      <td class="text-center">
                        <!-- Tombol Detail (selalu ada) -->
                        <button type="button" class="btn btn-sm btn-outline-info me-1 showBtn" data-id="{{ $item->id }}">
                          <iconify-icon icon="solar:eye-outline" width="18"></iconify-icon>
                        </button>

                        <!-- Tombol Batalkan (hanya untuk status menunggu persetujuan) -->
                        @if($item->status == 'menunggu_persetujuan')
                          <form action="{{ route('peminjaman.destroy', $item->id) }}" method="POST" class="d-inline deleteForm">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Batalkan">
                              <iconify-icon icon="solar:close-circle-outline" width="18"></iconify-icon>
                            </button>
                          </form>
                        @endif
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="7" class="text-center">Belum ada riwayat peminjaman</td>
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

  <!-- Modal Detail Peminjaman -->
  <div class="modal fade" id="showPeminjamanModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detail Peminjaman</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-3 mb-3">
              <label class="form-label">Tanggal Pinjam</label>
              <input type="text" class="form-control" id="show_tanggal_pinjam" readonly>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Tanggal Kembali</label>
              <input type="text" class="form-control" id="show_tanggal_kembali" readonly>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Disetujui Oleh</label>
              <input type="text" class="form-control" id="show_petugas" readonly>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Status</label>
              <input type="text" class="form-control" id="show_status" readonly>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12 mb-3">
              <label class="form-label">Keperluan</label>
              <textarea class="form-control" id="show_keperluan" rows="2" readonly></textarea>
            </div>
          </div>

          <hr>
          <h6 class="mb-3">Detail Alat yang Dipinjam</h6>
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead class="table-light">
                <tr>
                  <th>No</th>
                  <th>Nama Alat</th>
                  <th>Kategori</th>
                  <th>Jumlah</th>
                  <th>Harga</th>
                  <th>Kondisi Pinjam</th>
                </tr>
              </thead>
              <tbody id="show_detail_alat">
              </tbody>
            </table>
          </div>
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
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    function formatRupiah(angka) {
      return 'Rp ' + Number(angka).toLocaleString('id-ID');
    }

  $(document).ready(function () {

    /* ================= DATATABLE ================= */
    $('#peminjamanTable').DataTable({
      language: { 
        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
        emptyTable: 'Belum ada riwayat peminjaman',
        zeroRecords: 'Tidak ada data yang cocok'
      },
      pageLength: 10,
      order: [],
      columnDefs: [
        { orderable: false, targets: [5] }
      ]
    });

    /* ================= SHOW DETAIL ================= */
    $(document).on('click', '.showBtn', function () {
      let id = $(this).data('id');
      
      $.ajax({
        url: '/peminjaman/' + id,
        type: 'GET',
        success: function(response) {
          $('#show_tanggal_pinjam').val(new Date(response.tanggal_pinjam).toLocaleDateString('id-ID'));
          $('#show_tanggal_kembali').val(new Date(response.tanggal_kembali_rencana).toLocaleDateString('id-ID'));
          
          if (response.petugas) {
            $('#show_petugas').val(response.petugas.name);
          } else {
            $('#show_petugas').val('-');
          }
          
          $('#show_keperluan').val(response.keperluan);
          
          let statusBadge = '';
          if (response.status === 'menunggu_persetujuan') {
            statusBadge = 'Menunggu Persetujuan';
          } else if (response.status === 'disetujui') {
            statusBadge = 'Disetujui';
          } else if (response.status === 'dipinjam') {
            statusBadge = 'Dipinjam';
          } else if (response.status === 'dikembalikan') {
            statusBadge = 'Dikembalikan';
          } else if (response.status === 'ditolak') {
            statusBadge = 'Ditolak';
          } else {
            statusBadge = 'Terlambat';
          }
          $('#show_status').val(statusBadge);
          
          let detailHtml = '';
            response.details.forEach(function(detail, index) {

            let kondisiBadge = '';
            if (detail.kondisi_pinjam === 'baik') {
              kondisiBadge = '<span class="badge bg-success">Baik</span>';
            } else if (detail.kondisi_pinjam === 'rusak_ringan') {
              kondisiBadge = '<span class="badge bg-warning">Rusak Ringan</span>';
            } else {
              kondisiBadge = '<span class="badge bg-danger">Rusak Berat</span>';
            }

            let harga = detail.alat.harga_beli
              ? formatRupiah(detail.alat.harga_beli)
              : '-';

            detailHtml += `
              <tr>
                <td>${index + 1}</td>
                <td>${detail.alat.nama_alat}</td>
                <td>${detail.alat.kategori.nama_kategori}</td>
                <td class="text-center">${detail.jumlah}</td>
                <td class="text-center fw-semibold">${harga}</td>
                <td>${kondisiBadge}</td>
              </tr>
            `;
          });

          $('#show_detail_alat').html(detailHtml);
          
          $('#showPeminjamanModal').modal('show');
        },
        error: function() {
          Swal.fire('Error!', 'Gagal mengambil data peminjaman', 'error');
        }
      });
    });

    /* ================= DELETE CONFIRM ================= */
    $(document).on('submit', '.deleteForm', function (e) {
      e.preventDefault();
      const form = this;

      Swal.fire({
        title: 'Batalkan Peminjaman?',
        text: 'Pengajuan peminjaman akan dibatalkan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'Tidak',
        confirmButtonText: 'Ya, Batalkan'
      }).then((result) => {
        if (result.isConfirmed) form.submit();
      });
    });

    /* ================= AUTO HIDE ALERT ================= */
    setTimeout(() => $('.alert').fadeOut(), 3000);

  });
  </script>

</body>

</html>