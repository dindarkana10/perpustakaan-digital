<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengembalian extends Model
{
    use HasFactory;

    protected $table = 'pengembalians';

    protected $fillable = [
        'peminjaman_id',
        'tanggal_kembali_aktual',
        'petugas_id',
        'keterlambatan_hari',
        'denda_keterlambatan',
        'denda_kerusakan',
        'total_denda',
        'status_pembayaran',
        'status_pengembalian'
    ];

     protected $casts = [
        'tanggal_kembali_aktual' => 'date', // tambahkan ini
    ];
    
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class);
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function details()
    {
        return $this->hasMany(DetailPengembalian::class);
    }
}