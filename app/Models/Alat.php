<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alat extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kategori_id',
        'nama_alat',
        'kondisi',
        'stok_total',
        'stok_tersedia',
        'gambar',
        'harga_beli',
        'keterangan',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'harga_beli' => 'decimal:2',
        'stok_total' => 'integer',
        'stok_tersedia' => 'integer',
    ];

    /**
     * Get the kategori that owns the alat.
     */
    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    // Relasi ke DetailPeminjaman
    public function detailPeminjaman()
    {
        return $this->hasMany(DetailPeminjaman::class, 'alat_id');
    }

    // Relasi ke Peminjaman melalui DetailPeminjaman
    public function peminjaman()
    {
        return $this->hasManyThrough(Peminjaman::class, DetailPeminjaman::class);
    }

    /**
     * Get kondisi badge class.
     */
    public function getKondisiBadgeAttribute()
    {
        return match($this->kondisi) {
            'baik' => 'bg-success',
            'rusak_ringan' => 'bg-warning',
            'rusak_berat' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    /**
     * Get kondisi label.
     */
    public function getKondisiLabelAttribute()
    {
        return match($this->kondisi) {
            'baik' => 'Baik',
            'rusak_ringan' => 'Rusak Ringan',
            'rusak_berat' => 'Rusak Berat',
            default => 'Unknown',
        };
    }

    /**
     * Check if alat is available.
     */
    public function isAvailable()
    {
        return $this->stok_tersedia > 0 && $this->kondisi !== 'rusak_berat';
    }

    /**
     * Get formatted harga beli.
     */
    public function getFormattedHargaBeliAttribute()
    {
        return $this->harga_beli ? 'Rp ' . number_format($this->harga_beli, 0, ',', '.') : '-';
    }
}