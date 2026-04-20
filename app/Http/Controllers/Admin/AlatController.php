<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Alat;
use App\Models\Kategori;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AlatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Alat::with('kategori');

        //filter
        if ($request->kategori_id) {
            $query->where('kategori_id', $request->kategori_id);
        }

        $alats = $query->latest()->get();
        
        $kategoris = Kategori::all();
        return view('admin.alats.index', compact('alats', 'kategoris'));
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
            'kategori_id' => 'required|exists:kategoris,id',
            'nama_alat' => 'required|string|max:255',
            'kondisi' => 'required|in:baik,rusak_ringan,rusak_berat',
            'stok_total' => 'required|integer|min:0',
            'stok_tersedia' => 'required|integer|min:0|lte:stok_total',
            'gambar' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'harga_beli' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000'
        ], [
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'kategori_id.exists' => 'Kategori tidak valid.',
            'nama_alat.required' => 'Nama alat wajib diisi.',
            'kondisi.required' => 'Kondisi wajib dipilih.',
            'kondisi.in' => 'Kondisi tidak valid.',
            'stok_total.required' => 'Stok total wajib diisi.',
            'stok_total.min' => 'Stok total minimal 0.',
            'stok_tersedia.required' => 'Stok tersedia wajib diisi.',
            'stok_tersedia.lte' => 'Stok tersedia tidak boleh melebihi stok total.',
            'gambar.image' => 'File harus berupa gambar.',
            'gambar.mimes' => 'Format gambar harus jpeg, jpg, png, atau gif.',
            'gambar.max' => 'Ukuran gambar maksimal 2MB.',
            'harga_beli.numeric' => 'Harga beli harus berupa angka.',
            'harga_beli.min' => 'Harga beli minimal 0.',
            'keterangan.max' => 'Keterangan maksimal 1000 karakter.'
        ]);

        try {
            // Handle image upload
            if ($request->hasFile('gambar')) {
                $image = $request->file('gambar');
                $imageName = time() . '_' . Str::slug($request->nama_alat) . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/alats', $imageName);
                $validated['gambar'] = $imageName;
            }

            $alat = Alat::create($validated);
            
            // LOG AKTIVITAS - TAMBAH ALAT
            LogAktivitas::record(
                'Tambah Alat',
                'Alat',
                $alat->id,
                "Menambahkan alat baru: {$alat->nama_alat} dengan stok {$alat->stok_total}"
            );

            return redirect()->route('alats.index')
                ->with('success', 'Alat berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan alat: ' . $e->getMessage())
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
        $alat = Alat::findOrFail($id);

        // Simpan data lama untuk log
        $oldData = [
            'nama' => $alat->nama_alat,
            'stok' => $alat->stok_tersedia,
            'kondisi' => $alat->kondisi
        ];

        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'nama_alat' => 'required|string|max:255',
            'kondisi' => 'required|in:baik,rusak_ringan,rusak_berat',
            'stok_total' => 'required|integer|min:0',
            'stok_tersedia' => 'required|integer|min:0|lte:stok_total',
            'gambar' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'harga_beli' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000'
        ], [
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'kategori_id.exists' => 'Kategori tidak valid.',
            'nama_alat.required' => 'Nama alat wajib diisi.',
            'kondisi.required' => 'Kondisi wajib dipilih.',
            'kondisi.in' => 'Kondisi tidak valid.',
            'stok_total.required' => 'Stok total wajib diisi.',
            'stok_total.min' => 'Stok total minimal 0.',
            'stok_tersedia.required' => 'Stok tersedia wajib diisi.',
            'stok_tersedia.lte' => 'Stok tersedia tidak boleh melebihi stok total.',
            'gambar.image' => 'File harus berupa gambar.',
            'gambar.mimes' => 'Format gambar harus jpeg, jpg, png, atau gif.',
            'gambar.max' => 'Ukuran gambar maksimal 2MB.',
            'harga_beli.numeric' => 'Harga beli harus berupa angka.',
            'harga_beli.min' => 'Harga beli minimal 0.',
            'keterangan.max' => 'Keterangan maksimal 1000 karakter.'
        ]);

        try {
            // Handle image upload
            if ($request->hasFile('gambar')) {
                // Delete old image
                if ($alat->gambar && Storage::exists('public/alats/' . $alat->gambar)) {
                    Storage::delete('public/alats/' . $alat->gambar);
                }

                $image = $request->file('gambar');
                $imageName = time() . '_' . Str::slug($request->nama_alat) . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/alats', $imageName);
                $validated['gambar'] = $imageName;
            }

            $alat->update($validated);
            
            // LOG AKTIVITAS - EDIT ALAT
            LogAktivitas::record(
                'Edit Alat',
                'Alat',
                $alat->id,
                "Mengubah data alat: {$oldData['nama']} → {$alat->nama_alat}"
            );

            return redirect()->route('alats.index')
                ->with('success', 'Alat berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui alat: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $alat = Alat::findOrFail($id);
            
            // Simpan nama untuk log
            $namaAlat = $alat->nama_alat;
            $alatId = $alat->id;

            // Delete image if exists
            if ($alat->gambar && Storage::exists('public/alats/' . $alat->gambar)) {
                Storage::delete('public/alats/' . $alat->gambar);
            }
            
            $alat->delete();
            
            // LOG AKTIVITAS - HAPUS ALAT
            LogAktivitas::record(
                'Hapus Alat',
                'Alat',
                $alatId,
                "Menghapus alat: {$namaAlat}"
            );

            return redirect()->route('alats.index')
                ->with('success', 'Alat berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus alat: ' . $e->getMessage());
        }
    }
}
