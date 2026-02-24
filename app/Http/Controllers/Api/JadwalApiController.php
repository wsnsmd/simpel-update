<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Peserta;

class JadwalApiController extends Controller
{
    public function index(Request $request)
    {
        $jadwal = DB::table('v_front_beranda')->where('status', 2)->take(10)->get();
        return response()->json($jadwal, 200);
    }

    public function dinov(Request $request)
    {
        $validator = Validator::make($request->all(), ['tahun' => 'required']);
        if ($validator->fails())
            return response()->json(['success' => false, 'message' => 'Bad Request'], 400);

        $jadwal = DB::table('v_dinov')->where('tahun', $request->tahun)->get();
        return response()->json($jadwal, 200);
    }

    public function pesertaDinov(Request $request)
    {
        $validator = Validator::make($request->all(), ['jadwal' => 'required', 'nip' => 'required']);
        if ($validator->fails())
            return response()->json(['success' => false, 'message' => 'Bad Request'], 400);

        $data = [];
        $data['peserta'] = DB::table('v_peserta')->where(['nip' => $request->nip, 'diklat_jadwal_id' => $request->jadwal])->first();
        $data['seminar'] = DB::table('v_coachpenguji')->where(['peid' => $data['peserta']->id, 'jid' => $request->jadwal])->first();
        return response()->json($data, 200);
    }

    public function jpBulan(Request $request)
    {
        $jp = DB::select('call sp_jpbulan_wi(?,?)', array($request->bulan, $request->tahun));
        return response()->json($jp, 200);
    }

    public function auth(Request $request)
    {
        $peserta = Peserta::select('nip', 'nama_lengkap', 'jk', 'hp', 'email', 'jabatan', 'instansi', 'satker_nama', 'status_asn')
            ->where(['nip' => $request->username, 'hp' => $request->password, 'konfirmasi' => true, 'batal' => false, 'sebagai' => 'Peserta'])
            ->latest()->first();

        if ($peserta)
            return response()->json(['success' => true, 'message' => 'Data peserta benar', 'data' => $peserta]);
        return response()->json(['success' => false, 'message' => 'Data peserta tidak ditemukan'], 404);
    }
}