<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kategori;
use App\Models\LogAktivitas;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kategoris = Kategori::latest()->get();
        return view('admin.kategoris.index', compact('kategoris'));
    }

    /**
    * Show the form for creating a new resource.
    */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|min:3|max:255|unique:kategoris,nama_kategori',
            'deskripsi' => 'nullable|string|max:1000'
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.min' => 'Nama kategori minimal 3 karakter.',
            'nama_kategori.max' => 'Nama kategori maksimal 255 karakter.',
            'nama_kategori.unique' => 'Nama kategori sudah ada.',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter.'
        ]);

        try {
            $kategori = Kategori::create($validated);

            // LOG AKTIVITAS
            LogAktivitas::record(
                'Tambah Kategori',
                'Kategori',
                $kategori->id,
                "Menambahkan kategori: {$kategori->nama_kategori}"
            );

            return redirect()->route('kategoris.index')
                ->with('success', 'Kategori berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan kategori: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
    * Display the specified resource.
    */
    public function show(string $id)
    {
        //
    }

    /**
    * Show the form for editing the specified resource.
    */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $kategori = Kategori::findOrFail($id);

        // Simpan data lama untuk log
        $oldNama = $kategori->nama_kategori;
        $oldDeskripsi = $kategori->deskripsi;

        $validated = $request->validate([
            'nama_kategori' => 'required|string|min:3|max:255|unique:kategoris,nama_kategori,' . $id,
            'deskripsi' => 'nullable|string|max:1000'
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.min' => 'Nama kategori minimal 3 karakter.',
            'nama_kategori.max' => 'Nama kategori maksimal 255 karakter.',
            'nama_kategori.unique' => 'Nama kategori sudah ada.',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter.'
        ]);

        try {
            $kategori->update($validated);

            // LOG AKTIVITAS dengan detail perubahan
            $changes = [];
            if ($oldNama !== $kategori->nama_kategori) {
                $changes[] = "Nama: {$oldNama} → {$kategori->nama_kategori}";
            }
            if ($oldDeskripsi !== $kategori->deskripsi) {
                $changes[] = "Deskripsi diubah";
            }

            $keterangan = "Mengubah kategori: {$kategori->nama_kategori}";
            if (!empty($changes)) {
                $keterangan .= " | Perubahan: " . implode(', ', $changes);
            }

            LogAktivitas::record(
                'Edit Kategori',
                'Kategori',
                $kategori->id,
                $keterangan
            );

            return redirect()->route('kategoris.index')
                ->with('success', 'Kategori berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui kategori: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $kategori = Kategori::findOrFail($id);
            $namaKategori = $kategori->nama_kategori;
            $kategoriId = $kategori->id;

            $kategori->delete();
            
            // LOG AKTIVITAS
            LogAktivitas::record(
                'Hapus Kategori',
                'Kategori',
                $kategoriId,
                "Menghapus kategori: {$namaKategori}"
            );

            return redirect()->route('kategoris.index')
                ->with('success', 'Kategori berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }
}