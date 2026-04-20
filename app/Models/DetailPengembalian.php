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
        'alat_id',
        'jumlah_kembali',
        'kondisi_kembali',
        'keterangan_kondisi',
        'biaya_perbaikan',
        'biaya_penggantian'
    ];

    public function pengembalian()
    {
        return $this->belongsTo(Pengembalian::class);
    }

    public function alat()
    {
        return $this->belongsTo(Alat::class);
    }
}