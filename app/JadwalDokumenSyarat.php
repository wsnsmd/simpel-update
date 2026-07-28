<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JadwalDokumenSyarat extends Model
{
    protected $table = 'jadwal_dokumen_syarat';

    protected $fillable = [
        'diklat_jadwal_id',
        'nama',
        'keterangan',
        'format_izin',
        'max_size_kb',
        'wajib',
        'urutan',
    ];

    protected $casts = [
        'wajib' => 'boolean',
    ];

    /**
     * Jadwal diklat pemilik syarat ini
     */
    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'diklat_jadwal_id');
    }

    /**
     * Dokumen yang sudah diupload oleh para peserta untuk syarat ini
     */
    public function pesertaDokumen()
    {
        return $this->hasMany(PesertaDokumen::class, 'dokumen_syarat_id');
    }

    /**
     * Format ekstensi yang diizinkan sebagai array
     */
    public function getFormatArrayAttribute(): array
    {
        return array_map('trim', explode(',', $this->format_izin));
    }

    /**
     * String validasi Laravel, cth: "pdf,jpg,jpeg,png"
     */
    public function getMimesRuleAttribute(): string
    {
        return $this->format_izin;
    }
}
