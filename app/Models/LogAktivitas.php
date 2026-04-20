<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LogAktivitas extends Model
{
    protected $table = 'log_aktivitas';

    protected $fillable = [
        'user_id',
        'activity',
        'model',
        'model_id',
        'keterangan',
        'ip_address',
        'user_agent',
    ];

    /**
     * Helper untuk mencatat log aktivitas
     */
    public static function record(
        string $activity,
        $model = null,
        $modelId = null,
        $keterangan = null
    ) {
        self::create([
            'user_id' => Auth::id(),
            'activity'   => $activity,
            'model'      => $model,
            'model_id'   => $modelId,
            'keterangan' => $keterangan,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Relasi ke user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
