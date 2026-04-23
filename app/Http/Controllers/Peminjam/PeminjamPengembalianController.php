<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengembalian;
use App\Models\DetailPengembalian;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeminjamPengembalianController extends Controller
{
    public function index()
    {
        $pengembalians = Pengembalian::with(['peminjaman.details.buku'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
            
        return view('peminjam.pengembalian.index', compact('pengembalians'));
    }

    public function create()
    {
        // Ambil peminjaman yang sedang dipinjam oleh user ini
        $peminjamans = Peminjaman::with('details.buku')
            ->where('user_id', Auth::id())
            ->where('status', 'dipinjam')
            ->get();

        return view('peminjam.pengembalian.create', compact('peminjamans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'peminjaman_id' => 'required|exists:peminjamen,id',
            'tanggal_kembali_aktual' => 'required|date',
            'buku_id' => 'required|array',
            'kondisi_kembali' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::where('user_id', Auth::id())
                ->where('status', 'dipinjam')
                ->findOrFail($request->peminjaman_id);

            // Cek apakah sudah pernah diajukan sebelumnya
            $exists = Pengembalian::where('peminjaman_id', $peminjaman->id)
                ->where('status_pengembalian', 'diajukan')
                ->first();
            
            if ($exists) {
                throw new \Exception('Anda sudah mengajukan pengembalian untuk peminjaman ini. Silakan tunggu konfirmasi admin.');
            }
            
            $pengembalian = Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'user_id' => Auth::id(),
                'tanggal_kembali_aktual' => $request->tanggal_kembali_aktual,
                'status_pengembalian' => 'diajukan',
                'status_pembayaran' => 'belum_lunas', // Default sampai admin proses
            ]);

            foreach ($request->buku_id as $index => $buku_id) {
                DetailPengembalian::create([
                    'pengembalian_id' => $pengembalian->id,
                    'buku_id' => $buku_id,
                    'jumlah_kembali' => $request->jumlah_kembali[$index] ?? 1,
                    'kondisi_kembali' => $request->kondisi_kembali[$index],
                    'denda_kerusakan_buku' => 0, // Admin yang tentukan nanti
                ]);
            }
            
            DB::commit();
            return redirect()->route('pengembalian.index')->with('success', 'Pengajuan pengembalian berhasil dikirim.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $pengembalian = Pengembalian::with(['peminjaman.user', 'details.buku'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);
            
        return view('peminjam.pengembalian.show', compact('pengembalian'));
    }

    public function edit($id)
    {
        $pengembalian = Pengembalian::with(['peminjaman.details.buku', 'details'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        if ($pengembalian->status_pengembalian !== 'diajukan') {
            return redirect()->route('pengembalian.index')->with('error', 'Data yang sudah dikonfirmasi tidak dapat diubah.');
        }

        return view('peminjam.pengembalian.edit', compact('pengembalian'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_kembali_aktual' => 'required|date',
            'buku_id' => 'required|array',
            'kondisi_kembali' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $pengembalian = Pengembalian::where('user_id', Auth::id())->findOrFail($id);

            if ($pengembalian->status_pengembalian !== 'diajukan') {
                throw new \Exception('Data yang sudah dikonfirmasi tidak dapat diubah.');
            }

            $pengembalian->update([
                'tanggal_kembali_aktual' => $request->tanggal_kembali_aktual,
            ]);

            // Update details
            foreach ($request->buku_id as $index => $buku_id) {
                DetailPengembalian::where('pengembalian_id', $pengembalian->id)
                    ->where('buku_id', $buku_id)
                    ->update([
                        'kondisi_kembali' => $request->kondisi_kembali[$index],
                        'jumlah_kembali' => $request->jumlah_kembali[$index] ?? 1,
                    ]);
            }

            DB::commit();
            return redirect()->route('pengembalian.index')->with('success', 'Pengajuan pengembalian berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $pengembalian = Pengembalian::where('user_id', Auth::id())->findOrFail($id);

            if ($pengembalian->status_pengembalian !== 'diajukan') {
                return redirect()->route('pengembalian.index')->with('error', 'Data yang sudah dikonfirmasi tidak dapat dihapus.');
            }

            $pengembalian->details()->delete();
            $pengembalian->delete();

            return redirect()->route('pengembalian.index')->with('success', 'Pengajuan pengembalian berhasil dibatalkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}