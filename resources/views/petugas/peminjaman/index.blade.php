<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Petugas - Peminjaman</title>
  <link rel="stylesheet" href="{{ asset ('template/css/styles.min.css') }}" />
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">

    <!--  App Topstrip -->
    <x-navbar></x-navbar>

    <!-- Sidebar Start -->
    <x-sidebar></x-sidebar>
    <!--  Sidebar End -->

    <!--  Main wrapper -->
      <div class="body-wrapper">
        <div class="container-fluid">

        <div class="card">
            <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title fw-semibold">Kelola Peminjaman</h5>
                
                <!-- Info Badge -->
                <div>
                    <span class="badge bg-info text-white me-2">
                        <iconify-icon icon="solar:clock-circle-outline" width="16" class="me-1"></iconify-icon>
                        Menunggu: {{ $peminjamen->where('status', 'menunggu_persetujuan')->count() }}
                    </span>
                    <span class="badge bg-warning text-dark">
                        <iconify-icon icon="solar:box-outline" width="16" class="me-1"></iconify-icon>
                        Dipinjam: {{ $peminjamen->where('status', 'dipinjam')->count() }}
                    </span>
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
                <!-- Filter Form -->
                <form method="GET" class="row g-2 mb-3">
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="menunggu_persetujuan" {{ request('status') == 'menunggu_persetujuan' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                            <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                            <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select name="user_id" class="form-select">
                            <option value="">Semua Peminjam</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">
                            Filter
                        </button>
                    </div>

                    <div class="col-md-2">
                        <a href="{{ route('petugas.peminjaman.index') }}" class="btn btn-danger w-100">
                            <iconify-icon icon="solar:restart-outline" width="18" class="me-1"></iconify-icon>
                            Reset
                        </a>
                    </div>
                </form>

                <!-- Table -->
                <table id="peminjamanTable" class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                    <th>No</th>
                    <th>Peminjam</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Kembali</th>
                    <th>Status</th>
                    <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($peminjamen as $peminjaman)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $peminjaman->user->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('d/m/Y') }}</td>
                        <td>
                            @if($peminjaman->status == 'menunggu_persetujuan')
                                <span class="badge bg-info text-white">Menunggu Persetujuan</span>
                            @elseif($peminjaman->status == 'disetujui')
                                <span class="badge bg-primary">Disetujui</span>
                            @elseif($peminjaman->status == 'dipinjam')
                                <span class="badge bg-warning text-dark">Dipinjam</span>
                            @elseif($peminjaman->status == 'dikembalikan')
                                <span class="badge bg-success">Dikembalikan</span>
                            @elseif($peminjaman->status == 'ditolak')
                                <span class="badge bg-danger">Ditolak</span>
                            @else
                                <span class="badge bg-dark">Terlambat</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <!-- Tombol Detail (selalu ada) -->
                            <button type="button" class="btn btn-sm btn-outline-info me-1 showBtn" 
                                    data-id="{{ $peminjaman->id }}" title="Detail">
                                <iconify-icon icon="solar:eye-outline" width="18"></iconify-icon>
                            </button>

                            <!-- JIKA STATUS MENUNGGU PERSETUJUAN -->
                            @if($peminjaman->status == 'menunggu_persetujuan')
                                <!-- Tombol Setujui -->
                                <form action="{{ route('petugas.peminjaman.approve', $peminjaman->id) }}" 
                                      method="POST" class="d-inline approveForm">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success me-1" title="Setujui">
                                        <iconify-icon icon="solar:check-circle-bold" width="18"></iconify-icon>
                                    </button>
                                </form>
                                
                                <!-- Tombol Tolak -->
                                <form action="{{ route('petugas.peminjaman.reject', $peminjaman->id) }}" 
                                      method="POST" class="d-inline rejectForm">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger" title="Tolak">
                                        <iconify-icon icon="solar:close-circle-bold" width="18"></iconify-icon>
                                    </button>
                                </form>
                            @endif

                            {{-- <!-- JIKA STATUS DIPINJAM ATAU TERLAMBAT -->
                            @if(in_array($peminjaman->status, ['dipinjam', 'terlambat']))
                                <!-- Tombol Kembalikan -->
                                <form action="{{ route('petugas.peminjaman.pengembalian', $peminjaman->id) }}" 
                                      method="POST" class="d-inline returnForm">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary" title="Kembalikan">
                                        <iconify-icon icon="solar:box-minimalistic-bold" width="18"></iconify-icon>
                                    </button>
                                </form>
                            @endif --}}

                            <!-- STATUS LAIN: HANYA DETAIL -->
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Tidak ada data peminjaman</td>
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

  <!-- Modal Show Peminjaman -->
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
                <label class="form-label fw-semibold">Peminjam</label>
                <input type="text" class="form-control" id="show_peminjam" readonly>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label fw-semibold">Petugas</label>
                <input type="text" class="form-control" id="show_petugas" readonly>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label fw-semibold">Tanggal Pinjam</label>
                <input type="text" class="form-control" id="show_tanggal_pinjam" readonly>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label fw-semibold">Tanggal Kembali</label>
                <input type="text" class="form-control" id="show_tanggal_kembali" readonly>
            </div>
            </div>

            <div class="row">
            <div class="col-md-9 mb-3">
                <label class="form-label fw-semibold">Keperluan</label>
                <textarea class="form-control" id="show_keperluan" rows="2" readonly></textarea>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label fw-semibold">Status</label>
                <div id="show_status_badge"></div>
            </div>
            </div>

            <hr>
            <h6 class="mb-3 fw-semibold">Detail Alat yang Dipinjam</h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Alat</th>
                            <th>Kategori</th>
                            <th>Jumlah</th>
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
    <script src="{{ asset ('template/libs/simplebar/dist/simplebar.js') }}"></script>
    
    <!-- solar icons -->
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Sweetalert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(document).ready(function () {

    /* ================= DATATABLE ================= */
    $('#peminjamanTable').DataTable({
        language: { 
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
            emptyTable: 'Data peminjaman belum ada',
            zeroRecords: 'Tidak ada data yang cocok'
        },
        pageLength: 10,
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: [5] }
        ]
    });

    /* ================= SHOW DETAIL ================= */
    $(document).on('click', '.showBtn', function () {
        let id = $(this).data('id');
        
        $.ajax({
            url: '/petugas/peminjaman/' + id,
            type: 'GET',
            success: function(response) {
                $('#show_peminjam').val(response.user.name);
                
                // Cek apakah ada petugas
                if (response.petugas) {
                    $('#show_petugas').val(response.petugas.name);
                } else {
                    $('#show_petugas').val('-');
                }
                
                $('#show_tanggal_pinjam').val(new Date(response.tanggal_pinjam).toLocaleDateString('id-ID'));
                $('#show_tanggal_kembali').val(new Date(response.tanggal_kembali_rencana).toLocaleDateString('id-ID'));
                $('#show_keperluan').val(response.keperluan);
                
                // Status Badge
                let statusBadge = '';
                if (response.status === 'menunggu_persetujuan') {
                    statusBadge = '<span class="badge bg-info text-white w-100 py-2">Menunggu Persetujuan</span>';
                } else if (response.status === 'disetujui') {
                    statusBadge = '<span class="badge bg-primary w-100 py-2">Disetujui</span>';
                } else if (response.status === 'dipinjam') {
                    statusBadge = '<span class="badge bg-warning text-dark w-100 py-2">Dipinjam</span>';
                } else if (response.status === 'dikembalikan') {
                    statusBadge = '<span class="badge bg-success w-100 py-2">Dikembalikan</span>';
                } else if (response.status === 'ditolak') {
                    statusBadge = '<span class="badge bg-danger w-100 py-2">Ditolak</span>';
                } else {
                    statusBadge = '<span class="badge bg-dark w-100 py-2">Terlambat</span>';
                }
                $('#show_status_badge').html(statusBadge);
                
                // Detail Alat
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
                    
                    detailHtml += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${detail.alat.nama_alat}</td>
                            <td>${detail.alat.kategori.nama_kategori}</td>
                            <td class="text-center">${detail.jumlah}</td>
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

    /* ================= APPROVE CONFIRM ================= */
    $(document).on('submit', '.approveForm', function (e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            title: 'Setujui Peminjaman?',
            text: 'Stok alat akan dikurangi setelah disetujui.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            cancelButtonText: 'Batal',
            confirmButtonText: '<i class="fas fa-check"></i> Ya, Setujui'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    /* ================= REJECT CONFIRM ================= */
    $(document).on('submit', '.rejectForm', function (e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            title: 'Tolak Peminjaman?',
            text: 'Peminjaman akan ditandai sebagai ditolak.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            cancelButtonText: 'Batal',
            confirmButtonText: '<i class="fas fa-times"></i> Ya, Tolak'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    /* ================= RETURN CONFIRM ================= */
    $(document).on('submit', '.returnForm', function (e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            title: 'Proses Pengembalian?',
            text: 'Alat akan dikembalikan dan stok akan bertambah.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            cancelButtonText: 'Batal',
            confirmButtonText: '<i class="fas fa-box"></i> Ya, Kembalikan'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    /* ================= AUTO HIDE ALERT ================= */
    setTimeout(() => $('.alert').fadeOut(), 4000);

    });
    </script>

</body>

</html>