<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\Buku;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PeminjamPeminjamanController extends Controller
{
    public function index()
    {
        $peminjaman = Peminjaman::with(['petugas', 'details.buku.kategoriBuku'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('peminjam.peminjaman.index', compact('peminjaman'));
    }

    public function create(Request $request)
    {
        $bukus = Buku::where('stok_tersedia', '>', 0)->get();
        $selectedBukuId = $request->buku_id;

        $bukuJs = $bukus->map(function ($b) {
            return [
                'id'     => $b->id,
                'judul'  => $b->judul_buku,
                'stok'   => $b->stok_tersedia,
                'gambar' => $b->gambar ? asset('storage/bukus/' . $b->gambar) : null,
            ];
        })->values();

        return view('peminjam.peminjaman.create', compact('bukus', 'selectedBukuId', 'bukuJs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_pinjam'          => 'required|date|after_or_equal:today',
            'tanggal_kembali_rencana' => 'required|date|after:tanggal_pinjam',
            'keperluan'               => 'required|string|max:1000',
            'buku_id'                 => 'required|array|min:1',
            'buku_id.*'               => 'required|exists:bukus,id',
            'jumlah'                  => 'required|array|min:1',
            'jumlah.*'                => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->buku_id as $index => $buku_id) {
                $buku   = Buku::findOrFail($buku_id);
                $jumlah = $request->jumlah[$index];
                if ($buku->stok_tersedia < $jumlah) {
                    throw new \Exception("Stok buku {$buku->judul_buku} tidak mencukupi.");
                }
            }

            $peminjaman = Peminjaman::create([
                'user_id'                 => Auth::id(),
                'petugas_id'              => null,
                'tanggal_pinjam'          => $validated['tanggal_pinjam'],
                'tanggal_kembali_rencana' => $validated['tanggal_kembali_rencana'],
                'keperluan'               => $validated['keperluan'],
                'status'                  => 'menunggu_persetujuan',
            ]);

            foreach ($request->buku_id as $index => $buku_id) {
                $buku = Buku::find($buku_id);
                DetailPeminjaman::create([
                    'peminjaman_id' => $peminjaman->id,
                    'buku_id'       => $buku_id,
                    'jumlah'        => $request->jumlah[$index],
                    'kondisi_pinjam'=> $buku->kondisi,
                ]);
            }

            LogAktivitas::record('Ajukan Peminjaman', 'Peminjaman', $peminjaman->id, 'Peminjam mengajukan peminjaman buku');

            DB::commit();

            // ✅ BENAR: gunakan nama route lengkap dengan prefix peminjam
            return redirect()->route('peminjam.peminjaman.index')->with('success', 'Peminjaman berhasil diajukan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(string $id)
    {
        $peminjaman = Peminjaman::with(['user', 'petugas', 'details.buku'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return response()->json([
            'id'                      => $peminjaman->id,
            'tanggal_pinjam'          => \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y'),
            'tanggal_kembali_rencana' => \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('d/m/Y'),
            'status'                  => $peminjaman->status,
            'keperluan'               => $peminjaman->keperluan,

            'user' => [
                'name'          => $peminjaman->user->name,
                'NISN'          => $peminjaman->user->NISN,
                'kelas_jurusan' => $peminjaman->user->kelas_jurusan,
            ],

            'details' => $peminjaman->details->map(function ($d) {
                return [
                    'jumlah'         => $d->jumlah,
                    'kondisi_pinjam' => $d->kondisi_pinjam,
                    'buku'           => [
                        'judul_buku' => $d->buku->judul_buku,
                        'gambar'     => $d->buku->gambar,
                    ],
                ];
            }),
        ]);
    }

    public function edit(string $id)
    {
        $peminjaman = Peminjaman::with('details')->where('user_id', Auth::id())->findOrFail($id);

        if ($peminjaman->status !== 'menunggu_persetujuan') {
            // ✅ BENAR: pakai nama route lengkap
            return redirect()->route('peminjam.peminjaman.index')
                ->with('error', 'Peminjaman yang sudah diproses tidak bisa diedit.');
        }

        $bukus = Buku::where('stok_tersedia', '>', 0)->get();

        $bukuJs = $bukus->map(function ($b) {
            return [
                'id'     => $b->id,
                'judul'  => $b->judul_buku,
                'stok'   => $b->stok_tersedia,
                'gambar' => $b->gambar ? asset('storage/bukus/' . $b->gambar) : null,
            ];
        })->values();

        return view('peminjam.peminjaman.edit', compact('peminjaman', 'bukus', 'bukuJs'));
    }

    public function update(Request $request, string $id)
    {
        $peminjaman = Peminjaman::where('user_id', Auth::id())->findOrFail($id);

        if ($peminjaman->status !== 'menunggu_persetujuan') {
            // ✅ BENAR: pakai nama route lengkap
            return redirect()->route('peminjam.peminjaman.index')
                ->with('error', 'Peminjaman yang sudah diproses tidak bisa diupdate.');
        }

        $validated = $request->validate([
            'tanggal_pinjam'          => 'required|date|after_or_equal:today',
            'tanggal_kembali_rencana' => 'required|date|after:tanggal_pinjam',
            'keperluan'               => 'required|string|max:1000',
            'buku_id'                 => 'required|array|min:1',
            'buku_id.*'               => 'required|exists:bukus,id',
            'jumlah'                  => 'required|array|min:1',
            'jumlah.*'                => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->buku_id as $index => $buku_id) {
                $buku   = Buku::findOrFail($buku_id);
                $jumlah = $request->jumlah[$index];
                if ($buku->stok_tersedia < $jumlah) {
                    throw new \Exception("Stok buku {$buku->judul_buku} tidak mencukupi.");
                }
            }

            $peminjaman->update([
                'tanggal_pinjam'          => $validated['tanggal_pinjam'],
                'tanggal_kembali_rencana' => $validated['tanggal_kembali_rencana'],
                'keperluan'               => $validated['keperluan'],
            ]);

            $peminjaman->details()->delete();

            foreach ($request->buku_id as $index => $buku_id) {
                $buku = Buku::find($buku_id);
                DetailPeminjaman::create([
                    'peminjaman_id'  => $peminjaman->id,
                    'buku_id'        => $buku_id,
                    'jumlah'         => $request->jumlah[$index],
                    'kondisi_pinjam' => $buku->kondisi,
                ]);
            }

            LogAktivitas::record('Update Peminjaman', 'Peminjaman', $peminjaman->id, 'Peminjam mengupdate data peminjaman');

            DB::commit();

            // ✅ BUG FIX UTAMA: route sebelumnya 'peminjaman.index' (salah)
            //    Seharusnya 'peminjam.peminjaman.index' agar redirect ke halaman
            //    yang benar dan flash message 'success' tampil
            return redirect()->route('peminjam.peminjaman.index')->with('success', 'Peminjaman berhasil diupdate!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $peminjaman = Peminjaman::where('user_id', Auth::id())->findOrFail($id);

            if ($peminjaman->status !== 'menunggu_persetujuan') {
                throw new \Exception('Hanya status menunggu yang bisa dibatalkan.');
            }

            $peminjaman->delete();

            LogAktivitas::record('Batalkan Peminjaman', 'Peminjaman', $peminjaman->id, 'Peminjam membatalkan peminjaman');

            // ✅ BENAR: konsisten pakai nama route lengkap
            return redirect()->route('peminjam.peminjaman.index')->with('success', 'Peminjaman berhasil dibatalkan!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}