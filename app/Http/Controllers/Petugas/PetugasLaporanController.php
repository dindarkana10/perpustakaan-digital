<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PetugasLaporanController extends Controller
{
    // Halaman Index Laporan
    public function index()
    {
        return view('petugas.laporan.index');
    }

    // ==========================================
    // PREVIEW LAPORAN PEMINJAMAN (DI TABEL)
    // ==========================================
    public function previewPeminjaman(Request $request)
    {
        // Validasi hanya jika salah satu diisi
        if ($request->tanggal_mulai || $request->tanggal_selesai) {
            $request->validate([
                'tanggal_mulai' => 'required_with:tanggal_selesai|date',
                'tanggal_selesai' => 'required_with:tanggal_mulai|date|after_or_equal:tanggal_mulai',
            ], [
                'tanggal_mulai.required_with' => 'Tanggal mulai harus diisi jika tanggal selesai diisi',
                'tanggal_selesai.required_with' => 'Tanggal selesai harus diisi jika tanggal mulai diisi',
                'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai'
            ]);
        }

        $query = Peminjaman::with(['user', 'petugas', 'details.alat.kategori']);

        // Filter tanggal (opsional)
        if ($request->tanggal_mulai && $request->tanggal_selesai) {
            $query->whereBetween('tanggal_pinjam', [
                $request->tanggal_mulai, 
                $request->tanggal_selesai
            ]);
        }

        // Filter status (opsional)
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $peminjaman = $query->orderBy('tanggal_pinjam', 'desc')->get();

        return view('petugas.laporan.preview-peminjaman', [
            'peminjaman' => $peminjaman,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status' => $request->status
        ]);
    }

    // ==========================================
    // EXPORT PDF PEMINJAMAN
    // ==========================================
    public function exportPeminjaman(Request $request)
    {
        $query = Peminjaman::with(['user', 'petugas', 'details.alat.kategori']);

        // Filter tanggal (opsional)
        if ($request->tanggal_mulai && $request->tanggal_selesai) {
            $query->whereBetween('tanggal_pinjam', [
                $request->tanggal_mulai, 
                $request->tanggal_selesai
            ]);
        }

        // Filter status (opsional)
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $peminjaman = $query->orderBy('tanggal_pinjam', 'desc')->get();

        // Format tanggal untuk tampilan
        $tanggalMulai = $request->tanggal_mulai 
            ? Carbon::parse($request->tanggal_mulai)->format('d/m/Y') 
            : 'Semua';
        $tanggalSelesai = $request->tanggal_selesai 
            ? Carbon::parse($request->tanggal_selesai)->format('d/m/Y') 
            : 'Semua';

        $pdf = Pdf::loadView('petugas.laporan.peminjaman-pdf', [
            'peminjaman' => $peminjaman,
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'status' => $request->status ?? 'Semua'
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan_Peminjaman_' . date('Ymd_His') . '.pdf');
    }

    // ==========================================
    // PREVIEW LAPORAN PENGEMBALIAN (DI TABEL)
    // ==========================================
    public function previewPengembalian(Request $request)
    {
        // Validasi hanya jika salah satu diisi
        if ($request->tanggal_mulai || $request->tanggal_selesai) {
            $request->validate([
                'tanggal_mulai' => 'required_with:tanggal_selesai|date',
                'tanggal_selesai' => 'required_with:tanggal_mulai|date|after_or_equal:tanggal_mulai',
            ], [
                'tanggal_mulai.required_with' => 'Tanggal mulai harus diisi jika tanggal selesai diisi',
                'tanggal_selesai.required_with' => 'Tanggal selesai harus diisi jika tanggal mulai diisi',
                'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai'
            ]);
        }

        $query = Pengembalian::with(['peminjaman.user', 'peminjaman.details.alat', 'details', 'petugas'])
            ->where('status_pengembalian', 'dikonfirmasi');

        // Filter tanggal (opsional)
        if ($request->tanggal_mulai && $request->tanggal_selesai) {
            $query->whereBetween('tanggal_kembali_aktual', [
                $request->tanggal_mulai, 
                $request->tanggal_selesai
            ]);
        }

        $pengembalian = $query->orderBy('tanggal_kembali_aktual', 'desc')->get();

        return view('petugas.laporan.preview-pengembalian', [
            'pengembalian' => $pengembalian,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai
        ]);
    }

    // ==========================================
    // EXPORT PDF PENGEMBALIAN
    // ==========================================
    public function exportPengembalian(Request $request)
    {
        $query = Pengembalian::with(['peminjaman.user', 'peminjaman.details.alat', 'details', 'petugas'])
            ->where('status_pengembalian', 'dikonfirmasi');

        // Filter tanggal (opsional)
        if ($request->tanggal_mulai && $request->tanggal_selesai) {
            $query->whereBetween('tanggal_kembali_aktual', [
                $request->tanggal_mulai, 
                $request->tanggal_selesai
            ]);
        }

        $pengembalian = $query->orderBy('tanggal_kembali_aktual', 'desc')->get();

        // Format tanggal untuk tampilan
        $tanggalMulai = $request->tanggal_mulai 
            ? Carbon::parse($request->tanggal_mulai)->format('d/m/Y') 
            : 'Semua';
        $tanggalSelesai = $request->tanggal_selesai 
            ? Carbon::parse($request->tanggal_selesai)->format('d/m/Y') 
            : 'Semua';

        $pdf = Pdf::loadView('petugas.laporan.pengembalian-pdf', [
            'pengembalian' => $pengembalian,
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan_Pengembalian_' . date('Ymd_His') . '.pdf');
    }
}