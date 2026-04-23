<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\KategoriBuku;

class DaftarBukuController extends Controller
{
    public function index(Request $request)
    {
        $query = Buku::with('kategoriBuku')->where('stok_tersedia', '>', 0);

        if ($request->kategori_buku_id) {
            $query->where('kategori_buku_id', $request->kategori_buku_id);
        }

        if ($request->search) {
            $query->where('judul_buku', 'like', '%' . $request->search . '%')
                  ->orWhere('penulis', 'like', '%' . $request->search . '%');
        }

        $bukus = $query->latest()->get();
        $kategoris = KategoriBuku::all();
        
        return view('peminjam.buku.index', compact('bukus', 'kategoris'));
    }
}
