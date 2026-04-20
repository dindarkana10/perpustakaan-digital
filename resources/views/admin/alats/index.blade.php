<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Admin - Alat</title>
  <link rel="stylesheet" href="{{ asset ('template/css/styles.min.css') }}" />
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
  <style>
    .img-preview {
      max-width: 200px;
      max-height: 200px;
      margin-top: 10px;
      display: none;
    }
    .alat-image {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 5px;
    }
  </style>
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
                <h5 class="card-title fw-semibold">Data Alat</h5>

                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAlatModal">
                  <iconify-icon icon="solar:add-circle-outline" width="18" class="me-1"></iconify-icon>
                  Tambah Alat
                </button>
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

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <form method="GET" class="row g-2 mb-3">
                <div class="col-md-4">
                    <select name="kategori_id" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kategori)
                        <option value="{{ $kategori->id }}" 
                        {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>
                        {{ $kategori->nama_kategori }}
                        </option>
                    @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-2">
                        <a href="{{ route('alats.index') }}" class="btn btn-danger w-100">
                            <iconify-icon icon="solar:restart-outline" width="18" class="me-1"></iconify-icon>
                            Reset
                        </a>
                    </div>
                </form>

                <table id="alatTable" class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                    <th>No</th>
                    <th>Gambar</th>
                    <th>Nama Alat</th>
                    <th>Kategori</th>
                    <th>Kondisi</th>
                    <th>Jumlah Stok</th>
                    <th>Stok Tersedia</th>
                    <th>Harga Beli</th>
                    <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($alats as $alat)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="text-center">
                            @if($alat->gambar)
                                <img src="{{ asset('storage/alats/' . $alat->gambar) }}" 
                                     alt="{{ $alat->nama_alat }}" 
                                     class="alat-image">
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
                        <td class="text-center">{{ $alat->stok_total }}</td>
                        <td class="text-center">
                            <span class="badge {{ $alat->stok_tersedia > 0 ? 'bg-success' : 'bg-danger' }}">
                                {{ $alat->stok_tersedia }}
                            </span>
                        </td>
                        <td>{{ $alat->formatted_harga_beli }}</td>
                        <td class="text-center">
                        <button type="button"
                            class="btn btn-sm btn-outline-info me-1 showBtn"
                            data-nama="{{ $alat->nama_alat }}"
                            data-kategori="{{ $alat->kategori->nama_kategori }}"
                            data-kondisi="{{ $alat->kondisi_label }}"
                            data-stok-total="{{ $alat->stok_total }}"
                            data-stok-tersedia="{{ $alat->stok_tersedia }}"
                            data-harga="{{ $alat->formatted_harga_beli }}"
                            data-keterangan="{{ $alat->keterangan }}"
                            data-gambar="{{ $alat->gambar ? asset('storage/alats/'.$alat->gambar) : '' }}">
                            <iconify-icon icon="solar:eye-outline" width="18"></iconify-icon>
                        </button>             

                        <button type="button" 
                                class="btn btn-sm btn-outline-primary me-1 editBtn"
                                data-id="{{ $alat->id }}"
                                data-nama="{{ $alat->nama_alat }}"
                                data-kategori="{{ $alat->kategori_id }}"
                                data-kondisi="{{ $alat->kondisi }}"
                                data-stok-total="{{ $alat->stok_total }}"
                                data-stok-tersedia="{{ $alat->stok_tersedia }}"
                                data-harga="{{ $alat->harga_beli }}"
                                data-keterangan="{{ $alat->keterangan }}"
                                data-gambar="{{ $alat->gambar ? asset('storage/alats/' . $alat->gambar) : '' }}"
                                title="Edit">
                            <iconify-icon icon="solar:pen-2-outline" width="18"></iconify-icon>
                        </button>

                        <form action="{{ route('alats.destroy', $alat->id) }}"
                                method="POST"
                                class="d-inline deleteForm">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn btn-sm btn-outline-danger"
                                    title="Hapus">
                            <iconify-icon icon="solar:trash-bin-trash-outline" width="18"></iconify-icon>
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

  <!-- Modal Create Alat -->
  <div class="modal fade" id="createAlatModal" tabindex="-1" aria-labelledby="createAlatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createAlatModalLabel">Tambah Alat Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{ route('alats.store') }}" method="POST" enctype="multipart/form-data" id="createAlatForm">
          @csrf
          <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                      <label for="nama_alat" class="form-label">Nama Alat <span class="text-danger">*</span></label>
                      <input type="text" class="form-control @error('nama_alat') is-invalid @enderror" 
                             id="nama_alat" name="nama_alat" value="{{ old('nama_alat') }}" 
                             required minlength="3" maxlength="255">
                      <div class="invalid-feedback">
                        @error('nama_alat') {{ $message }} @else Nama alat minimal 3 karakter. @enderror
                      </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                      <label for="kategori_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                      <select class="form-select @error('kategori_id') is-invalid @enderror" 
                              id="kategori_id" name="kategori_id" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($kategoris as $kategori)
                            <option value="{{ $kategori->id }}" {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->nama_kategori }}
                            </option>
                        @endforeach
                      </select>
                      <div class="invalid-feedback">
                        @error('kategori_id') {{ $message }} @else Pilih kategori alat. @enderror
                      </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                      <label for="kondisi" class="form-label">Kondisi <span class="text-danger">*</span></label>
                      <select class="form-select @error('kondisi') is-invalid @enderror" 
                              id="kondisi" name="kondisi" required>
                        <option value="">Pilih Kondisi</option>
                        <option value="baik" {{ old('kondisi') == 'baik' ? 'selected' : '' }}>Baik</option>
                        <option value="rusak_ringan" {{ old('kondisi') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                        <option value="rusak_berat" {{ old('kondisi') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                      </select>
                      <div class="invalid-feedback">
                        @error('kondisi') {{ $message }} @else Pilih kondisi alat. @enderror
                      </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                      <label for="stok_total" class="form-label">Stok Total <span class="text-danger">*</span></label>
                      <input type="number" class="form-control @error('stok_total') is-invalid @enderror" 
                             id="stok_total" name="stok_total" value="{{ old('stok_total', 0) }}" 
                             required min="0">
                      <div class="invalid-feedback">
                        @error('stok_total') {{ $message }} @else Stok total minimal 0. @enderror
                      </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                      <label for="stok_tersedia" class="form-label">Stok Tersedia <span class="text-danger">*</span></label>
                      <input type="number" class="form-control @error('stok_tersedia') is-invalid @enderror" 
                             id="stok_tersedia" name="stok_tersedia" value="{{ old('stok_tersedia', 0) }}" 
                             required min="0">
                      <div class="invalid-feedback">
                        @error('stok_tersedia') {{ $message }} @else Stok tersedia tidak boleh melebihi stok total. @enderror
                      </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                      <label for="harga_beli" class="form-label">Harga Beli</label>
                      <input type="number" class="form-control @error('harga_beli') is-invalid @enderror" 
                             id="harga_beli" name="harga_beli" value="{{ old('harga_beli') }}" 
                             min="0" step="0.01">
                      <div class="invalid-feedback">
                        @error('harga_beli') {{ $message }} @else Harga beli minimal 0. @enderror
                      </div>
                      {{-- <small class="text-muted">Opsional - untuk perhitungan ganti rugi</small> --}}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                      <label for="gambar" class="form-label">Gambar Alat</label>
                      <input type="file" class="form-control @error('gambar') is-invalid @enderror" 
                             id="gambar" name="gambar" accept="image/*">
                      <div class="invalid-feedback">
                        @error('gambar') {{ $message }} @else Format: JPG, PNG, GIF. Max: 2MB. @enderror
                      </div>
                      <small class="text-muted">Format: JPG, PNG, GIF. Max: 2MB</small>
                      <img id="preview_gambar" class="img-preview img-thumbnail" alt="Preview">
                    </div>
                </div>
            </div>

            <div class="mb-3">
              <label for="keterangan" class="form-label">Keterangan</label>
              <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                        id="keterangan" name="keterangan" rows="3" 
                        maxlength="1000">{{ old('keterangan') }}</textarea>
              <div class="invalid-feedback">
                @error('keterangan') {{ $message }} @else Keterangan maksimal 1000 karakter. @enderror
              </div>
              <small class="text-muted">Opsional - Maksimal 1000 karakter</small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Edit Alat -->
  <div class="modal fade" id="editAlatModal" tabindex="-1" aria-labelledby="editAlatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editAlatModalLabel">Edit Alat</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="editAlatForm" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                      <label for="edit_nama_alat" class="form-label">Nama Alat <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="edit_nama_alat" name="nama_alat" 
                             required minlength="3" maxlength="255">
                      <div class="invalid-feedback">Nama alat minimal 3 karakter.</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                      <label for="edit_kategori_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                      <select class="form-select" id="edit_kategori_id" name="kategori_id" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($kategoris as $kategori)
                            <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                        @endforeach
                      </select>
                      <div class="invalid-feedback">Pilih kategori alat.</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                      <label for="edit_kondisi" class="form-label">Kondisi <span class="text-danger">*</span></label>
                      <select class="form-select" id="edit_kondisi" name="kondisi" required>
                        <option value="">Pilih Kondisi</option>
                        <option value="baik">Baik</option>
                        <option value="rusak_ringan">Rusak Ringan</option>
                        <option value="rusak_berat">Rusak Berat</option>
                      </select>
                      <div class="invalid-feedback">Pilih kondisi alat.</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                      <label for="edit_stok_total" class="form-label">Stok Total <span class="text-danger">*</span></label>
                      <input type="number" class="form-control" id="edit_stok_total" name="stok_total" 
                             required min="0">
                      <div class="invalid-feedback">Stok total minimal 0.</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                      <label for="edit_stok_tersedia" class="form-label">Stok Tersedia <span class="text-danger">*</span></label>
                      <input type="number" class="form-control" id="edit_stok_tersedia" name="stok_tersedia" 
                             required min="0">
                      <div class="invalid-feedback">Stok tersedia tidak boleh melebihi stok total.</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                      <label for="edit_harga_beli" class="form-label">Harga Beli</label>
                      <input type="number" class="form-control" id="edit_harga_beli" name="harga_beli" 
                             min="0" step="0.01">
                      <div class="invalid-feedback">Harga beli minimal 0.</div>
                      <small class="text-muted">Opsional - untuk perhitungan ganti rugi</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                      <label for="edit_gambar" class="form-label">Gambar Alat</label>
                      <input type="file" class="form-control" id="edit_gambar" name="gambar" accept="image/*">
                      <div class="invalid-feedback">Format: JPG, PNG, GIF. Max: 2MB.</div>
                      <small class="text-muted">Kosongkan jika tidak ingin mengubah gambar</small>
                      <img id="edit_preview_gambar" class="img-preview img-thumbnail" alt="Preview">
                      <div id="current_image_container" style="margin-top: 10px;"></div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
              <label for="edit_keterangan" class="form-label">Keterangan</label>
              <textarea class="form-control" id="edit_keterangan" name="keterangan" 
                        rows="3" maxlength="1000"></textarea>
              <div class="invalid-feedback">Keterangan maksimal 1000 karakter.</div>
              <small class="text-muted">Opsional - Maksimal 1000 karakter</small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>

<!-- Modal Show Alat -->
  <div class="modal fade" id="showAlatModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

        <div class="modal-header">
            <h5 class="modal-title">Detail Alat</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

            <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Nama Alat</label>
                <input type="text" class="form-control" id="show_nama" readonly>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Kategori</label>
                <input type="text" class="form-control" id="show_kategori" readonly>
            </div>
            </div>

            <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Kondisi</label>
                <input type="text" class="form-control" id="show_kondisi" readonly>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Stok Total</label>
                <input type="text" class="form-control" id="show_stok_total" readonly>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Stok Tersedia</label>
                <input type="text" class="form-control" id="show_stok_tersedia" readonly>
            </div>
            </div>

            <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Harga Beli</label>
                <input type="text" class="form-control" id="show_harga" readonly>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Gambar Alat</label><br>
                <img id="show_gambar" class="img-thumbnail" style="max-width:200px;display:none;">
            </div>
            </div>

            <div class="mb-3">
            <label class="form-label">Keterangan</label>
            <textarea class="form-control" id="show_keterangan" rows="3" readonly></textarea>
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
    $('#alatTable').DataTable({
        language: { 
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
            emptyTable: 'Data alat belum ada',
            zeroRecords: 'Tidak ada data yang cocok'
        },
        pageLength: 10,
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: [1, 8] }
        ]
    });

    /* ================= IMAGE PREVIEW ================= */
    function previewImage(input, previewId) {
        const file = input.files[0];
        const preview = $(previewId);
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.attr('src', e.target.result).show();
            }
            reader.readAsDataURL(file);
        } else {
            preview.hide();
        }
    }

    $('#gambar').on('change', function() {
        previewImage(this, '#preview_gambar');
    });

    $('#edit_gambar').on('change', function() {
        previewImage(this, '#edit_preview_gambar');
    });

    /* ================= EDIT BUTTON ================= */
    $(document).on('click', '.editBtn', function () {
        $('#edit_nama_alat').val($(this).data('nama'));
        $('#edit_kategori_id').val($(this).data('kategori'));
        $('#edit_kondisi').val($(this).data('kondisi'));
        $('#edit_stok_total').val($(this).data('stok-total'));
        $('#edit_stok_tersedia').val($(this).data('stok-tersedia'));
        $('#edit_harga_beli').val($(this).data('harga'));
        $('#edit_keterangan').val($(this).data('keterangan'));
        
        if ($(this).data('gambar')) {
            $('#current_image_container').html('<p class="mb-0"><small class="text-muted">Gambar saat ini:</small></p><img src="' + $(this).data('gambar') + '" class="img-thumbnail" style="max-width: 150px;">');
        } else {
            $('#current_image_container').html('');
        }
        
        $('#editAlatForm').attr('action', '/admin/alats/' + $(this).data('id'));
        $('#editAlatModal').modal('show');
    });

    /* ================= VALIDATE STOK ================= */
    function validateStok() {
        const stokTotal = parseInt($('#stok_total').val()) || 0;
        const stokTersedia = parseInt($('#stok_tersedia').val()) || 0;
        
        if (stokTersedia > stokTotal) {
            $('#stok_tersedia').addClass('is-invalid');
            return false;
        } else {
            $('#stok_tersedia').removeClass('is-invalid');
            return true;
        }
    }

    function validateStokEdit() {
        const stokTotal = parseInt($('#edit_stok_total').val()) || 0;
        const stokTersedia = parseInt($('#edit_stok_tersedia').val()) || 0;
        
        if (stokTersedia > stokTotal) {
            $('#edit_stok_tersedia').addClass('is-invalid');
            return false;
        } else {
            $('#edit_stok_tersedia').removeClass('is-invalid');
            return true;
        }
    }

    $('#stok_total, #stok_tersedia').on('input', validateStok);
    $('#edit_stok_total, #edit_stok_tersedia').on('input', validateStokEdit);

    /* ================= FORM SUBMIT VALIDATION ================= */
    $('#createAlatForm').on('submit', function(e) {
        if (!validateStok()) {
            e.preventDefault();
            Swal.fire('Error!', 'Stok tersedia tidak boleh melebihi stok total!', 'error');
        }
    });

    $('#editAlatForm').on('submit', function(e) {
        if (!validateStokEdit()) {
            e.preventDefault();
            Swal.fire('Error!', 'Stok tersedia tidak boleh melebihi stok total!', 'error');
        }
    });

    /* ================= RESET MODAL ================= */
    $('#createAlatModal, #editAlatModal').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
        $(this).find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
        $(this).find('.img-preview').hide();
        $('#current_image_container').html('');
    });

    /* ================= DELETE CONFIRM ================= */
    $(document).on('submit', '.deleteForm', function (e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: 'Data alat yang dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'Batal',
        confirmButtonText: 'Ya, Hapus'
        }).then((result) => {
        if (result.isConfirmed) form.submit();
        });
    });

    /* ================= AUTO HIDE ALERT ================= */
    setTimeout(() => $('.alert').fadeOut(), 3000);

    });

    /* ================= SHOW DETAIL  ================= */
    $(document).on('click', '.showBtn', function () {
    $('#show_nama').val($(this).data('nama'));
    $('#show_kategori').val($(this).data('kategori'));
    $('#show_kondisi').val($(this).data('kondisi'));
    $('#show_stok_total').val($(this).data('stok-total'));
    $('#show_stok_tersedia').val($(this).data('stok-tersedia'));
    $('#show_harga').val($(this).data('harga'));
    $('#show_keterangan').val($(this).data('keterangan') ?? '-');

    if ($(this).data('gambar')) {
        $('#show_gambar').attr('src', $(this).data('gambar')).show();
    } else {
        $('#show_gambar').hide();
    }

    $('#showAlatModal').modal('show');
    });
    </script>

</body>

</html>