<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ajukan Pengembalian - Peminjam</title>
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
            <h5 class="card-title fw-semibold mb-4">Ajukan Pengembalian Buku</h5>

            @if(session('error'))
              <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if($peminjamans->isEmpty())
              <div class="text-center py-5">
                <div class="mb-3">
                  <iconify-icon icon="solar: Smartphone-brk-outline" width="64" class="text-muted"></iconify-icon>
                </div>
                <h4 class="fw-bold">Tidak Ada Peminjaman Aktif</h4>
                <p class="text-muted">Semua buku telah dikembalikan atau Anda belum melakukan peminjaman.</p>
                <a href="{{ route('pengembalian.index') }}" class="btn btn-primary mt-3">
                  <iconify-icon icon="solar:arrow-left-outline" class="me-1"></iconify-icon> Kembali ke Daftar
                </a>
              </div>
            @else
              <form action="{{ route('pengembalian.store') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                  <label class="form-label fw-bold">Pilih Peminjaman yang Ingin Dikembalikan</label>
                  <select name="peminjaman_id" class="form-select" required id="peminjaman_select">
                    <option value="">-- Pilih Peminjaman --</option>
                    @foreach($peminjamans as $pj)
                      <option value="{{ $pj->id }}" data-details='@json($pj->details)'>
                        ID #{{ $pj->id }} - {{ \Carbon\Carbon::parse($pj->tanggal_pinjam)->format('d/m/Y') }} ({{ count($pj->details) }} Buku)
                      </option>
                    @endforeach
                  </select>
                </div>

                <div class="mb-4">
                  <label class="form-label fw-bold">Tanggal Kembali Hari Ini</label>
                  <input type="date" name="tanggal_kembali_aktual" class="form-control" value="{{ date('Y-m-d') }}" readonly>
                  <small class="text-muted">Tanggal pengajuan pengembalian diset otomatis hari ini.</small>
                </div>

                <div id="buku_list_container" style="display:none;">
                  <h6 class="fw-bold text-primary mb-3">Daftar Buku yang Akan Dikembalikan</h6>
                  <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                      <thead class="table-light">
                        <tr>
                          <th>Judul Buku</th>
                          <th class="text-center">Jumlah</th>
                          <th>Kondisi Saat Ini</th>
                        </tr>
                      </thead>
                      <tbody id="buku_list_body">
                        </tbody>
                    </table>
                  </div>

                  <div class="alert alert-warning mt-3">
                    <iconify-icon icon="solar:warning-outline" class="me-1"></iconify-icon>
                    <strong>Perhatian:</strong> Admin akan memvalidasi kondisi buku saat Anda mengembalikan buku secara fisik ke perpustakaan.
                  </div>

                  <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('pengembalian.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">Kirim Pengajuan</button>
                  </div>
                </div>
              </form>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset('template/libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('template/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#peminjaman_select').change(function() {
        const selected = $(this).find(':selected');
        const details = selected.data('details');

        if (details && details.length > 0) {
          let html = '';
          details.forEach(d => {
            html += `
              <tr>
                <td>
                  <input type="hidden" name="buku_id[]" value="${d.buku_id}">
                  <input type="hidden" name="jumlah_kembali[]" value="${d.jumlah}">
                  ${d.buku.judul_buku}
                </td>
                <td class="text-center">${d.jumlah}</td>
                <td>
                  <select name="kondisi_kembali[]" class="form-select" required>
                    <option value="baik">Baik</option>
                    <option value="rusak_ringan">Rusak Ringan</option>
                    <option value="rusak_berat">Rusak Berat</option>
                    <option value="hilang">Hilang</option>
                  </select>
                </td>
              </tr>
            `;
          });
          $('#buku_list_body').html(html);
          $('#buku_list_container').show();
        } else {
          $('#buku_list_container').hide();
        }
      });
    });
  </script>
</body>
</html>