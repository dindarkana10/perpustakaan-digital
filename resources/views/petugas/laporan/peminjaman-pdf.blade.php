<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Peminjaman</title>
    <style>
        /* Reset & Base Style */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            color: #333;
            line-height: 1.5;
            padding: 40px;
        }

        /* Header Style */
        .header {
            display: flex;
            align-items: center;
            border-bottom: 2px solid #0056b3;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }

        .header-text {
            text-align: left;
        }

        .header h1 {
            font-size: 20pt;
            color: #0056b3;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 12pt;
            color: #666;
            font-weight: normal;
        }

        /* Info Section - Modern Card Style */
        .info-section {
            margin-bottom: 25px;
            width: 100%;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 5px 0;
            vertical-align: top;
        }

        .label {
            color: #0056b3;
            font-weight: bold;
            width: 120px;
        }

        /* Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background-color: #fff;
        }

        .data-table th {
            background-color: #0056b3;
            color: #ffffff;
            text-align: left;
            padding: 12px 10px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
            border: 1px solid #004494;
        }

        .data-table td {
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
            border-left: 1px solid #f0f0f0;
            border-right: 1px solid #f0f0f0;
            vertical-align: middle;
        }

        .data-table tr:nth-child(even) {
            background-color: #f8faff;
        }

        /* Status Badges - Rounded & Clean */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 7.5pt;
            font-weight: bold;
            text-align: center;
            min-width: 80px;
        }

        .badge-warning { background-color: #fff4de; color: #ffa800; }
        .badge-info { background-color: #e1f0ff; color: #3699ff; }
        .badge-success { background-color: #c9f7f5; color: #1bc5bd; }
        .badge-danger { background-color: #ffe2e5; color: #f64e60; }

        /* List styling */
        .alat-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .alat-item {
            display: block;
            font-weight: 600;
            color: #2c3e50;
        }

        .alat-qty {
            font-size: 8pt;
            color: #7e8299;
        }

        /* Footer / Signature */
        .footer-container {
            margin-top: 50px;
            width: 100%;
        }

        .signature-wrapper {
            float: right;
            width: 200px;
            text-align: center;
        }

        .signature-space {
            height: 70px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            color: #0056b3;
        }

        /* Utilities */
        .text-center { text-align: center; }
        .clear { clear: both; }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-text">
            <h1>Laporan Peminjaman</h1>
            <h2>Sistem Informasi Inventaris Alat</h2>
        </div>
    </div>

    <div class="info-section">
        <table class="info-table">
            <tr>
                <td class="label">Periode</td>
                <td>: 
                    @if($tanggal_mulai == 'Semua')
                        <span style="font-style: italic;">Seluruh Record Data</span>
                    @else
                        <strong>{{ $tanggal_mulai }}</strong> s/d <strong>{{ $tanggal_selesai }}</strong>
                    @endif
                </td>
                <td class="label">Dicetak Pada</td>
                <td>: {{ \Carbon\Carbon::now()->format('d F Y, H:i') }}</td>
            </tr>
            <tr>
                <td class="label">Status Data</td>
                <td>: {{ $status == 'Semua' ? 'Semua Status' : ucwords(str_replace('_', ' ', $status)) }}</td>
                <td class="label">Petugas Cetak</td>
                <td>: {{ Auth::user()->name }}</td>
            </tr>
        </table>
    </div>

    @if($peminjaman->isEmpty())
        <div style="text-align: center; padding: 40px; border: 2px dashed #0056b3; color: #0056b3; border-radius: 10px;">
            <p>Data peminjaman tidak ditemukan untuk kriteria yang dipilih.</p>
        </div>
    @else
        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="12%">Tgl Pinjam</th>
                    <th width="18%">Peminjam</th>
                    <th width="25%">Detail Alat & Jumlah</th>
                    <th width="12%">Tgl Kembali</th>
                    <th width="13%" class="text-center">Status</th>
                    <th width="15%">Verifikator</th>
                </tr>
            </thead>
            <tbody>
                @foreach($peminjaman as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td><strong>{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}</strong></td>
                        <td>{{ $item->user->name }}</td>
                        <td>
                            @foreach($item->details as $detail)
                                <div style="margin-bottom: 4px;">
                                    <span class="alat-item">{{ $detail->alat->nama_alat }}</span>
                                    <span class="alat-qty">Jumlah: {{ $detail->jumlah }} unit</span>
                                </div>
                            @endforeach
                        </td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d/m/Y') }}</td>
                        <td class="text-center">
                            @if($item->status == 'menunggu_persetujuan')
                                <span class="badge badge-warning">Menunggu</span>
                            @elseif($item->status == 'dipinjam')
                                <span class="badge badge-info">Dipinjam</span>
                            @elseif($item->status == 'dikembalikan')
                                <span class="badge badge-success">Selesai</span>
                            @elseif($item->status == 'ditolak')
                                <span class="badge badge-danger">Ditolak</span>
                            @elseif($item->status == 'terlambat')
                                <span class="badge badge-danger">Terlambat</span>
                            @endif
                        </td>
                        <td style="font-size: 8pt; color: #666;">{{ $item->petugas->name ?? 'Belum Diverifikasi' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer-container">
        <div class="signature-wrapper">
            <p>Dicetak Oleh,</p>
            <div class="signature-space"></div>
            <p class="signature-name">{{ Auth::user()->name }}</p>
            <p style="text-transform: capitalize;">{{ Auth::user()->role }}</p>
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>