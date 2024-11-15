<?php

namespace App\Http\Controllers\Diklat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App;
use Gate;
use Session;

class SurtuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:isUser');
        $this->middleware(function ($request, $next) {
            $this->tahun = Session::get('apps_tahun');

           return $next($request);
        });

        $this->user = Auth::user();
    }

    public function index(Request $request)
    {
        $jadwal = DB::table('v_jadwal_detail')->where('id', $request->jadwal_id)->first();
        return view('backend.diklat.surtu.index', compact('jadwal'));
    }

    public function loadSurtu(Request $request)
    {
        $jadwal = DB::table('diklat_jadwal')->where('id', $request->jadwal_id)->first();
        $surtu = DB::table('surtu')->where('jid', $request->jadwal_id)->get();
        return view('backend.diklat.surtu.table', compact('surtu', 'jadwal'));
    }

    public function loadPegawai(Request $request)
    {
        $pegawai = DB::table('surtu_detail')->where('sid', $request->sid)->get();
        return view('backend.diklat.surtu.detail_pegawai', compact('pegawai'));
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'jadwal_id' => 'required',
            'keterangan' => 'required',
            'tipe' => 'required',
        ]);

        $input = $request->all();

        DB::beginTransaction();

        try
        {
            $created_at = date('Y-m-d H:i:s');

            $nosurat = DB::table('nosurat')->where('modul', 'surtu')->where('tahun', $this->tahun)->first();
            if(is_null($nosurat))
            {
                $nomor = 1;
                DB::table('nosurat')->insert([
                    'modul' => 'surtu',
                    'nomor' => $nomor,
                    'tahun' => $this->tahun,
                ]);
            }
            else
            {
                $nomor = $nosurat->nomor + 1;
                DB::table('nosurat')->where('modul', 'surtu')->where('tahun', $this->tahun)->update([
                    'modul' => 'surtu',
                    'nomor' => $nomor,
                    'tahun' => $this->tahun,
                ]);
            }
            $group = DB::table('ugroup')->where('kode', $this->user->usergroup)->first();
            $no_format = sprintf("%05s", $nomor);
            $nosurtu = '800.1.11.1/' . $no_format . '/BPSDM-' . $group->romawi;

            DB::table('surtu')->insert([
                'jid' => $input['jadwal_id'],
                'tipe' => $input['tipe'],
                'keterangan' => $input['keterangan'],
                'nomor'=> $nosurtu,
                'tahun' => $this->tahun,
                'created_at' => $created_at
            ]);
        }
        catch (\Exception $e)
        {
            DB::rollback();
            throw $e;
        }

        DB::commit();

        return response()->json(['status' => 'success', 'pesan' => 'Data surat tugas berhasil disimpan!'], 200);
    }

    public function detail($jadwal, $slug, $id)
    {
        $jadwal = DB::table('diklat_jadwal')->where('id', $jadwal)->first();
        $surtu = DB::table('surtu')->where('id', $id)->first();
        $pegawai = DB::table('surtu_detail')->where('sid', $id)->get();
        $detail = 'Surat Tugas';

        return view('backend.diklat.surtu.detail', compact('jadwal', 'surtu', 'pegawai', 'detail'));
    }

    public function update(Request $request, $id)
    {
        $validator = $request->validate([
            'tipe' => 'required',
            'keterangan' => 'required',
            // 'dasar' => 'required',
            // 'untuk' => 'required',
            // 'an' => 'required',
            // 'tempat' => 'required',
            // 'tanggal' => 'required',
            // 'jabatan' => 'required',
            // 'nip' => 'required',
            // 'nama' => 'required',
            // 'pangkat' => 'required',
        ]);

        try
        {
            $updated_at = date('Y-m-d H:i:s');
            DB::table('surtu')->where('id', $id)->update([
                'tipe' => $request->tipe,
                'keterangan' => $request->keterangan,
                'dasar' => $request->dasar,
                'untuk' => $request->untuk,
                'an' => $request->an,
                'tempat' => $request->tempat,
                'tanggal' => $request->tanggal,
                'jabatan' => $request->jabatan,
                'nip' => $request->nip,
                'nama' => $request->nama,
                'pangkat' => $request->pangkat,
                'paraf1_nama' => $request->paraf1_nama,
                'paraf1_jabatan' => $request->paraf1_jabatan,
                'paraf2_nama' => $request->paraf2_nama,
                'paraf2_jabatan' => $request->paraf2_jabatan,
                'updated_at' => $updated_at,
            ]);

            $notifikasi = 'Data surat tugas berhasil disimpan!';

            return redirect()->back()->with('success', $notifikasi);
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data surat tugas gagal disimpan!';
            // return redirect()->back()->with('error', $e->getMessage());
            return redirect()->back()->with('error', $notifikasi);
        }
    }

    public function addPegawai(Request $request)
    {
        $pegawai = DB::table('v_fasilitator')->find($request->pegawai);

        DB::beginTransaction();

        try
        {
            $created_at = date('Y-m-d H:i:s');
            $pangkat = '';

            if(is_null($pegawai->pangkat_id) || $pegawai->pangkat_id == 0)
                $pangkat = '-';
            else
            {
                $data = DB::table('pangkat')->find($pegawai->pangkat_id);
                $pangkat = $data->singkat;
            }

            if(is_null($pegawai->nip))
                $pegawai->nip = '-';

            DB::table('surtu_detail')->insert([
                'sid' => $request->sid,
                'nip' => $pegawai->nip,
                'nama' => $pegawai->nama,
                'pangkat' => $pangkat,
                'jabatan' => $pegawai->jabatan,
                'keterangan' => $request->keterangan,
                'created_at' => $created_at
            ]);
        }
        catch (\Exception $e)
        {
            DB::rollback();
            throw $e;
        }

        DB::commit();

        return response()->json(['status' => 'success', 'pesan' => 'Data pegawai berhasil disimpan!'], 200);
    }

    public function delPegawai($id)
    {
        $delete = DB::table('surtu_detail')->where('id', $id)->delete();
        $data = [];

        if($delete)
        {
            $data['status'] = 'success';
            $data['pesan'] = 'Data pegawai berhasil dihapus';
            return response()->json($data, 200);
        }

        $data['status'] = 'error';
        $data['pesan'] = 'Data pegawai gagal dihapus';
        return response()->json($data, 200);
    }

    public function cetak(Request $request, $id)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(30);

        $surtu = DB::table('surtu')->where('id', $id)->first();
        $pegawai = DB::table('surtu_detail')->where('sid', $id)->get();
        $template = $request->template;
        $bawah = $request->bawah;
        $view = 'report.dom.surtu-individu';

        $papersize = 'folio';
        $paperorientation = 'potrait';
        //$filename = 'Surat Tugas - ' . $fasilitator->nama . '-' . time();
        $filename = 'Surat Tugas - ' . time();
        if($surtu->tipe === 'panitia')
            $view = 'report.dom.surtu-panitia';

        // return view($view, compact('surtu', 'pegawai', 'template', 'bawah'))->render();
        $view = view($view, compact('surtu', 'pegawai', 'template', 'bawah'))->render();
        $pdf = App::make('dompdf.wrapper');
        $pdf->setOptions(['dpi' => '120', 'isRemoteEnabled' => true, 'chroot' => realpath(base_path()), 'enable_html5_parser' => true, ]);
        $pdf->loadHTML($view);
        $pdf->setPaper($papersize, $paperorientation);

        return $pdf->stream($filename.'.pdf');
    }

}
