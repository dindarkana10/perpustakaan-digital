<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pengembalian</title>
    <style>
        /* Reset & Base Style */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8.5pt; /* Sedikit lebih kecil karena kolom denda cukup banyak */
            color: #333;
            line-height: 1.4;
            padding: 30px;
        }

        /* Header Style */
        .header {
            border-bottom: 2px solid #0056b3;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18pt;
            color: #0056b3;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3px;
        }

        .header h2 {
            font-size: 11pt;
            color: #666;
            font-weight: normal;
        }

        /* Info Section */
        .info-section {
            margin-bottom: 20px;
            width: 100%;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .label {
            color: #0056b3;
            font-weight: bold;
            width: 130px;
        }

        /* Statistics Cards */
        .statistics {
            margin-bottom: 20px;
            width: 100%;
            display: table;
            border-spacing: 5px 0;
        }

        .stat-box {
            display: table-cell;
            background: #f8faff;
            border: 1px solid #d1d9e6;
            padding: 10px;
            text-align: center;
            border-radius: 6px;
        }

        .stat-box .number {
            font-size: 14pt;
            font-weight: bold;
            color: #0056b3;
            display: block;
        }

        .stat-box .label-stat {
            font-size: 7.5pt;
            color: #6c757d;
            text-transform: uppercase;
            margin-top: 3px;
        }

        /* Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .data-table th {
            background-color: #0056b3;
            color: #ffffff;
            text-align: center;
            padding: 10px 5px;
            font-weight: bold;
            font-size: 8pt;
            border: 1px solid #004494;
        }

        .data-table td {
            padding: 8px 5px;
            border: 1px solid #e0e0e0;
            vertical-align: middle;
        }

        .data-table tr:nth-child(even) {
            background-color: #f8faff;
        }

        /* Footer Tabel (Total) */
        .data-table tfoot td {
            background-color: #f0f4f8;
            font-weight: bold;
            color: #0056b3;
            border-top: 2px solid #0056b3;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 7pt;
            font-weight: bold;
            text-align: center;
        }

        .badge-success { background-color: #c9f7f5; color: #1bc5bd; }
        .badge-danger { background-color: #ffe2e5; color: #f64e60; }
        .badge-warning { background-color: #fff4de; color: #ffa800; }

        /* Summary Box */
        .summary-container {
            margin-top: 20px;
            padding: 15px;
            background-color: #ffffff;
            border: 1px solid #0056b3;
            border-left: 5px solid #0056b3;
            border-radius: 4px;
        }

        .summary-container h3 {
            color: #0056b3;
            font-size: 10pt;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }

        /* List styling */
        .alat-list {
            list-style: none;
            font-size: 7.5pt;
        }

        .alat-item {
            font-weight: bold;
            color: #444;
        }

        /* Footer Signature */
        .footer-sig {
            margin-top: 40px;
            float: right;
            width: 200px;
            text-align: center;
        }

        .sig-space { height: 60px; }

        .sig-name {
            font-weight: bold;
            color: #0056b3;
            border-bottom: 1px solid #0056b3;
            display: inline-block;
            margin-bottom: 2px;
        }

        /* Utils */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .clear { clear: both; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Pengembalian</h1>
        <h2>Sistem Informasi Inventaris Alat</h2>
    </div>

    <div class="info-section">
        <table class="info-table">
            <tr>
                <td class="label">Periode Laporan</td>
                <td>: 
                    @if($tanggal_mulai == 'Semua')
                        <strong>Semua Data</strong>
                    @else
                        <strong>{{ $tanggal_mulai }}</strong> s/d <strong>{{ $tanggal_selesai }}</strong>
                    @endif
                </td>
                <td class="label">Tanggal Cetak</td>
                <td>: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td class="label">Status Kembali</td>
                <td>: <span class="badge badge-success">Dikonfirmasi</span></td>
                <td class="label">Dicetak Oleh</td>
                <td>: {{ Auth::user()->name }}</td>
            </tr>
        </table>
    </div>

    @if($pengembalian->isEmpty())
        <div style="text-align: center; padding: 30px; border: 1px dashed #ccc; color: #999;">
            Tidak ada data pengembalian untuk periode ini.
        </div>
    @else
        <table class="data-table">
            <thead>
                <tr>
                    <th width="3%">No</th>
                    <th width="10%">Tgl Kembali</th>
                    <th width="12%">Peminjam</th>
                    <th width="20%">Detail Alat</th>
                    <th width="8%">Durasi</th>
                    <th width="10%">Denda Telat</th>
                    <th width="10%">Denda Rusak</th>
                    <th width="10%">Total</th>
                    <th width="7%">Bayar</th>
                    <th width="10%">Petugas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pengembalian as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal_kembali_aktual)->format('d/m/Y') }}</td>
                        <td><strong>{{ $item->peminjaman->user->name }}</strong></td>
                        <td>
                            <ul class="alat-list">
                                @foreach($item->peminjaman->details as $detail)
                                    <li><span class="alat-item">{{ $detail->alat->nama_alat }}</span> ({{ $detail->jumlah }})</li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="text-center">
                            @if($item->keterlambatan_hari > 0)
                                <span class="badge badge-danger">{{ $item->keterlambatan_hari }} Hari</span>
                            @else
                                <span class="badge badge-success">Tepat</span>
                            @endif
                        </td>
                        <td class="text-right">Rp {{ number_format($item->denda_keterlambatan, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item->denda_kerusakan, 0, ',', '.') }}</td>
                        <td class="text-right"><strong>Rp {{ number_format($item->total_denda, 0, ',', '.') }}</strong></td>
                        <td class="text-center">
                            @if($item->status_pembayaran == 'lunas')
                                <span class="badge badge-success">Lunas</span>
                            @else
                                <span class="badge badge-danger">Belum</span>
                            @endif
                        </td>
                        <td style="font-size: 7.5pt;">{{ $item->petugas->name ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-right">RINGKASAN TOTAL</td>
                    <td class="text-right">Rp {{ number_format($pengembalian->sum('denda_keterlambatan'), 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($pengembalian->sum('denda_kerusakan'), 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($pengembalian->sum('total_denda'), 0, ',', '.') }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>

        <div class="summary-container">
            <h3>📊 Analisis Pengembalian</h3>
            <table style="width: 100%; font-size: 9pt;">
                <tr>
                    <td width="30%">Tingkat Ketepatan Waktu</td>
                    <td>: <strong>{{ $pengembalian->count() > 0 ? round(($pengembalian->where('keterlambatan_hari', 0)->count() / $pengembalian->count()) * 100, 1) : 0 }}%</strong> 
                        ({{ $pengembalian->where('keterlambatan_hari', 0)->count() }} dari {{ $pengembalian->count() }} transaksi)</td>
                </tr>
                <tr>
                    <td>Rasio Pelunasan Denda</td>
                    <td>: <strong>{{ $pengembalian->count() > 0 ? round(($pengembalian->where('status_pembayaran', 'lunas')->count() / $pengembalian->count()) * 100, 1) : 0 }}%</strong>
                        ({{ $pengembalian->where('status_pembayaran', 'lunas')->count() }} transaksi lunas)</td>
                </tr>
            </table>
        </div>
    @endif

    <div class="footer-sig">
        <p>Petugas Penanggung Jawab,</p>
        <div class="sig-space"></div>
        <p class="sig-name">{{ Auth::user()->name }}</p>
        <p style="text-transform: capitalize; font-size: 8pt; color: #666;">{{ Auth::user()->role }}</p>
    </div>
    <div class="clear"></div>

</body>
</html>