<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sertifikat extends Model
{
    protected $table = 'sertifikat';

    protected $primaryKey = 'id';

    // kalau tabel pakai created_at / updated_at (iya, ada)
    public $timestamps = true;

    /**
     * Kolom yang boleh diisi mass-assignment
     */
    protected $fillable = [
        'tsid',
        'is_generate',
        'is_upload',
        'diklat_jadwal_id',
        'fasilitasi',
        'barcode',
        'kualifikasi',
        'import',
        'format_nomor',
        'tempat',
        'tanggal',
        'jabatan',
        'nama',
        'pangkat',
        'nip',
        'spesimen',
        'jabatan2',
        'nama2',
        'pangkat2',
        'nip2',
        'spesimen2',
        'is_final',
    ];

    /**
     * Casting tipe data supaya enak dipakai di PHP
     */
    protected $casts = [
        'tsid' => 'integer',
        'diklat_jadwal_id' => 'integer',

        'is_generate' => 'boolean',
        'is_upload' => 'boolean',
        'barcode' => 'boolean',
        'kualifikasi' => 'boolean',
        'import' => 'boolean',
        'is_final' => 'boolean',

        'tanggal' => 'date:Y-m-d',
    ];

    public function simasn()
    {
        return $this->hasOne(SertifikatSimasn::class, 'sertifikat_id', 'id');
    }

    /**
     * SCOPES (opsional, tapi sering kepakai)
     */
    public function scopeFinal($query)
    {
        return $query->where('is_final', 1);
    }

    public function scopeUpload($query)
    {
        return $query->where('is_upload', 1);
    }

    public function scopeGenerate($query)
    {
        return $query->where('is_generate', 1);
    }
}
