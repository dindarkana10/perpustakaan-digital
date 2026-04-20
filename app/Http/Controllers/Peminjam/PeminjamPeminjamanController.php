<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\Alat;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PeminjamPeminjamanController extends Controller
{
    public function index()
    {
        // Ambil peminjaman milik user yang login
        $peminjaman = Peminjaman::with(['petugas', 'details.alat.kategori'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'asc')
            ->get();
            
        return view('peminjam.peminjaman.index', compact('peminjaman'));
    }

    public function create(Request $request)
    {
        // Ambil alat yang tersedia
        $alats = Alat::where('stok_tersedia', '>', 0)->with('kategori')->get();
        
        // Jika ada alat_id dari parameter (dari tombol pinjam)
        $selectedAlatId = $request->alat_id;
        
        return view('peminjam.peminjaman.create', compact('alats', 'selectedAlatId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_pinjam' => 'required|date|after_or_equal:today',
            'tanggal_kembali_rencana' => 'required|date|after:tanggal_pinjam',
            'keperluan' => 'required|string|max:1000',
            'alat_id' => 'required|array|min:1',
            'alat_id.*' => 'required|exists:alats,id',
            'jumlah' => 'required|array|min:1',
            'jumlah.*' => 'required|integer|min:1',
        ], [
            'tanggal_pinjam.required' => 'Tanggal pinjam wajib diisi.',
            'tanggal_pinjam.after_or_equal' => 'Tanggal pinjam tidak boleh sebelum hari ini.',
            'tanggal_kembali_rencana.required' => 'Tanggal kembali rencana wajib diisi.',
            'tanggal_kembali_rencana.after' => 'Tanggal kembali harus setelah tanggal pinjam.',
            'keperluan.required' => 'Keperluan wajib diisi.',
            'keperluan.max' => 'Keperluan maksimal 1000 karakter.',
            'alat_id.required' => 'Minimal pilih 1 alat.',
            'alat_id.*.exists' => 'Alat tidak valid.',
            'jumlah.required' => 'Jumlah wajib diisi.',
            'jumlah.*.min' => 'Jumlah minimal 1.',
        ]);

        try {
            DB::beginTransaction();

            // Validasi stok tersedia
            foreach ($request->alat_id as $index => $alat_id) {
                $alat = Alat::findOrFail($alat_id);
                $jumlah = $request->jumlah[$index];
                
                if ($alat->stok_tersedia < $jumlah) {
                    throw new \Exception("Stok alat {$alat->nama_alat} tidak mencukupi. Tersedia: {$alat->stok_tersedia}");
                }
            }

            // Create peminjaman dengan status menunggu_persetujuan
            $peminjaman = Peminjaman::create([
                'user_id' => Auth::id(), // User yang login
                'petugas_id' => null, // NULL karena belum disetujui
                'tanggal_pinjam' => $validated['tanggal_pinjam'],
                'tanggal_kembali_rencana' => $validated['tanggal_kembali_rencana'],
                'keperluan' => $validated['keperluan'],
                'status' => 'menunggu_persetujuan',
            ]);

            $alatList = [];

            // Create detail peminjaman
            foreach ($request->alat_id as $index => $alat_id) {
                $alat = Alat::findOrFail($alat_id);
                $jumlah = $request->jumlah[$index];
                
                DetailPeminjaman::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id' => $alat_id,
                    'jumlah' => $jumlah,
                    'kondisi_pinjam' => $alat->kondisi, // Otomatis ambil kondisi alat saat ini
                ]);

                $alatList[] = "{$alat->nama_alat} ({$jumlah})";
            }

            LogAktivitas::record(
                'Ajukan Peminjaman',
                'Peminjaman',
                $peminjaman->id,
                'Peminjam mengajukan peminjaman | Alat: ' . implode(', ', $alatList)
            );

            DB::commit();
            
            return redirect()->route('peminjaman.index')
                ->with('success', 'Peminjaman berhasil diajukan! Menunggu persetujuan petugas.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal mengajukan peminjaman: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(string $id)
    {
        $peminjaman = Peminjaman::with(['user', 'petugas', 'details.alat.kategori'])
            ->where('user_id', Auth::id()) // Hanya bisa lihat punya sendiri
            ->findOrFail($id);
            
        return response()->json($peminjaman);
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            
            $peminjaman = Peminjaman::where('user_id', Auth::id())
                ->findOrFail($id);
            
            // Peminjam hanya bisa hapus yang masih menunggu persetujuan
            if ($peminjaman->status !== 'menunggu_persetujuan') {
                throw new \Exception('Hanya peminjaman yang menunggu persetujuan yang bisa dibatalkan.');
            }
            
            LogAktivitas::record(
                'Batalkan Peminjaman',
                'Peminjaman',
                $peminjaman->id,
                'Peminjam membatalkan pengajuan peminjaman'
            );

            $peminjaman->delete();
            
            DB::commit();
            
            return redirect()->route('peminjaman.index')
                ->with('success', 'Peminjaman berhasil dibatalkan!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal membatalkan peminjaman: ' . $e->getMessage());
        }
    }
}