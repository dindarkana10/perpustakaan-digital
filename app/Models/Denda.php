<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Denda extends Model
{
    use HasFactory;

    protected $fillable = [
        'denda_per_hari',
        'denda_rusak_ringan',
        'denda_rusak_berat',
        'persentase_penggantian_hilang'
    ];
}