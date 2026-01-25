<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JadwalSurvey extends Model
{
    protected $table = "jadwal_surveys";

    protected $fillable = [
        'jadwal_id',
        'survey_code',
        'survey_name',
        'params',
        'is_mandatory'
    ];
}
