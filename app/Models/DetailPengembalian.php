<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPengembalian extends Model
{
    use HasFactory;

    protected $table = 'detail_pengembalians';

    protected $fillable = [
        'pengembalian_id',
        'buku_id',
        'jumlah_kembali',
        'kondisi_kembali',
        'keterangan_kondisi',
        'denda_kerusakan_buku',
        'biaya_perbaikan',
        'biaya_penggantian'
    ];

    public function pengembalian()
    {
        return $this->belongsTo(Pengembalian::class);
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }
}