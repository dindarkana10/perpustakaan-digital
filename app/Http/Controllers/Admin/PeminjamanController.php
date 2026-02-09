<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\User;
use App\Models\Alat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PeminjamanController extends Controller
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
        
        // Ambil user peminjam untuk dropdown
        $users = User::where('role', 'peminjam')->get(); 
        
        // Ambil alat yang stok tersedia > 0
        $alats = Alat::where('stok_tersedia', '>', 0)->get();
        
        return view('admin.peminjaman.index', compact('peminjamen', 'users', 'alats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali_rencana' => 'required|date|after_or_equal:tanggal_pinjam',
            'keperluan' => 'required|string|max:1000',
            'alat_id' => 'required|array|min:1',
            'alat_id.*' => 'required|exists:alats,id',
            'jumlah' => 'required|array|min:1',
            'jumlah.*' => 'required|integer|min:1',
            'kondisi_pinjam' => 'required|array|min:1',
            'kondisi_pinjam.*' => 'required|in:baik,rusak_ringan,rusak_berat',
        ], [
            'user_id.required' => 'Peminjam wajib dipilih.',
            'user_id.exists' => 'Peminjam tidak valid.',
            'tanggal_pinjam.required' => 'Tanggal pinjam wajib diisi.',
            'tanggal_kembali_rencana.required' => 'Tanggal kembali rencana wajib diisi.',
            'tanggal_kembali_rencana.after_or_equal' => 'Tanggal kembali tidak boleh sebelum tanggal pinjam.',
            'keperluan.required' => 'Keperluan wajib diisi.',
            'keperluan.max' => 'Keperluan maksimal 1000 karakter.',
            'alat_id.required' => 'Minimal pilih 1 alat.',
            'alat_id.*.exists' => 'Alat tidak valid.',
            'jumlah.required' => 'Jumlah wajib diisi.',
            'jumlah.*.min' => 'Jumlah minimal 1.',
            'kondisi_pinjam.required' => 'Kondisi pinjam wajib dipilih.',
            'kondisi_pinjam.*.in' => 'Kondisi pinjam tidak valid.',
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
            // petugas_id masih NULL karena belum disetujui
            $peminjaman = Peminjaman::create([
                'user_id' => $validated['user_id'],
                'petugas_id' => null, // NULL saat dibuat
                'tanggal_pinjam' => $validated['tanggal_pinjam'],
                'tanggal_kembali_rencana' => $validated['tanggal_kembali_rencana'],
                'keperluan' => $validated['keperluan'],
                'status' => 'menunggu_persetujuan', // Status awal
            ]);

            // Create detail peminjaman (stok BELUM dikurangi)
            foreach ($request->alat_id as $index => $alat_id) {
                $jumlah = $request->jumlah[$index];
                
                DetailPeminjaman::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id' => $alat_id,
                    'jumlah' => $jumlah,
                    'kondisi_pinjam' => $request->kondisi_pinjam[$index],
                ]);
            }

            DB::commit();
            
            return redirect()->route('peminjaman.index')
                ->with('success', 'Peminjaman berhasil ditambahkan! Menunggu persetujuan petugas.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menambahkan peminjaman: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(string $id)
    {
        $peminjaman = Peminjaman::with(['user', 'petugas', 'details.alat.kategori'])
            ->findOrFail($id);
            
        return response()->json($peminjaman);
    }

    public function update(Request $request, string $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        // Admin hanya bisa edit yang masih menunggu persetujuan
        if ($peminjaman->status !== 'menunggu_persetujuan') {
            return redirect()->back()
                ->with('error', 'Hanya peminjaman yang menunggu persetujuan yang bisa diedit.');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali_rencana' => 'required|date|after_or_equal:tanggal_pinjam',
            'keperluan' => 'required|string|max:1000',
        ], [
            'user_id.required' => 'Peminjam wajib dipilih.',
            'tanggal_pinjam.required' => 'Tanggal pinjam wajib diisi.',
            'tanggal_kembali_rencana.after_or_equal' => 'Tanggal kembali tidak boleh sebelum tanggal pinjam.',
            'keperluan.required' => 'Keperluan wajib diisi.',
        ]);

        try {
            DB::beginTransaction();

            $peminjaman->update($validated);

            DB::commit();
            
            return redirect()->route('peminjaman.index')
                ->with('success', 'Peminjaman berhasil diperbarui!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui peminjaman: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            
            $peminjaman = Peminjaman::with('details')->findOrFail($id);
            
            // Admin hanya bisa hapus yang masih menunggu persetujuan atau ditolak
            if (!in_array($peminjaman->status, ['menunggu_persetujuan', 'ditolak'])) {
                throw new \Exception('Hanya peminjaman yang menunggu persetujuan atau ditolak yang bisa dihapus.');
            }
            
            $peminjaman->delete();
            
            DB::commit();
            
            return redirect()->route('peminjaman.index')
                ->with('success', 'Peminjaman berhasil dihapus!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menghapus peminjaman: ' . $e->getMessage());
        }
    }
}