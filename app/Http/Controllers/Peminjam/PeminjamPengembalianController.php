<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use App\Models\Pengembalian;
use App\Models\DetailPengembalian;
use App\Models\Peminjaman;
use App\Models\Alat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PeminjamPengembalianController extends Controller
{
    public function index()
    {
        $pengembalians = Pengembalian::with(['peminjaman.details.alat', 'petugas'])
            ->whereHas('peminjaman', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->latest()
            ->get();

        return view('peminjam.pengembalian.index', compact('pengembalians'));
    }

    public function create()
    {
        $peminjamans = Peminjaman::with('details.alat')
            ->where('user_id', Auth::id())
            ->where('status', 'dipinjam')
            ->whereDoesntHave('pengembalian')
            ->get();

        return view('peminjam.pengembalian.create', compact('peminjamans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'peminjaman_id'                     => 'required|exists:peminjamen,id',
            'tanggal_kembali_aktual'            => 'required|date',
            'kondisi_kembali'                   => 'required|array',
            'kondisi_kembali.*'                 => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
            'keterangan_kondisi'                => 'nullable|array',
            'keterangan_kondisi.*'              => 'nullable|string|max:500',
        ]);

        $peminjaman = Peminjaman::with('details.alat')
            ->where('id', $request->peminjaman_id)
            ->where('user_id', Auth::id())
            ->where('status', 'dipinjam')
            ->first();

        if (!$peminjaman) {
            return redirect()->back()->with('error', 'Peminjaman tidak valid atau sudah dikembalikan.');
        }

        $existingPengembalian = Pengembalian::where('peminjaman_id', $request->peminjaman_id)->first();
        if ($existingPengembalian) {
            return redirect()->back()->with('error', 'Pengembalian sudah pernah diajukan.');
        }

        // Validasi: setiap detail peminjaman harus punya kondisi kembali
        foreach ($peminjaman->details as $detail) {
            if (!isset($request->kondisi_kembali[$detail->id])) {
                return redirect()->back()
                    ->with('error', 'Kondisi kembali untuk semua alat wajib diisi.')
                    ->withInput();
            }
        }

        // Hitung keterlambatan
        $tanggalRencana = Carbon::parse($peminjaman->tanggal_kembali_rencana)->startOfDay();
        $tanggalAktual  = Carbon::parse($request->tanggal_kembali_aktual)->startOfDay();
        $keterlambatan  = max(0, $tanggalAktual->diffInDays($tanggalRencana, false) * -1);

        DB::beginTransaction();
        try {
            $pengembalian = Pengembalian::create([
                'peminjaman_id'          => $request->peminjaman_id,
                'tanggal_kembali_aktual' => $request->tanggal_kembali_aktual,
                'keterlambatan_hari'     => $keterlambatan,
                'denda_keterlambatan'    => 0,
                'denda_kerusakan'        => 0,
                'total_denda'            => 0,
                'status_pembayaran'      => 'belum_lunas',
                'status_pengembalian'    => 'diajukan',
            ]);

            // Simpan detail kondisi kembali per alat
            foreach ($peminjaman->details as $detail) {
                $kondisiKembali     = $request->kondisi_kembali[$detail->id];
                $keteranganKondisi  = $request->keterangan_kondisi[$detail->id] ?? null;

                DetailPengembalian::create([
                    'pengembalian_id'    => $pengembalian->id,
                    'alat_id'            => $detail->alat_id,
                    'jumlah_kembali'     => $detail->jumlah,
                    'kondisi_kembali'    => $kondisiKembali,
                    'keterangan_kondisi' => $keteranganKondisi,
                    'biaya_perbaikan'    => 0,
                    'biaya_penggantian'  => 0,
                ]);

                /**
                 * Logika stok:
                 * - baik / rusak_ringan → stok_tersedia bertambah (alat kembali & masih bisa dipakai)
                 * - rusak_berat         → stok_tersedia TIDAK bertambah, stok_total dikurangi 1
                 *   (alat perlu perbaikan besar / tidak layak pakai sementara)
                 * - hilang              → stok_tersedia TIDAK bertambah, stok_total dikurangi 1
                 *   (alat benar-benar hilang)
                 *
                 * Catatan: untuk kondisi rusak_ringan stok tersedia bertambah karena masih bisa
                 * dipinjam, namun petugas bisa menyesuaikan kondisi alat secara manual di admin.
                 */
                $alat = Alat::find($detail->alat_id);
                if ($alat) {
                    if (in_array($kondisiKembali, ['baik', 'rusak_ringan'])) {
                        // Alat kembali & masih bisa digunakan → tambah stok tersedia
                        $alat->increment('stok_tersedia', $detail->jumlah);
                    } elseif (in_array($kondisiKembali, ['rusak_berat', 'hilang'])) {
                        // Alat tidak bisa digunakan lagi → kurangi stok total
                        // stok_tersedia TIDAK ditambah
                        $alat->decrement('stok_total', $detail->jumlah);
                        // Pastikan stok_total tidak minus
                        if ($alat->stok_total < 0) {
                            $alat->stok_total = 0;
                        }
                        // Pastikan stok_tersedia tidak melebihi stok_total
                        if ($alat->stok_tersedia > $alat->stok_total) {
                            $alat->stok_tersedia = $alat->stok_total;
                        }
                        $alat->save();
                    }
                }
            }

            DB::commit();

            return redirect()->route('pengembalian.index')
                ->with('success', 'Pengembalian berhasil diajukan. Silakan menunggu validasi dari petugas.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal mengajukan pengembalian: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(string $id)
    {
        $pengembalian = Pengembalian::with([
            'peminjaman.details.alat',
            'details.alat',
            'petugas',
        ])
            ->whereHas('peminjaman', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->findOrFail($id);

        return view('peminjam.pengembalian.show', compact('pengembalian'));
    }

    public function edit(string $id)
    {
        $pengembalian = Pengembalian::with(['peminjaman.details.alat', 'details'])
            ->whereHas('peminjaman', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('status_pengembalian', 'diajukan')
            ->findOrFail($id);

        return view('peminjam.pengembalian.edit', compact('pengembalian'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'tanggal_kembali_aktual' => 'required|date',
            'kondisi_kembali'        => 'required|array',
            'kondisi_kembali.*'      => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
            'keterangan_kondisi'     => 'nullable|array',
            'keterangan_kondisi.*'   => 'nullable|string|max:500',
        ]);

        $pengembalian = Pengembalian::with('peminjaman.details.alat', 'details')
            ->whereHas('peminjaman', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('status_pengembalian', 'diajukan')
            ->findOrFail($id);

        // Hitung ulang keterlambatan
        $tanggalRencana = Carbon::parse($pengembalian->peminjaman->tanggal_kembali_rencana)->startOfDay();
        $tanggalAktual  = Carbon::parse($request->tanggal_kembali_aktual)->startOfDay();
        $keterlambatan  = max(0, $tanggalAktual->diffInDays($tanggalRencana, false) * -1);

        DB::beginTransaction();
        try {
            // ── Balikkan perubahan stok dari pengajuan sebelumnya ──────────────────
            foreach ($pengembalian->details as $oldDetail) {
                $alat = Alat::find($oldDetail->alat_id);
                if (!$alat) continue;

                $peminjamanDetail = $pengembalian->peminjaman->details
                    ->firstWhere('alat_id', $oldDetail->alat_id);
                $jumlah = $peminjamanDetail ? $peminjamanDetail->jumlah : $oldDetail->jumlah_kembali;

                if (in_array($oldDetail->kondisi_kembali, ['baik', 'rusak_ringan'])) {
                    // Kembalikan stok tersedia ke kondisi semula
                    $alat->decrement('stok_tersedia', $jumlah);
                } elseif (in_array($oldDetail->kondisi_kembali, ['rusak_berat', 'hilang'])) {
                    // Kembalikan stok total ke kondisi semula
                    $alat->increment('stok_total', $jumlah);
                }
            }

            // ── Update tanggal & keterlambatan ────────────────────────────────────
            $pengembalian->update([
                'tanggal_kembali_aktual' => $request->tanggal_kembali_aktual,
                'keterlambatan_hari'     => $keterlambatan,
            ]);

            // ── Update detail kondisi & terapkan stok baru ────────────────────────
            foreach ($pengembalian->peminjaman->details as $detail) {
                $kondisiKembali    = $request->kondisi_kembali[$detail->id]    ?? 'baik';
                $keteranganKondisi = $request->keterangan_kondisi[$detail->id] ?? null;

                // Update atau buat detail pengembalian
                DetailPengembalian::updateOrCreate(
                    [
                        'pengembalian_id' => $pengembalian->id,
                        'alat_id'         => $detail->alat_id,
                    ],
                    [
                        'jumlah_kembali'     => $detail->jumlah,
                        'kondisi_kembali'    => $kondisiKembali,
                        'keterangan_kondisi' => $keteranganKondisi,
                    ]
                );

                // Terapkan logika stok baru
                $alat = Alat::find($detail->alat_id);
                if ($alat) {
                    if (in_array($kondisiKembali, ['baik', 'rusak_ringan'])) {
                        $alat->increment('stok_tersedia', $detail->jumlah);
                    } elseif (in_array($kondisiKembali, ['rusak_berat', 'hilang'])) {
                        $alat->decrement('stok_total', $detail->jumlah);
                        if ($alat->stok_total < 0) $alat->stok_total = 0;
                        if ($alat->stok_tersedia > $alat->stok_total) {
                            $alat->stok_tersedia = $alat->stok_total;
                        }
                        $alat->save();
                    }
                }
            }

            DB::commit();

            return redirect()->route('pengembalian.index')
                ->with('success', 'Pengajuan pengembalian berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui pengembalian: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(string $id)
    {
        $pengembalian = Pengembalian::with('details', 'peminjaman.details')
            ->whereHas('peminjaman', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('status_pengembalian', 'diajukan')
            ->findOrFail($id);

        DB::beginTransaction();
        try {
            // Balikkan perubahan stok sebelum pengembalian dihapus
            foreach ($pengembalian->details as $detail) {
                $alat = Alat::find($detail->alat_id);
                if (!$alat) continue;

                $peminjamanDetail = $pengembalian->peminjaman->details
                    ->firstWhere('alat_id', $detail->alat_id);
                $jumlah = $peminjamanDetail ? $peminjamanDetail->jumlah : $detail->jumlah_kembali;

                if (in_array($detail->kondisi_kembali, ['baik', 'rusak_ringan'])) {
                    $alat->decrement('stok_tersedia', $jumlah);
                } elseif (in_array($detail->kondisi_kembali, ['rusak_berat', 'hilang'])) {
                    $alat->increment('stok_total', $jumlah);
                }
            }

            $pengembalian->delete();

            DB::commit();

            return redirect()->route('pengembalian.index')
                ->with('success', 'Pengajuan pengembalian berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal membatalkan pengembalian: ' . $e->getMessage());
        }
    }
}