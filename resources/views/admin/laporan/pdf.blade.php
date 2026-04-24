<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Peminjaman Buku</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
        .header { text-align: center; border-bottom: 2px solid #444; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 22px; text-transform: uppercase; letter-spacing: 2px; }
        .header p { margin: 5px 0 0; font-size: 12px; color: #666; }
        
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { vertical-align: top; }
        .summary-box { border: 1px solid #ddd; background-color: #fcfcfc; padding: 10px; }
        .summary-box h3 { margin-top: 0; font-size: 12px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        
        table.data { width: 100%; border-collapse: collapse; }
        table.data th { background-color: #f2f2f2; border: 1px solid #ccc; padding: 8px 5px; text-align: left; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        table.data td { border: 1px solid #ccc; padding: 6px 5px; vertical-align: top; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .badge { display: inline-block; padding: 2px 5px; font-size: 9px; border-radius: 3px; background-color: #eee; }
        
        .footer { margin-top: 40px; border-top: 1px solid #eee; padding-top: 10px; font-size: 9px; color: #888; }
        .page-number:after { content: counter(page); }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Peminjaman Buku</h1>
        <p>Sistem Manajemen Perpustakaan Digital</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="60%">
                <strong>Kriteria Filter:</strong><br>
                <span style="color: #555;">
                    Kategori: {{ $request->filled('kategori_id') ? \App\Models\KategoriBuku::find($request->kategori_id)->nama_kategori : 'Semua' }}<br>
                    Status: {{ $request->filled('status') ? ucwords(str_replace('_', ' ', $request->status)) : 'Semua Status' }}<br>
                    Periode: {{ $request->filled('tgl_pinjam_awal') ? $request->tgl_pinjam_awal : 'Awal' }} s/d {{ $request->filled('tgl_pinjam_akhir') ? $request->tgl_pinjam_akhir : 'Sekarang' }}
                </span>
            </td>
            <td width="40%">
                <div class="summary-box">
                    <h3>Ringkasan Laporan</h3>
                    <table width="100%">
                        <tr>
                            <td>Total Transaksi:</td>
                            <td class="text-right font-bold">{{ $summary['total_peminjaman'] }}</td>
                        </tr>
                        <tr>
                            <td>Total Kembali:</td>
                            <td class="text-right font-bold">{{ $summary['total_pengembalian'] }}</td>
                        </tr>
                        <tr>
                            <td>Total Denda:</td>
                            <td class="text-right font-bold" style="color: #d00;">Rp {{ number_format($summary['total_denda'], 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th width="20" class="text-center">No</th>
                <th width="100">Nama Peminjam</th>
                <th>Judul Buku</th>
                <th width="80">Kategori</th>
                <th width="70">Pinjam</th>
                <th width="70">Kembali</th>
                <th width="80" class="text-center">Status</th>
                <th width="80" class="text-right">Denda</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporan as $item)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="font-bold">{{ $item->peminjaman->user->name }}</td>
                <td>{{ $item->buku->judul_buku }}</td>
                <td>{{ $item->buku->kategoriBuku->nama_kategori }}</td>
                <td>{{ $item->peminjaman->tanggal_pinjam->format('d/m/Y') }}</td>
                <td>{{ $item->peminjaman->pengembalian ? $item->peminjaman->pengembalian->tanggal_kembali_aktual->format('d/m/Y') : '-' }}</td>
                <td class="text-center">
                    <span class="badge">{{ $item->peminjaman->status_label }}</span>
                </td>
               @php
                    $detailKembali = $item->peminjaman->pengembalian
                        ?->details
                        ->firstWhere('buku_id', $item->buku_id);

                    $dendaItem = $detailKembali
                        ? ($detailKembali->denda_kerusakan_buku + $detailKembali->biaya_perbaikan + $detailKembali->biaya_penggantian)
                        : 0;
                @endphp
                <td class="text-right">
                    Rp {{ number_format($dendaItem, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f9f9f9; font-weight: bold;">
                <td colspan="7" class="text-right">TOTAL DENDA KESELURUHAN</td>
                <td class="text-right" style="color: #d00;">Rp {{ number_format($summary['total_denda'], 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <table width="100%">
            <tr>
                <td>Dicetak oleh: {{ auth()->user()->name }} pada {{ now()->format('d/m/Y H:i:s') }}</td>
                <td class="text-right">Halaman <span class="page-number"></span></td>
            </tr>
        </table>
    </div>
</body>
</html>
