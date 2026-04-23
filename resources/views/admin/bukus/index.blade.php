<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Data Buku - Perpustakaan Digital</title>
  <link rel="stylesheet" href="{{ asset('template/css/styles.min.css') }}" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
  <style>
    /* ═══════════════════════════════════════
       FIX UTAMA: Modal scrollable dengan max-height
       Supaya semua field terlihat tanpa zoom out
    ═══════════════════════════════════════ */
    .modal-dialog-scrollable .modal-content {
      max-height: 90vh;
      overflow: hidden;
    }
    .modal-dialog-scrollable .modal-body {
      overflow-y: auto;
      max-height: calc(90vh - 130px); /* kurangi header + footer */
    }
    /* Pastikan modal tidak melampaui layar */
    .modal-dialog.modal-lg {
      max-height: 95vh;
    }

    /* ── Tabel cover ── */
    .buku-image {
      width: 50px;
      height: 68px;
      object-fit: cover;
      border-radius: 6px;
      box-shadow: 0 2px 6px rgba(0,0,0,.18);
      display: block;
      margin: 0 auto;
    }

    /* ── Preview gambar di modal ── */
    .img-preview-wrap {
      margin-top: 10px;
      display: none;
      text-align: center;
    }
    .img-preview-wrap img {
      max-width: 140px;
      max-height: 180px;
      border-radius: 8px;
      border: 2px dashed #cdd5e0;
      object-fit: cover;
      padding: 4px;
    }
    .img-preview-wrap .img-label {
      font-size: 0.75rem;
      color: #6c757d;
      margin-top: 4px;
    }

    /* ── Section title di modal ── */
    .form-section-title {
      font-size: 0.68rem;
      font-weight: 700;
      letter-spacing: .08em;
      text-transform: uppercase;
      color: #6c757d;
      border-bottom: 1px solid #e9ecef;
      padding-bottom: 4px;
      margin-bottom: 12px;
      margin-top: 4px;
    }

    /* ── Stok tersedia: tampilan readonly saat auto-sync ── */
    input.auto-synced {
      background-color: #f8f9fa;
    }

    /* ── Kurangi padding modal body supaya lebih compact ── */
    .modal-body {
      padding: 16px 20px;
    }
    .modal-body .row.g-3 {
      --bs-gutter-y: 0.6rem;
    }
    .modal-body .mb-2 {
      margin-bottom: 0.4rem !important;
    }
    .modal-body .mb-3 {
      margin-bottom: 0.6rem !important;
    }
  </style>
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

            {{-- ── Header ── --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="card-title fw-semibold mb-0">Data Buku</h5>
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBukuModal">
                <iconify-icon icon="solar:add-circle-outline" width="18" class="me-1"></iconify-icon>
                Tambah Buku
              </button>
            </div>

            {{-- ── Flash messages ── --}}
            @if (session('success'))
              <div class="alert alert-success alert-dismissible fade show auto-dismiss" role="alert">
                <iconify-icon icon="solar:check-circle-outline" width="16" class="me-1"></iconify-icon>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            @endif

            @if (session('error'))
              <div class="alert alert-danger alert-dismissible fade show auto-dismiss" role="alert">
                <iconify-icon icon="solar:close-circle-outline" width="16" class="me-1"></iconify-icon>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            @endif

            {{-- ── Tabel ── --}}
            <div class="table-responsive">
              <table id="bukuTable" class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                  <tr>
                    <th class="text-center" style="width:50px">No</th>
                    <th class="text-center" style="width:70px">Cover</th>
                    <th>Judul Buku</th>
                    <th>Penulis</th>
                    <th>ISBN</th>
                    <th class="text-center" style="width:80px">Stok</th>
                    <th class="text-center" style="width:90px">Tersedia</th>
                    <th class="text-center" style="width:100px">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($bukus as $buku)
                  <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">
                      @if($buku->gambar)
                        <img src="{{ Storage::url('bukus/' . $buku->gambar) }}"
                             class="buku-image"
                             alt="{{ $buku->judul_buku }}"
                             onerror="handleImgError(this, '{{ $buku->gambar }}')">
                      @else
                        <span class="badge bg-secondary">No Cover</span>
                      @endif
                    </td>
                    <td>
                      <strong>{{ $buku->judul_buku }}</strong>
                      @if($buku->kategoriBuku)
                        <br><small class="text-muted">{{ $buku->kategoriBuku->nama_kategori }}</small>
                      @endif
                    </td>
                    <td>{{ $buku->penulis }}</td>
                    <td>{{ $buku->ISBN ?? '-' }}</td>
                    <td class="text-center">{{ $buku->stok }}</td>
                    <td class="text-center">
                      <span class="badge 
                        @if($buku->stok_tersedia <= 5)
                          bg-danger
                        @elseif($buku->stok_tersedia <= 10)
                          bg-warning
                        @else
                          bg-success
                        @endif
                      ">
                        {{ $buku->stok_tersedia }}
                      </span>
                    </td>
                    <td class="text-center">
                      <button type="button"
                        class="btn btn-sm btn-outline-info me-1 editBtn"
                        data-id="{{ $buku->id }}"
                        data-json="{{ json_encode($buku) }}"
                        title="Edit">
                        <iconify-icon icon="solar:pen-2-outline" width="16"></iconify-icon>
                      </button>
                      <form action="{{ route('bukus.destroy', $buku->id) }}" method="POST" class="d-inline deleteForm">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                          <iconify-icon icon="solar:trash-bin-trash-outline" width="16"></iconify-icon>
                        </button>
                      </form>
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

  {{-- ════════════════════════════════════════
       MODAL CREATE
       modal-dialog-scrollable = body bisa di-scroll
       modal-lg                 = lebar cukup untuk 2 kolom
  ════════════════════════════════════════ --}}
  <div class="modal fade" id="createBukuModal" tabindex="-1" aria-labelledby="createBukuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">

        <form action="{{ route('bukus.store') }}" method="POST" enctype="multipart/form-data" id="createBukuForm">
          @csrf

          {{-- Header — tidak ikut scroll --}}
          <div class="modal-header bg-primary text-white py-2">
            <h5 class="modal-title fs-6" id="createBukuModalLabel">
              <iconify-icon icon="solar:book-2-outline" width="18" class="me-1"></iconify-icon>
              Tambah Buku Baru
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>

          {{-- Body — bisa di-scroll --}}
          <div class="modal-body">

            {{-- ── 1. Identitas Buku ── --}}
            <p class="form-section-title">Identitas Buku</p>
            <div class="row g-3 mb-2">
              <div class="col-md-8">
                <label class="form-label fw-semibold mb-1">Judul Buku <span class="text-danger">*</span></label>
                <input type="text" name="judul_buku" class="form-control form-control-sm"
                  placeholder="Masukkan judul buku" required>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold mb-1">Kategori <span class="text-danger">*</span></label>
                <select name="kategori_buku_id" class="form-select form-select-sm" required>
                  <option value="">-- Pilih --</option>
                  @foreach($kategoris as $k)
                    <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold mb-1">Penulis <span class="text-danger">*</span></label>
                <input type="text" name="penulis" class="form-control form-control-sm"
                  placeholder="Nama penulis" required>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold mb-1">Penerbit <span class="text-danger">*</span></label>
                <input type="text" name="penerbit" class="form-control form-control-sm"
                  placeholder="Nama penerbit" required>
              </div>
            </div>

            {{-- ── 2. Detail Penerbitan ── --}}
            <p class="form-section-title">Detail Penerbitan</p>
            <div class="row g-3 mb-3">
              <div class="col-md-4">
                <label class="form-label fw-semibold mb-1">Tahun Terbit <span class="text-danger">*</span></label>
                <input type="number" name="tahun_terbit" class="form-control form-control-sm"
                   min="1900" max="{{ date('Y') }}" required>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold mb-1">ISBN</label>
                <input type="text" name="ISBN" class="form-control form-control-sm">
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold mb-1">Harga Buku</label>
                <div class="input-group input-group-sm">
                  <span class="input-group-text">Rp</span>
                  <input type="number" name="harga_buku" class="form-control" placeholder="0" min="0">
                </div>
              </div>
            </div>

            {{-- ── 3. Stok & Kondisi ── --}}
            <p class="form-section-title">Stok &amp; Kondisi</p>
            <div class="row g-3 mb-3">
              <div class="col-md-4">
                <label class="form-label fw-semibold mb-1">Kondisi <span class="text-danger">*</span></label>
                <select name="kondisi" class="form-select form-select-sm" required>
                  <option value="baik" selected>Baik</option>
                  <option value="rusak_ringan">Rusak Ringan</option>
                  <option value="rusak_berat">Rusak Berat</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold mb-1">Stok Total <span class="text-danger">*</span></label>
                <input type="number" name="stok" id="create_stok" class="form-control form-control-sm"
                  placeholder="0" min="0" required>
                <div class="form-text">Stok tersedia otomatis mengikuti</div>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold mb-1">Stok Tersedia <span class="text-danger">*</span></label>
                <input type="number" name="stok_tersedia" id="create_stok_tersedia"
                  class="form-control form-control-sm auto-synced" placeholder="0" min="0" required>
              </div>
            </div>

            {{-- ── 4. Sampul Buku ── --}}
            <p class="form-section-title">Sampul Buku</p>
            <div class="row g-3">
              <div class="col-md-7">
                <label class="form-label fw-semibold mb-1">Upload Gambar</label>
                <input type="file" name="gambar" id="create_gambar"
                  class="form-control form-control-sm" accept="image/jpeg,image/jpg,image/png,image/gif">
                <div class="form-text">Format: JPG, PNG, GIF. Maks 2MB.</div>
              </div>
              <div class="col-md-5 d-flex align-items-center justify-content-center">
                <div class="img-preview-wrap" id="create_preview_wrap">
                  <img id="create_preview_img" src="#" alt="Preview">
                  <div class="img-label">Preview Sampul</div>
                </div>
              </div>
            </div>

          </div>{{-- /.modal-body --}}

          {{-- Footer — tidak ikut scroll --}}
          <div class="modal-footer py-2">
            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
              <iconify-icon icon="solar:close-square-outline" width="15" class="me-1"></iconify-icon>Batal
            </button>
            <button type="submit" class="btn btn-sm btn-primary">
              <iconify-icon icon="solar:diskette-outline" width="15" class="me-1"></iconify-icon>Simpan
            </button>
          </div>

        </form>
      </div>
    </div>
  </div>

  {{-- ════════════════════════════════════════
       MODAL EDIT
  ════════════════════════════════════════ --}}
  <div class="modal fade" id="editBukuModal" tabindex="-1" aria-labelledby="editBukuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">

        <form id="editBukuForm" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')

          {{-- Header --}}
          <div class="modal-header bg-info text-white py-2">
            <h5 class="modal-title fs-6" id="editBukuModalLabel">
              <iconify-icon icon="solar:pen-2-outline" width="18" class="me-1"></iconify-icon>
              Edit Buku
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>

          {{-- Body --}}
          <div class="modal-body">

            {{-- ── 1. Identitas Buku ── --}}
            <p class="form-section-title">Identitas Buku</p>
            <div class="row g-3 mb-2">
              <div class="col-md-8">
                <label class="form-label fw-semibold mb-1">Judul Buku <span class="text-danger">*</span></label>
                <input type="text" name="judul_buku" id="edit_judul" class="form-control form-control-sm" required>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold mb-1">Kategori <span class="text-danger">*</span></label>
                <select name="kategori_buku_id" id="edit_kategori" class="form-select form-select-sm" required>
                  @foreach($kategoris as $k)
                    <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold mb-1">Penulis <span class="text-danger">*</span></label>
                <input type="text" name="penulis" id="edit_penulis" class="form-control form-control-sm" required>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold mb-1">Penerbit <span class="text-danger">*</span></label>
                <input type="text" name="penerbit" id="edit_penerbit" class="form-control form-control-sm" required>
              </div>
            </div>

            {{-- ── 2. Detail Penerbitan ── --}}
            <p class="form-section-title">Detail Penerbitan</p>
            <div class="row g-3 mb-3">
              <div class="col-md-4">
                <label class="form-label fw-semibold mb-1">Tahun Terbit <span class="text-danger">*</span></label>
                <input type="number" name="tahun_terbit" id="edit_tahun" class="form-control form-control-sm"
                  min="1900" max="{{ date('Y') }}" required>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold mb-1">ISBN</label>
                <input type="text" name="ISBN" id="edit_ISBN" class="form-control form-control-sm">
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold mb-1">Harga Buku</label>
                <div class="input-group input-group-sm">
                  <span class="input-group-text">Rp</span>
                  <input type="number" name="harga_buku" id="edit_harga" class="form-control" min="0">
                </div>
              </div>
            </div>

            {{-- ── 3. Stok & Kondisi ── --}}
            <p class="form-section-title">Stok &amp; Kondisi</p>
            <div class="row g-3 mb-3">
              <div class="col-md-4">
                <label class="form-label fw-semibold mb-1">Kondisi <span class="text-danger">*</span></label>
                <select name="kondisi" id="edit_kondisi" class="form-select form-select-sm" required>
                  <option value="baik">Baik</option>
                  <option value="rusak_ringan">Rusak Ringan</option>
                  <option value="rusak_berat">Rusak Berat</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold mb-1">Stok Total <span class="text-danger">*</span></label>
                <input type="number" name="stok" id="edit_stok" class="form-control form-control-sm" min="0" required>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold mb-1">Stok Tersedia <span class="text-danger">*</span></label>
                <input type="number" name="stok_tersedia" id="edit_stok_tersedia"
                  class="form-control form-control-sm" min="0" required>
                <div class="form-text">Bisa disesuaikan manual saat edit</div>
              </div>
            </div>

            {{-- ── 4. Sampul Buku ── --}}
            <p class="form-section-title">Sampul Buku</p>
            <div class="row g-3">
              <div class="col-md-4">
                <div id="edit_current_cover_wrap" style="display:none">
                  <label class="form-label fw-semibold mb-1 d-block">Cover Saat Ini</label>
                  <img id="edit_current_cover" src="#" alt="Cover saat ini"
                    style="max-width:90px;max-height:120px;border-radius:6px;
                           border:1px solid #dee2e6;object-fit:cover;padding:3px;">
                </div>
              </div>
              <div class="col-md-8">
                <label class="form-label fw-semibold mb-1">
                  Ganti Gambar
                  <small class="text-muted fw-normal">(kosongkan jika tidak diubah)</small>
                </label>
                <input type="file" name="gambar" id="edit_gambar"
                  class="form-control form-control-sm" accept="image/jpeg,image/jpg,image/png,image/gif">
                <div class="form-text">Format: JPG, PNG, GIF. Maks 2MB.</div>
                <div class="img-preview-wrap" id="edit_preview_wrap">
                  <img id="edit_preview_img" src="#" alt="Preview baru">
                  <div class="img-label">Preview Gambar Baru</div>
                </div>
              </div>
            </div>

          </div>{{-- /.modal-body --}}

          {{-- Footer --}}
          <div class="modal-footer py-2">
            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
              <iconify-icon icon="solar:close-square-outline" width="15" class="me-1"></iconify-icon>Batal
            </button>
            <button type="submit" class="btn btn-sm btn-info text-white">
              <iconify-icon icon="solar:diskette-outline" width="15" class="me-1"></iconify-icon>Update
            </button>
          </div>

        </form>
      </div>
    </div>
  </div>

  {{-- ── Scripts ── --}}
  <script src="{{ asset('template/libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('template/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('template/js/app.min.js') }}"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
  /* ═══════════════════════════════════════════════
     Fallback gambar — coba path alternatif sebelum
     tampilkan badge "No Cover"
  ═══════════════════════════════════════════════ */
  var _imgTried = {};
  function handleImgError(img, filename) {
    var tried = _imgTried[filename] || 0;
    var alt = [
      '/storage/bukus/' + filename,
      '/public/storage/bukus/' + filename,
      '/bukus/' + filename,
      '{{ url("/") }}/storage/bukus/' + filename
    ];
    if (tried < alt.length) {
      _imgTried[filename] = tried + 1;
      img.onerror = function () { handleImgError(img, filename); };
      img.src = alt[tried];
    } else {
      img.onerror = null;
      img.style.display = 'none';
      var badge = document.createElement('span');
      badge.className = 'badge bg-secondary';
      badge.textContent = 'No Cover';
      img.parentNode.insertBefore(badge, img.nextSibling);
    }
  }

  $(document).ready(function () {

    /* ── DataTable ── */
    $('#bukuTable').DataTable({
      language: {
        search: "Cari:",
        lengthMenu: "Tampilkan _MENU_ data",
        info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
        paginate: { previous: "‹", next: "›" }
      }
    });

    /* ══════════════════════════════════════
       CREATE MODAL
    ══════════════════════════════════════ */
    // Stok total → stok tersedia auto-sync
    $('#create_stok').on('input', function () {
      var val = parseInt($(this).val());
      $('#create_stok_tersedia').val(isNaN(val) || val < 0 ? 0 : val);
    });

    // Preview gambar
    $('#create_gambar').on('change', function () {
      previewImage(this, '#create_preview_img', '#create_preview_wrap');
    });

    // Reset saat modal ditutup
    $('#createBukuModal').on('hidden.bs.modal', function () {
      $('#createBukuForm')[0].reset();
      $('#create_preview_wrap').hide();
      $('#create_stok_tersedia').val('');
    });

    /* ══════════════════════════════════════
       EDIT MODAL
    ══════════════════════════════════════ */
    $(document).on('click', '.editBtn', function () {
      var data = $(this).data('json');

      $('#edit_judul').val(data.judul_buku);
      $('#edit_kategori').val(data.kategori_buku_id);
      $('#edit_penulis').val(data.penulis);
      $('#edit_penerbit').val(data.penerbit);
      $('#edit_tahun').val(data.tahun_terbit);
      $('#edit_ISBN').val(data.ISBN);
      $('#edit_harga').val(data.harga_buku);
      $('#edit_kondisi').val(data.kondisi);
      $('#edit_stok').val(data.stok);
      $('#edit_stok_tersedia').val(data.stok_tersedia);
      $('#editBukuForm').attr('action', '/admin/bukus/' + data.id);

      // Tampilkan cover saat ini
      if (data.gambar) {
        $('#edit_current_cover').attr('src', '{{ url("/") }}/storage/bukus/' + data.gambar);
        $('#edit_current_cover_wrap').show();
      } else {
        $('#edit_current_cover_wrap').hide();
      }

      $('#edit_gambar').val('');
      $('#edit_preview_wrap').hide();
      $('#editBukuModal').modal('show');
    });

    // Preview gambar baru saat edit
    $('#edit_gambar').on('change', function () {
      previewImage(this, '#edit_preview_img', '#edit_preview_wrap');
    });

    /* ══════════════════════════════════════
       DELETE: konfirmasi SweetAlert
    ══════════════════════════════════════ */
    $(document).on('submit', '.deleteForm', function (e) {
      e.preventDefault();
      var form = this;
      Swal.fire({
        title: 'Hapus Buku?',
        text: 'Data yang dihapus tidak dapat dikembalikan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
      }).then(function (result) {
        if (result.isConfirmed) form.submit();
      });
    });

    /* ── Helper: preview file lokal ── */
    function previewImage(input, imgSel, wrapSel) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
          $(imgSel).attr('src', e.target.result);
          $(wrapSel).show();
        };
        reader.readAsDataURL(input.files[0]);
      } else {
        $(wrapSel).hide();
      }
    }

    /* ================= AUTO HIDE ALERT ================= */
    setTimeout(() => $('.alert').fadeOut(), 3000);

  });
  
  </script>
</body>
</html>