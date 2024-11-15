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

class MapelController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($kurikulum_id, Request $request)
    {
        $this->checkAuth($kurikulum_id);

        $kurikulum = DB::table('kurikulum')->where('id', $kurikulum_id)->first();
        $jpk = 0;
        $jpe = 0;

        switch($kurikulum->jenis_belajar)
        {
            case 1:
                $validator = $request->validate([
                    'nama' => 'required',
                    'jpk' => 'required|numeric',
                ]);
                $jpk = $request->jpk;
                break;

            case 2:
                $validator = $request->validate([
                    'nama' => 'required',
                    'jpk' => 'required|numeric',
                    'jpe' => 'required|numeric',
                ]);
                $jpk = $request->jpk;
                $jpe = $request->jpe;
                break;

            case 3:
                $validator = $request->validate([
                    'nama' => 'required',
                    'jpe' => 'required|numeric',
                ]);
                $jpe = $request->jpe;
                break;

            default:
                $notifikasi = 'Terjadi kesalahan!';
                return redirect()->back()->with('error', $notifikasi);

        }

        try
        {

            DB::table('mapel')->insert([
                'kurikulum_id' => $kurikulum_id,
                'nama' => $request->nama,
                'jpk' => $jpk,
                'jpe' => $jpe,
            ]);

            $notifikasi = 'Data mata pelatihan pada kurikulum ' . $kurikulum->nama . ' berhasil ditambahkan!';

            return redirect()->back()->with('success', $notifikasi);
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data mata pelatihan pada kurikulum ' . $kurikulum->nama . ' gagal ditambahkan!';
            return redirect()->back()->with('error', $notifikasi);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $data = DB::table('mapel')->where('id', $id)->first();
        return response()->json($data, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $mapel = DB::table('mapel')->where('id', $id)->first();
        $kurikulum = DB::table('kurikulum')->where('id', $mapel->kurikulum_id)->first();

        $this->checkAuth($kurikulum->id);

        $jpe = 0;

        switch($kurikulum->jenis_belajar)
        {
            case 1:
                $validator = $request->validate([
                    'nama' => 'required',
                    'jpk' => 'required|numeric',
                ]);
                break;

            case 2:
                $validator = $request->validate([
                    'nama' => 'required',
                    'jpk' => 'required|numeric',
                    'jpe' => 'required|numeric',
                ]);
                $jpe = $request->jpe;
                break;

            default:
                $notifikasi = 'Terjadi kesalahan!';
                return redirect()->back()->with('error', $notifikasi);

        }

        try
        {

            DB::table('mapel')->where('id', $id)->update([
                'nama' => $request->nama,
                'jpk' => $request->jpk,
                'jpe' => $jpe,
            ]);

            $notifikasi = 'Data mata pelatihan pada kurikulum ' . $kurikulum->nama . ' berhasil diubah!';

            return redirect()->back()->with('success', $notifikasi);
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data mata pelatihan pada kurikulum ' . $kurikulum->nama . ' gagal diubah!';
            return redirect()->back()->with('error', $notifikasi);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $mapel = DB::table('mapel')->where('id', $id)->first();

        $this->checkAuth($mapel->kurikulum_id);

        $delete = DB::table('mapel')->where('id', $id)->delete();

        if($delete)
        {
            $notifikasi = 'Data mata pelatihan berhasil dihapus!';
            return redirect()->back()->with('success', $notifikasi);
        }

        $notifikasi = 'Data mata pelatihan gagal dihapus!';
        return redirect()->back()->with('error', $notifikasi);
    }

    public function jadwal($jadwal, $slug, $mapel)
    {
        $jadwal = DB::table('diklat_jadwal')->where('id', $jadwal)->first();
        $mapel = DB::table('mapel')->where('id', $mapel)->first();
        $mapel_jadwal = DB::table('mapel_jadwal')->where('jid', $jadwal->id)->where('mid', $mapel->id)->orderBy('tanggal')->get();
        $wi = DB::table('mapel_fasilitator')->where('jid', $jadwal->id)->where('mid', $mapel->id)->get();

        $jp = DB::table('mapel')
                    ->select('jpk', 'jpe')
                    ->where('id', $mapel->id)
                    ->first();

        $max_jp = $jp->jpk + $jp->jpe;

        $detail = 'Mata Pelatihan';
        return view('backend.diklat.kurikulum.jadwal', compact('jadwal', 'mapel', 'mapel_jadwal', 'wi', 'max_jp', 'detail'));
    }

    public function jadwalStore(Request $request, $jadwal, $mapel)
    {
        $validator = $request->validate([
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_akhir' => 'required',
            'jp' => 'required|numeric'
        ]);

        try
        {

            DB::table('mapel_jadwal')->insert([
                'jid' => $jadwal,
                'mid' => $mapel,
                'tanggal' => $request->tanggal,
                'jam_mulai' => $request->jam_mulai,
                'jam_akhir' => $request->jam_akhir,
                'jp' => $request->jp,
            ]);

            $notifikasi = 'Data jadwal berhasil ditambahkan!';

            return redirect()->back()->with('success', $notifikasi);
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data jadwal gagal ditambahkan!';
            return redirect()->back()->with('error', $notifikasi);
        }
    }

    public function jadwalEdit($id)
    {
        $data = DB::table('mapel_jadwal')->where('id', $id)->first();
        return response()->json($data, 200);
    }

    public function jadwalUpdate(Request $request, $id)
    {
        $validator = $request->validate([
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_akhir' => 'required',
            'jp' => 'required|numeric'
        ]);

        try
        {

            DB::table('mapel_jadwal')->where('id', $id)->update([
                'tanggal' => $request->tanggal,
                'jam_mulai' => $request->jam_mulai,
                'jam_akhir' => $request->jam_akhir,
                'jp' => $request->jp,
            ]);

            $notifikasi = 'Data jadwal berhasil diubah!';

            return redirect()->back()->with('success', $notifikasi);
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data jadwal gagal diubah!';
            return redirect()->back()->with('error', $notifikasi);
        }
    }

    public function jadwalDestroy($jadwal, $mapel, $id)
    {
        DB::beginTransaction();
        $delete1 = DB::table('mapel_fasilitator')->where('jid', $jadwal)->where('mid', $mapel)->delete();
        $delete2 = DB::table('mapel_jadwal')->where('id', $id)->delete();

        if($delete1 and $delete2)
        {
            DB::commit();
            $notifikasi = 'Data jadwal berhasil dihapus!';
            return redirect()->back()->with('success', $notifikasi);
        }

        DB::rollBack();

        $notifikasi = 'Data jadwal gagal dihapus!';
        return redirect()->back()->with('error', $notifikasi);
    }

    public function wiStore(Request $request, $jadwal, $mapel)
    {
        $validator = $request->validate([
            'fasilitator' => 'required',
            'butir' => 'required|numeric'
        ]);

        try
        {
            $fas = DB::table('v_fasilitator')->find($request->fasilitator);
            $get_ajar = DB::table('mapel_jadwal')->where('jid', $jadwal)->where('mid', $mapel)->get();
            foreach($get_ajar as $ajar)
            {
                $query = "select * from v_spwi_query where fid=$request->fasilitator and tanggal='$ajar->tanggal' and (('$ajar->jam_mulai' between jam_mulai and jam_akhir) or ('$ajar->jam_akhir' between jam_mulai and jam_akhir))";
                $cek = DB::select(
                            DB::raw(
                                $query
                            )
                        );
                if(!empty($cek))
                {
                    $notifikasi = $fas->nama . ' tidak bisa ditambahkan karena memiliki jadwal yang sama pada tanggal dan jam tersebut!';
                    return redirect()->back()->with('error', $notifikasi);
                }
            }

            DB::beginTransaction();

            $nosurat = DB::table('nosurat')->where('modul', 'mapel_fasilitator')->where('tahun', $this->tahun)->first();
            if(is_null($nosurat))
            {
                $nomor = 1;
                DB::table('nosurat')->insert([
                    'modul' => 'mapel_fasilitator',
                    'nomor' => $nomor,
                    'tahun' => $this->tahun,
                ]);
            }
            else
            {
                $nomor = $nosurat->nomor + 1;
                DB::table('nosurat')->where('modul', 'mapel_fasilitator')->where('tahun', $this->tahun)->update([
                    'modul' => 'mapel_fasilitator',
                    'nomor' => $nomor,
                    'tahun' => $this->tahun,
                ]);
            }

            if(is_null($fas->pangkat_id) || $fas->pangkat_id == 0)
            {
                DB::table('mapel_fasilitator')->insert([
                    'jid' => $jadwal,
                    'mid' => $mapel,
                    'fid' => $request->fasilitator,
                    'nip' => $fas->nip,
                    'nama' => $fas->nama,
                    // Update Pangkat
                    'pangkat' => '-',
                    'jabatan' => $fas->jabatan,
                    'instansi' => $fas->satker_singkat . ' ' . $fas->instansi,
                    'butir' => $request->butir,
                    'nomor' => $nomor,
                    'tahun' => $this->tahun,
                ]);
            }
            else
            {
                $pangkat = DB::table('pangkat')->find($fas->pangkat_id);
                DB::table('mapel_fasilitator')->insert([
                    'jid' => $jadwal,
                    'mid' => $mapel,
                    'fid' => $request->fasilitator,
                    'nip' => $fas->nip,
                    'nama' => $fas->nama,
                    'pangkat' => $pangkat->singkat,
                    'jabatan' => $fas->jabatan,
                    'instansi' => $fas->satker_singkat . ' ' . $fas->instansi,
                    'butir' => $request->butir,
                    'nomor' => $nomor,
                    'tahun' => $this->tahun,
                ]);
            }

            DB::commit();

            $notifikasi = 'Data Widyaiswara berhasil ditambahkan!';

            return redirect()->back()->with('success', $notifikasi);
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data Widyaiswara gagal ditambahkan!';
            return redirect()->back()->with('error', $notifikasi);
        }
    }

    public function wiEdit($id)
    {
        $data = DB::table('mapel_fasilitator')->where('id', $id)->first();
        return response()->json($data, 200);
    }

    public function wiUpdate(Request $request, $id)
    {
        $validator = $request->validate([
            'fasilitator' => 'required',
            'butir' => 'required|numeric'
        ]);

        try
        {
            $mapel = DB::table('mapel_fasilitator')->where('id', $id)->first();
            $fas = DB::table('v_fasilitator')->find($request->fasilitator);
            $get_ajar = DB::table('mapel_jadwal')->where('jid', $mapel->jid)->where('mid', $mapel->mid)->get();
            foreach($get_ajar as $ajar)
            {
                $query = "select * from v_spwi_query where fid=$request->fasilitator and tanggal='$ajar->tanggal' and (('$ajar->jam_mulai' between jam_mulai and jam_akhir) or ('$ajar->jam_akhir' between jam_mulai and jam_akhir))";
                $cek = DB::select(
                            DB::raw(
                                $query
                            )
                        );
                if(!empty($cek))
                {
                    $notifikasi = $fas->nama . ' tidak bisa diubah karena memiliki jadwal yang sama pada tanggal dan jam tersebut!';
                    return redirect()->back()->with('error', $notifikasi);
                }
            }

            if(is_null($fas->pangkat_id) || $fas->pangkat_id == 0)
            {
                DB::table('mapel_fasilitator')->where('id', $id)->update([
                    'fid' => $request->fasilitator,
                    'nip' => $fas->nip,
                    'nama' => $fas->nama,
                    'pangkat' => '-',
                    'jabatan' => $fas->jabatan,
                    'instansi' => $fas->satker_singkat . ' ' . $fas->instansi,
                    'butir' => $request->butir,
                ]);
            }
            else
            {
                $pangkat = DB::table('pangkat')->find($fas->pangkat_id);
                DB::table('mapel_fasilitator')->where('id', $id)->update([
                    'fid' => $request->fasilitator,
                    'nip' => $fas->nip,
                    'nama' => $fas->nama,
                    'pangkat' => $pangkat->singkat,
                    'jabatan' => $fas->jabatan,
                    'instansi' => $fas->satker_singkat . ' ' . $fas->instansi,
                    'butir' => $request->butir,
                ]);
            }

            $notifikasi = 'Data Widyaiswara berhasil diubah! ' . $fas->pangkat_id;

            return redirect()->back()->with('success', $notifikasi);
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data Widyaiswara gagal diubah!';
            return redirect()->back()->with('error', $notifikasi);
        }

    }

    public function wiDestroy($jadwal, $mapel, $id)
    {
        $delete = DB::table('mapel_fasilitator')->where('id', $id)->delete();

        if($delete)
        {
            $notifikasi = 'Data Widyaiswara berhasil dihapus!';
            return redirect()->back()->with('success', $notifikasi);
        }

        $notifikasi = 'Data Widyaiswara gagal dihapus!';
        return redirect()->back()->with('error', $notifikasi);
    }

    public function wiCetak($id)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(30);

        $fasilitator = DB::table('mapel_fasilitator')->where('id', $id)->first();
        $jadwal = DB::table('mapel_jadwal as mj')
                    ->join('mapel', 'mj.mid', 'mapel.id')
                    ->select('mapel.nama', 'mj.tanggal', 'mj.jam_mulai', 'mj.jam_akhir', 'mj.jp')
                    ->where('jid', $fasilitator->jid)
                    ->where('mid', $fasilitator->mid)
                    ->get();
        $lokasi = DB::table('diklat_jadwal')
                ->join('lokasi', 'diklat_jadwal.lokasi_id', 'lokasi.id')
                ->select('diklat_jadwal.nama', 'diklat_jadwal.tgl_awal', 'lokasi.nama as lokasi')
                ->where('diklat_jadwal.id', $fasilitator->jid)
                ->first();
        $tandatangan = DB::table('mapel_tt')->where('jid', $fasilitator->jid)->first();
        $group = DB::table('ugroup')->where('kode', $this->user->usergroup)->first();
        $no_format = sprintf("%05s", $fasilitator->nomor);
        $nomor = '800.1.11.1 / ' . $no_format . ' / BPSDM-' . $group->romawi;

        if(is_null($tandatangan))
        {
            abort(404);
        }

        $papersize = 'folio';
        $paperorientation = 'potrait';
        //$filename = 'Surat Tugas - ' . $fasilitator->nama . '-' . time();
        $filename = 'Surat Tugas - ' . $fasilitator->nama . '-' . $fasilitator->tahun . '-' . $fasilitator->nomor;

        $view = view('report.dom.spwi', compact('fasilitator', 'jadwal', 'lokasi', 'tandatangan', 'nomor'))->render();
        $pdf = App::make('dompdf.wrapper');
        $pdf->setOptions(['dpi' => '120', 'isRemoteEnabled' => true, 'chroot' => realpath(base_path()), 'enable_html5_parser' => true, ]);
        $pdf->loadHTML($view);
        $pdf->setPaper($papersize, $paperorientation);

        return $pdf->stream($filename.'.pdf');
    }

    public function wiRekap($id)
    {
        $surat = DB::table('v_spwi_report2')->where('jid', $id)->orderby('nomor')->get();
        $jadwal = DB::table('v_jadwal_detail')->where('id', $id)->first();
        $data = [];
        $data['surat'] = json_encode($surat);
        $data['jadwal'] = $jadwal;
        // return response()->json($data, 200);
        return view('report.pkmf.spwi', $data);
    }

    public function TandaTanganEdit($jadwal)
    {
        $data = DB::table('mapel_tt')->where('jid', $jadwal)->first();
        return response()->json($data, 200);
    }

    public function TandaTanganUpdate(Request $request, $jadwal)
    {
        $validator = $request->validate([
            'tempat' => 'required',
            'tanggal' => 'required|date',
            'an' => 'required',
            'jabatan' => 'required',
            'nip' => 'required|min:18|max:18',
            'nama' => 'required',
            'pangkat' => 'required',
        ]);

        $jadwal = DB::table('v_jadwal_detail')->where('id', $jadwal)->first();

        try
        {
            DB::table('mapel_tt')->updateOrInsert(['jid' => $jadwal->id], [
                'jid' => $jadwal->id,
                'tempat' => $request->tempat,
                'tanggal' => $request->tanggal,
                'nama' => $request->nama,
                'jabatan' => $request->jabatan,
                'nip' => $request->nip,
                'pangkat' => $request->pangkat,
                'an' => $request->an,
                'paraf1_nama' => $request->paraf1_nama,
                'paraf1_jabatan' => $request->paraf1_jabatan,
                'paraf2_nama' => $request->paraf2_nama,
                'paraf2_jabatan' => $request->paraf2_jabatan,
            ]);

            $notifikasi = 'Data tanda tangan berhasil disimpan!';

            return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'mata-pelatihan'])
                        ->with([
                            'success' => $notifikasi,
                        ]);
        }
        catch(\Exception $e)
        {
            dd($e);
            $notifikasi = 'Data tanda tangan gagal disimpan!';
            return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'mata-pelatihan'])
                        ->with([
                            'error' => $notifikasi,
                        ]);
        }
    }

    public function checkAuth($id)
    {
        if($this->isAdmin())
            return true;

        $data = DB::table('kurikulum')->where('id', $id)
                    ->first();

        if(empty($data))
        {
            abort(404);
        }

        if(Gate::allows('isCreator', $data) && Auth::user()->instansi_id == 1)
        {
            return true;
        }

        abort(403);
    }
}
