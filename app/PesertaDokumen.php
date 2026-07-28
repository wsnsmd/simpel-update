<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PesertaDokumen extends Model
{
    protected $table = 'peserta_dokumen';

    protected $fillable = [
        'peserta_id',
        'dokumen_syarat_id',
        'file_path',
        'file_original_name',
        'uploaded_at',
        'verified_by_admin',
        'verified_by',
        'verified_at',
        'catatan_admin',
    ];

    protected $casts = [
        'verified_by_admin' => 'boolean',
        'uploaded_at'       => 'datetime',
        'verified_at'       => 'datetime',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }

    public function dokumenSyarat()
    {
        return $this->belongsTo(JadwalDokumenSyarat::class, 'dokumen_syarat_id');
    }
}
