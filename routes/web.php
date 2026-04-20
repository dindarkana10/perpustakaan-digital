<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\RoleMiddleware;
use App\Models\User;
use App\Models\Alat;
use App\Models\Peminjaman;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\AlatController;
use App\Http\Controllers\Admin\PeminjamanController;
use App\Http\Controllers\Admin\LogAktivitasController;
use App\Http\Controllers\Petugas\PetugasPeminjamanController;
use App\Http\Controllers\Petugas\PetugasPengembalianController;
use App\Http\Controllers\Peminjam\PeminjamPengembalianController;
use App\Http\Controllers\Petugas\PetugasLaporanController;

Route::get('/defaultroute', function () {
    return view('welcome');
});

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

//ROLE ADMIN
Route::middleware(['auth', 'role:admin'])->group(function () {

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard', [
        'totalUser' => User::count(),
        'totalAlat' => Alat::count(),

        // Statistik Peminjaman
        'totalPeminjaman' => Peminjaman::count(),
        'peminjamanDipinjam' => Peminjaman::where('status', 'dipinjam')->count(),
        'peminjamanDikembalikan' => Peminjaman::where('status', 'dikembalikan')->count(),
        'peminjamanTerlambat' => Peminjaman::where('status', 'terlambat')->count(),
    ]);
})->name('admin.dashboard');

    Route::resource('/admin/users', UserController::class)->names('users');
    Route::resource('/admin/kategoris', KategoriController::class)->names('kategoris');
    Route::resource('/admin/alats', AlatController::class)->names('alats');
    Route::resource('/admin/peminjaman', PeminjamanController::class)->names('admin.peminjaman');
    Route::resource('/admin/pengembalian', App\Http\Controllers\Admin\PengembalianController::class)->names('admin.pengembalian');
    Route::get('/admin/log-aktivitas', [LogAktivitasController::class, 'index'])->name('admin.log-aktivitas.index');
    Route::delete('/admin/log-aktivitas/delete-all', [LogAktivitasController::class, 'deleteAll'])->name('admin.log-aktivitas.deleteAll');
});

//ROLE PETUGAS
Route::middleware(['auth', 'role:petugas'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/dashboard', function () {
        return view('petugas.dashboard', [
        'totalPeminjaman' => Peminjaman::count(),
        'menunggu' => Peminjaman::where('status', 'menunggu_persetujuan')->count(),
        'dipinjam' => Peminjaman::where('status', 'dipinjam')->count(),
        'terlambat' => Peminjaman::where('status', 'terlambat')->count(),
        'menungguPengembalian' => \App\Models\Pengembalian::where('status_pengembalian', 'diajukan')->count(),
        ]);
    })->name('dashboard');

    // Resource route untuk CRUD dasar
    Route::resource('peminjaman', PetugasPeminjamanController::class);
    
    // Route khusus untuk approval
    Route::post('peminjaman/{id}/approve', [PetugasPeminjamanController::class, 'approve'])
        ->name('peminjaman.approve');
    Route::post('peminjaman/{id}/reject', [PetugasPeminjamanController::class, 'reject'])
        ->name('peminjaman.reject');
    Route::post('peminjaman/{id}/pengembalian', [PetugasPeminjamanController::class, 'pengembalian'])
        ->name('peminjaman.pengembalian');

     // Route pengembalian
    Route::get('pengembalian', [App\Http\Controllers\Petugas\PetugasPengembalianController::class, 'index'])
        ->name('pengembalian.index');
    // Validasi pengembalian
    Route::post('pengembalian/{id}/konfirmasi', [PetugasPengembalianController::class, 'konfirmasi'])
        ->name('pengembalian.konfirmasi');
    // Tandai lunas
    Route::post('pengembalian/{id}/lunasi', [PetugasPengembalianController::class, 'lunasi'])
        ->name('pengembalian.lunasi');

    // Route::get('pengembalian/{id}', [App\Http\Controllers\Petugas\PetugasPengembalianController::class, 'show'])
    //     ->name('pengembalian.show');
    // Route::post('pengembalian/{id}/konfirmasi', [App\Http\Controllers\Petugas\PetugasPengembalianController::class, 'konfirmasi'])
    //     ->name('pengembalian.konfirmasi');
    // Route::put('pengembalian/{id}/toggle-pembayaran', [PetugasPengembalianController::class, 'togglePembayaran'])
    // ->name('pengembalian.toggle-pembayaran');
    Route::get('pengembalian-riwayat', [App\Http\Controllers\Petugas\PetugasPengembalianController::class, 'riwayat'])
        ->name('pengembalian.riwayat');

    // Route laporan
    Route::get('laporan', [PetugasLaporanController::class, 'index'])
    ->name('laporan.index');

    // Preview sebelum export
    Route::post('laporan/peminjaman/preview', [PetugasLaporanController::class, 'previewPeminjaman'])
        ->name('laporan.peminjaman.preview');
    Route::post('laporan/pengembalian/preview', [PetugasLaporanController::class, 'previewPengembalian'])
        ->name('laporan.pengembalian.preview');

    // Export PDF
    Route::post('laporan/peminjaman/export', [PetugasLaporanController::class, 'exportPeminjaman'])
        ->name('laporan.peminjaman.export');
    Route::post('laporan/pengembalian/export', [PetugasLaporanController::class, 'exportPengembalian'])
        ->name('laporan.pengembalian.export');
    });

//ROLE PEMINJAM
Route::middleware(['auth', 'role:peminjam'])->group(function () {

    Route::get('/peminjam/dashboard', function () {
        $userId = Auth::id();

        return view('peminjam.dashboard', [
            'peminjamanMenunggu' => Peminjaman::where('user_id', $userId)
                ->where('status', 'menunggu_persetujuan')->count(),
            'peminjamanDipinjam' => Peminjaman::where('user_id', $userId)
                ->where('status', 'dipinjam')->count(),
            'peminjamanDikembalikan' => Peminjaman::where('user_id', $userId)
                ->where('status', 'dikembalikan')->count(),
                ]);
    })->name('peminjam.dashboard');

    // Route untuk daftar alat
    Route::get('/alat', [App\Http\Controllers\Peminjam\DaftarAlatController::class, 'index'])->name('alat.index');
    // Route untuk peminjaman
    Route::resource('peminjaman', App\Http\Controllers\Peminjam\PeminjamPeminjamanController::class);
    //Route untuk pengembalian
    Route::resource('pengembalian', PeminjamPengembalianController::class);

});
 
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
