<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PresensiPeserta extends Model
{
    protected $table = 'presensi_peserta';

    protected $fillable = [
        'presensi_sesi_id',
        'peserta_id',
        'scan_at',
        'status',
        'keterangan',
        'ip_address',
    ];

    protected $casts = [
        'scan_at' => 'datetime',
    ];

    public function sesi()
    {
        return $this->belongsTo(PresensiSesi::class, 'presensi_sesi_id');
    }

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id');
    }

    public static function labelStatus($status)
    {
        $map = [
            'hadir'     => 'Hadir',
            'terlambat' => 'Terlambat',
            'izin'      => 'Izin',
            'sakit'     => 'Sakit',
            'alpha'     => 'Tidak Hadir',
        ];
        return $map[$status] ?? $status;
    }

    public static function badgeStatus($status)
    {
        $map = [
            'hadir'     => 'success',
            'terlambat' => 'warning',
            'izin'      => 'info',
            'sakit'     => 'secondary',
            'alpha'     => 'danger',
        ];
        return $map[$status] ?? 'secondary';
    }
}
