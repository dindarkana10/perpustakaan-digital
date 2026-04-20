<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ajukan Peminjaman - Peminjam</title>
  <link rel="stylesheet" href="{{ asset ('template/css/styles.min.css') }}" />
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
              <h5 class="card-title fw-semibold mb-0">Ajukan Peminjaman Alat</h5>
              <a href="{{ route('alat.index') }}" class="btn btn-outline-secondary">
                <iconify-icon icon="solar:arrow-left-outline" width="18" class="me-1"></iconify-icon>
                Kembali
              </a>
            </div>

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

            <form action="{{ route('peminjaman.store') }}" method="POST" id="peminjamanForm">
              @csrf
              
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('tanggal_pinjam') is-invalid @enderror" 
                           id="tanggal_pinjam" name="tanggal_pinjam" 
                           value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" 
                           min="{{ date('Y-m-d') }}" required>
                    @error('tanggal_pinjam')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="tanggal_kembali_rencana" class="form-label">Tanggal Kembali <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('tanggal_kembali_rencana') is-invalid @enderror" 
                           id="tanggal_kembali_rencana" name="tanggal_kembali_rencana" 
                           value="{{ old('tanggal_kembali_rencana') }}" 
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                    @error('tanggal_kembali_rencana')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div class="mb-3">
                    <label for="keperluan" class="form-label">Keperluan <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('keperluan') is-invalid @enderror" 
                              id="keperluan" name="keperluan" rows="3" 
                              maxlength="1000" required>{{ old('keperluan') }}</textarea>
                    <div class="form-text">Jelaskan keperluan peminjaman alat (maksimal 1000 karakter)</div>
                    @error('keperluan')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <hr>
              <h6 class="mb-3">Detail Alat yang Dipinjam</h6>
              
              <div id="alatContainer">
                <div class="row alat-item mb-3">
                  <div class="col-md-6">
                    <label class="form-label">Pilih Alat <span class="text-danger">*</span></label>
                    <select class="form-select alat-select" name="alat_id[]" required>
                      <option value="">Pilih Alat</option>
                      @foreach($alats as $alat)
                        <option value="{{ $alat->id }}" 
                                data-stok="{{ $alat->stok_tersedia }}"
                                data-nama="{{ $alat->nama_alat }}"
                                {{ $selectedAlatId == $alat->id ? 'selected' : '' }}>
                          {{ $alat->nama_alat }} - {{ $alat->kategori->nama_kategori }} (Stok: {{ $alat->stok_tersedia }})
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                    <input type="number" class="form-control jumlah-input" name="jumlah[]" min="1" value="1" required>
                    <small class="text-muted stok-info"></small>
                  </div>
                  <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-danger w-100 removeAlat" style="display:none;">
                      <iconify-icon icon="solar:trash-bin-trash-outline" width="18"></iconify-icon>
                    </button>
                  </div>
                </div>
              </div>

              <button type="button" class="btn btn-sm btn-outline-primary mb-4" id="addAlat">
                <iconify-icon icon="solar:add-circle-outline" width="18"></iconify-icon>
                Tambah Alat Lain
              </button>

              <hr>

              <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('alat.index') }}" class="btn btn-secondary">
                  Batal
                </a>
                <button type="submit" class="btn btn-primary">
                  <iconify-icon icon="solar:check-circle-outline" width="18" class="me-1"></iconify-icon>
                  Ajukan Peminjaman
                </button>
              </div>
            </form>

          </div>
        </div>

      </div>
    </div>
  </div>

  <script src="{{ asset ('template/libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset ('template/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset ('template/js/sidebarmenu.js') }}"></script>
  <script src="{{ asset ('template/js/app.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
  $(document).ready(function () {
    
    /* ================= ADD/REMOVE ALAT ================= */
    let alatItemTemplate = $('.alat-item').first().clone();
    
    $('#addAlat').click(function() {
      let newItem = alatItemTemplate.clone();
      newItem.find('select, input').val('');
      newItem.find('.stok-info').text('');
      newItem.find('.removeAlat').show();
      $('#alatContainer').append(newItem);
      updateRemoveButtons();
    });

    $(document).on('click', '.removeAlat', function() {
      $(this).closest('.alat-item').remove();
      updateRemoveButtons();
    });

    function updateRemoveButtons() {
      let count = $('.alat-item').length;
      if (count === 1) {
        $('.removeAlat').hide();
      } else {
        $('.removeAlat').show();
      }
    }

    /* ================= VALIDASI STOK ================= */
    $(document).on('change', '.alat-select', function() {
      let stok = $(this).find(':selected').data('stok');
      let nama = $(this).find(':selected').data('nama');
      let container = $(this).closest('.alat-item');
      let jumlahInput = container.find('.jumlah-input');
      let stokInfo = container.find('.stok-info');
      
      if (stok) {
        jumlahInput.attr('max', stok);
        stokInfo.text('Stok tersedia: ' + stok);
      } else {
        jumlahInput.attr('max', '');
        stokInfo.text('');
      }
    });

    $(document).on('input', '.jumlah-input', function() {
      let container = $(this).closest('.alat-item');
      let select = container.find('.alat-select');
      let stok = select.find(':selected').data('stok');
      let jumlah = parseInt($(this).val());
      
      if (jumlah > stok) {
        $(this).val(stok);
        Swal.fire({
          icon: 'warning',
          title: 'Stok Tidak Cukup',
          text: 'Jumlah melebihi stok tersedia (' + stok + ')',
          timer: 2000
        });
      }
    });

    // Trigger untuk alat yang sudah terpilih (jika ada selectedAlatId)
    $('.alat-select').trigger('change');

    /* ================= VALIDATE DATE ================= */
    function validateDate() {
      const tanggalPinjam = new Date($('#tanggal_pinjam').val());
      const tanggalKembali = new Date($('#tanggal_kembali_rencana').val());
      
      if (tanggalKembali <= tanggalPinjam) {
        $('#tanggal_kembali_rencana').addClass('is-invalid');
        return false;
      } else {
        $('#tanggal_kembali_rencana').removeClass('is-invalid');
        return true;
      }
    }

    $('#tanggal_pinjam').on('change', function() {
      let minKembali = new Date($(this).val());
      minKembali.setDate(minKembali.getDate() + 1);
      $('#tanggal_kembali_rencana').attr('min', minKembali.toISOString().split('T')[0]);
      validateDate();
    });

    $('#tanggal_kembali_rencana').on('change', validateDate);

    /* ================= FORM SUBMIT VALIDATION ================= */
    $('#peminjamanForm').on('submit', function(e) {
      if (!validateDate()) {
        e.preventDefault();
        Swal.fire('Error!', 'Tanggal kembali harus setelah tanggal pinjam!', 'error');
        return false;
      }

      // Validasi minimal 1 alat
      if ($('.alat-item').length === 0) {
        e.preventDefault();
        Swal.fire('Error!', 'Pilih minimal 1 alat!', 'error');
        return false;
      }

      // Validasi duplikat alat
      let alatIds = [];
      let hasDuplicate = false;
      $('.alat-select').each(function() {
        let val = $(this).val();
        if (val && alatIds.includes(val)) {
          hasDuplicate = true;
          return false;
        }
        if (val) alatIds.push(val);
      });

      if (hasDuplicate) {
        e.preventDefault();
        Swal.fire('Error!', 'Tidak boleh memilih alat yang sama lebih dari sekali!', 'error');
        return false;
      }
    });

    /* ================= AUTO HIDE ALERT ================= */
    setTimeout(() => $('.alert').fadeOut(), 5000);

  });
  </script>

</body>

</html>