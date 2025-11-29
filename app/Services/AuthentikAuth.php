<?php

namespace App\Services;

use League\OAuth2\Client\Provider\GenericProvider;

class AuthentikAuth
{
    public static function makeProvider()
    {
        $baseUrl = rtrim(env('AUTHENTIK_BASE_URL'), '/');

        return new GenericProvider([
            'clientId'                => env('AUTHENTIK_CLIENT_ID'),
            'clientSecret'            => env('AUTHENTIK_CLIENT_SECRET'),
            'redirectUri'             => env('AUTHENTIK_REDIRECT_URI'),
            'urlAuthorize'            => $baseUrl.'/application/o/authorize/',
            'urlAccessToken'          => $baseUrl.'/application/o/token/',
            'urlResourceOwnerDetails' => $baseUrl.'/application/o/userinfo/',
        ]);
    }
}
