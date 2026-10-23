<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SertifikatSimasn extends Model
{
    protected $table = 'sertifikat_simasn';

    protected $fillable = [
        'sertifikat_id',
        'jenis',
        'kategori',
        'sub_kategori',
    ];

    public function sertifikat()
    {
        return $this->belongsTo(Sertifikat::class);
    }
}
