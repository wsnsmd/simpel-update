<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use App\RandomColor;
use App\ApiToken;

class AjaxController extends Controller
{
    public function cariPegawai(Request $request)
    {
        $id = $request->nip;
        $client = new Client(['http_errors' => false, 'verify' => false]);

        try {
            // $req_pegawai = $client->get(env('SIMPEG_PNS') . $id . '/?api_token=' . env('SIMPEG_KEY'));
            $tokenData = ApiToken::where('app_name', '=', 'SIMASN')->first();
            $headers = [
                'Authorization' => 'Bearer ' . $tokenData->token,
                'Accept' => 'application/json'
            ];
            $url_pegawai = env('SIMASN_PEGAWAI') . $id;
            $req_pegawai = $client->get($url_pegawai, [
                'headers' => $headers
            ]);

            if ($req_pegawai->getStatusCode() == 200) {
                $res_pegawai = $req_pegawai->getBody();
                $pegawai = json_decode($res_pegawai, true);
                if (!$pegawai['success'])
                    return redirect()->back()->with('error', $pegawai['keterangan']);

                // $req_satker = $client->get(env('SIMPEG_SATKER') . '/?id_skpd=' . $pegawai['id_skpd'] . '&api_token=' . env('SIMPEG_KEY'));
                $url_opd = env('SIMASN_LISTOPD');
                $req_satker = $client->get($url_opd, [
                    'headers' => $headers
                ]);

                if ($req_satker->getStatusCode() == 200) {
                    $res_satker = $req_satker->getBody();
                    $satker = json_decode($res_satker, true);
                    $pegawai = $pegawai['data'];
                    $data_opd = $satker['data'];
                    $col_opd = collect($data_opd);
                    $opd = $col_opd->firstWhere('id', $pegawai['opd_id']);

                    $instansi = DB::table('instansi')->where('id', 1)->first();

                    $nama_lengkap = $pegawai['nama_non_gelar'];
                    $tmp_nama = explode(' ', $nama_lengkap);
                    $singkat = '';

                    foreach ($tmp_nama as $i => $key) {
                        if ($i > 0)
                            $singkat = $singkat . substr($key, 0, 1);
                    }

                    $nama = $tmp_nama[0] . ' ' . $singkat;
                    $statusMapping = [
                        'pns' => 1,
                        'pppk' => 2,
                    ];

                    $arr_pegawai = array(
                        'status_asn' => $statusMapping[$pegawai['jenis_asn']] ?? null,
                        'nip' => $pegawai['nip'],
                        'nik' => $pegawai['nik'],
                        'nama_lengkap' => $nama_lengkap,
                        'nama' => $nama,
                        'telp' => $pegawai['hp'],
                        'email' => $pegawai['email'],
                        'tmp_lahir' => $pegawai['tempat_lahir'],
                        'tgl_lahir' => $pegawai['tgl_lahir'],
                        'jk' => simpegJK($pegawai['jk']),
                        'agama' => $pegawai['agama'],
                        // 'marital' => $pegawai['id_status_nikah'],
                        'alamat' => $pegawai['alamat'],
                        'jabatan' => $pegawai['jabatan'],
                        'pangkat' => konversiGolongan($pegawai['golongan_id'], $pegawai['jenis_asn']),
                        'instansi' => $instansi->nama,
                        'satker_nama' => $opd['opd'],
                        'satker_telp' => $opd['telp'],
                        'satker_alamat' => $opd['alamat'],
                    );

                    foreach ($arr_pegawai as $key => $value) {
                        if (empty($value) && $key != 'email')
                            $arr_pegawai[$key] = '(kosong)';

                        if (empty($value) && $key === 'email')
                            $arr_pegawai[$key] = 'kosong@kosong';
                    }

                    return response()->json($arr_pegawai, 200);
                }
            }
        } catch (\Exception $ex) {
            $returnData = array(
                'status' => 'error',
                'message' => 'Terjadi kesalahan, mohon cek kembali NIP Pegawai!'
            );

            return response()->json($returnData, 404);
        }
    }

    public function cariFasilitator(Request $request)
    {
        $fator = DB::table('fasilitator')->select('nama')->where('nama', 'like', '%' . $request->search . '%')->get();
        return response()->json($fator, 200);
    }

    public function widyaiswara(Request $request)
    {
        $search = $request->search;

        $data = DB::table('fasilitator')
            ->select('id', 'nama as text')
            ->where('nama', 'like', '%' . $search . '%')
            ->orderby('nama', 'asc')
            ->get();

        return response()->json($data, 200);
    }

    public function pegawai(Request $request)
    {
        $search = $request->search;

        $data = DB::table('fasilitator')
            ->select('id', 'nama as text')
            ->where('nama', 'like', '%' . $search . '%')
            ->orderby('nama', 'asc')
            ->get();

        return response()->json($data, 200);
    }

    public function cariMapel(Request $request)
    {
        $mapel = DB::table('mapel')->select('nama')
            ->where('kurikulum_id', $request->kurikulum_id)
            ->where('nama', 'like', '%' . $request->search . '%')
            ->get();
        return response()->json($mapel, 200);
    }

    public function kalendar(Request $request)
    {
        try {
            $kalendar = DB::table('v_kalendar')
                ->whereRaw("
                            (start BETWEEN '" . $request->start . "' and '" . $request->end . "') or
                            (end BETWEEN '" . $request->start . "' and '" . $request->end . "') or
                            ('" . $request->start . "' BETWEEN date(start) and date(end)) or
                            ('" . $request->end . "' between date(start) and date(end))
                        ")
                ->select('title', 'start', 'end')
                ->get();
            if (count($kalendar)) {
                $jum_color = count($kalendar);
                $color = (RandomColor::many($jum_color, array('luminosity' => 'light')));
                $data = [];

                foreach ($kalendar as $index => $value) {
                    $data[] = [
                        'title' => $value->title,
                        'start' => $value->start,
                        'end' => $value->end,
                        'color' => $color[$index],
                    ];
                }

                // dd($data);
                return response()->json($data, 200);
            }
            // dd(RandomColor::many(27, array('luminosity'=>'light')));
            return response()->json($kalendar, 200);
        } catch (\Exception $e) {
            return response()->json(null, 200);
        }
    }

    public function setTahun(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer'
        ]);
        $request->session()->put('apps_tahun', $request->tahun);
        return response()->json(['message' => 'Tahun berhasil disimpan', 'tahun' => $request->tahun]);
    }
}
