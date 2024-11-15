<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use DB;

class CetakController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $jadwal = DB::table('v_jadwal_detail')->where('id', $request->jadwal_id)->first();
        $umum = DB::table('cetak')->where('group', 'umum')->orderby('nama')->get();
        return view('backend.cetak.index', compact('jadwal', 'umum'));
    }

    public function cetak(Request $request, $id)
    {
        $jadwal_id = $request->jadwal_id;
        $cetak = DB::table('cetak')->where('id', $id)->first();
        $jadwal = DB::table('v_jadwal_detail')->where('id', $jadwal_id)->first();

        $papersize = $cetak->papersize;
        $paperorientation = $cetak->paperorientation;
        $filename = $cetak->filename . '-' . time() . '.pdf';

        $data = [];
        $data['jadwal'] = $jadwal;
        $data['cetak'] = $cetak;

        switch($id)
        {
            case 1:
                $peserta = DB::table('v_peserta')
                            ->select('nip', 'nama_lengkap', 'satker_singkat', 'hp')
                            ->where('diklat_jadwal_id', $jadwal_id)
                            ->orderby('nama_lengkap')
                            ->get();
                $data['peserta'] = json_encode($peserta);
                $report = $cetak->template;
                break;
            case 2:
                $peserta = DB::table('v_peserta')
                            ->select('nip', 'nama_lengkap', 'satker_nama', 'hp', 'tmp_lahir', 'tgl_lahir', 'jabatan', 'pangkat', 'golongan', 'email', 'foto')
                            ->where('diklat_jadwal_id', $jadwal_id)
                            ->orderby('nama_lengkap')
                            ->get();
                foreach ($peserta as $key => &$value)
                {
                    if(!is_null($peserta[$key]->foto))
                    {
                        $peserta[$key]->foto = asset(\Storage::url($peserta[$key]->foto));
                    }
                    else
                    {
                        $peserta[$key]->foto = asset('media/avatars/avatar8.jpg');
                    }
                }
                $data['peserta'] = json_encode($peserta);
                $report = $cetak->template;
                break;
            case 3:
                $peserta = DB::table('v_peserta')
                            ->select('nip', 'nama_lengkap', 'satker_singkat')
                            ->where('diklat_jadwal_id', $jadwal_id)
                            ->orderby('nama_lengkap')
                            ->get();
                $data['peserta'] = json_encode($peserta);
                $data['tanggal'] = $request->cetak_tgl;
                $report = $cetak->template;
                break;
            case 4:
                $peserta = DB::table('v_peserta')
                            ->select('nip', 'nama_lengkap', 'satker_singkat')
                            ->where('diklat_jadwal_id', $jadwal_id)
                            ->orderby('nama_lengkap')
                            ->get();
                $data['peserta'] = json_encode($peserta);
                $data['tanggal'] = $request->cetak_tgl;
                $data['mapel'] = $request->cetak_mapel;
                $data['fator'] = $request->cetak_fator;
                $report = $cetak->template;
                break;
        }

        return view($report, $data);
    }

    public function modal($id)
    {
        return view('backend.cetak.modal.' . $id);
    }
}
