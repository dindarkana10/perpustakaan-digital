<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetailPeminjaman;
use App\Models\KategoriBuku;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->buildQuery($request);
        $laporan = $query->paginate(10)->withQueryString();
        
        // Calculate Summary
        $baseQuery = $this->buildQuery($request);
        $peminjamanIds = (clone $baseQuery)->reorder()->distinct()->pluck('peminjaman_id');
        
        $summary = [
            'total_peminjaman' => $peminjamanIds->count(),
            'total_pengembalian' => Peminjaman::whereIn('id', $peminjamanIds)->where('status', 'dikembalikan')->count(),
            'total_denda' => Pengembalian::whereIn('peminjaman_id', $peminjamanIds)->sum('total_denda'),
        ];

        $categories = KategoriBuku::all();

        return view('admin.laporan.index', compact('laporan', 'summary', 'categories'));
    }

    public function exportPdf(Request $request)
    {
        $laporan = $this->buildQuery($request)->get();
        
        $baseQuery = $this->buildQuery($request);
        $peminjamanIds = (clone $baseQuery)->reorder()->distinct()->pluck('peminjaman_id');
        
        $summary = [
             'total_peminjaman' => $peminjamanIds->count(),
             'total_pengembalian' => Peminjaman::whereIn('id', $peminjamanIds)->where('status', 'dikembalikan')->count(),
             'total_denda' => Pengembalian::whereIn('peminjaman_id', $peminjamanIds)->sum('total_denda'),
        ];

        $pdf = Pdf::loadView('admin.laporan.pdf', compact('laporan', 'summary', 'request'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-peminjaman-' . now()->format('Y-m-d') . '.pdf');
    }

    private function buildQuery(Request $request)
    {
        $query = DetailPeminjaman::with(['peminjaman.user', 'buku.kategoriBuku', 'peminjaman.pengembalian']);

        // Filter: Kategori Buku
        if ($request->filled('kategori_id')) {
            $query->whereHas('buku', function($q) use ($request) {
                $q->where('kategori_buku_id', $request->kategori_id);
            });
        }

        // Filter: Judul Buku
        if ($request->filled('judul')) {
            $query->whereHas('buku', function($q) use ($request) {
                $q->where('judul_buku', 'like', '%' . $request->judul . '%');
            });
        }

        // Filter: Nama Peminjam
        if ($request->filled('peminjam')) {
            $query->whereHas('peminjaman.user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->peminjam . '%');
            });
        }

        // Filter: Status
        if ($request->filled('status')) {
            $query->whereHas('peminjaman', function($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        // Filter: Rentang Tanggal Pinjam
        if ($request->filled('tgl_pinjam_awal') && $request->filled('tgl_pinjam_akhir')) {
            $query->whereHas('peminjaman', function($q) use ($request) {
                $q->whereBetween('tanggal_pinjam', [$request->tgl_pinjam_awal, $request->tgl_pinjam_akhir]);
            });
        }

        // Filter: Rentang Tanggal Kembali (Aktual)
        if ($request->filled('tgl_kembali_awal') && $request->filled('tgl_kembali_akhir')) {
            $query->whereHas('peminjaman.pengembalian', function($q) use ($request) {
                $q->whereBetween('tanggal_kembali_aktual', [$request->tgl_kembali_awal, $request->tgl_kembali_akhir]);
            });
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        if ($sortField === 'tanggal_pinjam') {
            $query->join('peminjamen', 'detail_peminjamen.peminjaman_id', '=', 'peminjamen.id')
                  ->orderBy('peminjamen.tanggal_pinjam', $sortOrder)
                  ->select('detail_peminjamen.*');
        } else {
            $query->orderBy($sortField, $sortOrder);
        }

        return $query;
    }
}
