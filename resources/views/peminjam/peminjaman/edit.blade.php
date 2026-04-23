<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Peminjaman - Perpustakaan Digital</title>
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
            <h5 class="card-title fw-semibold mb-4">Edit Permohonan Peminjaman</h5>

            @if (session('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            @endif
            @if (session('error'))
              <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            @endif
            @if ($errors->any())
              <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                  @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                  @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            @endif

            {{-- Data buku untuk JS --}}
            <script>
              const daftarBuku = @json($bukuJs);
            </script>

            {{-- ✅ FIX: route peminjaman.update (resource peminjam tanpa prefix nama) --}}
            <form action="{{ route('peminjam.peminjaman.update', $peminjaman->id) }}" method="POST">
              @csrf
              @method('PUT')
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Tanggal Pinjam</label>
                  <input type="date" name="tanggal_pinjam" class="form-control"
                         value="{{ old('tanggal_pinjam', $peminjaman->tanggal_pinjam) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Tanggal Kembali (Rencana)</label>
                  <input type="date" name="tanggal_kembali_rencana" class="form-control"
                         value="{{ old('tanggal_kembali_rencana', $peminjaman->tanggal_kembali_rencana) }}" required>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Keperluan / Alasan Pinjam</label>
                <textarea name="keperluan" class="form-control" rows="3" required>{{ old('keperluan', $peminjaman->keperluan) }}</textarea>
              </div>
              <hr>
              <h6 class="mb-3">Buku yang Dipinjam</h6>

              <div id="bukuList">
                @foreach($peminjaman->details as $index => $detail)
                  @php $selectedGambar = $detail->buku->gambar ? asset('storage/bukus/' . $detail->buku->gambar) : ''; @endphp
                  <div class="row mb-3 align-items-start buku-item">
                    <div class="col-md-7">
                      <label class="form-label">Pilih Buku</label>
                      <select name="buku_id[]" class="form-select buku-select" required onchange="updatePreview(this)">
                        <option value="">— Pilih Buku —</option>
                        @foreach($bukus as $b)
                          <option value="{{ $b->id }}"
                            data-gambar="{{ $b->gambar ? asset('storage/bukus/' . $b->gambar) : '' }}"
                            {{ $detail->buku_id == $b->id ? 'selected' : '' }}>
                            {{ $b->judul_buku }} (Stok: {{ $b->stok_tersedia }})
                          </option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-md-2">
                      <label class="form-label">Jumlah</label>
                      <input type="number" name="jumlah[]" class="form-control"
                             value="{{ old('jumlah.' . $index, $detail->jumlah) }}" min="1" required>
                    </div>
                    <div class="col-md-1 pt-1">
                      <label class="form-label d-block">&nbsp;</label>
                      <div class="buku-preview">
                        @if($detail->buku->gambar)
                          <img src="{{ $selectedGambar }}"
                               alt="{{ $detail->buku->judul_buku }}"
                               class="rounded preview-img"
                               style="width:48px;height:64px;object-fit:cover;border:1px solid #dee2e6;cursor:pointer;"
                               onclick="bukaPreview('{{ $selectedGambar }}','{{ $detail->buku->judul_buku }}')">
                        @else
                          <div class="rounded d-flex align-items-center justify-content-center bg-light border preview-placeholder"
                               style="width:48px;height:64px;">
                            <iconify-icon icon="solar:book-outline" width="22" class="text-muted"></iconify-icon>
                          </div>
                        @endif
                      </div>
                    </div>
                    <div class="col-md-2 pt-1">
                      <label class="form-label d-block">&nbsp;</label>
                      <button type="button" class="btn btn-danger w-100 removeBuku"
                        {{ $index == 0 ? 'style=display:none;' : '' }}>Hapus</button>
                    </div>
                  </div>
                @endforeach
              </div>

              <button type="button" class="btn btn-outline-primary btn-sm mb-4" id="addBuku">
                <iconify-icon icon="solar:add-circle-outline" width="16" class="me-1"></iconify-icon>Tambah Buku Lain
              </button>

              <div class="d-flex justify-content-end gap-2 mt-4">
                {{-- ✅ FIX: route batal ke index dengan nama route yang benar --}}
                <a href="{{ route('peminjam.peminjaman.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Modal preview gambar --}}
  <div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
      <div class="modal-content">
        <div class="modal-header py-2">
          <h6 class="modal-title fw-semibold" id="previewModalTitle"></h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center p-2">
          <img id="previewModalImg" src="" alt="" class="img-fluid rounded" style="max-height:400px;">
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset ('template/libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset ('template/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

  <script>
    /* ---- Buka modal preview ---- */
    function bukaPreview(src, judul) {
      document.getElementById('previewModalImg').src = src;
      document.getElementById('previewModalTitle').textContent = judul;
      new bootstrap.Modal(document.getElementById('previewModal')).show();
    }

    /* ---- Update preview saat ganti pilihan buku ---- */
    function updatePreview(select) {
      const opt    = select.options[select.selectedIndex];
      const gambar = opt ? opt.dataset.gambar : '';
      const judul  = opt ? opt.text : '';
      const wrap   = select.closest('.buku-item').querySelector('.buku-preview');

      if (gambar) {
        wrap.innerHTML = `<img src="${gambar}" alt="${judul}"
                               class="rounded preview-img"
                               style="width:48px;height:64px;object-fit:cover;border:1px solid #dee2e6;cursor:pointer;"
                               onclick="bukaPreview('${gambar}','${judul}')">`;
      } else {
        wrap.innerHTML = `<div class="rounded d-flex align-items-center justify-content-center bg-light border preview-placeholder"
                               style="width:48px;height:64px;">
                            <iconify-icon icon="solar:book-outline" width="22" class="text-muted"></iconify-icon>
                          </div>`;
      }
    }

    /* ---- Clone row buku baru ---- */
    function cloneRow() {
      const template = `
        <div class="row mb-3 align-items-start buku-item">
          <div class="col-md-7">
            <label class="form-label">Pilih Buku</label>
            <select name="buku_id[]" class="form-select buku-select" required onchange="updatePreview(this)">
              <option value="">— Pilih Buku —</option>
              ${daftarBuku.map(b =>
                `<option value="${b.id}" data-gambar="${b.gambar ?? ''}">${b.judul} (Stok: ${b.stok})</option>`
              ).join('')}
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Jumlah</label>
            <input type="number" name="jumlah[]" class="form-control" value="1" min="1" required>
          </div>
          <div class="col-md-1 pt-1">
            <label class="form-label d-block">&nbsp;</label>
            <div class="buku-preview">
              <div class="rounded d-flex align-items-center justify-content-center bg-light border preview-placeholder"
                   style="width:48px;height:64px;">
                <iconify-icon icon="solar:book-outline" width="22" class="text-muted"></iconify-icon>
              </div>
            </div>
          </div>
          <div class="col-md-2 pt-1">
            <label class="form-label d-block">&nbsp;</label>
            <button type="button" class="btn btn-danger w-100 removeBuku">Hapus</button>
          </div>
        </div>`;
      document.getElementById('bukuList').insertAdjacentHTML('beforeend', template);
    }

    $(document).ready(function () {
      $('#addBuku').click(() => cloneRow());

      $(document).on('click', '.removeBuku', function () {
        $(this).closest('.buku-item').remove();
      });

      /* Auto hide alert */
      setTimeout(() => $('.alert').fadeOut(), 3000);
    });
  </script>
</body>
</html>