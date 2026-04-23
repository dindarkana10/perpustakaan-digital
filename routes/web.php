<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\KategoriBukuController;
use App\Http\Controllers\Admin\BukuController;
use App\Http\Controllers\Admin\PeminjamanController;
use App\Http\Controllers\Admin\PengembalianController;
use App\Http\Controllers\Admin\LogAktivitasController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Peminjam\PeminjamPeminjamanController;
use App\Http\Controllers\Peminjam\PeminjamPengembalianController;
use App\Http\Controllers\Peminjam\DaftarBukuController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    if (Auth::user()->role === 'admin') return redirect()->route('admin.dashboard');
    return redirect()->route('peminjam.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ===================== ROLE ADMIN =====================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {

    Route::get('/dashboard', function () {
        return view('admin.dashboard', [
            'totalUser'              => User::count(),
            'totalBuku'              => Buku::count(),
            'totalPeminjaman'        => Peminjaman::count(),
            'peminjamanMenunggu'     => Peminjaman::where('status', 'menunggu_persetujuan')->count(),
            'peminjamanDipinjam'     => Peminjaman::where('status', 'dipinjam')->count(),
            'peminjamanDikembalikan' => Peminjaman::where('status', 'dikembalikan')->count(),
            'peminjamanTerlambat'    => Peminjaman::where('status', 'terlambat')->count(),
        ]);
    })->name('admin.dashboard');

    Route::resource('users', UserController::class);
    Route::resource('kategoris', KategoriBukuController::class);
    Route::resource('bukus', BukuController::class);

    // ---- Peminjaman Admin ----
    Route::resource('peminjaman', PeminjamanController::class)->names('admin.peminjaman');
    Route::post('peminjaman/{id}/approve', [PeminjamanController::class, 'approve'])->name('admin.peminjaman.approve');
    Route::post('peminjaman/{id}/reject',  [PeminjamanController::class, 'reject'])->name('admin.peminjaman.reject');

    // ---- Pengembalian Admin ----
    // ⚠️ PENTING: Route statis (riwayat, lunasi, download-struk) HARUS didaftarkan
    //    SEBELUM Route::resource agar tidak tertangkap sebagai parameter {pengembalian}.

    // ---- Struk Admin ----
    Route::get( 'pengembalian/{id}/show-struk',    [PengembalianController::class, 'showStruk'])    ->name('admin.pengembalian.show-struk');
    Route::get( 'pengembalian/{id}/download-struk',[PengembalianController::class, 'downloadStruk'])->name('admin.pengembalian.download-struk');
    Route::post('pengembalian/{id}/kirim-struk',   [PengembalianController::class, 'kirimStruk'])   ->name('admin.pengembalian.kirim-struk');

    Route::get( 'pengembalian/riwayat', [PengembalianController::class, 'riwayat'])->name('admin.pengembalian.riwayat');
    Route::get( 'pengembalian/{id}/preview-konfirmasi', [PengembalianController::class, 'previewKonfirmasi']) ->name('admin.pengembalian.preview-konfirmasi');
    Route::post('pengembalian/{id}/konfirmasi', [PengembalianController::class, 'konfirmasi'])->name('admin.pengembalian.konfirmasi');
    Route::post('pengembalian/{id}/lunasi',  [PengembalianController::class, 'lunasi'])->name('admin.pengembalian.lunasi');
    Route::get( 'pengembalian/{id}/download-struk',  [PengembalianController::class, 'downloadStruk']) ->name('admin.pengembalian.download-struk');

    // Resource didaftarkan SETELAH route statis di atas
    Route::resource('pengembalian', PengembalianController::class)->names('admin.pengembalian');

    // ---- Log Aktivitas ----
    Route::get(   'log-aktivitas',            [LogAktivitasController::class, 'index'])     ->name('admin.log-aktivitas.index');
    Route::delete('log-aktivitas/delete-all', [LogAktivitasController::class, 'deleteAll']) ->name('admin.log-aktivitas.deleteAll');

    // ---- Laporan ----
    Route::get('laporan', [LaporanController::class, 'index'])->name('admin.laporan.index');
    Route::get('laporan/pdf', [LaporanController::class, 'exportPdf'])->name('admin.laporan.pdf');
});

// ===================== ROLE PEMINJAM =====================
Route::middleware(['auth', 'role:peminjam'])->group(function () {

    Route::get('/peminjam/dashboard', function () {
        $userId = Auth::id();
        return view('peminjam.dashboard', [
            'peminjamanMenunggu'     => Peminjaman::where('user_id', $userId)->where('status', 'menunggu_persetujuan')->count(),
            'peminjamanDipinjam'     => Peminjaman::where('user_id', $userId)->where('status', 'dipinjam')->count(),
            'peminjamanDikembalikan' => Peminjaman::where('user_id', $userId)->where('status', 'dikembalikan')->count(),
        ]);
    })->name('peminjam.dashboard');

    Route::get('/buku', [DaftarBukuController::class, 'index'])->name('buku.index');
    Route::resource('peminjaman',   PeminjamPeminjamanController::class)->names('peminjam.peminjaman');
    Route::resource('pengembalian', PeminjamPengembalianController::class);
});

// ===================== PROFILE =====================
Route::middleware('auth')->group(function () {
    Route::get(   '/profile', [ProfileController::class, 'edit'])    ->name('profile.edit');
    Route::patch( '/profile', [ProfileController::class, 'update'])  ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy']) ->name('profile.destroy');
});

require __DIR__.'/auth.php';