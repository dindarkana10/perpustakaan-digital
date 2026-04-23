<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; }
    .wrap { padding: 24px 28px; }

    /* Header */
    .header { text-align: center; padding-bottom: 14px;
              border-bottom: 2px solid #1a3c6e; margin-bottom: 16px; }
    .header .lib-name { font-size: 16px; font-weight: bold;
                        color: #1a3c6e; letter-spacing: 1px; }
    .header .doc-title { font-size: 13px; font-weight: bold;
                         color: #2563eb; margin-top: 4px; letter-spacing: 2px; }
    .header .meta      { font-size: 9.5px; color: #666; margin-top: 4px; }

    /* Section */
    .sec-title {
      font-size: 9.5px; font-weight: bold; color: #2563eb;
      text-transform: uppercase; letter-spacing: .5px;
      border-bottom: 1px solid #dbeafe; padding-bottom: 3px; margin: 14px 0 7px;
    }

    /* Info rows */
    .info-table { width: 100%; }
    .info-table td { padding: 3px 0; font-size: 10.5px; }
    .info-table .lbl { color: #555; width: 42%; }
    .info-table .sep { width: 4%; color: #999; }
    .info-table .val { font-weight: 600; color: #111; }

    /* Divider */
    .divider { border: none; border-top: 1px dashed #93c5fd; margin: 12px 0; }

    /* Buku table */
    .buku-table { width: 100%; border-collapse: collapse; font-size: 10px; }
    .buku-table thead th {
      background: #1a3c6e; color: #fff; padding: 6px 5px; text-align: left;
    }
    .buku-table tbody td { padding: 6px 5px; border-bottom: 1px solid #eff6ff; }
    .buku-table tbody tr:nth-child(even) td { background: #f0f5ff; }

    /* Badges */
    .badge { display: inline-block; padding: 2px 6px; border-radius: 8px;
             font-size: 8.5px; font-weight: bold; }
    .b-baik   { background:#d1fae5; color:#065f46; }
    .b-ringan { background:#fef9c3; color:#854d0e; }
    .b-berat  { background:#fee2e2; color:#7f1d1d; }
    .b-hilang { background:#e5e7eb; color:#374151; }

    /* Summary box */
    .summary {
      background: #f0f5ff; border: 1px solid #bfdbfe;
      border-radius: 6px; padding: 12px 14px; margin-top: 12px;
    }
    .s-row { display: flex; justify-content: space-between;
             font-size: 11px; padding: 3px 0; }
    .s-divider { border: none; border-top: 1.5px solid #93c5fd; margin: 6px 0; }
    .s-total {
      display: flex; justify-content: space-between;
      font-size: 14px; font-weight: bold; color: #1a3c6e; padding-top: 4px;
    }

    /* Status */
    .status-box { text-align: center; margin: 14px 0 8px; }
    .s-badge {
      display: inline-block; padding: 6px 22px;
      border-radius: 20px; font-size: 11px; font-weight: bold;
    }
    .s-lunas { background: #d1fae5; color: #065f46; border: 1.5px solid #6ee7b7; }
    .s-bebas { background: #dbeafe; color: #1e3a8a; border: 1.5px solid #93c5fd; }

    /* Footer */
    .footer {
      text-align: center; margin-top: 16px; padding-top: 12px;
      border-top: 1px dashed #93c5fd; color: #94a3b8; font-size: 9px; line-height: 1.7;
    }
    .ttd-area { margin-top: 20px; }
    .ttd-box { display: inline-block; text-align: center; float: right; font-size: 10px; }
    .ttd-box .garis { margin-top: 40px; border-top: 1px solid #333;
                      padding-top: 3px; font-weight: bold; }
  </style>
</head>
<body>
<div class="wrap">

  {{-- Header --}}
  <div class="header">
    <div class="lib-name">&#128218; PERPUSTAKAAN DIGITAL</div>
    <div class="doc-title">STRUK PENGEMBALIAN BUKU</div>
    <div class="meta">
      No: #{{ str_pad($pengembalian->id, 6, '0', STR_PAD_LEFT) }}
      &nbsp;&nbsp;|&nbsp;&nbsp;
      Dicetak: {{ now()->format('d/m/Y H:i') }}
    </div>
  </div>

  {{-- Info Peminjam --}}
  <div class="sec-title">Informasi Peminjam</div>
  <table class="info-table">
    <tr>
      <td class="lbl">Nama Peminjam</td>
      <td class="sep">:</td>
      <td class="val">{{ $pengembalian->peminjaman->user->name ?? '-' }}</td>
    </tr>
    <tr>
      <td class="lbl">Email</td>
      <td class="sep">:</td>
      <td class="val" style="font-size:9.5px">
        {{ $pengembalian->peminjaman->user->email ?? '-' }}
      </td>
    </tr>
  </table>

  {{-- Info Waktu --}}
  <div class="sec-title">Informasi Waktu Pengembalian</div>
  <table class="info-table">
    <tr>
      <td class="lbl">Tgl Pinjam</td>
      <td class="sep">:</td>
      <td class="val">
        {{ optional($pengembalian->peminjaman->tanggal_pinjam)->format('d/m/Y') ?? '-' }}
      </td>
    </tr>
    <tr>
      <td class="lbl">Rencana Kembali</td>
      <td class="sep">:</td>
      <td class="val">
        {{ optional($pengembalian->peminjaman->tanggal_kembali_rencana)->format('d/m/Y') ?? '-' }}
      </td>
    </tr>
    <tr>
      <td class="lbl">Kembali Aktual</td>
      <td class="sep">:</td>
      <td class="val">
        {{ \Carbon\Carbon::parse($pengembalian->tanggal_kembali_aktual)->format('d/m/Y') }}
      </td>
    </tr>
    <tr>
      <td class="lbl">Keterlambatan</td>
      <td class="sep">:</td>
      <td class="val" style="{{ ($pengembalian->keterlambatan_hari ?? 0) > 0 ? 'color:#dc2626' : 'color:#16a34a' }}">
        {{ ($pengembalian->keterlambatan_hari ?? 0) > 0
            ? $pengembalian->keterlambatan_hari . ' hari'
            : 'Tepat waktu' }}
      </td>
    </tr>
  </table>

  <hr class="divider">

  {{-- Detail Buku --}}
  <div class="sec-title">Detail Buku Dikembalikan</div>
  <table class="buku-table">
    <thead>
      <tr>
        <th style="width:40%">Judul Buku</th>
        <th style="width:8%;text-align:center">Jml</th>
        <th style="width:22%">Kondisi</th>
        <th style="width:30%;text-align:right">Denda Item</th>
      </tr>
    </thead>
    <tbody>
      @forelse($detailSource as $d)
        @php
          // Support detail pengembalian maupun fallback detail peminjaman
          $judulBuku  = $d->buku->judul_buku ?? ($d->buku->judul ?? '-');
          $jumlah     = $d->jumlah_kembali ?? $d->jumlah ?? 1;
          $kondisi    = $d->kondisi_kembali ?? 'baik';
          $harga      = $d->buku->harga_buku ?? 0;
          $dendaItem  = $d->denda_kerusakan_buku ?? 0;

          $badgeCls = match($kondisi) {
            'baik'         => 'b-baik',
            'rusak_ringan' => 'b-ringan',
            'rusak_berat'  => 'b-berat',
            'hilang'       => 'b-hilang',
            default        => 'b-baik',
          };
          $lblKondisi = match($kondisi) {
            'baik'         => 'Baik',
            'rusak_ringan' => 'Rusak Ringan',
            'rusak_berat'  => 'Rusak Berat',
            'hilang'       => 'Hilang',
            default        => $kondisi,
          };
        @endphp
        <tr>
          <td>{{ $judulBuku }}</td>
          <td style="text-align:center">{{ $jumlah }}</td>
          <td><span class="badge {{ $badgeCls }}">{{ $lblKondisi }}</span></td>
          <td style="text-align:right; {{ $dendaItem > 0 ? 'color:#dc2626;font-weight:bold' : 'color:#16a34a' }}">
            Rp {{ number_format($dendaItem, 0, ',', '.') }}
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="4" style="text-align:center;color:#aaa;padding:10px">
            Tidak ada data buku
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>

  {{-- Ringkasan Biaya --}}
  <div class="summary">
    <div class="s-row">
      <span>Denda Keterlambatan</span>
      <span>Rp {{ number_format($pengembalian->denda_keterlambatan ?? 0, 0, ',', '.') }}</span>
    </div>
    <div class="s-row">
      <span>Denda Kerusakan / Kehilangan</span>
      <span>Rp {{ number_format($pengembalian->denda_kerusakan ?? 0, 0, ',', '.') }}</span>
    </div>
    <hr class="s-divider">
    <div class="s-total">
      <span>TOTAL DENDA</span>
      <span>Rp {{ number_format($pengembalian->total_denda ?? 0, 0, ',', '.') }}</span>
    </div>
  </div>

  {{-- Status --}}
  <div class="status-box">
    @if(($pengembalian->total_denda ?? 0) == 0)
      <span class="s-badge s-bebas">&#10003; BEBAS DENDA / TIDAK ADA DENDA</span>
    @else
      <span class="s-badge s-lunas">&#10003; DENDA TELAH DILUNASI</span>
    @endif
  </div>

  {{-- TTD --}}
  <div class="ttd-area" style="overflow:hidden">
    <div class="ttd-box">
      <div>Petugas,</div>
      <div class="garis">{{ $pengembalian->petugas->name ?? 'Petugas Perpustakaan' }}</div>
    </div>
  </div>

  <div class="footer">
    <p>Dokumen ini digenerate otomatis oleh sistem perpustakaan.</p>
    <p>Simpan sebagai bukti resmi pengembalian buku Anda.</p>
    <p>&copy; {{ date('Y') }} Perpustakaan Digital</p>
  </div>

</div>
</body>
</html>