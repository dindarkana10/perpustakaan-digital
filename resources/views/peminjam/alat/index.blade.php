<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Daftar Alat - Peminjam</title>
  <link rel="stylesheet" href="{{ asset ('template/css/styles.min.css') }}" />
  <!-- DataTables CSS -->
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
              <h5 class="card-title fw-semibold">Daftar Alat Tersedia</h5>
              <a href="{{ route('peminjaman.index') }}" class="btn btn-outline-primary">
                <iconify-icon icon="solar:clipboard-list-outline" width="18" class="me-1"></iconify-icon>
                Riwayat Peminjaman
              </a>
            </div>

            <!-- Filter Section -->
            <form method="GET" class="row g-3 mb-4">
              <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari nama alat..." value="{{ request('search') }}">
              </div>
              <div class="col-md-3">
                <select name="kategori_id" class="form-select">
                  <option value="">Semua Kategori</option>
                  @foreach($kategoris as $kategori)
                    <option value="{{ $kategori->id }}" {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>
                      {{ $kategori->nama_kategori }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-3">
                <select name="kondisi" class="form-select">
                  <option value="">Semua Kondisi</option>
                  <option value="baik" {{ request('kondisi') == 'baik' ? 'selected' : '' }}>Baik</option>
                  <option value="rusak_ringan" {{ request('kondisi') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                </select>
              </div>
              <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                  <iconify-icon icon="solar:magnifer-outline" width="18" class="me-1"></iconify-icon>
                  Filter
                </button>
              </div>
              <div class="col-md-2">
                  <a href="{{ route('alat.index') }}" class="btn btn-danger w-100">
                    <iconify-icon icon="solar:restart-outline" width="18" class="me-1"></iconify-icon>
                    Reset
                </a>
              </div>
            </form>

            @if($alats->isEmpty())
              <div class="alert alert-info text-center">
                <iconify-icon icon="solar:box-outline" width="48" class="mb-2"></iconify-icon>
                <p class="mb-0">Tidak ada alat yang tersedia saat ini.</p>
              </div>
            @else
              <div class="table-responsive">
                <table id="alatTable" class="table table-bordered table-striped">
                  <thead class="table-light">
                    <tr>
                      <th width="5%">No</th>
                      <th width="10%">Gambar</th>
                      <th width="20%">Nama Alat</th>
                      <th width="15%">Kategori</th>
                      <th width="10%">Kondisi</th>
                      <th width="10%">Stok Tersedia</th>
                      {{-- <th width="20%">Keperluan</th> --}}
                      <th width="10%">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($alats as $alat)
                      <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">
                            @if($alat->gambar)
                                <img src="{{ asset('storage/alats/' . $alat->gambar) }}" 
                                     alt="{{ $alat->nama_alat }}" 
                                     class="alat-image" style="max-width: 80px; max-height: 80px; object-fit: cover;">
                            @else
                                <span class="badge bg-secondary">No Image</span>
                            @endif
                        </td>
                        <td>{{ $alat->nama_alat }}</td>
                        <td>{{ $alat->kategori->nama_kategori }}</td>
                        <td>
                          <span class="badge {{ $alat->kondisi_badge }}">
                            {{ $alat->kondisi_label }}
                          </span>
                        </td>
                        <td class="text-center">
                          <span class="badge {{ $alat->stok_tersedia > 10 ? 'bg-success' : 'bg-warning' }}">
                            {{ $alat->stok_tersedia }}
                          </span>
                        </td>
                        {{-- <td>
                          @if($alat->keterangan)
                            {{ Str::limit($alat->keterangan, 60) }}
                          @else
                            <span class="text-muted">-</span>
                          @endif
                        </td> --}}
                        <td class="text-center">
                          <div class="d-flex justify-content-center gap-2">

                            <button type="button"
                              class="btn btn-sm btn-outline-info showAlatBtn"
                              data-nama="{{ $alat->nama_alat }}"
                              data-kategori="{{ $alat->kategori->nama_kategori }}"
                              data-kondisi="{{ $alat->kondisi_label }}"
                              data-stok="{{ $alat->stok_tersedia }}"
                              data-harga="{{ $alat->formatted_harga_beli }}"
                              data-keterangan="{{ $alat->keterangan }}"
                              data-gambar="{{ $alat->gambar ? asset('storage/alats/'.$alat->gambar) : '' }}">
                              <iconify-icon icon="solar:eye-outline" width="18"></iconify-icon>
                            </button>

                            <a href="{{ route('peminjaman.create', ['alat_id' => $alat->id]) }}"
                              class="btn btn-sm btn-primary d-flex align-items-center gap-1">
                              <iconify-icon icon="solar:hand-money-outline" width="18"></iconify-icon>
                              Pinjam
                            </a>

                          </div>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @endif

          </div>
        </div>

      </div>
    </div>
  </div>

  {{-- Modal Show Alat --}}
  <div class="modal fade" id="showAlatModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">Detail Alat</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Nama Alat</label>
              <input type="text" id="show_nama" class="form-control" readonly>
            </div>
            <div class="col-md-6">
              <label class="form-label">Kategori</label>
              <input type="text" id="show_kategori" class="form-control" readonly>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-4">
              <label class="form-label">Kondisi</label>
              <input type="text" id="show_kondisi" class="form-control" readonly>
            </div>
            <div class="col-md-4">
              <label class="form-label">Stok Tersedia</label>
              <input type="text" id="show_stok" class="form-control" readonly>
            </div>
            <div class="col-md-4">
              <label class="form-label">Harga Beli</label>
              <input type="text" id="show_harga" class="form-control" readonly>
            </div>
          </div>

          <div class="row mb-3">
            <!-- GAMBAR -->
            <div class="col-md-5">
              <label class="form-label">Gambar</label>
              <div class="border rounded p-2 text-center">
                <img id="show_gambar"
                    class="img-fluid rounded"
                    style="max-height: 220px; display: none;">
                <p id="no_image_text" class="text-muted mb-0" style="display:none;">
                  Tidak ada gambar
                </p>
              </div>
            </div>

            <!-- KETERANGAN -->
            <div class="col-md-7">
              <label class="form-label">Keterangan</label>
              <textarea id="show_keterangan"
                class="form-control"
                rows="8"
                readonly
                placeholder="Tidak ada keterangan">
              </textarea>
            </div>
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
  
  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

  <script>
  $(document).ready(function () {
    
    /* ================= DATATABLE ================= */
    $('#alatTable').DataTable({
      language: { 
        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
        emptyTable: 'Tidak ada alat yang tersedia',
        zeroRecords: 'Tidak ada data yang cocok'
      },
      pageLength: 10,
      order: [], // Sort by nama alat
      columnDefs: [
        { orderable: false, targets: [1, 6] }, // Gambar & Aksi tidak bisa di-sort
        { className: 'text-center', targets: [0, 1, 5, 6] } // Center align
      ]
    });

  /* ================= SHOW DETAIL  ================= */
      $(document).on('click', '.showAlatBtn', function () {
      $('#show_nama').val($(this).data('nama'));
      $('#show_kategori').val($(this).data('kategori'));
      $('#show_kondisi').val($(this).data('kondisi'));
      $('#show_stok').val($(this).data('stok'));
      $('#show_harga').val($(this).data('harga'));
      $('#show_keterangan').val($(this).data('keterangan') ?? '-');

      if ($(this).data('gambar')) {
        $('#show_gambar').attr('src', $(this).data('gambar')).show();
      } else {
        $('#show_gambar').hide();
      }

      $('#showAlatModal').modal('show');
    });
  });
  </script>


</body>

</html>