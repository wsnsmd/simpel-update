<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    protected $table = 'checklist';
    protected $fillable = ['diklat_jadwal_id', 'dokumen'];
    //  public $timestamps = true;
}
