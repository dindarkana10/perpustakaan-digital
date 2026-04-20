<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengembalian;
use App\Models\Peminjaman;
use App\Models\Denda;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PetugasPengembalianController extends Controller
{
    public function index()
    {
        // Ambil: (1) diajukan, (2) dikonfirmasi tapi belum_lunas
        $pengembalians = Pengembalian::with([
                'peminjaman.user',
                'peminjaman.details.alat',
                'details.alat',          // ← detail kondisi kembali dari peminjam
            ])
            ->where(function ($q) {
                $q->where('status_pengembalian', 'diajukan')
                  ->orWhere(function ($q2) {
                      $q2->where('status_pengembalian', 'dikonfirmasi')
                         ->where('status_pembayaran', 'belum_lunas');
                  });
            })
            ->orderBy('created_at', 'asc')
            ->get();

        $denda = Denda::first();

        foreach ($pengembalians as $item) {
            // ── Hitung total harga alat ───────────────────────────────────────────
            $totalHargaAlat = $item->peminjaman->details->sum(function ($detail) {
                return ($detail->alat->harga_beli ?? 0) * $detail->jumlah;
            });

            $item->total_harga_alat = $totalHargaAlat;

            // ── Denda keterlambatan otomatis ──────────────────────────────────────
            $item->denda_keterlambatan_otomatis =
                $item->keterlambatan_hari * ($denda->denda_per_hari ?? 5000);

            // ── Denda kerusakan otomatis berdasarkan kondisi kembali per alat ─────
            //
            // Iterasi detail pengembalian yang sudah diisi peminjam.
            // Setiap alat dengan kondisi rusak_ringan / rusak_berat dihitung
            // berdasarkan persentase dari (harga_beli × jumlah) alat tersebut.
            // Kondisi 'hilang' dikenakan denda penuh 100% harga alat (biaya penggantian).
            //
            $dendaKerusakanOtomatis = 0;

            foreach ($item->details as $detailKembali) {
                $hargaSatuan = $detailKembali->alat->harga_beli ?? 0;
                $subtotal    = $hargaSatuan * $detailKembali->jumlah_kembali;

                switch ($detailKembali->kondisi_kembali) {
                    case 'rusak_ringan':
                        $dendaKerusakanOtomatis +=
                            $subtotal * (($denda->denda_rusak_ringan ?? 10) / 100);
                        break;

                    case 'rusak_berat':
                        $dendaKerusakanOtomatis +=
                            $subtotal * (($denda->denda_rusak_berat ?? 50) / 100);
                        break;

                    case 'hilang':
                        // Wajib ganti rugi penuh
                        $dendaKerusakanOtomatis += $subtotal;
                        break;

                    // 'baik' → tidak ada denda kerusakan
                }
            }

            $item->denda_kerusakan_otomatis = (int) $dendaKerusakanOtomatis;

            // Breakdown per kondisi (untuk ditampilkan di modal)
            $item->breakdown_kondisi = $item->details->map(function ($d) use ($denda) {
                $hargaSatuan = $d->alat->harga_beli ?? 0;
                $subtotal    = $hargaSatuan * $d->jumlah_kembali;

                $dendaItem = match ($d->kondisi_kembali) {
                    'rusak_ringan' => $subtotal * (($denda->denda_rusak_ringan ?? 10) / 100),
                    'rusak_berat'  => $subtotal * (($denda->denda_rusak_berat  ?? 50) / 100),
                    'hilang'       => $subtotal,
                    default        => 0,
                };

                return [
                    'nama_alat'       => $d->alat->nama_alat,
                    'jumlah'          => $d->jumlah_kembali,
                    'kondisi_kembali' => $d->kondisi_kembali,
                    'keterangan'      => $d->keterangan_kondisi,
                    'harga_satuan'    => $hargaSatuan,
                    'subtotal_harga'  => $subtotal,
                    'denda_item'      => (int) $dendaItem,
                ];
            });
        }

        return view('petugas.pengembalian.index', compact('pengembalians', 'denda'));
    }

    public function konfirmasi(Request $request, $id)
    {
        $request->validate([
            'denda_keterlambatan' => 'nullable|numeric|min:0',
            'denda_kerusakan'     => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $pengembalian = Pengembalian::findOrFail($id);

            $pengembalian->denda_keterlambatan = $request->denda_keterlambatan ?? 0;
            $pengembalian->denda_kerusakan     = $request->denda_kerusakan ?? 0;
            $pengembalian->total_denda         =
                $pengembalian->denda_keterlambatan + $pengembalian->denda_kerusakan;
            $pengembalian->status_pengembalian = 'dikonfirmasi';
            $pengembalian->status_pembayaran   =
                $pengembalian->total_denda > 0 ? 'belum_lunas' : 'lunas';
            $pengembalian->petugas_id          = Auth::id();
            $pengembalian->save();

            $peminjaman         = Peminjaman::findOrFail($pengembalian->peminjaman_id);
            $peminjaman->status = 'dikembalikan';
            $peminjaman->save();

            DB::commit();

            return redirect()->route('petugas.pengembalian.index')
                ->with('success', 'Pengembalian berhasil dikonfirmasi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function lunasi($id)
    {
        $pengembalian                    = Pengembalian::findOrFail($id);
        $pengembalian->status_pembayaran = 'lunas';
        $pengembalian->save();

        return redirect()->route('petugas.pengembalian.index')
            ->with('success', 'Pembayaran berhasil ditandai lunas. Data dipindahkan ke riwayat.');
    }

    public function riwayat()
    {
        $pengembalians = Pengembalian::with('peminjaman.user')
            ->where('status_pengembalian', 'dikonfirmasi')
            ->where('status_pembayaran', 'lunas')
            ->orderBy('tanggal_kembali_aktual', 'desc')
            ->get();

        return view('petugas.pengembalian.riwayat', compact('pengembalians'));
    }
}