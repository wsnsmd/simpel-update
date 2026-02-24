<?php

use Illuminate\Http\Request;

Route::get('/jadwal', 'Api\JadwalApiController@index');
Route::get('/jadwal/dinov', 'Api\JadwalApiController@dinov');
Route::get('/peserta/dinov', 'Api\JadwalApiController@pesertaDinov');
Route::get('/wi/jpbulan', 'Api\JadwalApiController@jpBulan');

Route::group(['prefix' => 'v1', 'middleware' => 'auth.api'], function () {
    // Route ini sudah benar karena memanggil Controller
    Route::post('/survei/callback', 'Api\SurveyCallbackController@store');

    // Pindahkan login auth ke controller juga
    Route::post('/auth', 'Api\JadwalApiController@auth');
});