<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cari Buku - Perpustakaan Digital</title>
  <link rel="stylesheet" href="{{ asset ('template/css/styles.min.css') }}" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
  <style>
    .buku-card { transition: transform 0.2s; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .buku-card:hover { transform: translateY(-5px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); }
    .buku-cover { height: 280px; object-fit: cover; border-radius: 8px 8px 0 0; }
  </style>
</head>
<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <x-navbar></x-navbar>
    <x-sidebar></x-sidebar>
    <div class="body-wrapper">
      <div class="container-fluid">
        <div class="card bg-info-subtle shadow-none position-relative overflow-hidden mb-4">
          <div class="card-body px-4 py-3">
            <div class="row align-items-center">
              <div class="col-9">
                <h4 class="fw-semibold mb-8">Koleksi Buku</h4>
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a class="text-muted text-decoration-none" href="{{ route('peminjam.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item" aria-current="page">Cari Buku</li>
                  </ol>
                </nav>
              </div>
            </div>
          </div>
        </div>

        <form action="" method="GET" class="row mb-4">
            <div class="col-md-5 mb-2">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari judul, penulis, atau ISBN..." value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit">Cari</button>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <select name="kategori_buku_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $k)
                        <option value="{{ $k->id }}" {{ request('kategori_buku_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <a href="{{ route('buku.index') }}" class="btn btn-outline-danger w-100">Reset</a>
            </div>
        </form>

        <div class="row">
          @foreach($bukus as $buku)
          <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card buku-card h-100">
              @if($buku->gambar)
                <img src="{{ asset('storage/bukus/' . $buku->gambar) }}" class="card-img-top buku-cover" alt="{{ $buku->judul_buku }}">
              @else
                <div class="card-img-top buku-cover bg-light d-flex align-items-center justify-content-center">
                    <iconify-icon icon="solar:book-linear" width="64" class="text-muted"></iconify-icon>
                </div>
              @endif
              <div class="card-body p-3 d-flex flex-column">
                <span class="badge bg-primary-subtle text-primary mb-2 w-fit-content" style="width: fit-content;">{{ $buku->kategoriBuku->nama_kategori }}</span>
                <h6 class="fw-semibold mb-1 text-truncate">{{ $buku->judul_buku }}</h6>
                <p class="fs-2 text-muted mb-3">{{ $buku->penulis }}</p>
                
                <div class="">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge bg-{{ $buku->stok_tersedia > 0 ? ($buku->stok_tersedia > 5 ? 'success' : 'warning') : 'danger' }}-subtle text-{{ $buku->stok_tersedia > 0 ? ($buku->stok_tersedia > 5 ? 'success' : 'warning') : 'danger' }} fs-2">
                            {{ $buku->stok_tersedia > 0 ? 'Tersedia: ' . $buku->stok_tersedia : 'Stok Habis' }}
                        </span>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm flex-grow-1 btn-detail" 
                                data-json="{{ json_encode($buku->load('kategoriBuku')) }}">
                            Detail
                        </button>
                        @if($buku->stok_tersedia > 0 && $buku->kondisi !== 'rusak_berat')
                        <a href="{{ route('peminjam.peminjaman.create', ['buku_id' => $buku->id]) }}" class="btn btn-primary btn-sm flex-grow-1">Pinjam</a>
                        @endif
                    </div>
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
        
        @if($bukus->isEmpty())
            <div class="text-center py-5">
                <iconify-icon icon="solar:ghost-linear" width="64" class="text-muted mb-3"></iconify-icon>
                <p class="text-muted">Buku tidak ditemukan.</p>
            </div>
        @endif

      </div>
    </div>
  </div>

  <!-- Modal Detail Buku -->
  <div class="modal fade" id="modalDetailBuku" tabindex="-1" aria-labelledby="modalDetailBukuLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title text-white" id="modalDetailBukuLabel">Detail Informasi Buku</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="row">
            <div class="col-md-4 text-center mb-4 mb-md-0">
              <div id="modal-gambar-container" class="shadow-sm rounded overflow-hidden">
                  <!-- Image or Icon will be injected here -->
              </div>
            </div>
            <div class="col-md-8">
              <div class="d-flex justify-content-between align-items-start mb-2">
                  <div>
                      <h3 id="modal-judul" class="fw-bold mb-1 text-primary"></h3>
                      <p id="modal-penulis" class="text-muted mb-0 fs-4"></p>
                  </div>
                  <span id="modal-kategori" class="badge bg-primary-subtle text-primary py-2 px-3"></span>
              </div>
              
              <hr class="my-3 opacity-25">

<div class="row g-3 mb-4">
    <div class="col-sm-6">
        <div class="d-flex align-items-center">
            <iconify-icon icon="solar:bookmark-opened-linear" class="text-primary me-2" width="20"></iconify-icon>
            <div>
                <small class="text-muted d-block">Penerbit</small>
                <span id="modal-penerbit" class="fw-semibold"></span>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="d-flex align-items-center">
            <iconify-icon icon="solar:calendar-linear" class="text-primary me-2" width="20"></iconify-icon>
            <div>
                <small class="text-muted d-block">Tahun Terbit</small>
                <span id="modal-tahun" class="fw-semibold"></span>
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="d-flex align-items-center">
            <iconify-icon icon="solar:tag-price-linear" class="text-primary me-2" width="20"></iconify-icon>
            <div>
                <small class="text-muted d-block">Harga Buku</small>
                <span id="modal-harga" class="fw-semibold text-success"></span>
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="d-flex align-items-center">
            <iconify-icon icon="solar:qr-code-linear" class="text-primary me-2" width="20"></iconify-icon>
            <div>
                <small class="text-muted d-block">ISBN</small>
                <span id="modal-isbn" class="fw-semibold"></span>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="d-flex align-items-center">
            <iconify-icon icon="solar:medal-ribbon-linear" class="text-primary me-2" width="20"></iconify-icon>
            <div>
                <small class="text-muted d-block">Kondisi</small>
                <span id="modal-kondisi" class="badge"></span>
            </div>
        </div>
    </div>
</div>

              <div class="card bg-light border-0 mb-4">
                  <div class="card-body p-3">
                      <div class="row text-center">
                          <div class="col-6 border-end">
                              <small class="text-muted d-block mb-1">Stok Total</small>
                              <h5 id="modal-stok-total" class="fw-bold mb-0"></h5>
                          </div>
                          <div class="col-6">
                              <small class="text-muted d-block mb-1">Stok Tersedia</small>
                              <h5 id="modal-stok-tersedia-val" class="fw-bold mb-0"></h5>
                          </div>
                      </div>
                  </div>
              </div>

              <div>
                  <h6 class="fw-bold mb-2">Keterangan:</h6>
                  <p id="modal-keterangan" class="text-muted fs-3" style="text-align: justify;"></p>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 p-4">
          <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Tutup</button>
          <a href="" id="modal-btn-pinjam" class="btn btn-primary px-4">
              <iconify-icon icon="solar:hand-money-linear" class="me-1"></iconify-icon>
              Pinjam Sekarang
          </a>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset ('template/libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset ('template/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset ('template/js/app.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

  <script>
  function formatRupiah(angka) {
      if (!angka) return 'Rp 0';
      return 'Rp ' + parseInt(angka).toLocaleString('id-ID');
  }
  </script>

  <script>
    $(document).ready(function() {
        $('.btn-detail').on('click', function() {
            const data = $(this).data('json');
            
            // Image logic
            const gambarContainer = $('#modal-gambar-container');
            if (data.gambar) {
                gambarContainer.html(`<img src="{{ asset('storage/bukus') }}/${data.gambar}" class="img-fluid w-100" alt="${data.judul_buku}" style="max-height: 400px; object-fit: cover;">`);
            } else {
                gambarContainer.html(`
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 300px;">
                        <iconify-icon icon="solar:book-linear" width="100" class="text-muted"></iconify-icon>
                    </div>
                `);
            }

            // Basic Info
            $('#modal-judul').text(data.judul_buku);
            $('#modal-penulis').text('Karya: ' + data.penulis);
            $('#modal-kategori').text(data.kategori_buku ? data.kategori_buku.nama_kategori : '-');
            $('#modal-isbn').text(data.ISBN || '-');
            $('#modal-penerbit').text(data.penerbit || '-');
            $('#modal-tahun').text(data.tahun_terbit || '-');
            $('#modal-harga').text(formatRupiah(data.harga_buku));
            $('#modal-stok-total').text(data.stok + ' Buku');
            $('#modal-stok-tersedia-val').text(data.stok_tersedia + ' Buku');
            $('#modal-keterangan').text(data.keterangan || 'Tidak ada keterangan tambahan untuk buku ini.');

            // Condition Badge
            const kondisiBadge = $('#modal-kondisi');
            let kondisiText = '';
            let kondisiClass = '';
            switch(data.kondisi) {
                case 'baik': kondisiText = 'Baik'; kondisiClass = 'bg-success'; break;
                case 'rusak_ringan': kondisiText = 'Rusak Ringan'; kondisiClass = 'bg-warning text-dark'; break;
                case 'rusak_berat': kondisiText = 'Rusak Berat'; kondisiClass = 'bg-danger'; break;
                default: kondisiText = data.kondisi; kondisiClass = 'bg-secondary';
            }
            kondisiBadge.text(kondisiText).attr('class', 'badge ' + kondisiClass);

            // Stock Available Color
            const stokVal = $('#modal-stok-tersedia-val');
            if (data.stok_tersedia > 5) {
                stokVal.attr('class', 'fw-bold mb-0 text-success');
            } else if (data.stok_tersedia > 0) {
                stokVal.attr('class', 'fw-bold mb-0 text-warning');
            } else {
                stokVal.attr('class', 'fw-bold mb-0 text-danger');
            }

            // Pinjam Button
            const btnPinjam = $('#modal-btn-pinjam');
            if (data.stok_tersedia > 0 && data.kondisi !== 'rusak_berat') {
                btnPinjam.show();
                // Construct URL manually to avoid complex route generation in JS
                const baseUrl = "{{ route('peminjam.peminjaman.create') }}";
                btnPinjam.attr('href', `${baseUrl}?buku_id=${data.id}`);
            } else {
                btnPinjam.hide();
            }

            $('#modalDetailBuku').modal('show');
        });
    });
  </script>
</body>
</html>