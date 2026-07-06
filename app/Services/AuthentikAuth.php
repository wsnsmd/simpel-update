<?php
namespace App\Services;
use League\OAuth2\Client\Provider\GenericProvider;
class AuthentikAuth
{
    public static function makeProvider()
    {
        $baseUrl = rtrim(config('services.authentik.base_url'), '/');
        return new GenericProvider([
            'clientId' => config('services.authentik.client_id'),
            'clientSecret' => config('services.authentik.client_secret'),
            'redirectUri' => config('services.authentik.redirect'),
            'urlAuthorize' => $baseUrl . '/application/o/authorize/',
            'urlAccessToken' => $baseUrl . '/application/o/token/',
            'urlResourceOwnerDetails' => $baseUrl . '/application/o/userinfo/',
        ]);
    }
}