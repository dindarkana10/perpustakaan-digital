<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Denda;

class DendaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Denda::firstOrCreate(
            [], // pastikan hanya 1 data
            [
                'denda_per_hari' => 1000,
                'denda_rusak_ringan' => 10,
                'denda_rusak_berat' => 50,
                'persentase_penggantian_hilang' => 100
            ]
        );
    }
}
