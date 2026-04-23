<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Riwayat Pinjam - Perpustakaan Digital</title>
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
              <h5 class="card-title fw-semibold">Riwayat Peminjaman Buku Saya</h5>
              <a href="{{ route('peminjam.peminjaman.create') }}" class="btn btn-primary">
                <iconify-icon icon="solar:add-circle-outline" width="18" class="me-1"></iconify-icon> Pinjam Buku Baru
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
              <table id="peminjamanTable" class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                  <tr>
                    <th>No</th>
                    <th>Buku</th>
                    <th>Tgl Pinjam</th>
                    <th>Tgl Kembali</th>
                    <th>Status</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($peminjaman as $item)
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>
                        @foreach($item->details as $detail)
                          <div class="d-flex align-items-center gap-2 mb-1">
                            {{-- Thumbnail gambar buku --}}
                            @if($detail->buku->gambar)
                              <img src="{{ asset('storage/bukus/' . $detail->buku->gambar) }}"
                                   alt="{{ $detail->buku->judul_buku }}"
                                   class="rounded"
                                   style="width:36px; height:48px; object-fit:cover; cursor:pointer; border:1px solid #dee2e6;"
                                   data-bs-toggle="tooltip"
                                   title="{{ $detail->buku->judul_buku }}"
                                   onclick="previewGambar('{{ asset('storage/' . $detail->buku->gambar) }}', '{{ $detail->buku->judul_buku }}')">
                            @else
                              <div class="rounded d-flex align-items-center justify-content-center bg-light border"
                                   style="width:36px; height:48px; min-width:36px;">
                                <iconify-icon icon="solar:book-outline" width="18" class="text-muted"></iconify-icon>
                              </div>
                            @endif
                            <span class="badge bg-light text-dark">{{ $detail->buku->judul_buku }} ({{ $detail->jumlah }})</span>
                          </div>
                        @endforeach
                      </td>
                      <td>{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}</td>
                      <td>{{ \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->format('d/m/Y') }}</td>
                      <td>
                        @if($item->status == 'menunggu_persetujuan')
                          <span class="badge bg-info text-white">Menunggu Persetujuan</span>
                        @elseif($item->status == 'dipinjam')
                          <span class="badge bg-warning text-dark">Sedang Dipinjam</span>
                        @elseif($item->status == 'dikembalikan')
                          <span class="badge bg-success">Sudah Kembali</span>
                        @elseif($item->status == 'ditolak')
                          <span class="badge bg-danger">Ditolak</span>
                        @else
                          <span class="badge bg-dark">Terlambat</span>
                        @endif
                      </td>
                      <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-info showBtn" data-id="{{ $item->id }}" title="Detail">
                          <iconify-icon icon="solar:eye-outline" width="18"></iconify-icon>
                        </button>
                        @if($item->status == 'menunggu_persetujuan')
                          <a href="{{ route('peminjam.peminjaman.edit', $item->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                            <iconify-icon icon="solar:pen-new-square-outline" width="18"></iconify-icon>
                          </a>
                          <form action="{{ route('peminjam.peminjaman.destroy', $item->id) }}" method="POST" class="d-inline deleteForm">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Batalkan">
                              <iconify-icon icon="solar:close-circle-outline" width="18"></iconify-icon>
                            </button>
                          </form>
                        @endif
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

  {{-- ============ MODAL DETAIL (SHOW) ============ --}}
  <div class="modal fade" id="showPeminjamanModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header border-bottom">
          <h5 class="modal-title fw-bold">Detail Peminjaman Saya</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <div id="detailContent">Memuat...</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  {{-- ============ MODAL PREVIEW GAMBAR ============ --}}
  <div class="modal fade" id="previewGambarModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
      <div class="modal-content">
        <div class="modal-header py-2">
          <h6 class="modal-title fw-semibold" id="previewGambarTitle"></h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center p-2">
          <img id="previewGambarImg" src="" alt="" class="img-fluid rounded" style="max-height:400px;">
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
    /* ---- Helper: buka modal preview gambar ---- */
    function previewGambar(src, judul) {
      document.getElementById('previewGambarImg').src = src;
      document.getElementById('previewGambarTitle').textContent = judul;
      new bootstrap.Modal(document.getElementById('previewGambarModal')).show();
    }

    /* ---- Helper: badge status ---- */
    function badgeStatus(status) {
      const map = {
        menunggu_persetujuan : '<span class="badge bg-info text-white">Menunggu Persetujuan</span>',
        dipinjam             : '<span class="badge bg-warning text-dark">Sedang Dipinjam</span>',
        dikembalikan         : '<span class="badge bg-success">Sudah Kembali</span>',
        ditolak              : '<span class="badge bg-danger">Ditolak</span>',
      };
      return map[status] ?? '<span class="badge bg-dark">Terlambat</span>';
    }

    $(document).ready(function () {
      /* Init DataTable */
      $('#peminjamanTable').DataTable();

      /* Init tooltips */
      $('[data-bs-toggle="tooltip"]').tooltip();

      /* ---- SHOW DETAIL ---- */
      $('.showBtn').click(function () {
        const id  = $(this).data('id');
        const url = "{{ route('peminjam.peminjaman.show', ':id') }}".replace(':id', id);

        $('#showPeminjamanModal').modal('show');
        $('#detailContent').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Memuat data...</p></div>');

        $.get(url, function (data) {
          /* Baris buku dengan gambar */
          let bukuRows = '';
          data.details.forEach(d => {
            const gambarHtml = d.buku.gambar
              ? `<img src="/storage/bukus/${d.buku.gambar}"
                      alt="${d.buku.judul_buku}"
                      class="rounded me-2"
                      style="width:48px;height:64px;object-fit:cover;cursor:pointer;border:1px solid #dee2e6;"
                      onclick="previewGambar('/storage/${d.buku.gambar}','${d.buku.judul_buku}')">`
              : `<div class="rounded me-2 d-inline-flex align-items-center justify-content-center bg-light border"
                      style="width:48px;height:64px;min-width:48px;">
                   <span style="font-size:10px;color:#aaa;">No Img</span>
                 </div>`;

            bukuRows += `
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    ${gambarHtml}
                    <span>${d.buku.judul_buku}</span>
                  </div>
                </td>
                <td class="text-center">${d.jumlah}</td>
                <td class="text-center"><span class="badge bg-secondary">${d.kondisi_pinjam}</span></td>
              </tr>`;
          });

          const html = `
            <div class="row mb-3">
              <div class="col-md-6 border-end">
                <h6 class="fw-bold text-primary mb-3">Informasi Waktu</h6>
                <p class="mb-1 text-muted fs-2">Tanggal Pinjam:</p>
                <p class="fw-semibold mb-3">${data.tanggal_pinjam}</p>
                <p class="mb-1 text-muted fs-2">Rencana Kembali:</p>
                <p class="fw-semibold">${data.tanggal_kembali_rencana}</p>
              </div>
              <div class="col-md-6 ps-md-4">
                <h6 class="fw-bold text-primary mb-3">Status</h6>
                <p class="mb-1 text-muted fs-2">Status:</p>
                <p class="fw-semibold mb-3">${badgeStatus(data.status)}</p>
              </div>
            </div>
            <div class="mb-4 bg-light p-3 rounded">
              <h6 class="fw-bold text-primary mb-2">Keperluan:</h6>
              <p class="mb-0 text-muted fs-3" style="text-align:justify;">${data.keperluan || 'Tidak ada keterangan.'}</p>
            </div>
            <h6 class="fw-bold text-primary mb-3">Buku yang Dipinjam:</h6>
            <div class="table-responsive">
              <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Judul Buku</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-center">Kondisi Saat Pinjam</th>
                  </tr>
                </thead>
                <tbody>${bukuRows}</tbody>
              </table>
            </div>`;

          $('#detailContent').html(html);
        });
      });

      /* ---- KONFIRMASI DELETE ---- */
      $('.deleteForm').submit(function (e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
          title: 'Batalkan Peminjaman?',
          text: 'Anda tidak dapat mengembalikan ini!',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Ya, Batalkan!',
          cancelButtonText: 'Batal'
        }).then(result => { if (result.isConfirmed) form.submit(); });
      });

      /* ---- AUTO HIDE ALERT ---- */
      setTimeout(() => $('.alert').fadeOut(), 3000);
    });
  </script>
</body>
</html>