<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Pengembalian - Admin</title>
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
            <h5 class="card-title fw-semibold mb-4">Edit Data Pengembalian</h5>

            @if(session('error'))
              <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('admin.pengembalian.update', $pengembalian->id) }}" method="POST">
              @csrf
              @method('PUT')
              
              <div class="row mb-4">
                <div class="col-md-6">
                  <div class="p-3 bg-light rounded">
                    <h6 class="fw-bold text-primary mb-3">Informasi Peminjaman</h6>
                    <table class="table table-sm table-borderless mb-0">
                      <tr><td width="150">Peminjam</td><td>: {{ $pengembalian->peminjaman->user->name }}</td></tr>
                      <tr><td>Tgl Pinjam</td><td>: {{ \Carbon\Carbon::parse($pengembalian->peminjaman->tanggal_pinjam)->format('d/m/Y') }}</td></tr>
                      <tr><td>Tgl Rencana Kembali</td><td>: <span class="text-danger fw-bold">{{ \Carbon\Carbon::parse($pengembalian->peminjaman->tanggal_kembali_rencana)->format('d/m/Y') }}</span></td></tr>
                    </table>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label fw-bold">Tanggal Kembali Aktual</label>
                    <input type="date" name="tanggal_kembali_aktual" id="tgl_aktual" class="form-control" 
                           value="{{ $pengembalian->tanggal_kembali_aktual->format('Y-m-d') }}" required onchange="hitungDendaOtomatis()">
                  </div>
                  <div id="delay_info" class="alert alert-info py-2 mb-0">
                    Terlambat: <span id="delay_days">{{ $pengembalian->keterlambatan_hari }}</span> Hari. 
                    Denda: <span id="delay_fine">Rp {{ number_format($pengembalian->denda_keterlambatan, 0, ',', '.') }}</span>
                  </div>
                </div>
              </div>

              <h6 class="fw-bold text-primary mb-3">Daftar Buku & Kondisi Kembali</h6>
              <div class="table-responsive">
                <table class="table table-bordered align-middle">
                  <thead class="table-light">
                    <tr>
                      <th>Judul Buku</th>
                      <th width="100" class="text-center">Jumlah</th>
                      <th width="200">Kondisi Kembali</th>
                      <th width="200">Denda Kerusakan (Rp)</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($pengembalian->details as $index => $detail)
                      <tr class="buku-row" data-harga="{{ $detail->buku->harga_buku ?? 0 }}">
                        <td>
                          <input type="hidden" name="detail_id[]" value="{{ $detail->id }}">
                          {{ $detail->buku->judul_buku }}
                        </td>
                        <td class="text-center">{{ $detail->jumlah_kembali }}</td>
                        <td>
                          <select name="kondisi_kembali[]" class="form-select kondisi-select" required onchange="hitungDendaOtomatis()">
                            <option value="baik" {{ $detail->kondisi_kembali == 'baik' ? 'selected' : '' }}>Baik</option>
                            <option value="rusak_ringan" {{ $detail->kondisi_kembali == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="rusak_berat" {{ $detail->kondisi_kembali == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                            <option value="hilang" {{ $detail->kondisi_kembali == 'hilang' ? 'selected' : '' }}>Hilang</option>
                          </select>
                        </td>
                        <td>
                          <input type="number" name="denda_kerusakan_buku[]" class="form-control denda-kerusakan-input" 
                                 value="{{ (int)$detail->denda_kerusakan_buku }}" min="0" oninput="hitungTotalDenda()">
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                  <tfoot class="table-light fw-bold">
                    <tr>
                      <td colspan="3" class="text-end">Total Denda Kerusakan</td>
                      <td><span id="total_damage_fine">Rp {{ number_format($pengembalian->denda_kerusakan, 0, ',', '.') }}</span></td>
                    </tr>
                    <tr class="table-primary">
                      <td colspan="3" class="text-end text-uppercase">Total Denda Akhir</td>
                      <td><span id="grand_total_fine">Rp {{ number_format($pengembalian->total_denda, 0, ',', '.') }}</span></td>
                    </tr>
                  </tfoot>
                </table>
              </div>

              <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.pengembalian.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary px-4">Update Pengembalian</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset('template/libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('template/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script>
    const dendaPerHari = {{ $dendaConfig->denda_per_hari ?? 5000 }};
    const %Ringan = {{ $dendaConfig->denda_rusak_ringan ?? 10 }};
    const %Berat = {{ $dendaConfig->denda_rusak_berat ?? 50 }};
    const tglRencana = new Date("{{ $pengembalian->peminjaman->tanggal_kembali_rencana }}");

    function hitungDendaOtomatis() {
      const tglAktual = new Date($('#tgl_aktual').val());
      let delayDays = 0;
      let delayFine = 0;

      if (tglAktual > tglRencana) {
        const diffTime = Math.abs(tglAktual - tglRencana);
        delayDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        delayFine = delayDays * dendaPerHari;
        $('#delay_info').removeClass('alert-success').addClass('alert-info').show();
      } else {
        $('#delay_info').hide();
      }

      $('#delay_days').text(delayDays);
      $('#delay_fine').text('Rp ' + delayFine.toLocaleString('id-ID'));

      // Hitung kerusakan hanya jika user mengubah kondisi? 
      // (Untuk edit, biasanya kita biarkan manual kecuali user ganti kondisi)
      // Tapi untuk kemudahan, kita ikut alur create saja.
      
      hitungTotalDenda();
    }

    function hitungTotalDenda() {
      let damageTotal = 0;
      $('.denda-kerusakan-input').each(function() {
        damageTotal += parseFloat($(this).val()) || 0;
      });

      const delayFineStr = $('#delay_fine').text().replace('Rp ', '').replace(/\./g, '');
      const delayFine = parseFloat(delayFineStr) || 0;
      const grandTotal = delayFine + damageTotal;

      $('#total_damage_fine').text('Rp ' + damageTotal.toLocaleString('id-ID'));
      $('#grand_total_fine').text('Rp ' + grandTotal.toLocaleString('id-ID'));
    }
  </script>
</body>
</html>