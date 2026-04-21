<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: Arial, sans-serif; color: #333; }
    .header { background: #4e73df; color: white; padding: 20px; text-align: center; }
    .body { padding: 20px; }
    .footer { padding: 15px; text-align: center; font-size: 12px; color: #888; }
  </style>
</head>
<body>
  <div class="header">
    <h2>Struk Pengembalian Alat</h2>
  </div>
  <div class="body">
    <p>Yth. <strong>{{ $pengembalian->peminjaman->user->name }}</strong>,</p>
    <p>Berikut adalah struk pengembalian alat Anda. Silakan lihat lampiran PDF untuk detail lengkap.</p>
    <p>Terima kasih telah menggunakan layanan kami.</p>
  </div>
  <div class="footer">
    <p>Email ini dikirim otomatis oleh sistem. Mohon tidak membalas email ini.</p>
  </div>
</body>
</html>