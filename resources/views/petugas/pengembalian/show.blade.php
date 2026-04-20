<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Validasi Pengembalian</title>
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
              <h5 class="card-title fw-semibold mb-0">Validasi Pengembalian Alat</h5>
              <a href="{{ route('petugas.pengembalian.index') }}" class="btn btn-outline-secondary">
                <iconify-icon icon="solar:arrow-left-outline" width="18" class="me-1"></iconify-icon>
                Kembali
              </a>
            </div>

            <!-- Informasi Peminjaman -->
            <div class="row mb-4">
              <div class="col-md-3">
                <label class="form-label fw-bold">Peminjam</label>
                <input type="text" class="form-control" value="{{ $pengembalian->peminjaman->user->name }}" readonly>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold">Tanggal Pinjam</label>
                <input type="text" class="form-control" 
                       value="{{ \Carbon\Carbon::parse($pengembalian->peminjaman->tanggal_pinjam)->format('d/m/Y') }}" readonly>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold">Tanggal Kembali Rencana</label>
                <input type="text" class="form-control" 
                       value="{{ \Carbon\Carbon::parse($pengembalian->peminjaman->tanggal_kembali_rencana)->format('d/m/Y') }}" readonly>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold">Tanggal Kembali Aktual</label>
                <input type="text" class="form-control" 
                       value="{{ \Carbon\Carbon::parse($pengembalian->tanggal_kembali_aktual)->format('d/m/Y') }}" readonly>
              </div>
            </div>

            <!-- Form Validasi -->
            <form action="{{ route('petugas.pengembalian.konfirmasi', $pengembalian->id) }}" method="POST" id="validasiForm">
              @csrf
              
              <h6 class="mb-3 fw-semibold">Detail Alat yang Dikembalikan</h6>
              
              <div class="table-responsive mb-4">
                <table class="table table-bordered">
                  <thead class="table-light">
                    <tr>
                      <th>No</th>
                      <th>Nama Alat</th>
                      <th>Jumlah</th>
                      <th>Kondisi Pinjam</th>
                      <th>Kondisi Kembali <span class="text-danger">*</span></th>
                      <th>Keterangan</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($pengembalian->peminjaman->details as $index => $detail)
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>{{ $detail->alat->nama_alat }}</td>
                      <td class="text-center">{{ $detail->jumlah }}</td>
                      <td>
                        @if($detail->kondisi_pinjam == 'baik')
                          <span class="badge bg-success">Baik</span>
                        @elseif($detail->kondisi_pinjam == 'rusak_ringan')
                          <span class="badge bg-warning">Rusak Ringan</span>
                        @else
                          <span class="badge bg-danger">Rusak Berat</span>
                        @endif
                      </td>
                      <td>
                        <select class="form-select kondisi-select" name="kondisi[{{ $detail->id }}]" 
                                data-harga="{{ $detail->alat->harga }}" required>
                          <option value="">Pilih Kondisi</option>
                          <option value="baik">Baik</option>
                          <option value="rusak_ringan">Rusak Ringan ({{ $denda->denda_rusak_ringan }}%)</option>
                          <option value="rusak_berat">Rusak Berat ({{ $denda->denda_rusak_berat }}%)</option>
                          <option value="hilang">Hilang ({{ $denda->persentase_penggantian_hilang }}%)</option>
                        </select>
                      </td>
                      <td>
                        <textarea class="form-control" name="keterangan[{{ $detail->id }}]" 
                                  rows="1" placeholder="Keterangan kondisi (opsional)"></textarea>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>

              <!-- Ringkasan Denda -->
              <div class="card bg-light mb-4">
                <div class="card-body">
                  <h6 class="fw-semibold mb-3">Ringkasan Denda</h6>
                  <div class="row">
                    <div class="col-md-4">
                      <label class="form-label">Keterlambatan</label>
                      <input type="text" class="form-control" id="display_keterlambatan" readonly>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Denda Keterlambatan</label>
                      <input type="text" class="form-control" id="display_denda_keterlambatan" readonly>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Denda Kerusakan</label>
                      <input type="text" class="form-control" id="display_denda_kerusakan" readonly>
                    </div>
                  </div>
                  <div class="row mt-3">
                    <div class="col-md-12">
                      <label class="form-label fw-bold">Total Denda</label>
                      <input type="text" class="form-control form-control-lg fw-bold text-danger" 
                             id="display_total_denda" readonly>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Status Pembayaran -->
              <div class="mb-4" id="pembayaran_section" style="display:none;">
                <label class="form-label fw-bold">Status Pembayaran Denda <span class="text-danger">*</span></label>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="status_pembayaran" 
                         id="lunas" value="lunas">
                  <label class="form-check-label" for="lunas">
                    <span class="badge bg-success">Lunas</span> - Peminjam sudah membayar denda
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="status_pembayaran" 
                         id="belum_lunas" value="belum_lunas">
                  <label class="form-check-label" for="belum_lunas">
                    <span class="badge bg-danger">Belum Lunas</span> - Peminjam belum membayar denda
                  </label>
                </div>
              </div>

              <input type="hidden" name="status_pembayaran" id="hidden_status_pembayaran" value="lunas">

              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success" id="btnKonfirmasi">
                  <iconify-icon icon="solar:check-circle-bold" width="18" class="me-1"></iconify-icon>
                  Konfirmasi Pengembalian
                </button>
                <a href="{{ route('petugas.pengembalian.index') }}" class="btn btn-secondary">Batal</a>
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
  <script src="{{ asset ('template/libs/simplebar/dist/simplebar.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    $(document).ready(function() {
      const dendaPerHari = {{ $denda->denda_per_hari }};
      const dendaRusakRingan = {{ $denda->denda_rusak_ringan }};
      const dendaRusakBerat = {{ $denda->denda_rusak_berat }};
      const persentaseHilang = {{ $denda->persentase_penggantian_hilang }};

      const tanggalKembaliRencana = new Date('{{ $pengembalian->peminjaman->tanggal_kembali_rencana }}');
      const tanggalKembaliAktual = new Date('{{ $pengembalian->tanggal_kembali_aktual }}');
      
      // Hitung keterlambatan
      const diffTime = tanggalKembaliAktual - tanggalKembaliRencana;
      const keterlambatan = Math.max(0, Math.ceil(diffTime / (1000 * 60 * 60 * 24)));
      const dendaKeterlambatan = keterlambatan * dendaPerHari;

      $('#display_keterlambatan').val(keterlambatan + ' hari');
      $('#display_denda_keterlambatan').val('Rp ' + dendaKeterlambatan.toLocaleString('id-ID'));

      function hitungDenda() {
        let totalDendaKerusakan = 0;

        $('.kondisi-select').each(function() {
          const kondisi = $(this).val();
          const harga = parseFloat($(this).data('harga'));

          if (kondisi === 'rusak_ringan') {
            totalDendaKerusakan += (harga * dendaRusakRingan / 100);
          } else if (kondisi === 'rusak_berat') {
            totalDendaKerusakan += (harga * dendaRusakBerat / 100);
          } else if (kondisi === 'hilang') {
            totalDendaKerusakan += (harga * persentaseHilang / 100);
          }
        });

        const totalDenda = dendaKeterlambatan + totalDendaKerusakan;

        $('#display_denda_kerusakan').val('Rp ' + totalDendaKerusakan.toLocaleString('id-ID'));
        $('#display_total_denda').val('Rp ' + totalDenda.toLocaleString('id-ID'));

        // Tampilkan/sembunyikan section pembayaran
        if (totalDenda > 0) {
          $('#pembayaran_section').show();
          $('input[name="status_pembayaran"]').prop('required', true);
          $('#hidden_status_pembayaran').remove();
        } else {
          $('#pembayaran_section').hide();
          $('input[name="status_pembayaran"]').prop('required', false);
          if ($('#hidden_status_pembayaran').length === 0) {
            $('<input>').attr({
              type: 'hidden',
              id: 'hidden_status_pembayaran',
              name: 'status_pembayaran',
              value: 'lunas'
            }).appendTo('#validasiForm');
          }
        }
      }

      $('.kondisi-select').on('change', hitungDenda);
      hitungDenda();

      $('#validasiForm').on('submit', function(e) {
        e.preventDefault();
        const form = this;

        const totalDenda = parseInt($('#display_total_denda').val().replace(/[^0-9]/g, ''));
        let confirmText = 'Konfirmasi pengembalian alat ini?';
        
        if (totalDenda > 0) {
          const statusPembayaran = $('input[name="status_pembayaran"]:checked').val();
          if (!statusPembayaran) {
            Swal.fire('Perhatian!', 'Pilih status pembayaran denda terlebih dahulu!', 'warning');
            return;
          }
          
          if (statusPembayaran === 'lunas') {
            confirmText = `Total denda: Rp ${totalDenda.toLocaleString('id-ID')}<br>Status: <strong>LUNAS</strong><br><br>Konfirmasi pengembalian?`;
          } else {
            confirmText = `Total denda: Rp ${totalDenda.toLocaleString('id-ID')}<br>Status: <strong>BELUM LUNAS</strong><br><br>Konfirmasi pengembalian?`;
          }
        }

        Swal.fire({
          title: 'Konfirmasi Pengembalian',
          html: confirmText,
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#28a745',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Ya, Konfirmasi',
          cancelButtonText: 'Batal'
        }).then((result) => {
          if (result.isConfirmed) {
            form.submit();
          }
        });
      });
    });
  </script>

</body>
</html>