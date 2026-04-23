<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Input Pengembalian - Admin</title>
  <link rel="stylesheet" href="{{ asset('template/css/styles.min.css') }}" />
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
            <h5 class="card-title fw-semibold mb-4">Input Pengembalian Buku</h5>

            @if(session('error'))
              <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- FORM PILIH PEMINJAMAN --}}
            <form action="{{ route('admin.pengembalian.create') }}" method="GET" id="selectPeminjamanForm">
              <div class="mb-4">
                <label class="form-label fw-bold">Pilih Data Peminjaman (Status Dipinjam)</label>
                <select name="peminjaman_id" class="form-select select2" onchange="this.form.submit()">
                  <option value="">-- Cari Nama Peminjam atau ID --</option>
                  @foreach($peminjamans as $pj)
                    <option value="{{ $pj->id }}" {{ request('peminjaman_id') == $pj->id ? 'selected' : '' }}>
                      #{{ $pj->id }} - {{ $pj->user->name }} ({{ count($pj->details) }} Buku)
                    </option>
                  @endforeach
                </select>
              </div>
            </form>

            @if($selectedPeminjaman)
              {{-- 
                TIDAK ADA @php BLOCK DI SINI.
                Semua variabel ($dendaPerHari, $persenRingan, dll.) 
                sudah di-pass dari controller via compact().
              --}}

              <form action="{{ route('admin.pengembalian.store') }}" method="POST">
                @csrf
                <input type="hidden" name="peminjaman_id" value="{{ $selectedPeminjaman->id }}">

                <div class="row mb-4">
                  <div class="col-md-6">
                    <div class="p-3 bg-light rounded">
                      <h6 class="fw-bold text-primary mb-3">Informasi Peminjaman</h6>
                      <table class="table table-sm table-borderless mb-0">
                        <tr><td width="170">Peminjam</td><td>: <strong>{{ $selectedPeminjaman->user->name }}</strong></td></tr>
                        <tr><td>Tgl Pinjam</td><td>: {{ \Carbon\Carbon::parse($selectedPeminjaman->tanggal_pinjam)->format('d/m/Y') }}</td></tr>
                        <tr><td>Tgl Rencana Kembali</td><td>: <span class="text-danger fw-bold">{{ \Carbon\Carbon::parse($tglRencana)->format('d/m/Y') }}</span></td></tr>
                      </table>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label fw-bold">Tanggal Kembali Aktual <span class="text-danger">*</span></label>
                      <input type="date" name="tanggal_kembali_aktual" id="tgl_aktual"
                             class="form-control"
                             value="{{ date('Y-m-d') }}"
                             required
                             onchange="hitungDendaOtomatis()">
                    </div>

                    {{-- CARD DENDA KETERLAMBATAN --}}
                    <div class="card border-warning mb-0">
                      <div class="card-body py-2 px-3">
                        <div class="d-flex justify-content-between align-items-center">
                          <span class="fw-semibold text-warning-emphasis">
                            <i class="ti ti-clock-exclamation me-1"></i> Denda Keterlambatan
                          </span>
                          <span id="delay_fine_display" class="fw-bold text-muted">Rp 0</span>
                        </div>
                        <small class="text-muted" id="delay_days_info">Tidak terlambat</small>
                      </div>
                    </div>
                  </div>
                </div>

                <h6 class="fw-bold text-primary mb-3">Daftar Buku & Kondisi Kembali</h6>
                <div class="table-responsive">
                  <table class="table table-bordered align-middle">
                    <thead class="table-light">
                      <tr>
                        <th>Judul Buku</th>
                        <th width="90" class="text-center">Jumlah</th>
                        <th width="200">Kondisi Kembali</th>
                        <th width="210">Denda Kerusakan (Rp)</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($selectedPeminjaman->details as $index => $detail)
                        <tr class="buku-row"
                            data-harga="{{ $detail->buku->harga_buku ?? 0 }}"
                            data-jumlah="{{ $detail->jumlah }}">
                          <td>
                            <input type="hidden" name="buku_id[]" value="{{ $detail->buku_id }}">
                            <input type="hidden" name="jumlah_kembali[]" value="{{ $detail->jumlah }}">
                            {{ $detail->buku->judul_buku }}
                            <br><small class="text-muted">Harga: Rp {{ number_format($detail->buku->harga_buku ?? 0, 0, ',', '.') }}</small>
                          </td>
                          <td class="text-center">{{ $detail->jumlah }}</td>
                          <td>
                            <select name="kondisi_kembali[]" class="form-select kondisi-select"
                                    required onchange="hitungDendaOtomatis()">
                              <option value="baik">Baik</option>
                              <option value="rusak_ringan">Rusak Ringan ({{ $persenRingan }}%)</option>
                              <option value="rusak_berat">Rusak Berat ({{ $persenBerat }}%)</option>
                              <option value="hilang">Hilang ({{ $persenHilang }}%)</option>
                            </select>
                          </td>
                          <td>
                            <input type="number" name="denda_kerusakan_buku[]"
                                   class="form-control denda-kerusakan-input"
                                   value="0" min="0"
                                   oninput="hitungTotalDenda()">
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold">
                      <tr>
                        <td colspan="3" class="text-end">Denda Keterlambatan</td>
                        <td><span id="foot_delay_fine">Rp 0</span></td>
                      </tr>
                      <tr>
                        <td colspan="3" class="text-end">Total Denda Kerusakan</td>
                        <td><span id="total_damage_fine">Rp 0</span></td>
                      </tr>
                      <tr class="table-primary">
                        <td colspan="3" class="text-end text-uppercase">Total Denda Akhir</td>
                        <td><span id="grand_total_fine" class="fs-5">Rp 0</span></td>
                      </tr>
                    </tfoot>
                  </table>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                  <a href="{{ route('admin.pengembalian.index') }}" class="btn btn-secondary">Batal</a>
                  <button type="submit" class="btn btn-primary px-4">
                    <iconify-icon icon="solar:check-circle-outline" width="18" class="me-1"></iconify-icon>
                    Simpan Pengembalian
                  </button>
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
  <script src="{{ asset('template/js/app.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

  <script>
    {{-- 
      Variabel JS diisi dari PHP yang sudah di-pass controller.
      Gunakan $tglRencana (string kosong '') jika belum ada peminjaman dipilih.
    --}}
    const dendaPerHari = {{ $dendaPerHari }};
    const persenRingan = {{ $persenRingan }};
    const persenBerat  = {{ $persenBerat }};
    const persenHilang = {{ $persenHilang }};
    const tglRencana   = "{{ $tglRencana }}" ? new Date("{{ $tglRencana }}T00:00:00") : null;

    $(document).ready(function() {
      $('.select2').select2({ theme: 'bootstrap-5' });

      // Hitung otomatis saat halaman load (hanya jika ada peminjaman terpilih)
      @if($selectedPeminjaman)
        hitungDendaOtomatis();
      @endif
    });

    function hitungDendaOtomatis() {
      const tglAktualVal = $('#tgl_aktual').val();
      let delayDays = 0;
      let delayFine = 0;

      if (tglAktualVal && tglRencana) {
        const tglAktual = new Date(tglAktualVal + 'T00:00:00');

        if (tglAktual > tglRencana) {
          const diffMs = tglAktual - tglRencana;
          delayDays    = Math.ceil(diffMs / (1000 * 60 * 60 * 24));
          delayFine    = delayDays * dendaPerHari;

          $('#delay_days_info').text('Terlambat ' + delayDays + ' hari × Rp ' + dendaPerHari.toLocaleString('id-ID'));
          $('#delay_fine_display').removeClass('text-muted').addClass('text-danger fw-bold');
        } else {
          $('#delay_days_info').text('Tidak terlambat');
          $('#delay_fine_display').removeClass('text-danger fw-bold').addClass('text-muted');
        }
      }

      $('#delay_fine_display').text('Rp ' + delayFine.toLocaleString('id-ID'));
      $('#foot_delay_fine').text('Rp ' + delayFine.toLocaleString('id-ID'));

      window._delayFine = delayFine;

      // Hitung denda kerusakan per buku
      $('.buku-row').each(function() {
        const harga   = parseFloat($(this).data('harga')) || 0;
        const jumlah  = parseInt($(this).data('jumlah')) || 1;
        const kondisi = $(this).find('.kondisi-select').val();
        let fine = 0;

        if (kondisi === 'rusak_ringan')      fine = harga * jumlah * (persenRingan / 100);
        else if (kondisi === 'rusak_berat')  fine = harga * jumlah * (persenBerat / 100);
        else if (kondisi === 'hilang')       fine = harga * jumlah * (persenHilang / 100);

        $(this).find('.denda-kerusakan-input').val(Math.round(fine));
      });

      hitungTotalDenda();
    }

    function hitungTotalDenda() {
      let damageTotal = 0;
      $('.denda-kerusakan-input').each(function() {
        damageTotal += parseFloat($(this).val()) || 0;
      });

      const delayFine  = window._delayFine || 0;
      const grandTotal = delayFine + damageTotal;

      $('#total_damage_fine').text('Rp ' + damageTotal.toLocaleString('id-ID'));
      $('#grand_total_fine').text('Rp ' + grandTotal.toLocaleString('id-ID'));
    }
  </script>
</body>
</html>