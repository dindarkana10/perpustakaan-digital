<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Models\User;
use App\Models\Alat;
use App\Models\Peminjaman;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\AlatController;
use App\Http\Controllers\Admin\PeminjamanController;
use App\Http\Controllers\Petugas\PetugasPeminjamanController;

Route::get('/defaultroute', function () {
    return view('welcome');
});

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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
    Route::resource('/admin/peminjaman', PeminjamanController::class)->names('peminjaman');
});

Route::middleware(['auth', 'role:petugas'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/dashboard', function () {
        return view('petugas.dashboard', [
        'totalPeminjaman' => Peminjaman::count(),
        'menunggu' => Peminjaman::where('status', 'menunggu_persetujuan')->count(),
        'dipinjam' => Peminjaman::where('status', 'dipinjam')->count(),
        'terlambat' => Peminjaman::where('status', 'terlambat')->count(),
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
});

Route::middleware(['auth', 'role:peminjam'])->group(function () {

    Route::get('/peminjam/dashboard', function () {
        return view('peminjam.dashboard');
    })->name('peminjam.dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
