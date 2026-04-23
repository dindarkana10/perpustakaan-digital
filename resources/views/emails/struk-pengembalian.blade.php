<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body {
      margin: 0; padding: 0;
      font-family: 'Segoe UI', Arial, sans-serif;
      background: #f4f6fb; color: #333;
    }
    .email-wrap {
      max-width: 560px; margin: 30px auto;
      background: #fff; border-radius: 12px;
      overflow: hidden; box-shadow: 0 2px 16px rgba(0,0,0,.08);
    }
    .email-header {
      background: linear-gradient(135deg, #1a3c6e, #2563eb);
      color: #fff; padding: 28px 32px; text-align: center;
    }
    .email-header h1 { margin: 0; font-size: 22px; letter-spacing: .5px; }
    .email-header p  { margin: 6px 0 0; font-size: 13px; opacity: .85; }
    .email-body { padding: 28px 32px; }
    .email-body p { margin: 0 0 12px; line-height: 1.7; font-size: 14px; }
    .info-box {
      background: #f0f5ff; border-left: 4px solid #2563eb;
      border-radius: 6px; padding: 14px 18px; margin: 18px 0;
    }
    .info-box .row { display: flex; justify-content: space-between;
                     font-size: 13px; padding: 4px 0; }
    .info-box .lbl { color: #666; }
    .info-box .val { font-weight: 600; color: #222; }
    .total-box {
      background: #fff8f0; border: 1.5px solid #f59e0b;
      border-radius: 8px; padding: 14px 18px; margin: 18px 0;
      display: flex; justify-content: space-between; align-items: center;
    }
    .total-box .label { font-size: 14px; font-weight: 600; color: #92400e; }
    .total-box .amount { font-size: 20px; font-weight: 700; color: #dc2626; }
    .total-box .amount.bebas { color: #16a34a; font-size: 16px; }
    .attach-note {
      background: #f0fdf4; border: 1px solid #bbf7d0;
      border-radius: 8px; padding: 12px 16px; margin: 16px 0;
      font-size: 13px; color: #166534;
    }
    .email-footer {
      background: #f8fafc; padding: 18px 32px; text-align: center;
      font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0;
    }
    .btn-primary {
      display: inline-block; background: #2563eb; color: #fff;
      padding: 10px 24px; border-radius: 6px; text-decoration: none;
      font-size: 14px; font-weight: 600; margin: 8px 0;
    }
  </style>
</head>
<body>
  <div class="email-wrap">
    <div class="email-header">
      <h1>&#128218; Perpustakaan Digital</h1>
      <p>Struk Pengembalian Buku Resmi</p>
    </div>

    <div class="email-body">
      <p>Yth. <strong>{{ $pengembalian->peminjaman->user->name ?? 'Peminjam' }}</strong>,</p>
      <p>
        Pengembalian buku Anda telah <strong>dikonfirmasi</strong> oleh petugas perpustakaan.
        Berikut adalah ringkasan transaksi pengembalian Anda.
      </p>

      <div class="info-box">
        <div class="row">
          <span class="lbl">No. Transaksi</span>
          <span class="val">#{{ str_pad($pengembalian->id, 6, '0', STR_PAD_LEFT) }}</span>
        </div>
        <div class="row">
          <span class="lbl">Tanggal Kembali</span>
          <span class="val">
            {{ \Carbon\Carbon::parse($pengembalian->tanggal_kembali_aktual)->format('d/m/Y') }}
          </span>
        </div>
        <div class="row">
          <span class="lbl">Keterlambatan</span>
          <span class="val" style="{{ ($pengembalian->keterlambatan_hari ?? 0) > 0 ? 'color:#dc2626' : 'color:#16a34a' }}">
            {{ ($pengembalian->keterlambatan_hari ?? 0) > 0
                ? $pengembalian->keterlambatan_hari . ' hari'
                : 'Tepat waktu' }}
          </span>
        </div>
        <div class="row">
          <span class="lbl">Status Pembayaran</span>
          <span class="val" style="color:#16a34a">
            {{ $pengembalian->status_pembayaran === 'tidak_ada_denda' ? 'Tidak Ada Denda' : 'Lunas' }}
          </span>
        </div>
      </div>

      <div class="total-box">
        <span class="label">Total Denda</span>
        @if(($pengembalian->total_denda ?? 0) == 0)
          <span class="amount bebas">&#10003; Bebas Denda</span>
        @else
          <span class="amount">Rp {{ number_format($pengembalian->total_denda, 0, ',', '.') }}</span>
        @endif
      </div>

      <div class="attach-note">
        &#128206; <strong>Struk PDF</strong> terlampir pada email ini.
        Simpan sebagai bukti resmi pengembalian buku Anda.
      </div>

      <p style="margin-top:16px">
        Terima kasih telah menggunakan layanan perpustakaan kami.
        Jika ada pertanyaan, silakan hubungi petugas perpustakaan.
      </p>
    </div>

    <div class="email-footer">
      <p>Email ini dikirim otomatis. Mohon tidak membalas email ini.</p>
      <p>&copy; {{ date('Y') }} Perpustakaan Digital. All rights reserved.</p>
    </div>
  </div>
</body>
</html>