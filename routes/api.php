<?php

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/jadwal', function (Request $request) {
    $jadwal = DB::table('v_front_beranda')->where('status', 2)->take(10)->get();

    return response()->json($jadwal, 200);
});

Route::get('/jadwal/dinov', function (Request $request) {

    $validator = Validator::make($request->all(), [
        'tahun' => 'required'
    ]);

    if($validator->fails()) 
    {
        $data = ['success' => false, 'message' => 'Bad Request'];
        return response()->json($data, 400);        
    }    
    
    $jadwal = DB::table('v_dinov')->where('tahun', $request->tahun)->get();
    return response()->json($jadwal, 200);
});

Route::get('/peserta/dinov', function (Request $request) {

    $validator = Validator::make($request->all(), [
        'jadwal' => 'required',
        'nip' => 'required'
    ]);

    if($validator->fails()) 
    {
        $data = ['success' => false, 'message' => 'Bad Request'];
        return response()->json($data, 400);        
    }    
    
    //$data = DB::table('v_peserta')->where(['nip' => $request->nip, 'diklat_jadwal_id' => $request->jadwal])->get();
    $data = [];
    $data['peserta'] = DB::table('v_peserta')->where(['nip' => $request->nip, 'diklat_jadwal_id' => $request->jadwal])->first();
    $data['seminar'] = DB::table('v_coachpenguji')->where(['peid' => $data['peserta']->id, 'jid' => $request->jadwal])->first();

    return response()->json($data, 200);
});

Route::get('/wi/jpbulan', function (Request $request) {
    // $jp = DB::table('v_jpbulan_wi')->select('nama', 'jpbulan', 'jpbulan_luar')->get();
    $param1 = $request->bulan;
    $param2 = $request->tahun;
    $jp = DB::select('call sp_jpbulan_wi(?,?)', array($param1, $param2));
    return response()->json($jp, 200);
});