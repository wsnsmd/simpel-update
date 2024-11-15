<?php

namespace App\Http\Controllers\Diklat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Gate;
use Session;
use App\Jadwal;

class JadwalController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->tahun = Session::get('apps_tahun');

           return $next($request);
        });
        $this->user = Auth::user();
    }

    public function index()
    {
        $level = $this->checkLevel();

        $jadwal = [];

        switch($level)
        {
            case 'admin':
                $jadwal = DB::table('v_jadwal_detail')
                            ->where('tahun', $this->tahun)
                            ->orderby('tgl_awal', 'desc')
                            ->get();
                break;

            case 'user':
                if($this->isViewer())
                {
                    $jadwal = DB::table('v_jadwal_detail')
                            ->where('tahun', $this->tahun)
                            ->orderby('tgl_awal', 'desc')
                            ->get();
                }
                else
                {
                    $jadwal = DB::table('v_jadwal_detail')
                                ->where('usergroup', $this->user->usergroup)
                                ->where('tahun', $this->tahun)
                                ->orderby('tgl_awal', 'desc')
                                ->get();
                }
                break;

            case 'kontribusi':
                $instansi = DB::table('instansi')->where('id', $this->user->instansi_id)->first();
                $jadwal = DB::table('v_jadwal_detail')
                            ->where('kelas', $instansi->nama)
                            ->where('tahun', $this->tahun)
                            ->orderby('tgl_awal', 'desc')
                            ->get();
                break;
        }

        return view('backend.diklat.jadwal.index', compact('jadwal'));
    }

    public function create()
    {
        if (Gate::denies('isUser'))
        {
            abort(403);
        }

        $jdiklat = DB::table('diklat_jenis')->orderBy('nama')->get();
        $lokasi = DB::table('lokasi')->orderBy('nama')->get();
        $kelas = DB::table('instansi')->orderBy('sort')->get();

        return view('backend.diklat.jadwal.create', compact('jdiklat', 'lokasi', 'kelas'));
    }

    public function store(Request $request)
    {
        if (Gate::denies('isUser'))
        {
            abort(403);
        }

        $validator = $request->validate([
            'nama' => 'required',
            'jenis_diklat' => 'required',
            'kurikulum' => 'required',
            'lokasi' => 'required',
            'kelas' => 'required',
            'kuota' => 'required|numeric',
            'tgl_awal' => 'required',
            'tgl_akhir' => 'required',
            'registrasi' => 'required',
            'registrasi_lengkap' => 'required',
            'panitia_nama' => 'required',
            'panitia_telp' => 'required',
            'panitia_email' => 'required',
            'status' => 'required',
        ]);

        try
        {
            $created_at = date('Y-m-d H:i:s');
            $created_by = Auth::user()->name;
            $usergroup = Auth::user()->usergroup;
            $tahun = $this->tahun;
            $path_lampiran = null;

            if(isset($request->lampiran))
            {
                $lampiran = $request->file('lampiran');
                $nama_file = time()."_".$lampiran->getClientOriginalName();
                $path_lampiran = $request->lampiran->storeAs('public/files/lampiran', $nama_file);
            }

            DB::table('diklat_jadwal')->insert([
                'diklat_jenis_id' => $request->jenis_diklat,
                'kurikulum_id' => $request->kurikulum,
                'lokasi_id' => $request->lokasi,
                'nama' => $request->nama,
                'tipe' => $request->tipe,
                'kuota' => $request->kuota,
                'tgl_awal' => $request->tgl_awal,
                'tgl_akhir' => $request->tgl_akhir,
                'tahun' => $tahun,
                'kelas' => $request->kelas,
                'registrasi' => $request->registrasi,
                'registrasi_lengkap' => $request->registrasi_lengkap,
                'reg_awal' => $request->reg_awal,
                'reg_akhir' => $request->reg_akhir,
                'panitia_nama' => $request->panitia_nama,
                'panitia_telp' => $request->panitia_telp,
                'panitia_email' => $request->panitia_email,
                'deskripsi' => $request->deskripsi,
                'syarat' => $request->syarat,
                'lampiran' => $path_lampiran,
                'status' => $request->status,
                'created_at' => $created_at,
                'created_by' => $created_by,
                'usergroup' => $usergroup,
                'var_1' => $request->var_1,
            ]);

            $notifikasi = 'Data jadwal diklat berhasil ditambahkan!';

            if(isset($request->add))
                return redirect()->route('backend.diklat.jadwal.index')->with('success', $notifikasi);

            return redirect()->back()->with('success', $notifikasi);
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data jadwal diklat gagal ditambahkan!';
            // return redirect()->back()->with('error', $e->getMessage());
            return redirect()->back()->with('error', $notifikasi);
        }
    }

    public function show($id)
    {

    }

    public function edit($id)
    {
        $this->checkAuth($id);

        $jadwal = DB::table('diklat_jadwal')->where('id', $id)
                    ->where('tahun', $this->tahun)
                    ->first();

        $jdiklat = DB::table('diklat_jenis')->orderBy('nama')->get();
        $lokasi = DB::table('lokasi')->orderBy('nama')->get();
        $kelas = DB::table('instansi')->orderBy('nama')->get();
        $kurikulum = DB::table('kurikulum')->where('id', $jadwal->kurikulum_id)->first();

        return view('backend.diklat.jadwal.edit', compact('jadwal', 'jdiklat', 'lokasi', 'kelas', 'kurikulum'));
    }

    public function update(Request $request, $id)
    {
        $this->checkAuth($id);

        $validator = $request->validate([
            'nama' => 'required',
            'jenis_diklat' => 'required',
            'kurikulum' => 'required',
            'lokasi' => 'required',
            'kelas' => 'required',
            'kuota' => 'required|numeric',
            'tgl_awal' => 'required',
            'tgl_akhir' => 'required',
            'registrasi' => 'required',
            'registrasi_lengkap' => 'required',
            'panitia_nama' => 'required',
            'panitia_telp' => 'required',
            'panitia_email' => 'required',
            'status' => 'required',
        ]);

        try
        {
            $updated_at = date('Y-m-d H:i:s');
            $updated_by = Auth::user()->name;
            $usergroup = Auth::user()->usergroup;
            $tahun = $this->tahun;
            $path_lampiran = null;

            if(isset($request->lampiran))
            {
                $lampiran = $request->file('lampiran');
                $nama_file = time()."_".$lampiran->getClientOriginalName();
                $path_lampiran = $request->lampiran->storeAs('public/files/lampiran', $nama_file);
            }
            else
            {
                $path_lampiran = $request->lampiran_lama;
            }

            DB::table('diklat_jadwal')->where('id', $id)->update([
                'diklat_jenis_id' => $request->jenis_diklat,
                'kurikulum_id' => $request->kurikulum,
                'lokasi_id' => $request->lokasi,
                'nama' => $request->nama,
                'tipe' => $request->tipe,
                'kuota' => $request->kuota,
                'tgl_awal' => $request->tgl_awal,
                'tgl_akhir' => $request->tgl_akhir,
                'tahun' => $tahun,
                'kelas' => $request->kelas,
                'registrasi' => $request->registrasi,
                'registrasi_lengkap' => $request->registrasi_lengkap,
                'reg_awal' => $request->reg_awal,
                'reg_akhir' => $request->reg_akhir,
                'panitia_nama' => $request->panitia_nama,
                'panitia_telp' => $request->panitia_telp,
                'panitia_email' => $request->panitia_email,
                'deskripsi' => $request->deskripsi,
                'syarat' => $request->syarat,
                'lampiran' => $path_lampiran,
                'status' => $request->status,
                'updated_at' => $updated_at,
                'updated_by' => $updated_by,
                'usergroup' => $usergroup,
                'var_1' => $request->var_1,
            ]);

            $notifikasi = 'Data jadwal diklat berhasil diubah!';

            return redirect()->route('backend.diklat.jadwal.index')->with('success', $notifikasi);
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data jadwal diklat gagal diubah!';
            // return redirect()->back()->with('error', $e->getMessage());
            return redirect()->back()->with('error', $notifikasi);
        }

    }

    public function destroy($id)
    {
        $this->checkAuth($id);

        $delete = DB::table('diklat_jadwal')->where('id', $id)->delete();

        if($delete)
        {
            $notifikasi = 'Data jadwal berhasil dihapus!';
            return redirect()->route('backend.diklat.jadwal.index')->with('success', $notifikasi);
        }

        $notifikasi = 'Data jadwal gagal dihapus!';
        return redirect()->route('backend.diklat.jadwal.index')->with('error', $notifikasi);
    }

    public function filterindex($id)
    {
        $level = $this->checkLevel();

        $jadwal = [];

        switch($id)
        {
            case 2:
                switch($level)
                {
                    case 'admin':
                        $jadwal = DB::table('v_jadwal_datang')
                                    ->where('tahun', $this->tahun)
                                    ->get();
                        break;

                    case 'user':
                        $jadwal = DB::table('v_jadwal_datang')
                                    ->where('tahun', $this->tahun)
                                    ->where('usergroup', $this->user->usergroup)
                                    ->get();
                        break;

                    case 'kontribusi':
                        $instansi = DB::table('instansi')->where('id', $this->user->instansi_id)->first();
                        $jadwal = DB::table('v_jadwal_datang')
                                    ->where('kelas', $instansi->nama)
                                    ->where('tahun', $this->tahun)
                                    ->get();
                        break;

                }
                break;

            case 3:
                switch($level)
                {
                    case 'admin':
                        $jadwal = DB::table('v_jadwal_berjalan')
                                    ->where('tahun', $this->tahun)
                                    ->get();
                        break;

                    case 'user':
                        $jadwal = DB::table('v_jadwal_berjalan')
                                    ->where('tahun', $this->tahun)
                                    ->where('usergroup', $this->user->usergroup)
                                    ->get();
                        break;

                    case 'kontribusi':
                        $instansi = DB::table('instansi')->where('id', $this->user->instansi_id)->first();
                        $jadwal = DB::table('v_jadwal_berjalan')
                                    ->where('kelas', $instansi->nama)
                                    ->where('tahun', $this->tahun)
                                    ->get();
                        break;
                }
                break;

            case 4:
                switch($level)
                {
                    case 'admin':
                        $jadwal = DB::table('v_jadwal_selesai')
                                    ->where('tahun', $this->tahun)
                                    ->get();
                        break;

                    case 'user':
                        $jadwal = DB::table('v_jadwal_selesai')
                                    ->where('tahun', $this->tahun)
                                    ->where('usergroup', $this->user->usergroup)
                                    ->get();
                        break;

                    case 'kontribusi':
                        $instansi = DB::table('instansi')->where('id', $this->user->instansi_id)->first();
                        $jadwal = DB::table('v_jadwal_selesai')
                                    ->where('kelas', $instansi->nama)
                                    ->where('tahun', $this->tahun)
                                    ->get();
                        break;
                }
                break;

            case 5:
                switch($level)
                {
                    case 'admin':
                        $jadwal = DB::table('v_jadwal_batal')
                                    ->where('tahun', $this->tahun)
                                    ->get();
                        break;

                    case 'user':
                        $jadwal = DB::table('v_jadwal_batal')
                                    ->where('tahun', $this->tahun)
                                    ->where('usergroup', $this->user->usergroup)
                                    ->get();
                        break;

                    case 'kontribusi':
                        $instansi = DB::table('instansi')->where('id', $this->user->instansi_id)->first();
                        $jadwal = DB::table('v_jadwal_batal')
                                    ->where('kelas', $instansi->nama)
                                    ->where('tahun', $this->tahun)
                                    ->get();
                        break;
                }
                break;

            default:
                switch($level)
                {
                    case 'admin':
                        $jadwal = DB::table('v_jadwal_detail')
                                    ->where('tahun', $this->tahun)
                                    ->orderby('tgl_awal', 'desc')
                                    ->get();
                        break;

                    case 'user':
                        $jadwal = DB::table('v_jadwal_detail')
                                    ->where('tahun', $this->tahun)
                                    ->where('usergroup', $this->user->usergroup)
                                    ->orderby('tgl_awal', 'desc')
                                    ->get();
                        break;

                    case 'kontribusi':
                        $instansi = DB::table('instansi')->where('id', $this->user->instansi_id)->first();
                        $jadwal = DB::table('v_jadwal_detail')
                                    ->where('kelas', $instansi->nama)
                                    ->where('tahun', $this->tahun)
                                    ->get();
                        break;
                }
                break;
        }

        return view('backend.diklat.jadwal.filter', compact('jadwal'));
    }

    public function detail($id, $slug)
    {
        $this->checkAuth($id);
        $jadwal = DB::table('v_jadwal_detail')->where('id', $id)->first();
        $peserta = DB::table('peserta')
                    ->where('diklat_jadwal_id', $id)
                    ->where('verifikasi', true)
                    ->where('batal', false)
                    ->get();

        $page = '';

        if(\Request::has('page'))
            $page = \Request::query('page');

        switch($page)
        {
            case 'peserta':
                return $this->peserta($jadwal, $peserta);
            break;

            case 'sertifikat':
                return $this->sertifikat($jadwal, $peserta);
            break;

            case 'cetak':
                return $this->cetak($jadwal, $peserta);
            break;

            case 'checklist':
                return $this->checklist($jadwal);
            break;

            case 'mata-pelatihan':
                return $this->mapel($jadwal);
            break;

            case 'seminar':
                return $this->seminar($jadwal);
            break;

            case 'surat-tugas':
                return $this->surtu($jadwal);
            break;

            default:
                return view('backend.diklat.jadwal.detail', compact('jadwal', 'peserta'));
        }
    }

    public function peserta($jadwal, $peserta)
    {
        $pes_verif = DB::table('peserta')
                    ->where('diklat_jadwal_id', $jadwal->id)
                    ->where('verifikasi', true)
                    ->where('batal', false)
                    ->orderby('nama_lengkap')
                    ->get();
        $pes_noverif = DB::table('peserta')
                    ->where('diklat_jadwal_id', $jadwal->id)
                    ->where('verifikasi', false)
                    ->where('batal', false)
                    ->where('konfirmasi', true)
                    ->get();
        $pes_confirm = DB::table('peserta')
                    ->where('diklat_jadwal_id', $jadwal->id)
                    ->where('verifikasi', false)
                    ->where('batal', false)
                    ->where('konfirmasi', false)
                    ->get();
        $pes_tolak = DB::table('peserta')
                    ->where('diklat_jadwal_id', $jadwal->id)
                    ->where('verifikasi', 2)
                    ->where('batal', false)
                    ->get();
        $pes_batal = DB::table('peserta')
                    ->where('diklat_jadwal_id', $jadwal->id)
                    ->where('batal', true)
                    ->get();

        $sertifikat = DB::table('sertifikat')
                    ->where('diklat_jadwal_id', $jadwal->id)
                    ->first();

        if(!$jadwal->registrasi_lengkap)
            return view('backend.diklat.jadwal.detail_peserta_s', compact('jadwal', 'pes_verif', 'pes_noverif', 'pes_confirm', 'pes_batal', 'pes_tolak', 'sertifikat'));

        return view('backend.diklat.jadwal.detail_peserta', compact('jadwal', 'pes_verif', 'pes_noverif', 'pes_confirm', 'pes_batal', 'pes_tolak', 'sertifikat'));
    }

    public function cetak($jadwal, $peserta)
    {
        $umum = DB::table('cetak')->where('group', 'umum')->orderby('nama')->get();
        return view('backend.diklat.jadwal.detail_cetak', compact('jadwal', 'peserta', 'umum'));
    }

    public function checklist($jadwal)
    {
        return view('backend.diklat.jadwal.detail_checklist', compact('jadwal'));
    }

    public function sertifikat($jadwal, $peserta)
    {
        $sertifikat = DB::table('sertifikat')->where('diklat_jadwal_id', $jadwal->id)->first();
        if(!is_null($sertifikat))
        {
            $sertPeserta = DB::table('v_sertifikat')->where('sertifikat_id', $sertifikat->id)->get();
            $email = DB::table('sertifikat_email')->where('sertifikat_id', $sertifikat->id)->first();
            return view('backend.diklat.jadwal.detail_sertifikat', compact('jadwal', 'sertifikat', 'sertPeserta', 'email'));
        }
        $template = DB::table('sertifikat_template')->where('is_tampil', true)->orderBy('nama')->get();

        return view('backend.diklat.jadwal.detail_sertifikat', compact('jadwal', 'sertifikat', 'template'));
    }

    public function mapel($jadwal)
    {
        $kurikulum = DB::table('kurikulum')->where('id', $jadwal->kurikulum_id)->first();
        $mapel = DB::table('mapel')->where('kurikulum_id', $jadwal->kurikulum_id)->get();
        $detail = 'Mata Pelatihan';

        return view('backend.diklat.jadwal.detail_mapel', compact('jadwal', 'kurikulum', 'mapel', 'detail'));
    }

    public function seminar($jadwal)
    {
        $seminar = DB::table('seminar')
                        ->join('fasilitator as f1', 'seminar.cid', 'f1.id')
                        ->join('fasilitator as f2', 'seminar.pid', 'f2.id')
                        ->select('seminar.*', 'f1.nama as coach', 'f2.nama as penguji')
                        ->where('seminar.jid', $jadwal->id)
                        ->get();

        $cetak = DB::table('seminar_form')->where('djid', $jadwal->diklat_jenis_id)->orderBy('nama')->get();

        $detail = 'Seminar';

        return view('backend.diklat.jadwal.detail_seminar', compact('jadwal', 'seminar', 'cetak', 'detail'));
    }

    public function surtu($jadwal)
    {
        return view('backend.diklat.jadwal.detail_surtu', compact('jadwal'));
    }

    public function checkAuth($id)
    {
        if($this->isAdmin() || $this->isViewer())
            return true;

        $data = DB::table('v_jadwal_detail')->where('id', $id)
                    ->where('tahun', $this->tahun)
                    ->first();

        if(empty($data))
        {
            abort(404);
        }

        if(Gate::allows('isCreator', $data) && Auth::user()->instansi_id == 1)
        {
            return true;
        }
        else
        {
            if(Gate::allows('isKelasKontribusi', $data))
                return true;
        }

        abort(403);
    }
}
