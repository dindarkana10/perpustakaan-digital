<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Petugas - Riwayat Pengembalian</title>
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
              <h5 class="card-title fw-semibold">Riwayat Pengembalian</h5>
            </div>

            @if(session('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            @endif

            <div class="table-responsive">
              <table id="riwayatTable" class="table table-bordered table-striped">
                <thead class="table-light">
                  <tr>
                    <th>No</th>
                    <th>Peminjam</th>
                    <th>Tanggal Kembali</th>
                    <th>Total Denda</th>
                    <th>Status Pembayaran</th>
                    <th>Status Pengembalian</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($pengembalians as $item)
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>{{ $item->peminjaman->user->name }}</td>
                      <td>{{ $item->tanggal_kembali_aktual->format('d-m-Y') }}</td>
                      <td class="{{ $item->total_denda > 0 ? 'text-danger' : 'text-success' }}">
                        Rp {{ number_format($item->total_denda, 0, ',', '.') }}
                      </td>
                      <td class="text-center">
                        {{-- Di riwayat semua sudah lunas, tapi tetap tampilkan badge --}}
                        <span class="badge bg-success">Lunas</span>
                      </td>
                      <td>
                        <span class="badge bg-success text-white">Dikonfirmasi</span>
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

  <script src="{{ asset('template/libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('template/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('template/js/sidebarmenu.js') }}"></script>
  <script src="{{ asset('template/js/app.min.js') }}"></script>
  <script src="{{ asset('template/libs/simplebar/dist/simplebar.js') }}"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

  <script>
    $(document).ready(function () {
      $('#riwayatTable').DataTable({
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
          emptyTable: 'Data riwayat pengembalian belum ada',
          zeroRecords: 'Tidak ada data yang cocok'
        },
        pageLength: 10,
        order: [[2, 'desc']]
      });

      setTimeout(() => $('.alert').fadeOut(), 4000);
    });
  </script>

</body>
</html>