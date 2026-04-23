<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\User;
use App\Models\Buku;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $query = Peminjaman::with(['user', 'petugas', 'details.buku']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $peminjamen = $query->latest()->get();
        
        return view('admin.peminjaman.index', compact('peminjamen'));
    }

    public function create()
    {
        $users = User::where('role', 'peminjam')->get();
        $bukus = Buku::where('stok_tersedia', '>', 0)->get();
        $bukuJs = $bukus->map(fn($b) => [
            'id' => $b->id,
            'judul' => $b->judul_buku,
            'stok' => $b->stok_tersedia,
            'gambar' => $b->gambar ? asset('storage/bukus/' . $b->gambar) : null,
        ]);

        return view('admin.peminjaman.create', compact('users', 'bukus', 'bukuJs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali_rencana' => 'required|date|after_or_equal:tanggal_pinjam',
            'keperluan' => 'required|string|max:1000',
            'buku_id' => 'required|array|min:1',
            'buku_id.*' => 'required|exists:bukus,id',
            'jumlah' => 'required|array|min:1',
            'jumlah.*' => 'required|integer|min:1',
            'kondisi_pinjam' => 'required|array|min:1',
            'kondisi_pinjam.*' => 'required|in:baik,rusak_ringan,rusak_berat',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->buku_id as $index => $buku_id) {
                $buku = Buku::findOrFail($buku_id);
                $jumlah = $request->jumlah[$index];
                if ($buku->stok_tersedia < $jumlah) {
                    throw new \Exception("Stok buku {$buku->judul_buku} tidak mencukupi.");
                }
            }

            $peminjaman = Peminjaman::create([
                'user_id' => $validated['user_id'],
                'petugas_id' => Auth::id(),
                'tanggal_pinjam' => $validated['tanggal_pinjam'],
                'tanggal_kembali_rencana' => $validated['tanggal_kembali_rencana'],
                'keperluan' => $validated['keperluan'],
                'status' => 'menunggu_persetujuan',
            ]);

            foreach ($request->buku_id as $index => $buku_id) {
                $buku = Buku::find($buku_id);
                DetailPeminjaman::create([
                    'peminjaman_id' => $peminjaman->id,
                    'buku_id' => $buku_id,
                    'jumlah' => $request->jumlah[$index],
                    'kondisi_pinjam' => $request->kondisi_pinjam[$index],
                ]);
            }

            LogAktivitas::record('Tambah Peminjaman', 'Peminjaman', $peminjaman->id, "Admin menambah peminjaman untuk " . User::find($validated['user_id'])->name);

            DB::commit();
            return redirect()->route('admin.peminjaman.index')->with('success', 'Peminjaman berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(string $id)
    {
        $peminjaman = Peminjaman::with(['user', 'details.buku'])->findOrFail($id);
        return response()->json([
            'id' => $peminjaman->id,
            'tanggal_pinjam' => \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y'),
            'tanggal_kembali_rencana' => \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('d/m/Y'),
            'status' => $peminjaman->status,
            'keperluan' => $peminjaman->keperluan,

            'user' => [
                'name' => $peminjaman->user->name,
                'NISN' => $peminjaman->user->NISN,
                'kelas_jurusan' => $peminjaman->user->kelas_jurusan,
            ],

            'details' => $peminjaman->details->map(function($d){
                return [
                    'jumlah' => $d->jumlah,
                    'kondisi_pinjam' => $d->kondisi_pinjam,
                    'buku' => [
                        'judul_buku' => $d->buku->judul_buku,
                        'gambar' => $d->buku->gambar
                    ]
                ];
            })
        ]);
    }

    public function edit(string $id)
    {
        $peminjaman = Peminjaman::with('details.buku')->findOrFail($id);
        
        if ($peminjaman->status !== 'menunggu_persetujuan') {
            return redirect()->route('admin.peminjaman.index')->with('error', 'Hanya peminjaman berstatus menunggu yang dapat diedit.');
        }

        $users = User::where('role', 'peminjam')->get();
        $bukus = Buku::where('stok_tersedia', '>', 0)->get();
        $bukuJs = $bukus->map(fn($b) => [
            'id' => $b->id,
            'judul' => $b->judul_buku,
            'stok' => $b->stok_tersedia,
            'gambar' => $b->gambar ? asset('storage/bukus/' . $b->gambar) : null,
        ]);

        return view('admin.peminjaman.edit', compact('peminjaman', 'users', 'bukus', 'bukuJs'));
    }

    public function update(Request $request, string $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        
        if ($peminjaman->status !== 'menunggu_persetujuan') {
            return redirect()->route('admin.peminjaman.index')->with('error', 'Peminjaman sudah diproses, tidak dapat diubah.');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali_rencana' => 'required|date|after_or_equal:tanggal_pinjam',
            'keperluan' => 'required|string|max:1000',
            'buku_id' => 'required|array|min:1',
            'buku_id.*' => 'required|exists:bukus,id',
            'jumlah' => 'required|array|min:1',
            'jumlah.*' => 'required|integer|min:1',
            'kondisi_pinjam' => 'required|array|min:1',
            'kondisi_pinjam.*' => 'required|in:baik,rusak_ringan,rusak_berat',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->buku_id as $index => $buku_id) {
                $buku = Buku::findOrFail($buku_id);
                $jumlah = $request->jumlah[$index];
                if ($buku->stok_tersedia < $jumlah) {
                    throw new \Exception("Stok buku {$buku->judul_buku} tidak mencukupi.");
                }
            }

            $peminjaman->update([
                'user_id' => $validated['user_id'],
                'tanggal_pinjam' => $validated['tanggal_pinjam'],
                'tanggal_kembali_rencana' => $validated['tanggal_kembali_rencana'],
                'keperluan' => $validated['keperluan'],
            ]);

            $peminjaman->details()->delete();

            foreach ($request->buku_id as $index => $buku_id) {
                DetailPeminjaman::create([
                    'peminjaman_id' => $peminjaman->id,
                    'buku_id' => $buku_id,
                    'jumlah' => $request->jumlah[$index],
                    'kondisi_pinjam' => $request->kondisi_pinjam[$index],
                ]);
            }

            LogAktivitas::record('Edit Peminjaman', 'Peminjaman', $peminjaman->id, "Admin mengedit peminjaman ID: " . $peminjaman->id);

            DB::commit();
            return redirect()->route('admin.peminjaman.index')->with('success', 'Peminjaman berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function approve(string $id)
    {
        try {
            DB::beginTransaction();
            $peminjaman = Peminjaman::with('details')->findOrFail($id);
            if ($peminjaman->status !== 'menunggu_persetujuan') throw new \Exception('Status tidak valid.');

            foreach ($peminjaman->details as $detail) {
                $buku = Buku::findOrFail($detail->buku_id);
                if ($buku->stok_tersedia < $detail->jumlah) throw new \Exception("Stok {$buku->judul_buku} habis.");
                $buku->decrement('stok_tersedia', $detail->jumlah);
            }

            $peminjaman->update(['status' => 'dipinjam', 'petugas_id' => Auth::id()]);
            LogAktivitas::record('Peminjaman Disetujui', 'Peminjaman', $id, "Menyetujui peminjaman " . $peminjaman->user->name);
            DB::commit();
            return redirect()->back()->with('success', 'Peminjaman disetujui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reject(string $id)
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);
            $peminjaman->update(['status' => 'ditolak', 'petugas_id' => Auth::id()]);
            LogAktivitas::record('Peminjaman Ditolak', 'Peminjaman', $id, "Menolak peminjaman " . $peminjaman->user->name);
            return redirect()->back()->with('success', 'Peminjaman ditolak!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);
            $peminjaman->delete();
            return redirect()->back()->with('success', 'Peminjaman dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}