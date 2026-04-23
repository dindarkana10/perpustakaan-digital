<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KategoriBuku;
use App\Models\LogAktivitas;

class KategoriBukuController extends Controller
{
    public function index()
    {
        $kategoris = KategoriBuku::latest()->get();
        return view('admin.kategoris.index', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori_bukus',
            'deskripsi' => 'nullable|string|max:1000'
        ]);

        $kategori = KategoriBuku::create($validated);

        LogAktivitas::record('Tambah Kategori', 'Kategori', $kategori->id, "Menambahkan kategori baru: {$kategori->nama_kategori}");

        return redirect()->route('kategoris.index')->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function update(Request $request, string $id)
    {
        $kategori = KategoriBuku::findOrFail($id);
        
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori_bukus,nama_kategori,' . $id,
            'deskripsi' => 'nullable|string|max:1000'
        ]);

        $kategori->update($validated);

        LogAktivitas::record('Edit Kategori', 'Kategori', $kategori->id, "Mengubah kategori: {$kategori->nama_kategori}");

        return redirect()->route('kategoris.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy(string $id)
    {
        try {
            $kategori = KategoriBuku::findOrFail($id);
            $nama = $kategori->nama_kategori;
            $kategori->delete();

            LogAktivitas::record('Hapus Kategori', 'Kategori', $id, "Menghapus kategori: {$nama}");

            return redirect()->route('kategoris.index')->with('success', 'Kategori berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }
}
