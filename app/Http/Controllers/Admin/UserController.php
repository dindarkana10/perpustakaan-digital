<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\LogAktivitas;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.users.index', compact('users'));
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
        $request->validate([
            'name'     => 'required|string|min:3|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|in:admin,petugas,peminjam',
        ], [
            'name.required' => 'Nama wajib diisi',
            'name.min' => 'Nama minimal 3 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'role.required' => 'Role wajib dipilih',
            'role.in' => 'Role tidak valid',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        // LOG AKTIVITAS - TAMBAH USER
        LogAktivitas::record(
            'Tambah User',
            'User',
            $user->id,
            "Menambahkan user baru: {$user->name} ({$user->email}) dengan role {$user->role}"
        );

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil ditambahkan');
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
    public function update(Request $request, User $user)
    {
        // Simpan data lama untuk log
        $oldData = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role
        ];

        $rules = [
            'name'  => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:admin,petugas,peminjam',
        ];

        // Validasi password hanya jika diisi
        if ($request->filled('password')) {
            $rules['password'] = 'required|min:8|confirmed';
        }

        $validated = $request->validate($rules, [
            'name.required' => 'Nama wajib diisi',
            'name.min' => 'Nama minimal 3 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'role.required' => 'Role wajib dipilih',
            'role.in' => 'Role tidak valid',
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
        ];

        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // 🔥 LOG AKTIVITAS - EDIT USER
        // Buat keterangan detail perubahan
        $changes = [];
        if ($oldData['name'] !== $user->name) {
            $changes[] = "Nama: {$oldData['name']} → {$user->name}";
        }
        if ($oldData['email'] !== $user->email) {
            $changes[] = "Email: {$oldData['email']} → {$user->email}";
        }
        if ($oldData['role'] !== $user->role) {
            $changes[] = "Role: {$oldData['role']} → {$user->role}";
        }
        if ($request->filled('password')) {
            $changes[] = "Password diubah";
        }

        $keterangan = "Mengubah user: {$user->name}";
        if (!empty($changes)) {
            $keterangan .= " | Perubahan: " . implode(', ', $changes);
        }

        LogAktivitas::record(
            'Edit User',
            'User',
            $user->id,
            $keterangan
        );

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            // Simpan data untuk log sebelum dihapus
            $userName = $user->name;
            $userEmail = $user->email;
            $userRole = $user->role;
            $userId = $user->id;

            $user->delete();
            
            // LOG AKTIVITAS - HAPUS USER
            LogAktivitas::record(
                'Hapus User',
                'User',
                $userId,
                "Menghapus user: {$userName} ({$userEmail}) dengan role {$userRole}"
            );

            return redirect()
                ->route('users.index')
                ->with('success', 'User berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }
}