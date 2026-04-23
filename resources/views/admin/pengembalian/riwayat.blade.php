<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Riwayat Pengembalian - Perpustakaan Digital</title>
  <link rel="stylesheet" href="{{ asset('template/css/styles.min.css') }}" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
  <style>
    .btn-icon {
      width: 32px; height: 32px;
      display: inline-flex; align-items: center; justify-content: center;
      border-radius: 7px; border: none; cursor: pointer;
      transition: transform .15s, box-shadow .15s; font-size: 15px;
    }
    .btn-icon:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,.15); }
    .btn-detail { background: #e0f2fe; color: #0369a1; }
    .btn-detail:hover { background: #0369a1; color: #fff; }
    .btn-struk  { background: #dcfce7; color: #166534; }
    .btn-struk:hover  { background: #166534; color: #fff; }

    /* Modal detail */
    .info-card { background:#f8faff; border:1px solid #dde8ff;
                 border-radius:10px; padding:14px 16px; margin-bottom:14px; }
    .info-card .il { font-size:10.5px; color:#6c757d; text-transform:uppercase;
                     letter-spacing:.4px; margin-bottom:2px; }
    .info-card .iv { font-size:13.5px; font-weight:600; color:#212529; }
    .denda-box { background:#fff8f0; border:1px solid #fed7aa;
                 border-radius:8px; padding:12px 14px; }

    /* Struk modal (nota style) */
    .struk-nota {
      max-width:400px; margin:0 auto;
      font-family: 'Courier New', monospace;
      background:#fff; border-radius:10px;
      border:1px solid #e2e8f0; padding:20px;
    }
    .struk-head { text-align:center; padding-bottom:12px;
                  border-bottom:2px dashed #1a3c6e; margin-bottom:12px; }
    .struk-head .s-lib  { font-size:15px; font-weight:700; color:#1a3c6e; letter-spacing:1px; }
    .struk-head .s-title{ font-size:12px; font-weight:700; color:#2563eb;
                          letter-spacing:2px; margin-top:3px; }
    .struk-head .s-meta { font-size:10px; color:#94a3b8; margin-top:4px; }
    .s-sec { font-size:9.5px; font-weight:700; color:#2563eb; text-transform:uppercase;
             letter-spacing:.5px; border-bottom:1px solid #dbeafe;
             padding-bottom:3px; margin:10px 0 6px; }
    .s-row { display:flex; justify-content:space-between; font-size:11px; padding:2px 0; }
    .s-lbl { color:#666; } .s-val { font-weight:600; color:#222; }
    .s-divider { border:none; border-top:1px dashed #93c5fd; margin:8px 0; }
    .s-table { width:100%; font-size:10px; border-collapse:collapse; }
    .s-table th { background:#1a3c6e; color:#fff; padding:5px 4px; text-align:left; }
    .s-table td { padding:5px 4px; border-bottom:1px solid #eff6ff; }
    .s-table tr:nth-child(even) td { background:#f0f5ff; }
    .s-summary { background:#f0f5ff; border:1px solid #bfdbfe;
                 border-radius:6px; padding:10px 12px; margin-top:10px; }
    .s-sum-row { display:flex; justify-content:space-between; font-size:11px; padding:2px 0; }
    .s-sum-total { border-top:2px solid #1a3c6e; margin-top:5px; padding-top:6px;
                   display:flex; justify-content:space-between;
                   font-size:13px; font-weight:700; color:#1a3c6e; }
    .s-status { text-align:center; margin:10px 0 6px; }
    .s-badge { display:inline-block; padding:4px 16px; border-radius:16px;
               font-size:10.5px; font-weight:700; }
    .s-lunas { background:#d1fae5; color:#065f46; border:1.5px solid #6ee7b7; }
    .s-bebas { background:#dbeafe; color:#1e3a8a; border:1.5px solid #93c5fd; }
    .s-footer { text-align:center; margin-top:12px; padding-top:10px;
                border-top:1px dashed #93c5fd; color:#94a3b8; font-size:9px; line-height:1.6; }
    .k-badge { padding:2px 6px; border-radius:8px; font-size:9px; font-weight:600;
               display:inline-block; }
    .k-baik   { background:#d1fae5; color:#065f46; }
    .k-ringan { background:#fef9c3; color:#854d0e; }
    .k-berat  { background:#fee2e2; color:#7f1d1d; }
    .k-hilang { background:#e5e7eb; color:#374151; }
    .spin-box { display:flex; flex-direction:column; align-items:center;
                justify-content:center; padding:36px 0; color:#64748b; }
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
            <h5 class="card-title fw-semibold mb-4">Riwayat Seluruh Pengembalian</h5>

            @if(session('success'))
              <div class="alert alert-success alert-dismissible fade show">
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
                    <th>Buku Dikembalikan</th>
                    <th>Tgl Kembali</th>
                    <th class="text-center">Keterlambatan</th>
                    <th>Total Denda</th>
                    <th class="text-center">Status Bayar</th>
                    <th class="text-center" style="width:80px">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($riwayats as $item)
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td><strong>{{ $item->peminjaman->user->name ?? '-' }}</strong></td>
                      <td>
                        @foreach($item->details as $d)
                          <span class="badge bg-light text-dark border mb-1">
                            {{ $d->buku->judul_buku ?? '-' }}
                          </span><br>
                        @endforeach
                      </td>
                      <td>{{ \Carbon\Carbon::parse($item->tanggal_kembali_aktual)->format('d/m/Y') }}</td>
                      <td class="text-center">
                        @if($item->keterlambatan_hari > 0)
                          <span class="badge bg-danger">{{ $item->keterlambatan_hari }} hari</span>
                        @else
                          <span class="badge bg-success">Tepat Waktu</span>
                        @endif
                      </td>
                      <td class="fw-bold">Rp {{ number_format($item->total_denda, 0, ',', '.') }}</td>
                      <td class="text-center">
                        @if($item->status_pembayaran == 'lunas')
                          <span class="badge bg-success">Lunas</span>
                        @elseif($item->status_pembayaran == 'tidak_ada_denda')
                          <span class="badge bg-info text-white">Bebas Denda</span>
                        @else
                          <span class="badge bg-warning text-dark">Belum Lunas</span>
                        @endif
                      </td>
                      <td class="text-center">
                        {{-- Tombol Detail --}}
                        <button class="btn-icon btn-detail me-1 btn-open-detail"
                          data-id="{{ $item->id }}" title="Lihat Detail"
                          data-bs-toggle="modal" data-bs-target="#modalDetail">
                          <iconify-icon icon="solar:eye-outline" width="16"></iconify-icon>
                        </button>
                        {{-- Tombol Struk --}}
                        <button class="btn-icon btn-struk btn-open-struk"
                          data-id="{{ $item->id }}" title="Cetak Struk"
                          data-bs-toggle="modal" data-bs-target="#modalStruk">
                          <iconify-icon icon="solar:bill-list-bold" width="16"></iconify-icon>
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
  {{-- MODAL 1: DETAIL --}}
  {{-- ================================================================ --}}
  <div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content border-0 shadow">
        <div class="modal-header" style="background:linear-gradient(135deg,#1a3c6e,#2563eb);color:#fff">
          <h5 class="modal-title fw-semibold">
            <iconify-icon icon="solar:clipboard-list-outline" style="vertical-align:middle;margin-right:6px"></iconify-icon>
            Detail Pengembalian
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4" id="bodyDetail">
          <div class="spin-box">
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
  {{-- MODAL 2: STRUK --}}
  {{-- ================================================================ --}}
  <div class="modal fade" id="modalStruk" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable" style="max-width:480px">
      <div class="modal-content border-0 shadow">
        <div class="modal-header" style="background:linear-gradient(135deg,#166534,#16a34a);color:#fff">
          <h5 class="modal-title fw-semibold">
            <iconify-icon icon="solar:receipt-outline" style="vertical-align:middle;margin-right:6px"></iconify-icon>
            Preview Struk Pengembalian
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-3 bg-light" id="bodyStruk">
          <div class="spin-box">
            <div class="spinner-border text-success mb-2"></div>
            <small>Memuat struk...</small>
          </div>
        </div>
        <div class="modal-footer border-0">
          <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
          <a href="#" id="btnDownload" class="btn btn-primary btn-sm" target="_blank">
            <iconify-icon icon="solar:download-outline" style="vertical-align:middle"></iconify-icon>
            Download PDF
          </a>
          <button type="button" id="btnKirim" class="btn btn-success btn-sm" data-id="">
            <iconify-icon icon="solar:letter-outline" style="vertical-align:middle"></iconify-icon>
            Kirim ke Peminjam
          </button>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset('template/libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('template/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('template/js/app.min.js') }}"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

  <script>
    const BASE_URL   = "{{ url('admin/pengembalian') }}";
    const CSRF_TOKEN = "{{ csrf_token() }}";

    $(document).ready(function () {
      $('#riwayatTable').DataTable({
        order: [[3, 'desc']],
        columnDefs: [{ orderable: false, targets: [7] }]
      });
      setTimeout(() => $('.alert').fadeOut(), 3500);

      // ── Helpers ──────────────────────────────────────────────────────────
      const rp  = n => 'Rp ' + Number(n).toLocaleString('id-ID');
      const spin = color => `
        <div class="spin-box">
          <div class="spinner-border text-${color} mb-2"></div>
          <small>Memuat data...</small>
        </div>`;

      const kondisiHtml = k => {
        const map = {
          baik:         ['k-baik',   'Baik'],
          rusak_ringan: ['k-ringan', 'Rusak Ringan'],
          rusak_berat:  ['k-berat',  'Rusak Berat'],
          hilang:       ['k-hilang', 'Hilang'],
        };
        const [c, l] = map[k] ?? ['k-baik', k];
        return `<span class="k-badge ${c}">${l}</span>`;
      };

      // ════════════════════════════════════════════════════════════════════
      // MODAL DETAIL
      // ════════════════════════════════════════════════════════════════════
      $(document).on('click', '.btn-open-detail', function () {
        const id = $(this).data('id');
        $('#bodyDetail').html(spin('primary'));

        $.get(`${BASE_URL}/${id}/show-struk`)
          .done(function (d) {
            let rows = '';
            (d.details || []).forEach(bk => {
              rows += `
                <tr>
                  <td><span class="fw-semibold">${bk.judul_buku}</span></td>
                  <td class="text-center">${bk.jumlah}</td>
                  <td>${kondisiHtml(bk.kondisi_kembali)}</td>
                  <td class="text-end ${bk.denda_kerusakan_buku > 0 ? 'text-danger fw-semibold' : 'text-success'}">
                    ${rp(bk.denda_kerusakan_buku)}
                  </td>
                </tr>`;
            });
            if (!rows) rows = `<tr><td colspan="4" class="text-center text-muted">Tidak ada data</td></tr>`;

            $('#bodyDetail').html(`
              <div class="info-card">
                <div class="row g-3">
                  <div class="col-6">
                    <div class="il">Nama Peminjam</div>
                    <div class="iv">${d.nama_peminjam}</div>
                  </div>
                  <div class="col-6">
                    <div class="il">Email</div>
                    <div class="iv" style="font-size:12px">${d.email_peminjam}</div>
                  </div>
                  <div class="col-4">
                    <div class="il">Tgl Pinjam</div>
                    <div class="iv">${d.tanggal_pinjam}</div>
                  </div>
                  <div class="col-4">
                    <div class="il">Rencana Kembali</div>
                    <div class="iv">${d.tanggal_rencana}</div>
                  </div>
                  <div class="col-4">
                    <div class="il">Kembali Aktual</div>
                    <div class="iv">${d.tanggal_kembali}</div>
                  </div>
                  <div class="col-6">
                    <div class="il">Keterlambatan</div>
                    <div class="iv ${d.keterlambatan_hari > 0 ? 'text-danger' : 'text-success'}">
                      ${d.keterlambatan_hari > 0 ? d.keterlambatan_hari + ' hari' : '&#10003; Tepat waktu'}
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="il">Petugas</div>
                    <div class="iv">${d.petugas}</div>
                  </div>
                </div>
              </div>

              <h6 class="fw-semibold mb-2">
                <iconify-icon icon="solar:book-outline" style="vertical-align:middle;margin-right:4px"></iconify-icon>
                Daftar Buku Dikembalikan
              </h6>
              <div class="table-responsive mb-3">
                <table class="table table-sm table-bordered align-middle">
                  <thead class="table-primary">
                    <tr>
                      <th>Judul Buku</th>
                      <th class="text-center">Jml</th>
                      <th>Kondisi</th>
                      <th class="text-end">Denda Item</th>
                    </tr>
                  </thead>
                  <tbody>${rows}</tbody>
                </table>
              </div>

              <div class="denda-box">
                <div class="d-flex justify-content-between mb-1 text-muted small">
                  <span>Denda Keterlambatan</span><span>${rp(d.denda_keterlambatan)}</span>
                </div>
                <div class="d-flex justify-content-between mb-2 text-muted small">
                  <span>Denda Kerusakan</span><span>${rp(d.denda_kerusakan)}</span>
                </div>
                <div class="d-flex justify-content-between fw-bold border-top pt-2">
                  <span>Total Denda</span>
                  <span class="text-danger fs-5">${rp(d.total_denda)}</span>
                </div>
              </div>`);
          })
          .fail(() => $('#bodyDetail').html(
            `<div class="alert alert-danger">Gagal memuat data.</div>`));
      });

      // ════════════════════════════════════════════════════════════════════
      // MODAL STRUK
      // ════════════════════════════════════════════════════════════════════
      $(document).on('click', '.btn-open-struk', function () {
        const id = $(this).data('id');
        $('#bodyStruk').html(spin('success'));
        $('#btnDownload').attr('href', '#');
        $('#btnKirim').data('id', '');

        $.get(`${BASE_URL}/${id}/show-struk`)
          .done(function (d) {
            const noTrx = String(d.id).padStart(6, '0');

            const kondisiLabel = k => ({
              baik: 'Baik', rusak_ringan: 'Rusak Ringan',
              rusak_berat: 'Rusak Berat', hilang: 'Hilang'
            }[k] ?? k);

            const kondisiCls = k => ({
              baik: 'k-baik', rusak_ringan: 'k-ringan',
              rusak_berat: 'k-berat', hilang: 'k-hilang'
            }[k] ?? 'k-baik');

            let rows = '';
            (d.details || []).forEach(bk => {
              rows += `
                <tr>
                  <td>${bk.judul_buku}</td>
                  <td style="text-align:center">${bk.jumlah}</td>
                  <td><span class="k-badge ${kondisiCls(bk.kondisi_kembali)}">${kondisiLabel(bk.kondisi_kembali)}</span></td>
                  <td style="text-align:right;${bk.denda_kerusakan_buku > 0 ? 'color:#dc2626;font-weight:700' : 'color:#16a34a'}">
                    Rp ${Number(bk.denda_kerusakan_buku).toLocaleString('id-ID')}
                  </td>
                </tr>`;
            });
            if (!rows) rows = `<tr><td colspan="4" style="text-align:center;color:#aaa">Tidak ada data buku</td></tr>`;

            const statusBadge = d.total_denda == 0
              ? `<span class="s-badge s-bebas">&#10003; BEBAS DENDA</span>`
              : `<span class="s-badge s-lunas">&#10003; LUNAS</span>`;

            const terlambat = d.keterlambatan_hari > 0
              ? `<span style="color:#dc2626">${d.keterlambatan_hari} hari</span>`
              : `<span style="color:#16a34a">&#10003; Tepat waktu</span>`;

            $('#bodyStruk').html(`
              <div class="struk-nota">
                <div class="struk-head">
                  <div class="s-lib">&#128218; PERPUSTAKAAN DIGITAL</div>
                  <div class="s-title">STRUK PENGEMBALIAN BUKU</div>
                  <div class="s-meta">#${noTrx}</div>
                </div>

                <div class="s-sec">Informasi Peminjam</div>
                <div class="s-row"><span class="s-lbl">Nama</span><span class="s-val">${d.nama_peminjam}</span></div>
                <div class="s-row"><span class="s-lbl">Email</span><span class="s-val" style="font-size:10px">${d.email_peminjam}</span></div>

                <div class="s-sec">Waktu</div>
                <div class="s-row"><span class="s-lbl">Tgl Pinjam</span><span class="s-val">${d.tanggal_pinjam}</span></div>
                <div class="s-row"><span class="s-lbl">Rencana Kembali</span><span class="s-val">${d.tanggal_rencana}</span></div>
                <div class="s-row"><span class="s-lbl">Kembali Aktual</span><span class="s-val">${d.tanggal_kembali}</span></div>
                <div class="s-row"><span class="s-lbl">Keterlambatan</span><span class="s-val">${terlambat}</span></div>

                <hr class="s-divider">

                <div class="s-sec">Detail Buku</div>
                <table class="s-table">
                  <thead>
                    <tr>
                      <th style="width:38%">Judul Buku</th>
                      <th style="width:8%;text-align:center">Jml</th>
                      <th style="width:22%">Kondisi</th>
                      <th style="width:32%;text-align:right">Denda</th>
                    </tr>
                  </thead>
                  <tbody>${rows}</tbody>
                </table>

                <div class="s-summary">
                  <div class="s-sum-row">
                    <span>Denda Keterlambatan</span>
                    <span>Rp ${Number(d.denda_keterlambatan).toLocaleString('id-ID')}</span>
                  </div>
                  <div class="s-sum-row">
                    <span>Denda Kerusakan</span>
                    <span>Rp ${Number(d.denda_kerusakan).toLocaleString('id-ID')}</span>
                  </div>
                  <div class="s-sum-total">
                    <span>TOTAL DENDA</span>
                    <span>Rp ${Number(d.total_denda).toLocaleString('id-ID')}</span>
                  </div>
                </div>

                <div class="s-status">${statusBadge}</div>

                <div class="s-footer">
                  <div>Dokumen digenerate otomatis oleh sistem perpustakaan.</div>
                  <div>Simpan sebagai bukti resmi pengembalian buku.</div>
                </div>
              </div>`);

            $('#btnDownload').attr('href', `${BASE_URL}/${id}/download-struk`);
            $('#btnKirim').data('id', id);
          })
          .fail(() => $('#bodyStruk').html(
            `<div class="alert alert-danger m-2">Gagal memuat struk.</div>`));
      });

      // ── Kirim email ───────────────────────────────────────────────────────
      $(document).on('click', '#btnKirim', function () {
        const id = $(this).data('id');
        if (!id) return;

        Swal.fire({
          title: 'Kirim Struk ke Email?',
          html: 'Struk PDF akan dikirim ke <b>email peminjam</b>.',
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#166534',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Ya, kirim!',
          cancelButtonText: 'Batal'
        }).then(res => {
          if (!res.isConfirmed) return;

          Swal.fire({
            title: 'Mengirim...',
            html: 'Mohon tunggu sebentar.',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
          });

          $.ajax({
            url:    `${BASE_URL}/${id}/kirim-struk`,
            method: 'POST',
            data:   { _token: CSRF_TOKEN },
            success: r => Swal.fire({
              icon: 'success', title: 'Terkirim!',
              text: r.message, confirmButtonColor: '#166534'
            }),
            error: e => Swal.fire({
              icon: 'error', title: 'Gagal!',
              text: e.responseJSON?.message ?? 'Terjadi kesalahan.',
              confirmButtonColor: '#dc2626'
            })
          });
        });
      });
    });
  </script>
</body>
</html>