<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
    .wrap { max-width: 380px; margin: 0 auto; padding: 20px 16px; }
    .header { text-align: center; padding-bottom: 12px; border-bottom: 2px dashed #4e73df; margin-bottom: 14px; }
    .header .logo { font-size: 16px; font-weight: bold; color: #4e73df; letter-spacing: 1px; }
    .header .sub  { font-size: 10px; color: #888; margin-top: 2px; }
    .header .no   { font-size: 12px; font-weight: bold; color: #333; margin-top: 5px; }
    .sec-title { font-size: 10px; font-weight: bold; color: #4e73df; text-transform: uppercase;
                 letter-spacing: .4px; border-bottom: 1px solid #d0dcff; padding-bottom: 3px; margin: 12px 0 6px; }
    .info-row { display: flex; justify-content: space-between; padding: 2px 0; font-size: 10.5px; }
    .info-row .lbl { color: #666; width: 45%; }
    .info-row .val { font-weight: 600; color: #222; width: 55%; text-align: right; }
    .divider { border: none; border-top: 1px dashed #ccc; margin: 10px 0; }
    table { width: 100%; border-collapse: collapse; font-size: 10px; }
    thead th { background: #4e73df; color: #fff; padding: 5px 4px; text-align: left; }
    tbody td { padding: 5px 4px; border-bottom: 1px solid #f0f0f0; vertical-align: top; }
    tbody tr:nth-child(even) td { background: #f7f9ff; }
    .badge { display: inline-block; padding: 1px 5px; border-radius: 8px; font-size: 9px; font-weight: bold; }
    .b-baik   { background:#d4edda; color:#155724; }
    .b-ringan { background:#fff3cd; color:#856404; }
    .b-berat  { background:#f8d7da; color:#721c24; }
    .b-hilang { background:#e2e3e5; color:#383d41; }
    .summary { background:#f7f9ff; border:1px solid #d0dcff; border-radius:6px; padding:10px 12px; margin-top:10px; }
    .s-row { display:flex; justify-content:space-between; font-size:11px; padding:3px 0; }
    .s-total { border-top:2px solid #4e73df; margin-top:6px; padding-top:8px;
               font-weight:bold; font-size:13px; color:#4e73df; }
    .status-box { text-align:center; margin:12px 0 6px; }
    .s-badge { display:inline-block; padding:5px 18px; border-radius:20px;
               font-size:11px; font-weight:bold; letter-spacing:.5px; }
    .s-lunas { background:#d4edda; color:#155724; border:1.5px solid #c3e6cb; }
    .s-bebas { background:#cce5ff; color:#004085; border:1.5px solid #b8daff; }
    .footer { text-align:center; margin-top:14px; padding-top:10px;
              border-top:1px dashed #ccc; color:#aaa; font-size:9px; line-height:1.7; }
  </style>
</head>
<body>
<div class="wrap">

  <div class="header">
    <div class="logo">STRUK PENGEMBALIAN ALAT</div>
    <div class="sub">Sistem Peminjaman Alat</div>
    <div class="no">#{{ str_pad($pengembalian->id, 6, '0', STR_PAD_LEFT) }}</div>
    <div class="sub" style="margin-top:3px">{{ now()->format('d/m/Y H:i') }}</div>
  </div>

  <div class="sec-title">Informasi Peminjam</div>
  <div class="info-row">
    <span class="lbl">Nama</span>
    <span class="val">{{ $pengembalian->peminjaman->user->name }}</span>
  </div>
  <div class="info-row">
    <span class="lbl">Email</span>
    <span class="val" style="font-size:9.5px">{{ $pengembalian->peminjaman->user->email }}</span>
  </div>

  <div class="sec-title">Informasi Waktu</div>
  <div class="info-row">
    <span class="lbl">Tgl Pinjam</span>
    <span class="val">
      {{ optional($pengembalian->peminjaman->tanggal_pinjam)->format('d/m/Y') ?? '-' }}
    </span>
  </div>
  <div class="info-row">
    <span class="lbl">Rencana Kembali</span>
    <span class="val">
      {{-- ✅ FIX: gunakan tanggal_kembali_rencana --}}
      {{ optional($pengembalian->peminjaman->tanggal_kembali_rencana)->format('d/m/Y') ?? '-' }}
    </span>
  </div>
  <div class="info-row">
    <span class="lbl">Kembali Aktual</span>
    <span class="val">{{ $pengembalian->tanggal_kembali_aktual->format('d/m/Y') }}</span>
  </div>
  <div class="info-row">
    <span class="lbl">Keterlambatan</span>
    <span class="val" style="{{ ($pengembalian->keterlambatan_hari ?? 0) > 0 ? 'color:#dc3545' : 'color:#28a745' }}">
      {{ ($pengembalian->keterlambatan_hari ?? 0) > 0
          ? $pengembalian->keterlambatan_hari . ' hari'
          : 'Tepat waktu' }}
    </span>
  </div>

  <hr class="divider">

  <div class="sec-title">Detail Alat Dikembalikan</div>
  <table>
    <thead>
      <tr>
        <th style="width:36%">Alat</th>
        <th style="width:8%;text-align:center">Jumlah</th>
        <th style="width:22%">Kondisi</th>
        <th style="width:34%;text-align:right">Denda Item</th>
      </tr>
    </thead>
    <tbody>
      {{-- ✅ Gunakan $detailSource yang sudah di-resolve di controller --}}
      @forelse($detailSource as $d)
        @php
          $harga    = $d->alat->harga_beli ?? 0;
          $subtotal = $harga * $d->jumlah_kembali;
          $dendaItem = match($d->kondisi_kembali) {
              'rusak_ringan' => $subtotal * (($denda->denda_rusak_ringan ?? 10) / 100),
              'rusak_berat'  => $subtotal * (($denda->denda_rusak_berat  ?? 50) / 100),
              'hilang'       => $subtotal,
              default        => 0,
          };
          $badgeCls = match($d->kondisi_kembali) {
              'baik'         => 'b-baik',
              'rusak_ringan' => 'b-ringan',
              'rusak_berat'  => 'b-berat',
              'hilang'       => 'b-hilang',
              default        => 'b-baik',
          };
          $lblKondisi = match($d->kondisi_kembali) {
              'baik'         => 'Baik',
              'rusak_ringan' => 'Rusak Ringan',
              'rusak_berat'  => 'Rusak Berat',
              'hilang'       => 'Hilang',
              default        => $d->kondisi_kembali,
          };
        @endphp
        <tr>
          <td>
            {{ $d->alat->nama_alat }}
            @if(!empty($d->keterangan_kondisi) && $d->keterangan_kondisi !== '-')
              <br><span style="color:#999;font-size:8.5px">{{ $d->keterangan_kondisi }}</span>
            @endif
          </td>
          <td style="text-align:center">{{ $d->jumlah_kembali }}</td>
          <td><span class="badge {{ $badgeCls }}">{{ $lblKondisi }}</span></td>
          <td style="text-align:right; {{ $dendaItem > 0 ? 'color:#dc3545;font-weight:bold' : 'color:#28a745' }}">
            Rp {{ number_format($dendaItem, 0, ',', '.') }}
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="4" style="text-align:center;color:#aaa;padding:10px">
            Tidak ada data alat
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <hr class="divider">

  <div class="summary">
    <div class="s-row">
      <span>Denda Keterlambatan</span>
      <span>Rp {{ number_format($pengembalian->denda_keterlambatan ?? 0, 0, ',', '.') }}</span>
    </div>
    <div class="s-row">
      <span>Denda Kerusakan / Kehilangan</span>
      <span>Rp {{ number_format($pengembalian->denda_kerusakan ?? 0, 0, ',', '.') }}</span>
    </div>
    <div class="s-row s-total">
      <span>TOTAL DENDA</span>
      <span>Rp {{ number_format($pengembalian->total_denda ?? 0, 0, ',', '.') }}</span>
    </div>
  </div>

  <div class="status-box">
    @if(($pengembalian->total_denda ?? 0) == 0)
      <span class="s-badge s-bebas">&#10003; BEBAS DENDA</span>
    @else
      <span class="s-badge s-lunas">&#10003; LUNAS</span>
    @endif
  </div>

  <div class="footer">
    <p>Dokumen ini digenerate otomatis oleh sistem.</p>
    <p>Simpan sebagai bukti resmi pengembalian alat.</p>
    <p>&copy; {{ date('Y') }} Sistem Peminjaman Alat</p>
  </div>

</div>
</body>
</html>