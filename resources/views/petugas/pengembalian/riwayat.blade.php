<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Petugas - Riwayat Pengembalian</title>
  <link rel="stylesheet" href="{{ asset('template/css/styles.min.css') }}" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
  <style>
    /* ── Tombol aksi icon ── */
    .btn-icon {
      width: 34px; height: 34px;
      display: inline-flex; align-items: center; justify-content: center;
      border-radius: 8px; border: none; cursor: pointer;
      transition: transform 0.15s, box-shadow 0.15s;
      font-size: 16px;
    }
    .btn-icon:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.15); }
    .btn-icon-detail { background: #e8f4fd; color: #0d6efd; }
    .btn-icon-detail:hover { background: #0d6efd; color: #fff; }
    .btn-icon-struk  { background: #e8f7ef; color: #198754; }
    .btn-icon-struk:hover  { background: #198754; color: #fff; }

    /* ── Modal Detail ── */
    .detail-info-card {
      background: #f8faff;
      border: 1px solid #dde8ff;
      border-radius: 12px;
      padding: 16px;
      margin-bottom: 16px;
    }
    .detail-info-card .info-label {
      font-size: 11px; color: #6c757d; margin-bottom: 2px; text-transform: uppercase; letter-spacing: .4px;
    }
    .detail-info-card .info-value {
      font-size: 14px; font-weight: 600; color: #212529;
    }
    .denda-summary {
      background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
      border: 1px solid #dee2e6; border-radius: 10px; padding: 14px;
    }
    .denda-total-row {
      background: #fff8f0; border-radius: 8px; padding: 10px 14px; margin-top: 10px;
    }

    /* ── Modal Struk (nota style) ── */
    .struk-wrap {
      max-width: 380px; margin: 0 auto;
      font-family: 'Courier New', Courier, monospace;
    }
    .struk-header {
      text-align: center; padding-bottom: 14px;
      border-bottom: 2px dashed #4e73df; margin-bottom: 14px;
    }
    .struk-header h4 { color: #4e73df; font-size: 17px; letter-spacing: 2px; margin-bottom: 2px; }
    .struk-header .struk-sub { font-size: 11px; color: #888; }
    .struk-header .struk-no { font-size: 13px; font-weight: 700; color: #333; margin-top: 6px; }

    .struk-section-title {
      font-size: 10px; font-weight: 700; color: #4e73df;
      text-transform: uppercase; letter-spacing: .5px;
      border-bottom: 1px solid #d0dcff; padding-bottom: 4px; margin: 12px 0 6px;
    }
    .struk-row { display: flex; justify-content: space-between; font-size: 11.5px; padding: 2px 0; }
    .struk-row .s-label { color: #666; }
    .struk-row .s-value { font-weight: 600; color: #222; }

    .struk-divider { border: none; border-top: 1px dashed #ccc; margin: 10px 0; }

    .struk-table { width: 100%; font-size: 10.5px; border-collapse: collapse; }
    .struk-table th {
      background: #4e73df; color: #fff; padding: 5px 6px; text-align: left; font-weight: 600;
    }
    .struk-table td { padding: 5px 6px; border-bottom: 1px solid #f0f0f0; }
    .struk-table tr:nth-child(even) td { background: #f7f9ff; }

    .struk-summary { background: #f7f9ff; border: 1px solid #d0dcff; border-radius: 8px; padding: 12px; }
    .struk-summary-row { display: flex; justify-content: space-between; font-size: 11.5px; padding: 3px 0; }
    .struk-summary-row.s-total {
      border-top: 2px solid #4e73df; margin-top: 6px; padding-top: 8px;
      font-weight: 700; font-size: 14px; color: #4e73df;
    }

    .struk-status { text-align: center; margin: 12px 0 6px; }
    .struk-status-badge {
      display: inline-block; padding: 5px 18px; border-radius: 20px;
      font-size: 11px; font-weight: 700; letter-spacing: .5px;
    }
    .badge-lunas { background: #d4edda; color: #155724; border: 1.5px solid #c3e6cb; }
    .badge-bebas { background: #cce5ff; color: #004085; border: 1.5px solid #b8daff; }

    .struk-footer {
      text-align: center; margin-top: 14px; padding-top: 10px;
      border-top: 1px dashed #ccc; color: #bbb; font-size: 9.5px; line-height: 1.7;
    }

    .spinner-overlay {
      display: flex; flex-direction: column; align-items: center;
      justify-content: center; padding: 40px 0; color: #6c757d;
    }

    /* kondisi badge */
    .k-baik    { background:#d4edda; color:#155724; }
    .k-ringan  { background:#fff3cd; color:#856404; }
    .k-berat   { background:#f8d7da; color:#721c24; }
    .k-hilang  { background:#e2e3e5; color:#383d41; }
    .k-badge   { padding:2px 7px; border-radius:10px; font-size:10px; font-weight:600; display:inline-block; }
  </style>
</head>
<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6"
    data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">

    <x-navbar></x-navbar>
    <x-sidebar></x-sidebar>

    <div class="body-wrapper">
      <div class="container-fluid">
        <div class="card shadow-sm">
          <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="card-title fw-semibold mb-0">Riwayat Pengembalian</h5>
            </div>

            @if(session('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            @endif

            <div class="table-responsive">
              <table id="riwayatTable" class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th>No</th>
                    <th>Peminjam</th>
                    <th>Tanggal Kembali</th>
                    <th>Total Denda</th>
                    <th class="text-center">Status Bayar</th>
                    <th class="text-center">Status Kembali</th>
                    <th class="text-center" style="width:90px">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($pengembalians as $item)
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>{{ $item->peminjaman->user->name }}</td>
                      <td>{{ $item->tanggal_kembali_aktual->format('d-m-Y') }}</td>
                      <td class="{{ $item->total_denda > 0 ? 'text-danger fw-semibold' : 'text-success' }}">
                        Rp {{ number_format($item->total_denda, 0, ',', '.') }}
                      </td>
                      <td class="text-center">
                        <span class="badge bg-success">Lunas</span>
                      </td>
                      <td class="text-center">
                        <span class="badge bg-success">Dikonfirmasi</span>
                      </td>
                      <td class="text-center">
                        {{-- Tombol Detail --}}
                        <button class="btn-icon btn-icon-detail me-1 btn-open-detail"
                          data-id="{{ $item->id }}"
                          title="Lihat Detail"
                          data-bs-toggle="modal" data-bs-target="#modalDetail">
                          <i class="ti ti-eye"></i>
                        </button>
                        {{-- Tombol Struk --}}
                        <button class="btn-icon btn-icon-struk btn-open-struk"
                          data-id="{{ $item->id }}"
                          title="Lihat Struk"
                          data-bs-toggle="modal" data-bs-target="#modalStruk">
                          <i class="ti ti-receipt"></i>
                        </button>
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

  {{-- ================================================================ --}}
  {{-- MODAL 1: DETAIL PENGEMBALIAN --}}
  {{-- ================================================================ --}}
  <div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content border-0 shadow">
        <div class="modal-header" style="background:linear-gradient(135deg,#0d6efd,#6ea8fe); color:#fff;">
          <h5 class="modal-title fw-semibold">
            <i class="ti ti-clipboard-list me-2"></i>Detail Pengembalian
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4" id="bodyDetail">
          <div class="spinner-overlay">
            <div class="spinner-border text-primary mb-2"></div>
            <small>Memuat data...</small>
          </div>
        </div>
        <div class="modal-footer border-0">
          <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  {{-- ================================================================ --}}
  {{-- MODAL 2: STRUK PENGEMBALIAN --}}
  {{-- ================================================================ --}}
  <div class="modal fade" id="modalStruk" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable" style="max-width:460px">
      <div class="modal-content border-0 shadow">
        <div class="modal-header" style="background:linear-gradient(135deg,#198754,#6dd08c); color:#fff;">
          <h5 class="modal-title fw-semibold">
            <i class="ti ti-receipt me-2"></i>Preview Struk
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-3 bg-light" id="bodyStruk">
          <div class="spinner-overlay">
            <div class="spinner-border text-success mb-2"></div>
            <small>Memuat struk...</small>
          </div>
        </div>
        <div class="modal-footer border-0">
          <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
          <a href="#" id="btnDownload" class="btn btn-primary btn-sm" target="_blank">
            <i class="ti ti-download me-1"></i>Download PDF
          </a>
          <button type="button" id="btnKirim" class="btn btn-success btn-sm" data-id="">
            <i class="ti ti-send me-1"></i>Kirim ke Peminjam
          </button>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset('template/libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('template/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('template/js/sidebarmenu.js') }}"></script>
  <script src="{{ asset('template/js/app.min.js') }}"></script>
  <script src="{{ asset('template/libs/simplebar/dist/simplebar.js') }}"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    const BASE_URL   = "{{ url('petugas/pengembalian') }}";
    const CSRF_TOKEN = "{{ csrf_token() }}";

    // ── DataTable ─────────────────────────────────────────────────────────
    $(document).ready(function () {
      $('#riwayatTable').DataTable({
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
          emptyTable: 'Data riwayat pengembalian belum ada',
          zeroRecords: 'Tidak ada data yang cocok'
        },
        pageLength: 10,
        order: [[2, 'desc']],
        columnDefs: [{ orderable: false, targets: [6] }]
      });

      setTimeout(() => $('.alert').fadeOut(), 4000);

      // ── Helpers render ──────────────────────────────────────────────────
      function rupiah(n) {
        return 'Rp ' + Number(n).toLocaleString('id-ID');
      }

      function kondisiBadge(k) {
        const map = {
          baik:         ['k-baik',   'Baik'],
          rusak_ringan: ['k-ringan', 'Rusak Ringan'],
          rusak_berat:  ['k-berat',  'Rusak Berat'],
          hilang:       ['k-hilang', 'Hilang'],
        };
        const [cls, label] = map[k] ?? ['k-baik', k];
        return `<span class="k-badge ${cls}">${label}</span>`;
      }

      const spinnerHtml = (color) => `
        <div class="spinner-overlay">
          <div class="spinner-border text-${color} mb-2"></div>
          <small>Memuat data...</small>
        </div>`;

      // ══════════════════════════════════════════════════════════════════════
      // MODAL DETAIL
      // ══════════════════════════════════════════════════════════════════════
      $(document).on('click', '.btn-open-detail', function () {
        const id = $(this).data('id');
        $('#bodyDetail').html(spinnerHtml('primary'));

        $.get(`${BASE_URL}/${id}/show`)
          .done(function (data) {

            // Rows tabel alat
            let rows = '';
            if (data.details && data.details.length) {
              data.details.forEach(d => {
                rows += `
                  <tr>
                    <td>
                      <span class="fw-semibold">${d.nama_alat}</span>
                      ${d.keterangan && d.keterangan !== '-'
                        ? `<br><small class="text-muted">${d.keterangan}</small>` : ''}
                    </td>
                    <td class="text-center">${d.jumlah}</td>
                    <td class="text-center">${kondisiBadge(d.kondisi_kembali)}</td>
                    <td class="text-end">${rupiah(d.harga_satuan)}</td>
                    <td class="text-end ${d.denda_item > 0 ? 'text-danger fw-semibold' : 'text-success'}">
                      ${rupiah(d.denda_item)}
                    </td>
                  </tr>`;
              });
            } else {
              rows = `<tr><td colspan="5" class="text-center text-muted">
                        Tidak ada data alat</td></tr>`;
            }

            const html = `
              {{-- Info peminjam --}}
              <div class="detail-info-card">
                <div class="row g-3">
                  <div class="col-6">
                    <div class="info-label">Nama Peminjam</div>
                    <div class="info-value">${data.nama_peminjam}</div>
                  </div>
                  <div class="col-6">
                    <div class="info-label">Email</div>
                    <div class="info-value" style="font-size:12px">${data.email_peminjam}</div>
                  </div>
                  <div class="col-4">
                    <div class="info-label">Tgl Pinjam</div>
                    <div class="info-value">${data.tanggal_pinjam}</div>
                  </div>
                  <div class="col-4">
                    <div class="info-label">Rencana Kembali</div>
                    <div class="info-value">${data.tanggal_rencana}</div>
                  </div>
                  <div class="col-4">
                    <div class="info-label">Kembali Aktual</div>
                    <div class="info-value">${data.tanggal_kembali}</div>
                  </div>
                  <div class="col-12">
                    <div class="info-label">Keterlambatan</div>
                    <div class="info-value ${data.keterlambatan_hari > 0 ? 'text-danger' : 'text-success'}">
                      ${data.keterlambatan_hari > 0
                          ? data.keterlambatan_hari + ' hari'
                          : '&#10003; Tepat waktu'}
                    </div>
                  </div>
                </div>
              </div>

              {{-- Tabel alat --}}
              <h6 class="fw-semibold mb-2">
                <i class="ti ti-tools me-1 text-primary"></i>Daftar Alat Dikembalikan
              </h6>
              <div class="table-responsive mb-3">
                <table class="table table-sm table-bordered align-middle">
                  <thead class="table-primary">
                    <tr>
                      <th>Alat</th>
                      <th class="text-center">Jumlah</th>
                      <th class="text-center">Kondisi</th>
                      <th class="text-end">Harga Satuan</th>
                      <th class="text-end">Denda Item</th>
                    </tr>
                  </thead>
                  <tbody>${rows}</tbody>
                </table>
              </div>

              {{-- Ringkasan denda --}}
              <div class="denda-summary">
                <div class="d-flex justify-content-between mb-1 text-muted small">
                  <span>Denda Keterlambatan</span>
                  <span>${rupiah(data.denda_keterlambatan)}</span>
                </div>
                <div class="d-flex justify-content-between mb-1 text-muted small">
                  <span>Denda Kerusakan / Kehilangan</span>
                  <span>${rupiah(data.denda_kerusakan)}</span>
                </div>
                <div class="denda-total-row d-flex justify-content-between fw-bold">
                  <span>Total Denda</span>
                  <span class="text-danger fs-5">${rupiah(data.total_denda)}</span>
                </div>
              </div>`;

            $('#bodyDetail').html(html);
          })
          .fail(() => {
            $('#bodyDetail').html(`
              <div class="alert alert-danger">
                <i class="ti ti-alert-circle me-2"></i>
                Gagal memuat data. Pastikan koneksi Anda dan coba lagi.
              </div>`);
          });
      });

      // ══════════════════════════════════════════════════════════════════════
      // MODAL STRUK
      // ══════════════════════════════════════════════════════════════════════
      $(document).on('click', '.btn-open-struk', function () {
        const id = $(this).data('id');
        $('#bodyStruk').html(spinnerHtml('success'));
        $('#btnDownload').attr('href', '#');
        $('#btnKirim').data('id', '');

        $.get(`${BASE_URL}/${id}/show`)
          .done(function (data) {

            // Rows tabel struk
            let rows = '';
            if (data.details && data.details.length) {
              data.details.forEach(d => {
                const label = {
                  baik: 'Baik', rusak_ringan: 'Rusak Ringan',
                  rusak_berat: 'Rusak Berat', hilang: 'Hilang'
                }[d.kondisi_kembali] ?? d.kondisi_kembali;

                const bcls = {
                  baik: 'k-baik', rusak_ringan: 'k-ringan',
                  rusak_berat: 'k-berat', hilang: 'k-hilang'
                }[d.kondisi_kembali] ?? 'k-baik';

                rows += `
                  <tr>
                    <td>${d.nama_alat}
                      ${d.keterangan && d.keterangan !== '-'
                        ? `<br><span style="color:#999;font-size:9px">${d.keterangan}</span>` : ''}
                    </td>
                    <td style="text-align:center">${d.jumlah}</td>
                    <td><span class="k-badge ${bcls}">${label}</span></td>
                    <td style="text-align:right;${d.denda_item > 0 ? 'color:#dc3545;font-weight:700' : 'color:#28a745'}">
                      Rp ${Number(d.denda_item).toLocaleString('id-ID')}
                    </td>
                  </tr>`;
              });
            } else {
              rows = `<tr><td colspan="4" style="text-align:center;color:#aaa">
                Tidak ada data alat</td></tr>`;
            }

            const noTrx = String(data.id).padStart(6, '0');
            const terlambat = data.keterlambatan_hari > 0
              ? `<span style="color:#dc3545">${data.keterlambatan_hari} hari</span>`
              : `<span style="color:#28a745">&#10003; Tepat waktu</span>`;

            const statusBadge = data.total_denda == 0
              ? `<span class="struk-status-badge badge-bebas">&#10003; BEBAS DENDA</span>`
              : `<span class="struk-status-badge badge-lunas">&#10003; LUNAS</span>`;

            const struk = `
              <div class="struk-wrap bg-white p-3 rounded shadow-sm border">

                <div class="struk-header">
                  <div style="font-size:17px;font-weight:700;color:#4e73df;letter-spacing:2px">
                    &#9741; STRUK PENGEMBALIAN
                  </div>
                  <div class="struk-sub">Sistem Peminjaman Alat</div>
                  <div class="struk-no">#${noTrx}</div>
                </div>

                <div class="struk-section-title">Informasi Peminjam</div>
                <div class="struk-row">
                  <span class="s-label">Nama</span>
                  <span class="s-value">${data.nama_peminjam}</span>
                </div>
                <div class="struk-row">
                  <span class="s-label">Email</span>
                  <span class="s-value" style="font-size:10px">${data.email_peminjam}</span>
                </div>

                <div class="struk-section-title">Waktu</div>
                <div class="struk-row">
                  <span class="s-label">Tgl Pinjam</span>
                  <span class="s-value">${data.tanggal_pinjam}</span>
                </div>
                <div class="struk-row">
                  <span class="s-label">Rencana Kembali</span>
                  <span class="s-value">${data.tanggal_rencana}</span>
                </div>
                <div class="struk-row">
                  <span class="s-label">Kembali Aktual</span>
                  <span class="s-value">${data.tanggal_kembali}</span>
                </div>
                <div class="struk-row">
                  <span class="s-label">Keterlambatan</span>
                  <span class="s-value">${terlambat}</span>
                </div>

                <hr class="struk-divider">

                <div class="struk-section-title">Detail Alat</div>
                <table class="struk-table">
                  <thead>
                    <tr>
                      <th style="width:38%">Alat</th>
                      <th style="width:8%;text-align:center">Jumlah</th>
                      <th style="width:22%">Kondisi</th>
                      <th style="width:32%;text-align:right">Denda</th>
                    </tr>
                  </thead>
                  <tbody>${rows}</tbody>
                </table>

                <hr class="struk-divider">

                <div class="struk-section-title">Ringkasan Biaya</div>
                <div class="struk-summary">
                  <div class="struk-summary-row">
                    <span>Denda Keterlambatan</span>
                    <span>Rp ${Number(data.denda_keterlambatan).toLocaleString('id-ID')}</span>
                  </div>
                  <div class="struk-summary-row">
                    <span>Denda Kerusakan</span>
                    <span>Rp ${Number(data.denda_kerusakan).toLocaleString('id-ID')}</span>
                  </div>
                  <div class="struk-summary-row s-total">
                    <span>TOTAL DENDA</span>
                    <span>Rp ${Number(data.total_denda).toLocaleString('id-ID')}</span>
                  </div>
                </div>

                <div class="struk-status">${statusBadge}</div>

                <div class="struk-footer">
                  <div>Dokumen ini digenerate otomatis oleh sistem.</div>
                  <div>Simpan sebagai bukti resmi pengembalian alat.</div>
                </div>

              </div>`;

            $('#bodyStruk').html(struk);
            $('#btnDownload').attr('href', `${BASE_URL}/${id}/download-struk`);
            $('#btnKirim').data('id', id);
          })
          .fail(() => {
            $('#bodyStruk').html(`
              <div class="alert alert-danger m-2">
                <i class="ti ti-alert-circle me-2"></i>Gagal memuat struk.
              </div>`);
          });
      });

      // ── Kirim email ───────────────────────────────────────────────────────
      $(document).on('click', '#btnKirim', function () {
        const id = $(this).data('id');
        if (!id) return;

        Swal.fire({
          title: 'Kirim Struk ke Email?',
          html: 'Struk akan dikirim dalam bentuk <b>PDF</b> ke email peminjam.',
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#198754',
          cancelButtonColor: '#6c757d',
          confirmButtonText: '<i class="ti ti-send"></i> Ya, kirim!',
          cancelButtonText: 'Batal'
        }).then(result => {
          if (!result.isConfirmed) return;

          Swal.fire({
            title: 'Mengirim Email...',
            html: 'Mohon tunggu sebentar.',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
          });

          $.ajax({
            url:    `${BASE_URL}/${id}/kirim-struk`,
            method: 'POST',
            data:   { _token: CSRF_TOKEN },
            success: res => Swal.fire({
              icon: 'success', title: 'Berhasil!',
              text: res.message, confirmButtonColor: '#198754'
            }),
            error: err => Swal.fire({
              icon: 'error', title: 'Gagal!',
              text: err.responseJSON?.message ?? 'Gagal mengirim email.',
              confirmButtonColor: '#dc3545'
            })
          });
        });
      });

    }); // end ready
  </script>

</body>
</html>