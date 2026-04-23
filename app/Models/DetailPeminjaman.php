<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPeminjaman extends Model
{
    use HasFactory;

    protected $table = 'detail_peminjamen';

    protected $fillable = [
        'peminjaman_id',
        'buku_id',
        'jumlah',
        'kondisi_pinjam',
    ];

    protected $casts = [
        'jumlah' => 'integer',
    ];

    // Relasi ke Peminjaman
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    // Relasi ke Buku
    public function buku()
    {
        return $this->belongsTo(Buku::class, 'buku_id');
    }

    // Accessor untuk badge kondisi
    public function getKondisiBadgeAttribute()
    {
        return match($this->kondisi_pinjam) {
            'baik' => 'bg-success',
            'rusak_ringan' => 'bg-warning',
            'rusak_berat' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    // Accessor untuk label kondisi
    public function getKondisiLabelAttribute()
    {
        return match($this->kondisi_pinjam) {
            'baik' => 'Baik',
            'rusak_ringan' => 'Rusak Ringan',
            'rusak_berat' => 'Rusak Berat',
            default => '-'
        };
    }

    // Scope untuk filter berdasarkan kondisi
    public function scopeKondisiBaik($query)
    {
        return $query->where('kondisi_pinjam', 'baik');
    }

    public function scopeKondisiRusakRingan($query)
    {
        return $query->where('kondisi_pinjam', 'rusak_ringan');
    }

    public function scopeKondisiRusakBerat($query)
    {
        return $query->where('kondisi_pinjam', 'rusak_berat');
    }

    // Method helper untuk cek kondisi
    public function isRusak()
    {
        return in_array($this->kondisi_pinjam, ['rusak_ringan', 'rusak_berat']);
    }
}