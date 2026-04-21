<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengembalian;
use App\Models\DetailPengembalian;
use App\Models\Peminjaman;
use App\Models\Denda;
use App\Mail\StrukPengembalianMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class PetugasPengembalianController extends Controller
{
    public function index()
    {
        $pengembalians = Pengembalian::with([
                'peminjaman.user',
                'peminjaman.details.alat',
                'details.alat',
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
            $this->hitungDenda($item, $denda);
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
            $pengembalian->total_denda =
                $pengembalian->denda_keterlambatan + $pengembalian->denda_kerusakan;
            $pengembalian->status_pengembalian = 'dikonfirmasi';
            $pengembalian->status_pembayaran   =
                $pengembalian->total_denda > 0 ? 'belum_lunas' : 'lunas';
            $pengembalian->petugas_id = Auth::id();
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
            ->with('success', 'Pembayaran berhasil ditandai lunas.');
    }

    public function riwayat()
    {
        // ✅ FIX: eager load details.alat dan peminjaman.details.alat sekaligus
        $pengembalians = Pengembalian::with([
                'peminjaman.user',
                'peminjaman.details.alat',  // ← detail peminjaman (fallback sumber alat)
                'details.alat',             // ← detail pengembalian (kondisi kembali)
            ])
            ->where('status_pengembalian', 'dikonfirmasi')
            ->where('status_pembayaran', 'lunas')
            ->orderBy('tanggal_kembali_aktual', 'desc')
            ->get();

        return view('petugas.pengembalian.riwayat', compact('pengembalians'));
    }

    /**
     * AJAX JSON untuk modal detail & struk
     * FIX: nama field tanggal_kembali_rencana, fallback ke detail peminjaman
     */
    public function show($id)
    {
        $pengembalian = Pengembalian::with([
            'peminjaman.user',
            'peminjaman.details.alat',   // ← sumber fallback jika detail pengembalian kosong
            'details.alat',
        ])->findOrFail($id);

        $denda = Denda::first();

        // ════════════════════════════════════════════════════════════════
        // STRATEGI:
        // Prioritas 1: pakai detail_pengembalians (kondisi kembali dari peminjam)
        // Prioritas 2: fallback ke detail_peminjamen (kondisi saat pinjam)
        // ════════════════════════════════════════════════════════════════
        $detailPengembalian = $pengembalian->details; // detail_pengembalians
        $detailPeminjaman   = $pengembalian->peminjaman->details; // detail_peminjamen

        $hasDetailKembali = $detailPengembalian->isNotEmpty();

        if ($hasDetailKembali) {
            // Gunakan detail pengembalian — ada kondisi kembali per alat
            $details = $detailPengembalian->map(function ($d) use ($denda) {
                $hargaSatuan = $d->alat->harga_beli ?? 0;
                $subtotal    = $hargaSatuan * $d->jumlah_kembali;

                $dendaItem = match ($d->kondisi_kembali) {
                    'rusak_ringan' => $subtotal * (($denda->denda_rusak_ringan ?? 10) / 100),
                    'rusak_berat'  => $subtotal * (($denda->denda_rusak_berat  ?? 50) / 100),
                    'hilang'       => $subtotal,
                    default        => 0,
                };

                return [
                    'nama_alat'       => $d->alat->nama_alat ?? '-',
                    'jumlah'          => $d->jumlah_kembali,
                    'kondisi_kembali' => $d->kondisi_kembali,
                    'keterangan'      => $d->keterangan_kondisi ?? '-',
                    'harga_satuan'    => (float) $hargaSatuan,
                    'subtotal'        => (float) $subtotal,
                    'denda_item'      => (int) $dendaItem,
                    'sumber'          => 'pengembalian',
                ];
            });
        } else {
            // Fallback: pakai detail peminjaman, kondisi kembali dianggap 'baik'
            $details = $detailPeminjaman->map(function ($d) {
                $hargaSatuan = $d->alat->harga_beli ?? 0;
                return [
                    'nama_alat'       => $d->alat->nama_alat ?? '-',
                    'jumlah'          => $d->jumlah,
                    'kondisi_kembali' => 'baik',
                    'keterangan'      => 'Keterangan kondisi kembali tidak tersedia',
                    'harga_satuan'    => (float) $hargaSatuan,
                    'subtotal'        => (float) ($hargaSatuan * $d->jumlah),
                    'denda_item'      => 0,
                    'sumber'          => 'peminjaman',
                ];
            });
        }

        // ✅ FIX: gunakan field yang benar sesuai model Peminjaman
        $tanggalPinjam  = optional($pengembalian->peminjaman->tanggal_pinjam)
                            ->format('d-m-Y') ?? '-';
        $tanggalRencana = optional($pengembalian->peminjaman->tanggal_kembali_rencana) // ← FIXED
                            ->format('d-m-Y') ?? '-';

        return response()->json([
            'id'                  => $pengembalian->id,
            'nama_peminjam'       => $pengembalian->peminjaman->user->name ?? '-',
            'email_peminjam'      => $pengembalian->peminjaman->user->email ?? '-',
            'tanggal_pinjam'      => $tanggalPinjam,
            'tanggal_rencana'     => $tanggalRencana,             // ← FIXED
            'tanggal_kembali'     => $pengembalian->tanggal_kembali_aktual->format('d-m-Y'),
            'keterlambatan_hari'  => $pengembalian->keterlambatan_hari ?? 0,
            'denda_keterlambatan' => $pengembalian->denda_keterlambatan ?? 0,
            'denda_kerusakan'     => $pengembalian->denda_kerusakan ?? 0,
            'total_denda'         => $pengembalian->total_denda ?? 0,
            'status_pembayaran'   => $pengembalian->status_pembayaran,
            'has_detail_kembali'  => $hasDetailKembali,           // info debug
            'details'             => $details,
        ]);
    }

    public function downloadStruk($id)
    {
        $pengembalian = Pengembalian::with([
            'peminjaman.user',
            'peminjaman.details.alat',
            'details.alat',
        ])->findOrFail($id);

        $denda = Denda::first();

        // Resolusi detail (sama dengan show())
        $detailSource = $pengembalian->details->isNotEmpty()
            ? $pengembalian->details
            : $pengembalian->peminjaman->details->map(function ($d) {
                // Adapter: buat object-like agar blade bisa pakai property sama
                $d->jumlah_kembali    = $d->jumlah;
                $d->kondisi_kembali   = 'baik';
                $d->keterangan_kondisi = '-';
                return $d;
            });

        $pdf = Pdf::loadView('pdf.struk-pengembalian', [
                'pengembalian'  => $pengembalian,
                'denda'         => $denda,
                'detailSource'  => $detailSource,   // ← pass ke view
            ])
            ->setPaper([0, 0, 226.77, 595.28]);

        return $pdf->download(
            'struk-pengembalian-' . str_pad($id, 6, '0', STR_PAD_LEFT) . '.pdf'
        );
    }

    public function kirimStruk($id)
    {
        $pengembalian = Pengembalian::with([
            'peminjaman.user',
            'peminjaman.details.alat',
            'details.alat',
        ])->findOrFail($id);

        $emailTujuan = $pengembalian->peminjaman->user->email ?? null;

        if (! $emailTujuan) {
            return response()->json([
                'success' => false,
                'message' => 'Email peminjam tidak ditemukan.',
            ], 422);
        }

        try {
            Mail::to($emailTujuan)->send(new StrukPengembalianMail($pengembalian));
            return response()->json([
                'success' => true,
                'message' => 'Struk berhasil dikirim ke ' . $emailTujuan,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ── Helper hitung denda (untuk index) ────────────────────────────────
    private function hitungDenda(Pengembalian $item, $denda): void
    {
        $totalHargaAlat = $item->peminjaman->details->sum(fn ($d) =>
            ($d->alat->harga_beli ?? 0) * $d->jumlah
        );
        $item->total_harga_alat = $totalHargaAlat;

        $item->denda_keterlambatan_otomatis =
            ($item->keterlambatan_hari ?? 0) * ($denda->denda_per_hari ?? 5000);

        $dendaKerusakan = 0;
        foreach ($item->details as $d) {
            $subtotal = ($d->alat->harga_beli ?? 0) * $d->jumlah_kembali;
            $dendaKerusakan += match ($d->kondisi_kembali) {
                'rusak_ringan' => $subtotal * (($denda->denda_rusak_ringan ?? 10) / 100),
                'rusak_berat'  => $subtotal * (($denda->denda_rusak_berat  ?? 50) / 100),
                'hilang'       => $subtotal,
                default        => 0,
            };
        }
        $item->denda_kerusakan_otomatis = (int) $dendaKerusakan;

        $item->breakdown_kondisi = $item->details->map(fn ($d) => [
            'nama_alat'       => $d->alat->nama_alat ?? '-',
            'jumlah'          => $d->jumlah_kembali,
            'kondisi_kembali' => $d->kondisi_kembali,
            'keterangan'      => $d->keterangan_kondisi,
            'harga_satuan'    => $d->alat->harga_beli ?? 0,
            'denda_item'      => (int) match ($d->kondisi_kembali) {
                'rusak_ringan' => ($d->alat->harga_beli ?? 0) * $d->jumlah_kembali * (($denda->denda_rusak_ringan ?? 10) / 100),
                'rusak_berat'  => ($d->alat->harga_beli ?? 0) * $d->jumlah_kembali * (($denda->denda_rusak_berat  ?? 50) / 100),
                'hilang'       => ($d->alat->harga_beli ?? 0) * $d->jumlah_kembali,
                default        => 0,
            },
        ]);
    }
}