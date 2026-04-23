<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Sample Peminjam (Student)
        User::create([
            'name' => 'Siswa Peminjam',
            'email' => 'siswa@gmail.com',
            'password' => Hash::make('siswa123'),
            'role' => 'peminjam',
            'NISN' => '1234567890',
            'kelas_jurusan' => '12 PPLG 1',
        ]);
    }
}
