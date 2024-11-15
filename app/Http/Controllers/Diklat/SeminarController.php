<?php

namespace App\Http\Controllers\Diklat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Validator;

class SeminarController extends Controller
{
    public $const_jam = array(
        '08.00 - 08.30',
        '08.35 - 09.05',
        '09.10 - 09.40',
        '09.45 - 10.15',
        '10.20 - 10.50',
        '10.55 - 11.25',
        '11.30 - 12.00',
        '13.00 - 13.30',
        '13.35 - 14.05',
        '14.10 - 14.40',
    );

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:isPKMF');

        $this->user = Auth::user();
    }

    public function index()
    {
        //
    }

    public function createKelompok($jadwal, $slug)
    {
        $jadwal = DB::table('diklat_jadwal')->where('id', $jadwal)->first();
        $detail = 'Seminar';
        $edit = false;

        return view('backend.diklat.seminar.create', compact('jadwal', 'detail', 'edit'));
    }

    public function storeKelompok(Request $request, $jadwal)
    {
        $validator = $request->validate([            
            'kelompok' => 'required',
            'cid' => 'required',
            'pid' => 'required',
        ]);
        
        try 
        {

            DB::table('seminar')->insert([
                'jid' => $jadwal,
                'cid' => $request->cid,
                'pid' => $request->pid,
                'kelompok' => $request->kelompok,
            ]);

            $notifikasi = 'Data kelompok berhasil ditambahkan!';

            if(isset($request->add))
            {
                $jadwal = DB::table('v_jadwal_detail')->where('id', $jadwal)->first();

                return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'seminar'])
                        ->with([
                            'success' => $notifikasi,
                            'page' => 'seminar'
                        ]);
            }
            return redirect()->back()->with('success', $notifikasi); 
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data kelompok gagal ditambahkan!';
            return redirect()->back()->with('error', $notifikasi); 
        }
    }

    public function show($id)
    {
        //
    }

    public function editKelompok($jadwal, $slug, $id)
    {
        $jadwal = DB::table('diklat_jadwal')->where('id', $jadwal)->first();
        $seminar = DB::table('seminar')
                    ->join('fasilitator as f1', 'seminar.cid', 'f1.id')
                    ->join('fasilitator as f2', 'seminar.pid', 'f2.id')
                    ->select('seminar.*', 'f1.nama as coach', 'f2.nama as penguji')
                    ->where('seminar.id', $id)
                    ->first();
        $detail = 'Seminar';
        $edit = true;

        return view('backend.diklat.seminar.create', compact('jadwal', 'seminar', 'detail', 'edit'));
    }

    public function updateKelompok(Request $request, $jadwal, $id)
    {
        $validator = $request->validate([            
            'kelompok' => 'required',
            'cid' => 'required',
            'pid' => 'required',
        ]);
        
        try 
        {

            DB::table('seminar')->where('id', $id)->update([
                'cid' => $request->cid,
                'pid' => $request->pid,
                'kelompok' => $request->kelompok,
            ]);

            $notifikasi = 'Data kelompok berhasil diubah!';
            
            $jadwal = DB::table('v_jadwal_detail')->where('id', $jadwal)->first();

            return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'seminar'])
                    ->with([
                        'success' => $notifikasi,
                        'page' => 'seminar'
                    ]);
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data kelompok gagal diubah!';
            return redirect()->back()->with('error', $notifikasi); 
        }
    }

    public function destroyKelompok($id)
    {
        DB::beginTransaction();
        $delete = DB::table('seminar')->where('id', $id)->delete();
        $anggota = DB::table('seminar_anggota')->where('sid', $id)->delete();
        DB::commit();

        if($delete && $anggota)
        {
            $notifikasi = 'Data kelompok berhasil dihapus!';
            return redirect()->back()->with('success', $notifikasi); 
        }

        $notifikasi = 'Data kelompok gagal dihapus!';
        return redirect()->back()->with('error', $notifikasi); 
    }

    public function indexAnggota($seminar)
    {
        $seminar = DB::table('seminar')->where('id', $seminar)->first();
        $anggota = DB::table('seminar_anggota as sa')
                        ->join('v_peserta as p', 'sa.peid', 'p.id')
                        ->select('sa.id', 'p.nip', 'p.nama_lengkap', 'p.satker_singkat', 'p.instansi')
                        ->where('sa.sid', $seminar->id)
                        ->orderby('p.nama_lengkap')
                        ->get();
        $jadwal =  DB::table('v_jadwal_detail')->where('id', $seminar->jid)->first();

        $detail = 'Anggota Kelompok';

        return view('backend.diklat.seminar.anggota', compact('jadwal', 'seminar', 'anggota', 'detail'));
    }

    public function storeAnggota(Request $request, $seminar)
    {
        $validator = $request->validate([            
            'peserta' => 'required',
        ]);
        
        try 
        {          
            DB::table('seminar_anggota')->insert([
                'sid' => $seminar,
                'peid' => $request->peserta,
            ]);

            $notifikasi = 'Data anggota berhasil ditambahkan!';
            
            return redirect()->back()->with('success', $notifikasi); 
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data anggota gagal ditambahkan!';
            return redirect()->back()->with('error', $notifikasi); 
        }
    }

    public function editAnggota($id)
    {
        $data = DB::table('seminar_anggota as sa')
                    ->join('v_peserta as p', 'sa.peid', 'p.id')
                    ->select('sa.*', 'p.nama_lengkap as nama')
                    ->where('sa.id', $id)
                    ->first();
        return response()->json($data, 200);
    }

    public function updateAnggota(Request $request, $id)
    {
        $validator = $request->validate([            
            'peserta' => 'required',
        ]);
        
        try 
        {          
            DB::table('seminar_anggota')->where('id', $id)->update([
                'peid' => $request->peserta,
            ]);

            $notifikasi = 'Data anggota berhasil diubah!';
            
            return redirect()->back()->with('success', $notifikasi); 
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data anggota gagal diubah!';
            return redirect()->back()->with('error', $notifikasi); 
        }
    }

    public function destroyAnggota($id)
    {
        $delete = DB::table('seminar_anggota')->where('id', $id)->delete();

        if($delete)
        {
            $notifikasi = 'Data angoota berhasil dihapus!';
            return redirect()->back()->with('success', $notifikasi); 
        }

        $notifikasi = 'Data anggota gagal dihapus!';
        return redirect()->back()->with('error', $notifikasi);
    }

    public function printForm(Request $request, $id)
    {
        $cetak = DB::table('seminar_form')->where('id', $request->form)->first();
        $seminar = DB::table('seminar')
                        ->join('fasilitator as f1', 'seminar.cid', 'f1.id')
                        ->join('fasilitator as f2', 'seminar.pid', 'f2.id')
                        ->select('seminar.*', 'f1.nama as coach', 'f2.nama as penguji')
                        ->where('seminar.id', $id)
                        ->first();
        $anggota = DB::table('seminar_anggota as sa')
                        ->join('v_peserta as p', 'sa.peid', 'p.id')
                        ->select('sa.id', 'p.nip', 'p.nama_lengkap', 'p.satker_nama', 'p.satker_singkat', 'p.instansi')
                        ->where('sa.sid', $seminar->id)
                        ->orderby('p.nama_lengkap')
                        ->get();
        $jadwal =  DB::table('v_jadwal_detail')->where('id', $seminar->jid)->first();
        $tt = DB::table('mapel_tt')->where('jid', $seminar->jid)->first();

        switch(substr($seminar->kelompok, -1))
        {
            case 1:
                $ruang = 'A';
                break;
            case 2:
                $ruang = 'B';
                break;
            case 3:
                $ruang = 'C';
                break;
            case 4:
                $ruang = 'D';
                break;
            default:
                $ruang = 'A';
        }

        $i = 0;

        foreach ($anggota as $key) 
        {
            $key->pukul = $this->const_jam[$i++];
        }

        $data = [];
        $data['cetak'] = $cetak;
        $data['seminar'] = $seminar;
        $data['anggota'] = json_encode($anggota);
        $data['jadwal'] = $jadwal;
        $data['tempat'] = $request->tempat;
        $data['tanggal'] = $request->tanggal;
        $data['tt'] = $tt;
        $data['ruang'] = $ruang;
        return view('report.pkmf.pkp-nilai', $data);
    }

    public function ajaxPeserta(Request $request, $jadwal)
    {
        $search = $request->search;

        $data = DB::table('peserta')
                    ->select('id', 'nama_lengkap as text')
                    ->where('diklat_jadwal_id', $jadwal)
                    ->where('verifikasi', true)
                    ->where('batal', false)
                    ->where('nama_lengkap', 'like', '%'.$search.'%')
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                                ->from('seminar_anggota')
                                ->whereRaw('peserta.id = seminar_anggota.peid');
                    })
                    ->orderby('nama_lengkap', 'asc')
                    ->get();
        
        return response()->json($data, 200);
    }
}
