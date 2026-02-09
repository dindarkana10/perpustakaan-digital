<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjamen';

    protected $fillable = [
        'user_id',
        'petugas_id',
        'tanggal_pinjam',
        'tanggal_kembali_rencana',
        'keperluan',
        'status',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali_rencana' => 'date',
    ];

    // Relasi ke User (peminjam)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke User (petugas)
    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    // Relasi ke DetailPeminjaman
    public function details()
    {
        return $this->hasMany(DetailPeminjaman::class, 'peminjaman_id');
    }

    // Accessor untuk badge status
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'menunggu_persetujuan' => 'bg-info text-white',
            'disetujui' => 'bg-primary',
            'dipinjam' => 'bg-warning text-dark',
            'dikembalikan' => 'bg-success',
            'ditolak' => 'bg-danger',
            'terlambat' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    // Accessor untuk label status
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'menunggu_persetujuan' => 'Menunggu Persetujuan',
            'disetujui' => 'Disetujui',
            'dipinjam' => 'Dipinjam',
            'dikembalikan' => 'Dikembalikan',
            'ditolak' => 'Ditolak',
            'terlambat' => 'Terlambat',
            default => '-'
        };
    }

    // Method untuk format tanggal
    public function getFormattedTanggalPinjamAttribute()
    {
        return Carbon::parse($this->tanggal_pinjam)->format('d/m/Y');
    }

    public function getFormattedTanggalKembaliRencanaAttribute()
    {
        return Carbon::parse($this->tanggal_kembali_rencana)->format('d/m/Y');
    }

    // Method untuk cek apakah terlambat
    public function isTerlambat()
    {
        return in_array($this->status, ['disetujui', 'dipinjam']) 
            && $this->tanggal_kembali_rencana < now()->toDateString();
    }

    // Method untuk cek apakah bisa disetujui
    public function canBeApproved()
    {
        return $this->status === 'menunggu_persetujuan';
    }

    // Method untuk cek apakah bisa ditolak
    public function canBeRejected()
    {
        return $this->status === 'menunggu_persetujuan';
    }

    // Method untuk cek apakah bisa dikembalikan
    public function canBeReturned()
    {
        return in_array($this->status, ['disetujui', 'dipinjam', 'terlambat']);
    }

    // Method untuk cek apakah bisa diedit
    public function canBeEdited()
    {
        return in_array($this->status, ['menunggu_persetujuan', 'dipinjam', 'terlambat']);
    }

    // Method untuk cek apakah bisa dihapus
    public function canBeDeleted()
    {
        return $this->status !== 'dikembalikan';
    }

    // Scope
    public function scopeMenungguPersetujuan($query)
    {
        return $query->where('status', 'menunggu_persetujuan');
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status', 'disetujui');
    }

    public function scopeDipinjam($query)
    {
        return $query->where('status', 'dipinjam');
    }

    public function scopeDikembalikan($query)
    {
        return $query->where('status', 'dikembalikan');
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', 'ditolak');
    }

    public function scopeTerlambat($query)
    {
        return $query->where('status', 'terlambat');
    }

    // Scope untuk filter yang sedang aktif (dipinjam atau terlambat)
    public function scopeAktif($query)
    {
        return $query->whereIn('status', ['disetujui', 'dipinjam', 'terlambat']);
    }

    // Scope untuk filter yang butuh persetujuan
    public function scopePerluPersetujuan($query)
    {
        return $query->where('status', 'menunggu_persetujuan');
    }
}