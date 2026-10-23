<?php

use Illuminate\Support\Facades\Route;

Route::namespace('App\Http\Controllers')->group(function () {
    Route::get('/jadwal', 'Api\JadwalApiController@index');
    Route::get('/jadwal/dinov', 'Api\JadwalApiController@dinov');
    Route::get('/peserta/dinov', 'Api\JadwalApiController@pesertaDinov');
    Route::get('/wi/jpbulan', 'Api\JadwalApiController@jpBulan');

    Route::group(['prefix' => 'v1', 'middleware' => 'auth.api'], function () {
        Route::post('/survei/callback', 'Api\SurveyCallbackController@store');
        Route::post('/auth', 'Api\JadwalApiController@auth');
    });

    // Fallback tanpa prefix v1 jika app survei memanggil /api/survei/callback
    Route::group(['middleware' => 'auth.api'], function () {
        Route::post('/survei/callback', 'Api\SurveyCallbackController@store');
    });
});