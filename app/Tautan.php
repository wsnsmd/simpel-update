<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tautan extends Model
{
    protected $table = 'tautan';
    protected $fillable = ['jadwal_id', 'title', 'url', 'is_active'];

    protected $hidden = ['created_at', 'updated_at'];
}
