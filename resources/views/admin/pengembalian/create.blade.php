<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tambah Pengembalian</title>
  <link rel="stylesheet" href="{{ asset('template/css/styles.min.css') }}" />
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
              <h5 class="card-title fw-semibold mb-0">Tambah Data Pengembalian</h5>
              <a href="{{ route('admin.pengembalian.index') }}" class="btn btn-outline-secondary">
                <iconify-icon icon="solar:arrow-left-outline" width="18" class="me-1"></iconify-icon>
                Kembali
              </a>
            </div>

            @if(session('error'))
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            @endif

            @if($peminjamans->isEmpty())
              <div class="alert alert-info">
                <iconify-icon icon="solar:info-circle-outline" width="20"></iconify-icon>
                Tidak ada peminjaman yang perlu dikembalikan.
              </div>
              <a href="{{ route('admin.pengembalian.index') }}" class="btn btn-primary">
                Kembali ke Daftar
              </a>
            @else
              <form action="{{ route('admin.pengembalian.store') }}" method="POST">
                @csrf

                {{-- ── Pilih Peminjaman ──────────────────────────────────────────── --}}
                <div class="mb-4">
                  <label class="form-label fw-bold">Pilih Peminjaman <span class="text-danger">*</span></label>
                  <select name="peminjaman_id" id="peminjaman_id"
                          class="form-select @error('peminjaman_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Peminjaman --</option>
                    @foreach($peminjamans as $peminjaman)
                      <option value="{{ $peminjaman->id }}"
                              data-peminjam="{{ $peminjaman->user->name }}"
                              data-tanggal-pinjam="{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') }}"
                              data-tanggal-kembali="{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('d/m/Y') }}"
                              data-keperluan="{{ $peminjaman->keperluan }}"
                              data-details="{{ json_encode($peminjaman->details->map(function($d) {
                                return [
                                  'detail_id'      => $d->id,
                                  'alat_id'        => $d->alat_id,
                                  'nama_alat'      => $d->alat->nama_alat,
                                  'jumlah'         => $d->jumlah,
                                  'kondisi_pinjam' => $d->kondisi_pinjam,
                                ];
                              })) }}">
                        {{ $peminjaman->user->name }}
                        — {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') }}
                        ({{ $peminjaman->details->count() }} Alat)
                      </option>
                    @endforeach
                  </select>
                  @error('peminjaman_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                {{-- ── Detail Peminjaman + Form Kondisi Kembali ─────────────────── --}}
                <div id="detailPeminjaman" style="display:none;">
                  <div class="card bg-light mb-4">
                    <div class="card-body">

                      <h6 class="fw-semibold mb-3">Detail Peminjaman</h6>
                      <div class="row">
                        <div class="col-md-3 mb-2">
                          <label class="form-label">Peminjam</label>
                          <input type="text" class="form-control" id="display_peminjam" readonly>
                        </div>
                        <div class="col-md-3 mb-2">
                          <label class="form-label">Tanggal Pinjam</label>
                          <input type="text" class="form-control" id="display_tanggal_pinjam" readonly>
                        </div>
                        <div class="col-md-3 mb-2">
                          <label class="form-label">Tanggal Kembali Rencana</label>
                          <input type="text" class="form-control" id="display_tanggal_kembali" readonly>
                        </div>
                        <div class="col-md-3 mb-2">
                          <label class="form-label">Keperluan</label>
                          <input type="text" class="form-control" id="display_keperluan" readonly>
                        </div>
                      </div>

                      <h6 class="fw-semibold mt-4 mb-2">Kondisi Pengembalian Alat</h6>
                      <p class="text-muted small mb-3">
                        <iconify-icon icon="solar:info-circle-outline" width="16"></iconify-icon>
                        Isi kondisi setiap alat saat dikembalikan. Alat dengan kondisi
                        <strong>Rusak Berat</strong> atau <strong>Hilang</strong> akan dicatat dan
                        tidak akan menambah stok tersedia.
                      </p>

                      <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                          <thead class="table-light">
                            <tr>
                              <th style="width:40px">No</th>
                              <th>Nama Alat</th>
                              <th style="width:80px" class="text-center">Jumlah</th>
                              <th style="width:130px" class="text-center">Kondisi Pinjam</th>
                              <th style="width:180px">Kondisi Kembali <span class="text-danger">*</span></th>
                              <th>Keterangan Kondisi</th>
                            </tr>
                          </thead>
                          <tbody id="display_details">
                            {{-- diisi oleh JavaScript --}}
                          </tbody>
                        </table>
                      </div>

                    </div>
                  </div>
                </div>

                {{-- ── Tanggal Pengembalian Aktual ───────────────────────────────── --}}
                <div class="mb-4">
                  <label class="form-label fw-bold">
                    Tanggal Pengembalian Aktual <span class="text-danger">*</span>
                  </label>
                  <input type="date" name="tanggal_kembali_aktual"
                         class="form-control @error('tanggal_kembali_aktual') is-invalid @enderror"
                         value="{{ old('tanggal_kembali_aktual', date('Y-m-d')) }}" required>
                  @error('tanggal_kembali_aktual')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                  <small class="text-muted">Tanggal hari ini: {{ date('d/m/Y') }}</small>
                </div>

                <button type="submit" class="btn btn-primary" id="btnSubmit" disabled>
                  <iconify-icon icon="solar:check-circle-bold" width="18" class="me-1"></iconify-icon>
                  Simpan Pengembalian
                </button>
              </form>
            @endif

          </div>
        </div>

      </div>
    </div>
  </div>

  <script src="{{ asset('template/libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('template/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('template/js/sidebarmenu.js') }}"></script>
  <script src="{{ asset('template/js/app.min.js') }}"></script>
  <script src="{{ asset('template/libs/simplebar/dist/simplebar.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

  <script>
    $(document).ready(function () {

      const kondisiLabel = {
        baik:         { text: 'Baik',        badge: 'bg-success' },
        rusak_ringan: { text: 'Rusak Ringan', badge: 'bg-warning text-dark' },
        rusak_berat:  { text: 'Rusak Berat',  badge: 'bg-danger' },
        hilang:       { text: 'Hilang',        badge: 'bg-secondary' },
      };

      function buildKondisiBadge(kondisi) {
        const k = kondisiLabel[kondisi] || { text: kondisi, badge: 'bg-light text-dark' };
        return `<span class="badge ${k.badge}">${k.text}</span>`;
      }

      function buildKondisiSelect(detailId) {
        return `
          <select name="kondisi_kembali[${detailId}]"
                  class="form-select form-select-sm kondisi-select" required>
            <option value="">-- Pilih --</option>
            <option value="baik">Baik</option>
            <option value="rusak_ringan">Rusak Ringan</option>
            <option value="rusak_berat">Rusak Berat</option>
            <option value="hilang">Hilang</option>
          </select>
        `;
      }

      function buildKeteranganInput(detailId) {
        return `
          <input type="text"
                 name="keterangan_kondisi[${detailId}]"
                 class="form-control form-control-sm"
                 placeholder="Opsional...">
        `;
      }

      // Saat pilih peminjaman berubah
      $('#peminjaman_id').on('change', function () {
        const selected = $(this).find(':selected');

        if (!selected.val()) {
          $('#detailPeminjaman').hide();
          $('#btnSubmit').prop('disabled', true);
          return;
        }

        // Isi info ringkas
        $('#display_peminjam').val(selected.data('peminjam'));
        $('#display_tanggal_pinjam').val(selected.data('tanggal-pinjam'));
        $('#display_tanggal_kembali').val(selected.data('tanggal-kembali'));
        $('#display_keperluan').val(selected.data('keperluan'));

        // Render baris tabel dengan form kondisi kembali
        const details = selected.data('details');
        let html = '';
        details.forEach((detail, index) => {
          html += `
            <tr>
              <td class="text-center">${index + 1}</td>
              <td>${detail.nama_alat}</td>
              <td class="text-center">${detail.jumlah}</td>
              <td class="text-center">${buildKondisiBadge(detail.kondisi_pinjam)}</td>
              <td>${buildKondisiSelect(detail.detail_id)}</td>
              <td>${buildKeteranganInput(detail.detail_id)}</td>
            </tr>
          `;
        });

        $('#display_details').html(html);
        $('#detailPeminjaman').show();

        checkAllKondisi();
      });

      // Pantau perubahan select kondisi (event delegation karena elemen dinamis)
      $(document).on('change', '.kondisi-select', function () {
        checkAllKondisi();
      });

      function checkAllKondisi() {
        const selects = $('.kondisi-select');
        if (selects.length === 0) {
          $('#btnSubmit').prop('disabled', true);
          return;
        }
        const allFilled = selects.toArray().every(el => el.value !== '');
        $('#btnSubmit').prop('disabled', !allFilled);
      }

    });
  </script>

</body>
</html>