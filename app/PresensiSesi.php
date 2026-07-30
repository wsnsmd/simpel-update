<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PresensiSesi extends Model
{
    protected $table = 'presensi_sesi';

    protected $fillable = [
        'diklat_jadwal_id',
        'nama_materi',
        'widyaiswara',
        'jp',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'batas_terlambat',
        'token',
        'token_expired_at',
        'aktif',
    ];

    protected $casts = [
        'tanggal'          => 'date',
        'token_expired_at' => 'datetime',
        'aktif'            => 'boolean',
    ];

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'diklat_jadwal_id');
    }

    public function presensiPeserta()
    {
        return $this->hasMany(PresensiPeserta::class, 'presensi_sesi_id');
    }

    /**
     * Cek apakah QR/token masih valid (sesi sedang berlangsung)
     */
    public function isTokenValid()
    {
        $now   = Carbon::now();
        $mulai = Carbon::parse($this->tanggal->format('Y-m-d') . ' ' . $this->jam_mulai);
        $sls   = Carbon::parse($this->tanggal->format('Y-m-d') . ' ' . $this->jam_selesai);

        return $this->aktif && $now->between($mulai, $sls);
    }

    /**
     * Tentukan status presensi berdasarkan waktu scan
     */
    public function getStatusByScanTime(Carbon $scanAt)
    {
        $mulai         = Carbon::parse($this->tanggal->format('Y-m-d') . ' ' . $this->jam_mulai);
        $batasTerlambat = $mulai->copy()->addMinutes($this->batas_terlambat);

        if ($scanAt->lte($batasTerlambat)) {
            return 'hadir';
        }

        return 'terlambat';
    }

    /**
     * URL presensi peserta
     */
    public function getPresensiUrlAttribute()
    {
        return url('/presensi/' . $this->token);
    }
}
