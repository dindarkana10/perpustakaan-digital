<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\LogAktivitas;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $rules = [
            'name'     => 'required|string|min:3|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|in:admin,peminjam',
        ];

        // Tambah validasi NISN & kelas_jurusan hanya jika role = peminjam
        if ($request->role === 'peminjam') {
            $rules['NISN']          = 'required|string|max:20|unique:users,NISN';
            $rules['kelas_jurusan'] = 'required|string|max:100';
        }

        $messages = [
            'name.required'          => 'Nama wajib diisi',
            'name.min'               => 'Nama minimal 3 karakter',
            'email.required'         => 'Email wajib diisi',
            'email.email'            => 'Format email tidak valid',
            'email.unique'           => 'Email sudah terdaftar',
            'password.required'      => 'Password wajib diisi',
            'password.min'           => 'Password minimal 8 karakter',
            'password.confirmed'     => 'Konfirmasi password tidak cocok',
            'role.required'          => 'Role wajib dipilih',
            'role.in'                => 'Role tidak valid',
            'NISN.required'          => 'NISN wajib diisi',
            'NISN.unique'            => 'NISN sudah terdaftar',
            'kelas_jurusan.required' => 'Kelas & Jurusan wajib diisi',
        ];

        $request->validate($rules, $messages);

        $data = [
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ];

        if ($request->role === 'peminjam') {
            $data['NISN']          = $request->NISN;
            $data['kelas_jurusan'] = $request->kelas_jurusan;
        }

        $user = User::create($data);

        LogAktivitas::record(
            'Tambah User',
            'User',
            $user->id,
            "Menambahkan user baru: {$user->name} ({$user->email}) dengan role {$user->role}"
        );

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, User $user)
    {
        $oldData = [
            'name'          => $user->name,
            'email'         => $user->email,
            'role'          => $user->role,
            'NISN'          => $user->NISN,
            'kelas_jurusan' => $user->kelas_jurusan,
        ];

        $rules = [
            'name'  => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:admin,peminjam',
        ];

        if ($request->role === 'peminjam') {
            $rules['NISN']          = 'required|string|max:20|unique:users,NISN,' . $user->id;
            $rules['kelas_jurusan'] = 'required|string|max:100';
        }

        if ($request->filled('password')) {
            $rules['password'] = 'required|min:8|confirmed';
        }

        $request->validate($rules, [
            'name.required'          => 'Nama wajib diisi',
            'name.min'               => 'Nama minimal 3 karakter',
            'email.required'         => 'Email wajib diisi',
            'email.email'            => 'Format email tidak valid',
            'email.unique'           => 'Email sudah terdaftar',
            'password.required'      => 'Password wajib diisi',
            'password.min'           => 'Password minimal 8 karakter',
            'password.confirmed'     => 'Konfirmasi password tidak cocok',
            'role.required'          => 'Role wajib dipilih',
            'role.in'                => 'Role tidak valid',
            'NISN.required'          => 'NISN wajib diisi',
            'NISN.unique'            => 'NISN sudah terdaftar',
            'kelas_jurusan.required' => 'Kelas & Jurusan wajib diisi',
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
        ];

        if ($request->role === 'peminjam') {
            $data['NISN']          = $request->NISN;
            $data['kelas_jurusan'] = $request->kelas_jurusan;
        } else {
            // Kosongkan field peminjam jika role bukan peminjam
            $data['NISN']          = null;
            $data['kelas_jurusan'] = null;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Buat log perubahan
        $changes = [];
        if ($oldData['name'] !== $user->name)
            $changes[] = "Nama: {$oldData['name']} → {$user->name}";
        if ($oldData['email'] !== $user->email)
            $changes[] = "Email: {$oldData['email']} → {$user->email}";
        if ($oldData['role'] !== $user->role)
            $changes[] = "Role: {$oldData['role']} → {$user->role}";
        if ($oldData['NISN'] !== $user->NISN)
            $changes[] = "NISN: {$oldData['NISN']} → {$user->NISN}";
        if ($oldData['kelas_jurusan'] !== $user->kelas_jurusan)
            $changes[] = "Kelas/Jurusan: {$oldData['kelas_jurusan']} → {$user->kelas_jurusan}";
        if ($request->filled('password'))
            $changes[] = "Password diubah";

        $keterangan = "Mengubah user: {$user->name}";
        if (!empty($changes))
            $keterangan .= " | Perubahan: " . implode(', ', $changes);

        LogAktivitas::record('Edit User', 'User', $user->id, $keterangan);

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate');
    }

    public function destroy(User $user)
    {
        try {
            $userName  = $user->name;
            $userEmail = $user->email;
            $userRole  = $user->role;
            $userId    = $user->id;

            $user->delete();

            LogAktivitas::record(
                'Hapus User',
                'User',
                $userId,
                "Menghapus user: {$userName} ({$userEmail}) dengan role {$userRole}"
            );

            return redirect()->route('users.index')->with('success', 'User berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('users.index')->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }
}