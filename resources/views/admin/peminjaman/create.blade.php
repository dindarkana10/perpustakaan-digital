<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tambah Peminjaman - Admin</title>
  <link rel="stylesheet" href="{{ asset ('template/css/styles.min.css') }}" />
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
            <h5 class="card-title fw-semibold mb-4">Tambah Peminjaman Buku Baru (Admin)</h5>
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('admin.peminjaman.store') }}" method="POST">
              @csrf
              <div class="row">
                <div class="col-md-12 mb-3">
                  <label class="form-label">Pilih Peminjam (User)</label>
                  <select name="user_id" class="form-select select2" required>
                    <option value="">Cari Nama / NISN Peminjam...</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->NISN }} - {{ $user->kelas_jurusan }})
                        </option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Tanggal Pinjam</label>
                  <input type="date" name="tanggal_pinjam" class="form-control" value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Tanggal Kembali (Rencana)</label>
                  <input type="date" name="tanggal_kembali_rencana" class="form-control" value="{{ old('tanggal_kembali_rencana') }}" required>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Keperluan</label>
                <textarea name="keperluan" class="form-control" rows="3" required placeholder="Contoh: Tugas sekolah / Referensi materi">{{ old('keperluan') }}</textarea>
              </div>
              
              <hr class="my-4">
              <div class="d-flex justify-content-between align-items-center mb-3">
                  <h6 class="fw-bold mb-0">Daftar Buku yang Dipinjam</h6>
                  <button type="button" class="btn btn-outline-primary btn-sm" id="addBuku">
                    <iconify-icon icon="solar:add-circle-outline" width="18" class="me-1"></iconify-icon>Tambah Buku
                  </button>
              </div>

              <div id="bukuList">
                {{-- Row template --}}
                <div class="card bg-light border-0 mb-3 buku-item">
                    <div class="card-body p-3">
                        <div class="row align-items-end g-3">
                            <div class="col-md-5">
                                <label class="form-label">Pilih Buku</label>
                                <select name="buku_id[]" class="form-select buku-select" required onchange="updatePreview(this)">
                                    <option value="">— Pilih Buku —</option>
                                    @foreach($bukus as $b)
                                        <option value="{{ $b->id }}" data-gambar="{{ $b->gambar ? asset('storage/bukus/' . $b->gambar) : '' }}">
                                            {{ $b->judul_buku }} (Stok: {{ $b->stok_tersedia }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Jumlah</label>
                                <input type="number" name="jumlah[]" class="form-control" value="1" min="1" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Kondisi</label>
                                <select name="kondisi_pinjam[]" class="form-select" required>
                                    <option value="baik">Baik</option>
                                    <option value="rusak_ringan">Rusak Ringan</option>
                                    <option value="rusak_berat">Rusak Berat</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <div class="buku-preview text-center">
                                    <div class="rounded bg-white border d-flex align-items-center justify-content-center" style="width:40px;height:55px;">
                                        <iconify-icon icon="solar:book-outline" width="20" class="text-muted"></iconify-icon>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger w-100 removeBuku" style="display:none;">
                                    <iconify-icon icon="solar:trash-bin-trash-outline" width="20"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
              </div>

              <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.peminjaman.index') }}" class="btn btn-light px-4">Batal</a>
                <button type="submit" class="btn btn-primary px-4">Simpan Peminjaman</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset ('template/libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset ('template/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

  <script>
    const daftarBuku = @json($bukuJs);

    function updatePreview(select) {
        const opt = select.options[select.selectedIndex];
        const gambar = opt ? opt.dataset.gambar : '';
        const wrap = select.closest('.buku-item').querySelector('.buku-preview');

        if (gambar) {
            wrap.innerHTML = `<img src="${gambar}" class="rounded border" style="width:40px;height:55px;object-fit:cover;">`;
        } else {
            wrap.innerHTML = `<div class="rounded bg-white border d-flex align-items-center justify-content-center" style="width:40px;height:55px;">
                                <iconify-icon icon="solar:book-outline" width="20" class="text-muted"></iconify-icon>
                            </div>`;
        }
    }

    $(document).ready(function() {
        $('.select2').select2({ theme: 'bootstrap-5' });

        $('#addBuku').click(function() {
            let item = $('#bukuList .buku-item').first().clone();
            item.find('select, input').val('');
            item.find('select[name="kondisi_pinjam[]"]').val('baik');
            item.find('input[name="jumlah[]"]').val('1');
            item.find('.removeBuku').show();
            item.find('.buku-preview').html(`<div class="rounded bg-white border d-flex align-items-center justify-content-center" style="width:40px;height:55px;">
                                <iconify-icon icon="solar:book-outline" width="20" class="text-muted"></iconify-icon>
                            </div>`);
            $('#bukuList').append(item);
        });

        $(document).on('click', '.removeBuku', function() {
            $(this).closest('.buku-item').remove();
        });
    });
  </script>
  
  <style>
      .select2-container--bootstrap-5 .select2-selection { border-radius: 0.375rem; border: 1px solid #dee2e6; height: calc(3.5rem + 2px); padding: 1rem 0.75rem; }
  </style>
</body>
</html>
