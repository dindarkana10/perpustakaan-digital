<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\KategoriBuku;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BukuController extends Controller
{
    public function index(Request $request)
    {
        $query = Buku::with('kategoriBuku');

        if ($request->kategori_buku_id) {
            $query->where('kategori_buku_id', $request->kategori_buku_id);
        }

        if ($request->search) {
            $query->where('judul_buku', 'like', '%' . $request->search . '%')
                  ->orWhere('penulis', 'like', '%' . $request->search . '%')
                  ->orWhere('ISBN', 'like', '%' . $request->search . '%');
        }

        $bukus = $query->latest()->get();
        $kategoris = KategoriBuku::all();
        
        return view('admin.bukus.index', compact('bukus', 'kategoris'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_buku_id' => 'required|exists:kategori_bukus,id',
            'judul_buku' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'tahun_terbit' => 'required|integer|min:1900|max:' . date('Y'),
            'ISBN' => 'nullable|string|max:20',
            'kondisi' => 'required|in:baik,rusak_ringan,rusak_berat',
            'stok' => 'required|integer|min:0',
            'stok_tersedia' => 'required|integer|min:0|lte:stok',
            'gambar' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'harga_buku' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000'
        ]);

        try {
            if ($request->hasFile('gambar')) {
                $image = $request->file('gambar');
                $imageName = time() . '_' . Str::slug($request->judul_buku) . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/bukus', $imageName);
                $validated['gambar'] = $imageName;
            }

            $buku = Buku::create($validated);
            
            LogAktivitas::record(
                'Tambah Buku',
                'Buku',
                $buku->id,
                "Menambahkan buku baru: {$buku->judul_buku} dengan stok {$buku->stok}"
            );

            return redirect()->route('bukus.index')->with('success', 'Buku berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan buku: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, string $id)
    {
        $buku = Buku::findOrFail($id);

        $validated = $request->validate([
            'kategori_buku_id' => 'required|exists:kategori_bukus,id',
            'judul_buku' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'tahun_terbit' => 'required|integer|min:1900|max:' . date('Y'),
            'ISBN' => 'nullable|string|max:20',
            'kondisi' => 'required|in:baik,rusak_ringan,rusak_berat',
            'stok' => 'required|integer|min:0',
            'stok_tersedia' => 'required|integer|min:0|lte:stok',
            'gambar' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'harga_buku' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000'
        ]);

        try {
            if ($request->hasFile('gambar')) {
                if ($buku->gambar && Storage::exists('public/bukus/' . $buku->gambar)) {
                    Storage::delete('public/bukus/' . $buku->gambar);
                }

                $image = $request->file('gambar');
                $imageName = time() . '_' . Str::slug($request->judul_buku) . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/bukus', $imageName);
                $validated['gambar'] = $imageName;
            }

            $buku->update($validated);
            
            LogAktivitas::record(
                'Edit Buku',
                'Buku',
                $buku->id,
                "Mengubah data buku: {$buku->judul_buku}"
            );

            return redirect()->route('bukus.index')->with('success', 'Buku berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui buku: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $buku = Buku::findOrFail($id);
            $judul = $buku->judul_buku;
            
            if ($buku->gambar && Storage::exists('public/bukus/' . $buku->gambar)) {
                Storage::delete('public/bukus/' . $buku->gambar);
            }
            
            $buku->delete();
            
            LogAktivitas::record(
                'Hapus Buku',
                'Buku',
                $id,
                "Menghapus buku: {$judul}"
            );

            return redirect()->route('bukus.index')->with('success', 'Buku berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus buku: ' . $e->getMessage());
        }
    }
}
