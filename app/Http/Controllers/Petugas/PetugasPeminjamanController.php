<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\User;
use App\Models\Alat;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PetugasPeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $query = Peminjaman::with(['user', 'petugas', 'details.alat']);

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by user
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $peminjamen = $query->latest()->get();
        
        // Ambil user peminjam untuk dropdown filter
        $users = User::where('role', 'peminjam')->get();
        
        return view('petugas.peminjaman.index', compact('peminjamen', 'users'));
    }

    public function show(string $id)
    {
        $peminjaman = Peminjaman::with(['user', 'petugas', 'details.alat.kategori'])
            ->findOrFail($id);
            
        return response()->json($peminjaman);
    }

    // Approve Peminjaman
    public function approve(string $id)
    {
        try {
            DB::beginTransaction();
            
            $peminjaman = Peminjaman::with('details')->findOrFail($id);
            
            if ($peminjaman->status !== 'menunggu_persetujuan') {
                throw new \Exception('Peminjaman tidak dalam status menunggu persetujuan.');
            }
            
            // Validasi stok
            foreach ($peminjaman->details as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                if ($alat->stok_tersedia < $detail->jumlah) {
                    throw new \Exception("Stok alat {$alat->nama_alat} tidak mencukupi. Tersedia: {$alat->stok_tersedia}");
                }
            }
            
            // Kurangi stok
            foreach ($peminjaman->details as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->decrement('stok_tersedia', $detail->jumlah);
            }
            
            // Update status dan petugas yang menyetujui
            $peminjaman->update([
                'status' => 'dipinjam',
                'petugas_id' => Auth::id()
            ]);
            
            // ✅ CATAT LOG
            LogAktivitas::record(
                'Peminjaman Disetujui',
                'Peminjaman',
                $peminjaman->id,
                'Menyetujui peminjaman oleh ' . $peminjaman->user->name
            );

            DB::commit();
            
            return redirect()->route('petugas.peminjaman.index')
                ->with('success', 'Peminjaman berhasil disetujui!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menyetujui peminjaman: ' . $e->getMessage());
        }
    }

    // Reject Peminjaman
    public function reject(string $id)
    {
        try {
            DB::beginTransaction();
            
            $peminjaman = Peminjaman::findOrFail($id);
            
            if ($peminjaman->status !== 'menunggu_persetujuan') {
                throw new \Exception('Peminjaman tidak dalam status menunggu persetujuan.');
            }
            
            // Update status dan petugas yang menolak
            $peminjaman->update([
                'status' => 'ditolak',
                'petugas_id' => Auth::id()
            ]);
            
            // ✅ CATAT LOG
            LogAktivitas::record(
                'Peminjaman Ditolak',
                'Peminjaman',
                $peminjaman->id,
                'Menolak peminjaman oleh ' . $peminjaman->user->name
            );

            DB::commit();
            
            return redirect()->route('petugas.peminjaman.index')
                ->with('success', 'Peminjaman berhasil ditolak!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menolak peminjaman: ' . $e->getMessage());
        }
    }
}