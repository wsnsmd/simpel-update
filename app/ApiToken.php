<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApiToken extends Model

{
    protected $table = 'api_tokens';
    protected $fillable = ['app_name', 'token', 'type', 'expires_at'];
}
