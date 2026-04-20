<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LogAktivitas;
use App\Models\User;

class LogAktivitasController extends Controller
{
    /**
     * Display a listing of the resource with filters.
     */
    public function index(Request $request)
    {
        $users = User::orderBy('name', 'asc')->get();
        
        $query = LogAktivitas::with('user');

        // Filter berdasarkan User
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter berdasarkan Aktivitas
        if ($request->filled('activity')) {
            $query->where('activity', 'LIKE', '%' . $request->activity . '%');
        }

        // Filter berdasarkan Rentang Tanggal
        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }

        $logs = $query->latest()->get();

        return view('admin.log-aktivitas.index', compact('logs', 'users'));
    }

    /**
     * Remove all logs from storage.
     */
    public function deleteAll()
    {
        try {
            LogAktivitas::truncate();
            return redirect()->back()->with('success', 'Semua log aktivitas berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus log: ' . $e->getMessage());
        }
    }

    // Method lainnya bisa tetap kosong atau dihapus jika tidak digunakan
}