<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;

    protected $fillable = [
        'kategori_buku_id',
        'judul_buku',
        'penulis',
        'penerbit',
        'tahun_terbit',
        'ISBN',
        'kondisi',
        'stok',
        'stok_tersedia',
        'gambar',
        'harga_buku',
        'keterangan',
    ];

    protected $casts = [
        'harga_buku' => 'decimal:2',
        'stok' => 'integer',
        'stok_tersedia' => 'integer',
        'tahun_terbit' => 'integer',
    ];

    public function kategoriBuku()
    {
        return $this->belongsTo(KategoriBuku::class, 'kategori_buku_id');
    }

    public function detailPeminjaman()
    {
        return $this->hasMany(DetailPeminjaman::class, 'buku_id');
    }

    public function getKondisiBadgeAttribute()
    {
        return match($this->kondisi) {
            'baik' => 'bg-success',
            'rusak_ringan' => 'bg-warning',
            'rusak_berat' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function getKondisiLabelAttribute()
    {
        return match($this->kondisi) {
            'baik' => 'Baik',
            'rusak_ringan' => 'Rusak Ringan',
            'rusak_berat' => 'Rusak Berat',
            default => 'Unknown',
        };
    }

    public function isAvailable()
    {
        return $this->stok_tersedia > 0 && $this->kondisi !== 'rusak_berat';
    }

    public function getFormattedHargaBukuAttribute()
    {
        return $this->harga_buku ? 'Rp ' . number_format($this->harga_buku, 0, ',', '.') : '-';
    }
}
