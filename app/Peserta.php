<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;

class Peserta extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $table = 'peserta';

    /**
     * Tabel peserta tidak punya kolom password/remember_token,
     * jadi kita non-aktifkan mekanisme itu (login murni via SSO).
     */
    public function getAuthPassword()
    {
        return null;
    }

    public function getRememberToken()
    {
        return null;
    }

    public function setRememberToken($value)
    {
        // no-op, tidak ada kolom remember_token di tabel peserta
    }

    public function getRememberTokenName()
    {
        // string kosong = Laravel tidak akan mencoba set cookie remember-me
        return '';
    }

    /**
     * Query semua riwayat pendaftaran diklat milik peserta ini (berdasarkan NIP sama)
     */
    public function scopeRiwayatByNip($query, $nip)
    {
        return $query->where('nip', $nip);
    }
}
