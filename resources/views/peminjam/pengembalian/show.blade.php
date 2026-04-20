<div class="row mb-3">
  <div class="col-md-3">
    <label class="form-label fw-bold">Tanggal Pinjam</label>
    <input type="text" class="form-control" 
           value="{{ \Carbon\Carbon::parse($pengembalian->peminjaman->tanggal_pinjam)->format('d/m/Y') }}" readonly>
  </div>
  <div class="col-md-3">
    <label class="form-label fw-bold">Tanggal Kembali Rencana</label>
    <input type="text" class="form-control" 
           value="{{ \Carbon\Carbon::parse($pengembalian->peminjaman->tanggal_kembali_rencana)->format('d/m/Y') }}" readonly>
  </div>
  <div class="col-md-3">
    <label class="form-label fw-bold">Tanggal Kembali Aktual</label>
    <input type="text" class="form-control" 
           value="{{ \Carbon\Carbon::parse($pengembalian->tanggal_kembali_aktual)->format('d/m/Y') }}" readonly>
  </div>
  <div class="col-md-3">
    <label class="form-label fw-bold">Keterlambatan</label>
    <input type="text" class="form-control" 
           value="{{ $pengembalian->keterlambatan_hari > 0 ? $pengembalian->keterlambatan_hari . ' hari' : 'Tepat Waktu' }}" readonly>
  </div>
</div>

@if($pengembalian->status_pengembalian == 'dikonfirmasi')
<div class="row mb-3">
  <div class="col-md-4">
    <label class="form-label fw-bold">Denda Keterlambatan</label>
    <input type="text" class="form-control" 
           value="Rp {{ number_format($pengembalian->denda_keterlambatan, 0, ',', '.') }}" readonly>
  </div>
  <div class="col-md-4">
    <label class="form-label fw-bold">Denda Kerusakan</label>
    <input type="text" class="form-control" 
           value="Rp {{ number_format($pengembalian->denda_kerusakan, 0, ',', '.') }}" readonly>
  </div>
  <div class="col-md-4">
    <label class="form-label fw-bold">Total Denda</label>
    <input type="text" class="form-control fw-bold text-danger" 
           value="Rp {{ number_format($pengembalian->total_denda, 0, ',', '.') }}" readonly>
  </div>
</div>

<div class="row mb-3">
  <div class="col-md-6">
    <label class="form-label fw-bold">Status Pengembalian</label><br>
    <span class="badge bg-success fs-6">Dikonfirmasi</span>
  </div>
  <div class="col-md-6">
    <label class="form-label fw-bold">Status Pembayaran</label><br>
    @if($pengembalian->total_denda > 0)
      @if($pengembalian->status_pembayaran == 'lunas')
        <span class="badge bg-success fs-6">Lunas</span>
      @else
        <span class="badge bg-danger fs-6">Belum Lunas</span>
      @endif
    @else
      <span class="badge bg-secondary fs-6">Tidak Ada Denda</span>
    @endif
  </div>
</div>
@else
<div class="alert alert-warning">
  <iconify-icon icon="solar:clock-circle-outline" width="20"></iconify-icon>
  Status: <strong>Menunggu Validasi Petugas</strong>
</div>
@endif

<hr>
<h6 class="mb-3 fw-semibold">Detail Alat</h6>
<div class="table-responsive">
  <table class="table table-bordered">
    <thead class="table-light">
      <tr>
        <th>No</th>
        <th>Nama Alat</th>
        <th>Jumlah</th>
        <th>Kondisi Pinjam</th>
        @if($pengembalian->status_pengembalian == 'dikonfirmasi')
          <th>Kondisi Kembali</th>
          <th>Keterangan</th>
        @endif
      </tr>
    </thead>
    <tbody>
      @if($pengembalian->status_pengembalian == 'dikonfirmasi')
        {{-- Gunakan peminjaman->details sebagai acuan utama agar semua alat tampil --}}
        @foreach($pengembalian->peminjaman->details as $peminjamanDetail)
          @php
            $returnDetail = $pengembalian->details->where('alat_id', $peminjamanDetail->alat_id)->first();
          @endphp
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $peminjamanDetail->alat->nama_alat ?? '-' }}</td>
            <td class="text-center">
              {{ $returnDetail ? $returnDetail->jumlah_kembali : $peminjamanDetail->jumlah }}
            </td>
            <td>
              @if($peminjamanDetail->kondisi_pinjam == 'baik')
                <span class="badge bg-success">Baik</span>
              @elseif($peminjamanDetail->kondisi_pinjam == 'rusak_ringan')
                <span class="badge bg-warning text-dark">Rusak Ringan</span>
              @else
                <span class="badge bg-danger">Rusak Berat</span>
              @endif
            </td>
            <td>
              @if($returnDetail)
                @if($returnDetail->kondisi_kembali == 'baik')
                  <span class="badge bg-success">Baik</span>
                @elseif($returnDetail->kondisi_kembali == 'rusak_ringan')
                  <span class="badge bg-warning text-dark">Rusak Ringan</span>
                @elseif($returnDetail->kondisi_kembali == 'rusak_berat')
                  <span class="badge bg-danger">Rusak Berat</span>
                @else
                  <span class="badge bg-dark">Hilang</span>
                @endif
              @else
                <span class="badge bg-secondary">Belum dicatat</span>
              @endif
            </td>
            <td>{{ $returnDetail->keterangan_kondisi ?? '-' }}</td>
          </tr>
        @endforeach
      @else
        {{-- Status diajukan: tampilkan dari peminjaman->details --}}
        @forelse($pengembalian->peminjaman->details as $detail)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $detail->alat->nama_alat ?? '-' }}</td>
            <td class="text-center">{{ $detail->jumlah }}</td>
            <td>
              @if($detail->kondisi_pinjam == 'baik')
                <span class="badge bg-success">Baik</span>
              @elseif($detail->kondisi_pinjam == 'rusak_ringan')
                <span class="badge bg-warning text-dark">Rusak Ringan</span>
              @else
                <span class="badge bg-danger">Rusak Berat</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="text-center text-muted">Tidak ada data alat</td>
          </tr>
        @endforelse
      @endif
    </tbody>
  </table>
</div>