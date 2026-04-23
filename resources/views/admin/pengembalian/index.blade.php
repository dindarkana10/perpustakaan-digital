<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manajemen Pengembalian - Perpustakaan Digital</title>
  <link rel="stylesheet" href="{{ asset('template/css/styles.min.css') }}" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
  <style>
    /* ✅ Perbaikan agar modal tidak 'tenggelam' di layar 100% */
    #modalKonfirmasi .modal-body {
        max-height: calc(100vh - 210px); /* Membatasi tinggi body modal */
        overflow-y: auto; /* Scroll aktif di dalam body saja */
    }
    .table-xs th, .table-xs td {
        padding: 0.4rem; /* Tabel lebih rapat */
        font-size: 0.85rem;
    }
  </style>
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
              <h5 class="card-title fw-semibold">Daftar Pengembalian Buku</h5>
              <a href="{{ route('admin.pengembalian.create') }}" class="btn btn-primary">
                <iconify-icon icon="solar:add-circle-outline" width="18" class="me-1"></iconify-icon>
                Input Pengembalian
              </a>
            </div>

            @if (session('success'))
              <div class="alert alert-success alert-dismissible fade show">
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

            <div class="table-responsive">
              <table id="pengembalianTable" class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                  <tr>
                    <th>No</th>
                    <th>Peminjam</th>
                    <th>Tgl Kembali Aktual</th>
                    <th>Terlambat</th>
                    <th>Status Pengembalian</th>
                    <th>Pembayaran</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($pengembalians as $item)
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>
                        <strong>{{ $item->peminjaman->user->name ?? ($item->user->name ?? 'User Deleted') }}</strong>
                        <br><small class="text-muted">#{{ $item->user->NISN ?? '-' }} - {{ $item->user->kelas_jurusan ?? '-' }}</small>
                      </td>
                      <td>{{ \Carbon\Carbon::parse($item->tanggal_kembali_aktual)->format('d/m/Y') }}</td>
                      <td>
                        @if($item->status_pengembalian === 'diajukan')
                          <span class="text-muted">—</span>
                        @else
                          {{ $item->keterlambatan_hari }} Hari
                        @endif
                      </td>
                      <td>
                        @if($item->status_pengembalian === 'diajukan')
                          <span class="badge bg-warning text-dark">Diajukan Peminjam</span>
                        @else
                          <span class="badge bg-success">Dikonfirmasi</span>
                        @endif
                      </td>
                      <td>
                        @if($item->status_pengembalian === 'diajukan')
                          <span class="text-muted">—</span>
                        @elseif($item->status_pembayaran === 'lunas')
                          <span class="badge bg-success">Lunas</span>
                        @elseif($item->status_pembayaran === 'tidak_ada_denda')
                          <span class="badge bg-light text-dark border">Tidak Ada Denda</span>
                        @else
                          <span class="badge bg-danger">Belum Lunas</span>
                        @endif
                      </td>
                      <td>
                        <div class="d-flex gap-1 flex-wrap">
                          <a href="{{ route('admin.pengembalian.show', $item->id) }}"
                             class="btn btn-sm btn-info" title="Detail">
                            <iconify-icon icon="solar:eye-outline" width="18"></iconify-icon>
                          </a>

                          @if($item->status_pengembalian === 'diajukan')
                            <button type="button"
                                    class="btn btn-sm btn-success btn-konfirmasi"
                                    data-url="{{ route('admin.pengembalian.preview-konfirmasi', $item->id) }}"
                                    data-action="{{ route('admin.pengembalian.konfirmasi', $item->id) }}"
                                    title="Konfirmasi & Validasi Denda">
                              <iconify-icon icon="solar:check-circle-outline" width="18"></iconify-icon>
                            </button>
                          @endif

                          @if($item->status_pengembalian === 'dikonfirmasi' && $item->status_pembayaran === 'belum_lunas')
                            <form action="{{ route('admin.pengembalian.lunasi', $item->id) }}"
                                  method="POST" class="d-inline lunasiForm">
                              @csrf
                              <button type="submit" class="btn btn-sm btn-primary" title="Lunasi Denda">
                                <iconify-icon icon="solar:wallet-money-outline" width="18"></iconify-icon>
                              </button>
                            </form>
                          @endif

                          <form action="{{ route('admin.pengembalian.destroy', $item->id) }}"
                                method="POST" class="d-inline deleteForm">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                              <iconify-icon icon="solar:trash-bin-trash-outline" width="18"></iconify-icon>
                            </button>
                          </form>
                        </div>
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

  {{-- ✅ FIX: Modal menggunakan modal-dialog-scrollable agar footer tetap stay --}}
  <div class="modal fade" id="modalKonfirmasi" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-success text-white py-3">
          <h5 class="modal-title d-flex align-items-center text-white">
            <iconify-icon icon="solar:check-circle-outline" width="22" class="me-2"></iconify-icon>
            Konfirmasi & Validasi Pengembalian Buku
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <form id="formKonfirmasi" method="POST">
          @csrf
          <div class="modal-body p-4" id="modalKonfirmasiBody">
            <div class="text-center py-5">
              <div class="spinner-border text-success" role="status"></div>
              <p class="mt-2 text-muted">Memuat data pengembalian...</p>
            </div>
          </div>
          <div class="modal-footer bg-light py-2">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-success px-4 fw-bold">
              <iconify-icon icon="solar:check-circle-outline" width="18" class="me-1"></iconify-icon>
              Konfirmasi Sekarang
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="{{ asset('template/libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('template/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    function formatRp(val) {
      return 'Rp ' + Number(val).toLocaleString('id-ID');
    }

    function hitungUlangModal() {
      let totalKerusakan = 0;
      document.querySelectorAll('.modal-denda-input').forEach(function(input) {
        totalKerusakan += parseFloat(input.value) || 0;
      });

      const dendaKeterlambatan = parseFloat(document.getElementById('modal_denda_keterlambatan_val').value) || 0;
      const grandTotal = dendaKeterlambatan + totalKerusakan;

      document.getElementById('modal_total_kerusakan').textContent  = formatRp(totalKerusakan);
      document.getElementById('modal_grand_total').textContent       = formatRp(grandTotal);
      document.getElementById('modal_grand_total').className         = grandTotal > 0
        ? 'fw-bold text-danger fs-5'
        : 'fw-bold text-success fs-5';
    }

    function hitungDendaOtomatisBaris(selectEl) {
      const row        = selectEl.closest('tr');
      const kondisi    = selectEl.value;
      const harga      = parseFloat(row.dataset.harga) || 0;
      const jumlah     = parseInt(row.dataset.jumlah) || 1;
      const persenRingan = parseFloat(row.dataset.persenRingan) || 10;
      const persenBerat  = parseFloat(row.dataset.persenBerat) || 50;
      const persenHilang = parseFloat(row.dataset.persenHilang) || 100;
      const inputDenda = row.querySelector('.modal-denda-input');

      let dendaOtomatis = 0;
      if (kondisi === 'rusak_ringan')     dendaOtomatis = harga * jumlah * (persenRingan / 100);
      else if (kondisi === 'rusak_berat') dendaOtomatis = harga * jumlah * (persenBerat / 100);
      else if (kondisi === 'hilang')       dendaOtomatis = harga * jumlah * (persenHilang / 100);
      else                                dendaOtomatis = 0;

      inputDenda.value = Math.round(dendaOtomatis);
      hitungUlangModal();
    }

    $(document).ready(function () {
      $('#pengembalianTable').DataTable({
        language: { url: "//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json" }
      });

      $(document).on('click', '.btn-konfirmasi', function () {
        const previewUrl = $(this).data('url');
        const actionUrl  = $(this).data('action');

        $('#formKonfirmasi').attr('action', actionUrl);
        $('#modalKonfirmasiBody').html(`
          <div class="text-center py-5">
            <div class="spinner-border text-success" role="status"></div>
            <p class="mt-2 text-muted">Memuat data pengembalian...</p>
          </div>`);

        new bootstrap.Modal(document.getElementById('modalKonfirmasi')).show();

        $.ajax({
          url: previewUrl,
          method: 'GET',
          success: function (data) {
            let detailRows = '';
            data.details.forEach((d, index) => {
              detailRows += `
                <tr data-harga="${d.harga_buku}"
                    data-jumlah="${d.jumlah}"
                    data-persen-ringan="${data.persen_ringan}"
                    data-persen-berat="${data.persen_berat}"
                    data-persen-hilang="${data.persen_hilang}">
                  <td>
                    <input type="hidden" name="detail_id[]" value="${d.detail_id}">
                    <div class="fw-bold text-dark">${d.judul_buku}</div>
                    <small class="text-muted">Harga: ${formatRp(d.harga_buku)}</small>
                  </td>
                  <td class="text-center">${d.jumlah}</td>
                  <td>
                    <select name="kondisi_kembali[]"
                            class="form-select form-select-sm modal-kondisi-select"
                            onchange="hitungDendaOtomatisBaris(this)">
                      <option value="baik" ${d.kondisi_kembali === 'baik' ? 'selected' : ''}>Baik</option>
                      <option value="rusak_ringan" ${d.kondisi_kembali === 'rusak_ringan' ? 'selected' : ''}>Rusak Ringan (${data.persen_ringan}%)</option>
                      <option value="rusak_berat" ${d.kondisi_kembali === 'rusak_berat' ? 'selected' : ''}>Rusak Berat (${data.persen_berat}%)</option>
                      <option value="hilang" ${d.kondisi_kembali === 'hilang' ? 'selected' : ''}>Hilang (${data.persen_hilang}%)</option>
                    </select>
                  </td>
                  <td>
                    <div class="input-group input-group-sm">
                      <span class="input-group-text">Rp</span>
                      <input type="number" name="denda_kerusakan_buku[]"
                             class="form-control modal-denda-input"
                             value="${Math.round(d.denda_kerusakan_buku)}"
                             oninput="hitungUlangModal()">
                    </div>
                    <small><a href="javascript:void(0)" class="text-primary" onclick="hitungDendaOtomatisBaris(this.closest('tr').querySelector('.modal-kondisi-select'))">Hitung Otomatis</a></small>
                  </td>
                </tr>`;
            });

            const html = `
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <div class="p-3 border rounded bg-light h-100">
                    <h6 class="fw-bold text-primary mb-3"><iconify-icon icon="solar:user-outline"></iconify-icon> Informasi Peminjam</h6>
                    <div class="d-flex justify-content-between mb-1"><span>Nama</span><span class="fw-bold">${data.nama_peminjam}</span></div>
                    <div class="d-flex justify-content-between mb-1"><span>NISN</span><span>${data.nisn}</span></div>
                    <div class="d-flex justify-content-between"><span>Kelas</span><span>${data.kelas_jurusan}</span></div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="p-3 border rounded bg-light h-100">
                    <h6 class="fw-bold text-primary mb-3"><iconify-icon icon="solar:calendar-outline"></iconify-icon> Waktu Pengembalian</h6>
                    <div class="d-flex justify-content-between mb-1"><span>Rencana Kembali</span><span class="text-danger fw-bold">${data.tanggal_rencana_kembali}</span></div>
                    <div class="d-flex justify-content-between mb-1"><span>Kembali Aktual</span><span class="fw-bold">${data.tanggal_kembali_aktual}</span></div>
                    <div class="d-flex justify-content-between"><span>Keterlambatan</span><span class="badge ${data.keterlambatan_hari > 0 ? 'bg-danger' : 'bg-success'}">${data.keterlambatan_hari} Hari</span></div>
                  </div>
                </div>
              </div>

              <input type="hidden" id="modal_denda_keterlambatan_val" value="${data.denda_keterlambatan}">

              <h6 class="fw-bold text-primary mb-3"><iconify-icon icon="solar:book-outline"></iconify-icon> Validasi Kondisi & Denda Buku</h6>
              <div class="table-responsive border rounded mb-4">
                <table class="table table-hover table-xs align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Judul Buku</th>
                      <th class="text-center">Jml</th>
                      <th width="200">Kondisi</th>
                      <th width="200">Denda</th>
                    </tr>
                  </thead>
                  <tbody>${detailRows}</tbody>
                </table>
              </div>

              <div class="p-3 rounded border bg-light">
                <div class="d-flex justify-content-between mb-2">
                  <span class="text-muted">Denda Keterlambatan (${data.keterlambatan_hari} hari)</span>
                  <span class="fw-semibold text-danger">${formatRp(data.denda_keterlambatan)}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                  <span class="text-muted">Total Denda Kerusakan</span>
                  <span id="modal_total_kerusakan" class="fw-semibold text-danger">${formatRp(data.denda_kerusakan)}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                  <span class="fw-bold fs-6">TOTAL HARUS DIBAYAR</span>
                  <span id="modal_grand_total" class="fw-bold fs-5 ${data.total_denda > 0 ? 'text-danger' : 'text-success'}">${formatRp(data.total_denda)}</span>
                </div>
              </div>`;

            $('#modalKonfirmasiBody').html(html);
          },
          error: function () {
            $('#modalKonfirmasiBody').html(`<div class="alert alert-danger m-3">Gagal memuat data.</div>`);
          }
        });
      });
      
      // Sweetalert forms... (lunasiForm & deleteForm sama seperti sebelumnya)
      $(document).on('submit', '.lunasiForm', function (e) {
        e.preventDefault();
        let form = this;
        Swal.fire({
          title: 'Lunasi Denda?',
          text: "Konfirmasi bahwa denda telah dibayar secara tunai.",
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#28a745',
          confirmButtonText: 'Ya, Lunasi!'
        }).then(result => { if (result.isConfirmed) form.submit(); });
      });

      $(document).on('submit', '.deleteForm', function (e) {
        e.preventDefault();
        let form = this;
        Swal.fire({
          title: 'Hapus Data?',
          text: "Status peminjaman akan dikembalikan ke 'Dipinjam'!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          confirmButtonText: 'Ya, Hapus!'
        }).then(result => { if (result.isConfirmed) form.submit(); });
      });

    /* ================= AUTO HIDE ALERT ================= */
    setTimeout(() => $('.alert').fadeOut(), 3000);

    });
  </script>
</body>
</html>