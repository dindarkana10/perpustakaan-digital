<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manajemen Peminjaman - Perpustakaan Digital</title>
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title fw-semibold mb-0">Daftar Peminjaman Buku</h5>
                <a href="{{ route('admin.peminjaman.create') }}" class="btn btn-primary">
                    <iconify-icon icon="solar:add-circle-outline" width="18" class="me-1"></iconify-icon> Tambah Peminjaman
                </a>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  {{ session('success') }}
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- 
              PENTING: Di controller PeminjamanController@index, tambahkan filter:
              $peminjamen = Peminjaman::with(['user','details.buku'])
                  ->whereIn('status', ['menunggu_persetujuan', 'dipinjam', 'ditolak'])
                  // status 'dikembalikan' TIDAK ditampilkan di sini, hanya di riwayat pengembalian
                  ->latest()->get();
            --}}

            <div class="table-responsive">
              <table id="peminjamanTable" class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                  <tr>
                    <th>No</th>
                    <th>Peminjam</th>
                    <th>Buku</th>
                    <th>Tgl Pinjam</th>
                    <th>Tgl Kembali</th>
                    <th>Status</th>
                    <th width="200">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($peminjamen as $peminjaman)
                  {{-- Hanya tampilkan yang BELUM dikembalikan --}}
                  @if($peminjaman->status !== 'dikembalikan')
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <strong>{{ $peminjaman->user->name }}</strong><br>
                        <small class="text-muted">{{ $peminjaman->user->NISN }} | {{ $peminjaman->user->kelas_jurusan }}</small>
                    </td>
                    <td>
                        @foreach($peminjaman->details as $detail)
                            <span class="badge bg-light text-dark mb-1 border">{{ $detail->buku->judul_buku }} ({{ $detail->jumlah }})</span><br>
                        @endforeach
                    </td>
                    <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('d/m/Y') }}</td>
                    <td>
                        @if($peminjaman->status == 'menunggu_persetujuan')
                            <span class="badge bg-info text-white">Menunggu</span>
                        @elseif($peminjaman->status == 'dipinjam')
                            @if(\Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->isPast())
                                <span class="badge bg-danger">Terlambat</span>
                            @else
                                <span class="badge bg-warning text-dark">Dipinjam</span>
                            @endif
                        @elseif($peminjaman->status == 'ditolak')
                            <span class="badge bg-danger">Ditolak</span>
                        @else
                            <span class="badge bg-dark">{{ $peminjaman->status }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-flex flex-wrap gap-1 justify-content-center">
                            @if($peminjaman->status == 'menunggu_persetujuan')
                                <form action="{{ route('admin.peminjaman.approve', $peminjaman->id) }}" method="POST" class="approveForm">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="Setujui">
                                        <iconify-icon icon="solar:check-circle-outline" width="18"></iconify-icon>
                                    </button>
                                </form>
                                <form action="{{ route('admin.peminjaman.reject', $peminjaman->id) }}" method="POST" class="rejectForm">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger" title="Tolak">
                                        <iconify-icon icon="solar:close-circle-outline" width="18"></iconify-icon>
                                    </button>
                                </form>
                                <a href="{{ route('admin.peminjaman.edit', $peminjaman->id) }}" class="btn btn-sm btn-warning text-white" title="Edit">
                                    <iconify-icon icon="solar:pen-new-square-outline" width="18"></iconify-icon>
                                </a>
                            @endif

                            @if($peminjaman->status == 'dipinjam')
                                {{-- Tombol input pengembalian langsung --}}
                                <a href="{{ route('admin.pengembalian.create', ['peminjaman_id' => $peminjaman->id]) }}"
                                   class="btn btn-sm btn-success" title="Input Pengembalian">
                                    <iconify-icon icon="solar:arrow-left-down-outline" width="18"></iconify-icon>
                                </a>
                            @endif

                            <button type="button" class="btn btn-sm btn-info showBtn" data-id="{{ $peminjaman->id }}" title="Detail">
                                <iconify-icon icon="solar:eye-outline" width="18"></iconify-icon>
                            </button>

                            <form action="{{ route('admin.peminjaman.destroy', $peminjaman->id) }}" method="POST" class="deleteForm">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                    <iconify-icon icon="solar:trash-bin-trash-outline" width="18"></iconify-icon>
                                </button>
                            </form>
                        </div>
                    </td>
                  </tr>
                  @endif
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset ('template/libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset ('template/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset ('template/js/app.min.js') }}"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    $(document).ready(function() {
      $('#peminjamanTable').DataTable();

      $('.approveForm').submit(function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
          title: 'Setujui Peminjaman?',
          text: "Peminjaman akan disetujui dan stok buku akan dikurangi.",
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Ya, Setujui',
          cancelButtonText: 'Batal'
        }).then((r) => { if(r.isConfirmed) form.submit(); });
      });

      $('.rejectForm').submit(function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
          title: 'Tolak Peminjaman?',
          text: "Status akan diubah menjadi Ditolak.",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Ya, Tolak',
          cancelButtonText: 'Batal'
        }).then((r) => { if(r.isConfirmed) form.submit(); });
      });

      $('.deleteForm').submit(function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
          title: 'Hapus Data?',
          text: "Data peminjaman ini akan dihapus permanen!",
          icon: 'error',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          confirmButtonText: 'Ya, Hapus!',
          cancelButtonText: 'Batal'
        }).then((r) => { if(r.isConfirmed) form.submit(); });
      });

      setTimeout(() => $('.alert').fadeOut(), 3000);
    });
  </script>

  <!-- Modal Detail Peminjaman -->
  <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header border-bottom">
          <h5 class="modal-title fw-bold" id="detailModalLabel">Detail Peminjaman Buku</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row mb-4">
            <div class="col-md-6 border-end">
              <h6 class="fw-bold text-primary mb-3">Informasi Peminjam</h6>
              <table class="table table-sm table-borderless fs-3">
                <tr><td width="100">Nama</td><td width="10">:</td><td id="det-nama" class="fw-semibold"></td></tr>
                <tr><td>NISN</td><td>:</td><td id="det-nisn"></td></tr>
                <tr><td>Kelas</td><td>:</td><td id="det-kelas"></td></tr>
              </table>
            </div>
            <div class="col-md-6">
              <h6 class="fw-bold text-primary mb-3">Informasi Waktu & Status</h6>
              <table class="table table-sm table-borderless fs-3">
                <tr><td width="120">Tgl Pinjam</td><td width="10">:</td><td id="det-tgl-pinjam"></td></tr>
                <tr><td>Tgl Kembali</td><td>:</td><td id="det-tgl-kembali"></td></tr>
                <tr><td>Status</td><td>:</td><td id="det-status"></td></tr>
              </table>
            </div>
          </div>
          <div class="mb-4">
            <h6 class="fw-bold text-primary mb-2">Keperluan</h6>
            <div class="bg-light p-3 rounded fs-3" id="det-keperluan"></div>
          </div>
          <h6 class="fw-bold text-primary mb-3">Daftar Buku Dipinjam</h6>
          <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
              <thead class="table-light">
                <tr>
                  <th>Judul Buku</th>
                  <th width="100" class="text-center">Jumlah</th>
                  <th width="150" class="text-center">Kondisi Pinjam</th>
                </tr>
              </thead>
              <tbody id="det-buku-list" class="fs-3"></tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer border-top">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    $(document).ready(function() {
      $('.showBtn').click(function() {
        const id  = $(this).data('id');
        const url = "{{ route('admin.peminjaman.show', ':id') }}".replace(':id', id);

        $('#det-buku-list').html('<tr><td colspan="3" class="text-center">Memuat...</td></tr>');

        $.ajax({
          url: url,
          type: 'GET',
          dataType: 'json',
          success: function(data) {
            $('#det-nama').text(data.user.name);
            $('#det-nisn').text(data.user.NISN);
            $('#det-kelas').text(data.user.kelas_jurusan);
            $('#det-tgl-pinjam').text(data.tanggal_pinjam);
            $('#det-tgl-kembali').text(data.tanggal_kembali_rencana);
            $('#det-keperluan').text(data.keperluan || 'Tidak ada keterangan.');

            let statusBadge = '';
            if(data.status == 'menunggu_persetujuan')      statusBadge = '<span class="badge bg-info">Menunggu Persetujuan</span>';
            else if(data.status == 'dipinjam')             statusBadge = '<span class="badge bg-warning text-dark">Sedang Dipinjam</span>';
            else if(data.status == 'dikembalikan')         statusBadge = '<span class="badge bg-success">Sudah Kembali</span>';
            else if(data.status == 'ditolak')              statusBadge = '<span class="badge bg-danger">Ditolak</span>';
            else                                           statusBadge = '<span class="badge bg-dark">Terlambat</span>';
            $('#det-status').html(statusBadge);

            let bukuHtml = '';
            data.details.forEach(detail => {
              let kondisiBadge = detail.kondisi_pinjam == 'baik' ? 'bg-success'
                               : detail.kondisi_pinjam == 'rusak_ringan' ? 'bg-warning text-dark'
                               : 'bg-danger';
              bukuHtml += `
                <tr>
                  <td>${detail.buku.judul_buku}</td>
                  <td class="text-center">${detail.jumlah}</td>
                  <td class="text-center"><span class="badge ${kondisiBadge}">${detail.kondisi_pinjam.replace('_', ' ')}</span></td>
                </tr>`;
            });
            $('#det-buku-list').html(bukuHtml);

            $('#detailModal').modal('show');
          },
          error: function() {
            Swal.fire('Gagal!', 'Tidak dapat mengambil data peminjaman.', 'error');
          }
        });
      });
    });
  </script>
</body>
</html>