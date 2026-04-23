<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengembalian;
use App\Models\DetailPengembalian;
use App\Models\Peminjaman;
use App\Models\Denda;
use App\Models\LogAktivitas;
use App\Mail\StrukPengembalianMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PengembalianController extends Controller
{
    public function index()
    {
        // Tampilkan semua yang belum selesai (belum lunas / tidak ada denda / diajukan)
        $pengembalians = Pengembalian::with(['peminjaman.user', 'user'])
            ->where(function($q) {
                $q->whereIn('status_pembayaran', ['belum_lunas', 'tidak_ada_denda'])
                  ->orWhere('status_pengembalian', 'diajukan');
            })
            ->latest()
            ->get();

        return view('admin.pengembalian.index', compact('pengembalians'));
    }

    public function create(Request $request)
    {
        $peminjamans = Peminjaman::with('user')
            ->where('status', 'dipinjam')
            ->get();

        $selectedPeminjaman = null;
        if ($request->peminjaman_id) {
            $selectedPeminjaman = Peminjaman::with(['user', 'details.buku'])
                ->findOrFail($request->peminjaman_id);
        }

        $dendaConfig = Denda::first();

        $dendaPerHari = $dendaConfig->denda_per_hari                ?? 1000;
        $persenRingan = $dendaConfig->denda_rusak_ringan            ?? 10;
        $persenBerat  = $dendaConfig->denda_rusak_berat             ?? 50;
        $persenHilang = $dendaConfig->persentase_penggantian_hilang ?? 100;
        $tglRencana   = $selectedPeminjaman->tanggal_kembali_rencana ?? '';

        return view('admin.pengembalian.create', compact(
            'peminjamans', 'selectedPeminjaman', 'dendaConfig',
            'dendaPerHari', 'persenRingan', 'persenBerat', 'persenHilang', 'tglRencana'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'peminjaman_id'          => 'required|exists:peminjamen,id',
            'tanggal_kembali_aktual' => 'required|date',
            'buku_id'                => 'required|array',
            'kondisi_kembali'        => 'required|array',
            'denda_kerusakan_buku'   => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $peminjaman   = Peminjaman::with('user')->findOrFail($request->peminjaman_id);
            $dendaConfig  = Denda::first();
            $dendaPerHari = $dendaConfig->denda_per_hari ?? 5000;

            $tglRencana = Carbon::parse($peminjaman->tanggal_kembali_rencana)->startOfDay();
            $tglAktual  = Carbon::parse($request->tanggal_kembali_aktual)->startOfDay();

            $keterlambatan      = 0;
            $dendaKeterlambatan = 0;

            // ✅ FIX BUG #1: diffInDays harus dari rencana KE aktual
            if ($tglAktual->gt($tglRencana)) {
                $keterlambatan      = $tglRencana->diffInDays($tglAktual); // FIXED
                $dendaKeterlambatan = $keterlambatan * $dendaPerHari;
            }

            $dendaKerusakanTotal = array_sum($request->denda_kerusakan_buku);
            $totalDenda          = $dendaKeterlambatan + $dendaKerusakanTotal;
            $statusPembayaran    = ($totalDenda > 0) ? 'belum_lunas' : 'tidak_ada_denda';

            $pengembalian = Pengembalian::create([
                'peminjaman_id'          => $peminjaman->id,
                'user_id'                => $peminjaman->user_id,
                'petugas_id'             => Auth::id(),
                'tanggal_kembali_aktual' => $request->tanggal_kembali_aktual,
                'keterlambatan_hari'     => $keterlambatan,
                'denda_keterlambatan'    => $dendaKeterlambatan,
                'denda_kerusakan'        => $dendaKerusakanTotal,
                'total_denda'            => $totalDenda,
                'status_pembayaran'      => $statusPembayaran,
                'status_pengembalian'    => 'dikonfirmasi',
            ]);

            foreach ($request->buku_id as $index => $buku_id) {
                DetailPengembalian::create([
                    'pengembalian_id'      => $pengembalian->id,
                    'buku_id'              => $buku_id,
                    'jumlah_kembali'       => $request->jumlah_kembali[$index] ?? 1,
                    'kondisi_kembali'      => $request->kondisi_kembali[$index],
                    'denda_kerusakan_buku' => $request->denda_kerusakan_buku[$index],
                ]);

                if (!in_array($request->kondisi_kembali[$index], ['hilang', 'rusak_berat'])) {
                    $buku = \App\Models\Buku::find($buku_id);
                    if ($buku) {
                        $buku->increment('stok_tersedia', $request->jumlah_kembali[$index] ?? 1);
                    }
                }
            }

            $peminjaman->update(['status' => 'dikembalikan']);

            LogAktivitas::record('Input Pengembalian', 'Pengembalian', $pengembalian->id,
                "Admin menginput pengembalian untuk " . $peminjaman->user->name);

            DB::commit();
            return redirect()->route('admin.pengembalian.index')
                ->with('success', 'Pengembalian berhasil dicatat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $pengembalian = Pengembalian::with(['peminjaman.user', 'details.buku', 'petugas'])->findOrFail($id);
        return view('admin.pengembalian.show', compact('pengembalian'));
    }

    public function edit($id)
    {
        $pengembalian = Pengembalian::with(['peminjaman.user', 'details.buku'])->findOrFail($id);

        // ✅ FIX BUG #5: Blokir edit jika sudah dikonfirmasi
        if ($pengembalian->status_pengembalian === 'dikonfirmasi') {
            return redirect()->route('admin.pengembalian.index')
                ->with('error', 'Pengembalian yang sudah dikonfirmasi tidak dapat diedit.');
        }

        $dendaConfig = Denda::first();
        if (!$dendaConfig) {
            $dendaConfig = (object)[
                'denda_per_hari'                => 5000,
                'denda_rusak_ringan'            => 10,
                'denda_rusak_berat'             => 50,
                'persentase_penggantian_hilang' => 100,
            ];
        }

        return view('admin.pengembalian.edit', compact('pengembalian', 'dendaConfig'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_kembali_aktual' => 'required|date',
            'denda_kerusakan_buku'   => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $pengembalian = Pengembalian::findOrFail($id);

            if ($pengembalian->status_pengembalian === 'dikonfirmasi') {
                throw new \Exception('Pengembalian yang sudah dikonfirmasi tidak dapat diubah.');
            }

            $peminjaman   = $pengembalian->peminjaman;
            $dendaConfig  = Denda::first();
            $dendaPerHari = $dendaConfig->denda_per_hari ?? 5000;

            $tglRencana = Carbon::parse($peminjaman->tanggal_kembali_rencana)->startOfDay();
            $tglAktual  = Carbon::parse($request->tanggal_kembali_aktual)->startOfDay();

            $keterlambatan      = 0;
            $dendaKeterlambatan = 0;

            // ✅ FIX BUG #1
            if ($tglAktual->gt($tglRencana)) {
                $keterlambatan      = $tglRencana->diffInDays($tglAktual);
                $dendaKeterlambatan = $keterlambatan * $dendaPerHari;
            }

            $dendaKerusakanTotal = array_sum($request->denda_kerusakan_buku);
            $totalDenda          = $dendaKeterlambatan + $dendaKerusakanTotal;

            $statusPembayaran = $pengembalian->status_pembayaran;
            if ($totalDenda == 0) {
                $statusPembayaran = 'tidak_ada_denda';
            } elseif ($statusPembayaran == 'tidak_ada_denda') {
                $statusPembayaran = 'belum_lunas';
            }

            $pengembalian->update([
                'tanggal_kembali_aktual' => $request->tanggal_kembali_aktual,
                'keterlambatan_hari'     => $keterlambatan,
                'denda_keterlambatan'    => $dendaKeterlambatan,
                'denda_kerusakan'        => $dendaKerusakanTotal,
                'total_denda'            => $totalDenda,
                'status_pembayaran'      => $statusPembayaran,
                'petugas_id'             => Auth::id(),
            ]);

            foreach ($request->detail_id as $index => $detail_id) {
                $detail = DetailPengembalian::find($detail_id);
                if ($detail) {
                    $detail->update([
                        'kondisi_kembali'      => $request->kondisi_kembali[$index],
                        'denda_kerusakan_buku' => $request->denda_kerusakan_buku[$index],
                    ]);
                }
            }

            LogAktivitas::record('Update Pengembalian', 'Pengembalian', $pengembalian->id,
                "Admin memperbarui data pengembalian #" . $id);

            DB::commit();
            return redirect()->route('admin.pengembalian.index')->with('success', 'Data pengembalian diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $pengembalian = Pengembalian::with('details')->findOrFail($id);
            $peminjaman   = $pengembalian->peminjaman;

            $peminjaman->update(['status' => 'dipinjam']);

            foreach ($pengembalian->details as $detail) {
                if (!in_array($detail->kondisi_kembali, ['hilang', 'rusak_berat'])) {
                    $buku = \App\Models\Buku::find($detail->buku_id);
                    if ($buku) {
                        $buku->decrement('stok_tersedia', $detail->jumlah_kembali);
                    }
                }
            }

            $pengembalian->delete();

            LogAktivitas::record('Hapus Pengembalian', 'Pengembalian', $id,
                "Admin menghapus data pengembalian #" . $id);

            DB::commit();
            return redirect()->route('admin.pengembalian.index')
                ->with('success', 'Data pengembalian dihapus. Peminjaman dikembalikan ke status dipinjam.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function lunasi($id)
    {
        $pengembalian = Pengembalian::findOrFail($id);
        $pengembalian->update(['status_pembayaran' => 'lunas']);

        LogAktivitas::record('Pelunasan Denda', 'Pengembalian', $id,
            "Admin melunasi denda pengembalian #" . $id);

        return redirect()->back()->with('success', 'Denda telah dilunasi.');
    }

    // ✅ Method konfirmasi: terima kondisi & denda dari form modal
    public function konfirmasi(Request $request, $id) {
        DB::beginTransaction();
        try {
            $pengembalian = Pengembalian::with(['peminjaman.user', 'details.buku'])->findOrFail($id);

            if ($pengembalian->status_pengembalian !== 'diajukan') {
                throw new \Exception('Pengembalian ini sudah dikonfirmasi sebelumnya.');
            }

            $peminjaman   = $pengembalian->peminjaman;
            $dendaConfig  = Denda::first();
            $dendaPerHari = $dendaConfig->denda_per_hari ?? 5000;

            $tglRencana = Carbon::parse($peminjaman->tanggal_kembali_rencana)->startOfDay();
            $tglAktual  = Carbon::parse($pengembalian->tanggal_kembali_aktual)->startOfDay();

            $keterlambatan      = 0;
            $dendaKeterlambatan = 0;

            if ($tglAktual->gt($tglRencana)) {
                $keterlambatan      = $tglRencana->diffInDays($tglAktual);
                $dendaKeterlambatan = $keterlambatan * $dendaPerHari;
            }

            // ✅ Ambil kondisi & denda dari input form modal (bukan hitung otomatis)
            $kondisiKembali      = $request->input('kondisi_kembali', []);
            $dendaKerusakanInput = $request->input('denda_kerusakan_buku', []);
            $detailIds           = $request->input('detail_id', []);

            $dendaKerusakanTotal = 0;

            foreach ($pengembalian->details as $index => $detail) {
                $detailId    = $detailIds[$index] ?? null;
                $kondisi     = $kondisiKembali[$index] ?? $detail->kondisi_kembali;
                $dendaItem   = isset($dendaKerusakanInput[$index]) ? (float)$dendaKerusakanInput[$index] : 0;

                $dendaKerusakanTotal += $dendaItem;

                $detail->update([
                    'kondisi_kembali'      => $kondisi,
                    'denda_kerusakan_buku' => $dendaItem,
                ]);

                // Update stok: hanya tambah jika kondisi bukan hilang/rusak_berat
                if (!in_array($kondisi, ['hilang', 'rusak_berat'])) {
                    $buku = \App\Models\Buku::find($detail->buku_id);
                    if ($buku) {
                        $buku->increment('stok_tersedia', $detail->jumlah_kembali);
                    }
                }
            }

            $totalDenda       = $dendaKeterlambatan + $dendaKerusakanTotal;
            $statusPembayaran = ($totalDenda > 0) ? 'belum_lunas' : 'tidak_ada_denda';

            $pengembalian->update([
                'status_pengembalian' => 'dikonfirmasi',
                'status_pembayaran'   => $statusPembayaran,
                'petugas_id'          => Auth::id(),
                'keterlambatan_hari'  => $keterlambatan,
                'denda_keterlambatan' => $dendaKeterlambatan,
                'denda_kerusakan'     => $dendaKerusakanTotal,
                'total_denda'         => $totalDenda,
            ]);

            $peminjaman->update(['status' => 'dikembalikan']);

            LogAktivitas::record('Konfirmasi Pengembalian', 'Pengembalian', $pengembalian->id,
                "Admin mengkonfirmasi pengembalian dari " . $peminjaman->user->name);

            DB::commit();
            return redirect()->back()->with('success', 'Pengembalian berhasil dikonfirmasi.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function previewKonfirmasi($id)
    {
        $pengembalian = Pengembalian::with(['peminjaman.user', 'details.buku'])->findOrFail($id);
        $peminjaman   = $pengembalian->peminjaman;
        $dendaConfig  = Denda::first();
        $dendaPerHari = $dendaConfig->denda_per_hari ?? 5000;
        $persenRingan = $dendaConfig->denda_rusak_ringan            ?? 10;
        $persenBerat  = $dendaConfig->denda_rusak_berat             ?? 50;
        $persenHilang = $dendaConfig->persentase_penggantian_hilang ?? 100;

        $tglRencana = Carbon::parse($peminjaman->tanggal_kembali_rencana)->startOfDay();
        $tglAktual  = Carbon::parse($pengembalian->tanggal_kembali_aktual)->startOfDay();

        $keterlambatan      = 0;
        $dendaKeterlambatan = 0;

        if ($tglAktual->gt($tglRencana)) {
            $keterlambatan      = $tglRencana->diffInDays($tglAktual);
            $dendaKeterlambatan = $keterlambatan * $dendaPerHari;
        }

        $details = [];
        $dendaKerusakanTotal = 0;

        foreach ($pengembalian->details as $detail) {
            $hargaBuku = $detail->buku->harga_buku ?? 0;
            $dendaItem = 0;

            // Hitung denda otomatis sebagai nilai default (bisa diubah admin di modal)
            if ($detail->kondisi_kembali === 'rusak_ringan') {
                $dendaItem = $hargaBuku * ($persenRingan / 100);
            } elseif ($detail->kondisi_kembali === 'rusak_berat') {
                $dendaItem = $hargaBuku * ($persenBerat / 100);
            } elseif ($detail->kondisi_kembali === 'hilang') {
                $dendaItem = $hargaBuku * ($persenHilang / 100);
            }

            $dendaKerusakanTotal += $dendaItem;
            $details[] = [
                'detail_id'            => $detail->id,           // ✅ Kirim detail_id untuk update
                'judul_buku'           => $detail->buku->judul_buku,
                'jumlah'               => $detail->jumlah_kembali,
                'kondisi_kembali'      => $detail->kondisi_kembali,
                'harga_buku'           => $hargaBuku,
                'denda_kerusakan_buku' => $dendaItem,
            ];
        }

        $totalDenda = $dendaKeterlambatan + $dendaKerusakanTotal;

        return response()->json([
            'peminjaman_id'           => $peminjaman->id,
            'nama_peminjam'           => $peminjaman->user->name,
            'nisn'                    => $peminjaman->user->NISN          ?? '-',
            'kelas_jurusan'           => $peminjaman->user->kelas_jurusan ?? '-',
            'tanggal_pinjam'          => Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y'),
            'tanggal_rencana_kembali' => Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('d/m/Y'),
            'tanggal_kembali_aktual'  => Carbon::parse($pengembalian->tanggal_kembali_aktual)->format('d/m/Y'),
            'keterlambatan_hari'      => $keterlambatan,
            'denda_keterlambatan'     => $dendaKeterlambatan,
            'denda_kerusakan'         => $dendaKerusakanTotal,
            'total_denda'             => $totalDenda,
            'persen_ringan'           => $persenRingan,   // ✅ Kirim persentase untuk JS
            'persen_berat'            => $persenBerat,
            'persen_hilang'           => $persenHilang,
            'details'                 => $details,
        ]);
    }

    public function riwayat()
    {
        $riwayats = Pengembalian::with(['peminjaman.user', 'petugas', 'details.buku'])
            ->where('status_pengembalian', 'dikonfirmasi')
            ->whereIn('status_pembayaran', ['lunas', 'tidak_ada_denda'])
            ->latest()
            ->get();

        return view('admin.pengembalian.riwayat', compact('riwayats'));
    }

    // ════════════════════════════════════════════════════════════════
    // BARU: Show data JSON untuk modal struk
    // ════════════════════════════════════════════════════════════════
    public function showStruk($id)
    {
        $pengembalian = Pengembalian::with([
            'peminjaman.user',
            'peminjaman.details.buku',
            'details.buku',
            'petugas',
        ])->findOrFail($id);

        $details = $pengembalian->details->isNotEmpty()
            ? $pengembalian->details->map(fn ($d) => [
                'judul_buku'           => $d->buku->judul_buku ?? '-',
                'jumlah'               => $d->jumlah_kembali,
                'kondisi_kembali'      => $d->kondisi_kembali,
                'denda_kerusakan_buku' => $d->denda_kerusakan_buku ?? 0,
              ])
            : $pengembalian->peminjaman->details->map(fn ($d) => [
                'judul_buku'           => $d->buku->judul_buku ?? '-',
                'jumlah'               => $d->jumlah ?? 1,
                'kondisi_kembali'      => 'baik',
                'denda_kerusakan_buku' => 0,
              ]);

        return response()->json([
            'id'                  => $pengembalian->id,
            'nama_peminjam'       => $pengembalian->peminjaman->user->name ?? '-',
            'email_peminjam'      => $pengembalian->peminjaman->user->email ?? '-',
            'petugas'             => $pengembalian->petugas->name ?? '-',
            'tanggal_pinjam'      => optional($pengembalian->peminjaman->tanggal_pinjam)->format('d/m/Y') ?? '-',
            'tanggal_rencana'     => optional($pengembalian->peminjaman->tanggal_kembali_rencana)->format('d/m/Y') ?? '-',
            'tanggal_kembali'     => Carbon::parse($pengembalian->tanggal_kembali_aktual)->format('d/m/Y'),
            'keterlambatan_hari'  => $pengembalian->keterlambatan_hari ?? 0,
            'denda_keterlambatan' => $pengembalian->denda_keterlambatan ?? 0,
            'denda_kerusakan'     => $pengembalian->denda_kerusakan ?? 0,
            'total_denda'         => $pengembalian->total_denda ?? 0,
            'status_pembayaran'   => $pengembalian->status_pembayaran,
            'details'             => $details,
        ]);
    }

    // ════════════════════════════════════════════════════════════════
    // BARU: Download PDF struk
    // ════════════════════════════════════════════════════════════════
    public function downloadStruk($id)
    {
        $pengembalian = Pengembalian::with([
            'peminjaman.user',
            'peminjaman.details.buku',
            'details.buku',
            'petugas',
        ])->findOrFail($id);

        $detailSource = $pengembalian->details->isNotEmpty()
            ? $pengembalian->details
            : $pengembalian->peminjaman->details->map(function ($d) {
                $d->jumlah_kembali     = $d->jumlah ?? 1;
                $d->kondisi_kembali    = 'baik';
                $d->keterangan_kondisi = '-';
                $d->denda_kerusakan_buku = 0;
                return $d;
            });

        $pdf = Pdf::loadView('pdf.struk-pengembalian', [
            'pengembalian' => $pengembalian,
            'detailSource' => $detailSource,
        ])->setPaper('A5', 'portrait');

        $noTrx = str_pad($id, 6, '0', STR_PAD_LEFT);
        return $pdf->download("struk-pengembalian-{$noTrx}.pdf");
    }

        // ════════════════════════════════════════════════════════════════
    // BARU: Kirim struk via email
    // ════════════════════════════════════════════════════════════════
    public function kirimStruk($id)
    {
        $pengembalian = Pengembalian::with([
            'peminjaman.user',
            'peminjaman.details.buku',
            'details.buku',
            'petugas',
        ])->findOrFail($id);

        $email = $pengembalian->peminjaman->user->email ?? null;

        if (! $email) {
            return response()->json([
                'success' => false,
                'message' => 'Email peminjam tidak ditemukan.',
            ], 422);
        }

        try {
            Mail::to($email)->send(new StrukPengembalianMail($pengembalian));
            LogAktivitas::record('Kirim Struk Email', 'Pengembalian', $id,
                "Admin mengirim struk pengembalian #{$id} ke {$email}");
            return response()->json([
                'success' => true,
                'message' => "Struk berhasil dikirim ke {$email}",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal kirim email: ' . $e->getMessage(),
            ], 500);
        }
    }
}