<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Alat;
use App\Models\Kategori;

class DaftarAlatController extends Controller
{
    public function index(Request $request)
    {
        $query = Alat::with('kategori')->where('stok_tersedia', '>', 0);

        // Filter by kategori
        if ($request->kategori_id) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Filter by kondisi
        if ($request->kondisi) {
            $query->where('kondisi', $request->kondisi);
        }

        // Search by nama alat
        if ($request->search) {
            $query->where('nama_alat', 'like', '%' . $request->search . '%');
        }

        $alats = $query->get();
        $kategoris = Kategori::all();
        
        return view('peminjam.alat.index', compact('alats', 'kategoris'));
    }
}