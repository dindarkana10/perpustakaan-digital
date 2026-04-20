<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengembalian;
use App\Models\DetailPengembalian;
use App\Models\Peminjaman;
use App\Models\Alat;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PengembalianController extends Controller
{
    public function index()
    {
        $pengembalians = Pengembalian::with(['peminjaman.user', 'peminjaman.details.alat', 'petugas'])
            ->latest()
            ->get();

        return view('admin.pengembalian.index', compact('pengembalians'));
    }

    public function create()
    {
        $peminjamans = Peminjaman::with(['user', 'details.alat'])
            ->where('status', 'dipinjam')
            ->whereDoesntHave('pengembalian')
            ->get();

        return view('admin.pengembalian.create', compact('peminjamans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'peminjaman_id'          => 'required|exists:peminjamen,id',
            'tanggal_kembali_aktual' => 'required|date',
            'kondisi_kembali'        => 'required|array',
            'kondisi_kembali.*'      => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
            'keterangan_kondisi'     => 'nullable|array',
            'keterangan_kondisi.*'   => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $peminjaman = Peminjaman::with('details.alat', 'user')
                ->where('id', $request->peminjaman_id)
                ->where('status', 'dipinjam')
                ->first();

            if (!$peminjaman) {
                return redirect()->back()->with('error', 'Peminjaman tidak valid.');
            }

            // Validasi setiap detail peminjaman harus punya kondisi kembali
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

            // Simpan pengembalian
            $pengembalian = Pengembalian::create([
                'peminjaman_id'          => $request->peminjaman_id,
                'tanggal_kembali_aktual' => $request->tanggal_kembali_aktual,
                'keterlambatan_hari'     => $keterlambatan,
                'denda_keterlambatan'    => 0,
                'denda_kerusakan'        => 0,
                'total_denda'            => 0,
                'status_pembayaran'      => 'belum_lunas',
                'status_pengembalian'    => 'diajukan',
                'petugas_id'             => Auth::id(),
            ]);

            // Simpan detail kondisi kembali per alat + update stok
            foreach ($peminjaman->details as $detail) {
                $kondisiKembali    = $request->kondisi_kembali[$detail->id];
                $keteranganKondisi = $request->keterangan_kondisi[$detail->id] ?? null;

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
                 * - baik / rusak_ringan → stok_tersedia bertambah
                 * - rusak_berat / hilang → stok_total dikurangi, stok_tersedia TIDAK bertambah
                 */
                $alat = Alat::find($detail->alat_id);
                if ($alat) {
                    if (in_array($kondisiKembali, ['baik', 'rusak_ringan'])) {
                        $alat->increment('stok_tersedia', $detail->jumlah);
                    } elseif (in_array($kondisiKembali, ['rusak_berat', 'hilang'])) {
                        $alat->decrement('stok_total', $detail->jumlah);
                        if ($alat->stok_total < 0) {
                            $alat->stok_total = 0;
                        }
                        if ($alat->stok_tersedia > $alat->stok_total) {
                            $alat->stok_tersedia = $alat->stok_total;
                        }
                        $alat->save();
                    }
                }
            }

            LogAktivitas::record(
                'Tambah Pengembalian',
                'Pengembalian',
                $pengembalian->id,
                "Menambahkan pengembalian untuk peminjam: {$peminjaman->user->name} | Keterlambatan: {$keterlambatan} hari"
            );

            DB::commit();

            return redirect()->route('admin.pengembalian.index')
                ->with('success', 'Pengembalian berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(string $id)
    {
        $pengembalian = Pengembalian::with([
            'peminjaman.user',
            'peminjaman.details.alat',
            'details.alat',
            'petugas',
        ])->findOrFail($id);

        return view('admin.pengembalian.show', compact('pengembalian'));
    }

    public function edit(string $id)
    {
        $pengembalian = Pengembalian::with(['peminjaman.details.alat', 'peminjaman.user', 'details'])
            ->where('status_pengembalian', 'diajukan')
            ->findOrFail($id);

        return view('admin.pengembalian.edit', compact('pengembalian'));
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

        try {
            DB::beginTransaction();

            $pengembalian = Pengembalian::with('peminjaman.user', 'peminjaman.details.alat', 'details')
                ->where('status_pengembalian', 'diajukan')
                ->findOrFail($id);

            $oldTanggal = $pengembalian->tanggal_kembali_aktual;

            // ── Balikkan perubahan stok dari pengajuan sebelumnya ─────────────────
            foreach ($pengembalian->details as $oldDetail) {
                $alat = Alat::find($oldDetail->alat_id);
                if (!$alat) continue;

                $peminjamanDetail = $pengembalian->peminjaman->details
                    ->firstWhere('alat_id', $oldDetail->alat_id);
                $jumlah = $peminjamanDetail ? $peminjamanDetail->jumlah : $oldDetail->jumlah_kembali;

                if (in_array($oldDetail->kondisi_kembali, ['baik', 'rusak_ringan'])) {
                    $alat->decrement('stok_tersedia', $jumlah);
                } elseif (in_array($oldDetail->kondisi_kembali, ['rusak_berat', 'hilang'])) {
                    $alat->increment('stok_total', $jumlah);
                }
            }

            // ── Hitung ulang keterlambatan ────────────────────────────────────────
            $tanggalRencana = Carbon::parse($pengembalian->peminjaman->tanggal_kembali_rencana)->startOfDay();
            $tanggalAktual  = Carbon::parse($request->tanggal_kembali_aktual)->startOfDay();
            $keterlambatan  = max(0, $tanggalAktual->diffInDays($tanggalRencana, false) * -1);

            $pengembalian->update([
                'tanggal_kembali_aktual' => $request->tanggal_kembali_aktual,
                'keterlambatan_hari'     => $keterlambatan,
            ]);

            // ── Update detail kondisi & terapkan stok baru ────────────────────────
            foreach ($pengembalian->peminjaman->details as $detail) {
                $kondisiKembali    = $request->kondisi_kembali[$detail->id]    ?? 'baik';
                $keteranganKondisi = $request->keterangan_kondisi[$detail->id] ?? null;

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

            LogAktivitas::record(
                'Edit Pengembalian',
                'Pengembalian',
                $pengembalian->id,
                "Mengubah pengembalian #{$pengembalian->id} | Peminjam: {$pengembalian->peminjaman->user->name} | Tanggal: {$oldTanggal} → {$request->tanggal_kembali_aktual} | Keterlambatan: {$keterlambatan} hari"
            );

            DB::commit();

            return redirect()->route('admin.pengembalian.index')
                ->with('success', 'Data pengembalian berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui pengembalian: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $pengembalian = Pengembalian::with('peminjaman.user', 'peminjaman.details', 'details')
                ->where('status_pengembalian', 'diajukan')
                ->findOrFail($id);

            $userName       = $pengembalian->peminjaman->user->name;
            $pengembalianId = $pengembalian->id;
            $peminjamanId   = $pengembalian->peminjaman_id;

            // Balikkan perubahan stok sebelum dihapus
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

            LogAktivitas::record(
                'Hapus Pengembalian',
                'Pengembalian',
                $pengembalianId,
                "Menghapus pengembalian #{$pengembalianId} | Peminjam: {$userName} | Peminjaman #{$peminjamanId}"
            );

            DB::commit();

            return redirect()->route('admin.pengembalian.index')
                ->with('success', 'Data pengembalian berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menghapus pengembalian: ' . $e->getMessage());
        }
    }
}