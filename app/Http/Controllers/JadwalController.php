<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Mail\DaftarMailable;
use App\Mail\VerifikasiWaitMailable;
use App\ApiToken;
use App\Jobs\EmailDaftarHadirJob;
use App\Jobs\EmailKonfirmasiJob;
use App\Jobs\EmailVerifikasiWaitJob;
use Storage;
use AppSettings;

class JadwalController extends Controller
{
    public function __construct()
    {
        $this->tahun = AppSettings::get('app_tahun');
    }

    public function index()
    {
        //
        $jenis = DB::table('diklat_jenis')->where('aktif', true)->orderBy('nama')->get();
        $jadwal = DB::table('v_front_jadwal')
            //->where('tahun', $this->tahun)
            ->where('status_jadwal', '<', 3)
            ->where('status', '=', 1)
            ->where('is_tampil', '=', 1)
            ->orderBy('tgl_awal')->get();
        $tahun = $this->tahun;

        return view('frontend.jadwal', compact('jenis', 'jadwal', 'tahun'));
    }

    public function cari(Request $request)
    {
        // dd($request->all());
        $jenis = DB::table('diklat_jenis')->orderBy('nama')->get();
        $sql = "SELECT * FROM v_front_jadwal";
        $where = array();

        if (!is_null($request->nama))
            $where[] = " nama like '%" . $request->nama . "%'";

        if (!is_null($request->tgl_awal) and !is_null($request->tgl_akhir)) {
            $where[] = " ('" . $request->tgl_awal . "' <= tgl_awal)";
            $where[] = " ('" . $request->tgl_akhir . "' >= tgl_akhir)";
        }

        if ($request->has('waktu')) {
            switch ($request->waktu) {
                case 1:
                    $where[] = " (status=1)";
                    $where[] = " (CURDATE() BETWEEN tgl_awal AND tgl_akhir)";
                    break;
                case 2:
                    $where[] = " (status=1)";
                    $where[] = " (CURDATE() < tgl_awal)";
                    break;
                case 3:
                    $where[] = " (status=1)";
                    $where[] = " (CURDATE() > tgl_akhir)";
                    break;
            }
        }
        if ($request->has('jenis')) {
            if ($request->jenis > 0)
                $where[] = " diklat_jenis_id=" . $request->jenis;
        }

        $where[] = " tahun=" . $this->tahun;
        $where[] = " is_tampil=1";

        if (count($where) > 0)
            $sql .= " WHERE" . implode(" AND", $where);

        $sql .= ' ORDER BY tgl_awal DESC';

        $jadwal = DB::select($sql);

        return view('frontend.jadwal_cari', compact('jadwal'));
    }

    public function detail($id, $slug)
    {
        $jadwal = DB::table('v_front_jadwal')->where('id', $id)->where('tahun', $this->tahun)->first();
        if (empty($jadwal))
            abort(404);

        if ($slug != str_slug($jadwal->nama))
            abort(404);

        $peserta = DB::table('peserta')
            ->where('diklat_jadwal_id', $id)
            ->where('verifikasi', 1)
            ->where('batal', 0)
            ->count();

        return view('frontend.jadwal_detail', compact('jadwal', 'peserta'));
    }

    public function daftar(Request $request)
    {
        $request->session()->flush();

        $jadwal = DB::table('v_front_jadwal')->where('id', $request->jadwal_id)->first();
        if (empty($jadwal))
            abort(404);

        $request->session()->put('jadwal_id', $jadwal->id);
        return redirect()->route('jadwal.daftar.step1');
    }

    public function step1()
    {
        $request = \Request::session();
        $request->forget([
            'nip',
            'instansi',
            'peserta',
            'foto_temp',
            'status_asn',
        ]);
        if ($request->has('jadwal_id')) {
            $jadwal = DB::table('v_front_jadwal')->where('id', session('jadwal_id'))->first();
            $peserta = DB::table('peserta')
                ->where('diklat_jadwal_id', $jadwal->id)
                ->whereIn('verifikasi', [0, 1])
                ->where('batal', 0)
                ->get();

            if ($jadwal->status_registrasi == true && $jadwal->kuota > count($peserta))
                return view('frontend.daftar.1', compact('jadwal'));

            return view('frontend.daftar.tutup', compact('jadwal'));
        }
        abort(404);
    }

    public function poststep1(Request $request)
    {
        $request->validate([
            'captcha' => 'required|captcha',
        ]);

        if ($request->status != 0) {
            $validator = $request->validate([
                'nip' => 'required|digits:18|numeric',
            ]);

            $peserta = DB::table('peserta')
                ->where('nip', $request->nip)
                ->where('diklat_jadwal_id', session('jadwal_id'))
                ->first();

            if (!empty($peserta)) {
                $notifikasi = 'NIP Anda telah terdaftar untuk mengikuti kegiatan ini!';
                return redirect()->back()->with('error', $notifikasi);
            }

            $request->session()->put('nip', $request->nip);
        }
        $request->session()->put('instansi', $request->instansi);
        $request->session()->put('status_asn', $request->status);

        return redirect()->route('jadwal.daftar.step2');
    }

    public function step2()
    {
        $request = \Request::session();
        if (!$request->has('jadwal_id') && !$request->has('instansi') && !$request->has('nip')) {
            abort(404);
        }

        if ($request->has('jadwal_id') && !$request->has('instansi') && !$request->has('nip')) {
            return redirect()->route('jadwal.daftar.step1');
        }

        $jadwal = DB::table('v_front_jadwal')->where('id', session('jadwal_id'))->first();
        $agama = DB::table('agama')->get();
        if (session('status_asn') == 1)
            $pangkat = DB::table('pangkat')->where('pangkat', '<>', 'PPPK')->where('pangkat', '<>', 'Non-ASN')->get();
        else if (session('status_asn') == 2)
            $pangkat = DB::table('pangkat')->where('pangkat', '=', 'PPPK')->get();
        else
            $pangkat = DB::table('pangkat')->where('pangkat', '=', 'Non-ASN')->get();

        if (session('instansi') == 1) {
            if (session('status_asn') == 1 or session('status_asn') == 2) {
                $instansi = DB::table('instansi')->where('id', 1)->first();
                $id = session('nip');
                $client = new Client(['http_errors' => true, 'verify' => false]);

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
                    // $content = $req_pegawai->getBody()->getContents();

                    if ($req_pegawai->getStatusCode() == 200) {
                        $res_pegawai = $req_pegawai->getBody();
                        $data_pegawai = json_decode($res_pegawai, true);

                        if (!$data_pegawai['success'])
                            return redirect()->back()->with('error', $data_pegawai['keterangan']);

                        //$req_satker = $client->get(env('SIMPEG_SATKER') . $data_pegawai['id_skpd'] . '/?api_token=' . env('SIMPEG_KEY'));
                        // $req_satker = $client->get(env('SIMPEG_SATKER') . '/?id_skpd=' . $data_pegawai['id_skpd'] . '&api_token=' . env('SIMPEG_KEY'));
                        $url_opd = env('SIMASN_LISTOPD');
                        $req_satker = $client->get($url_opd, [
                            'headers' => $headers
                        ]);

                        if ($req_satker->getStatusCode() == 200) {
                            $res_satker = $req_satker->getBody();
                            $satker = json_decode($res_satker, true);
                            $data_pegawai = $data_pegawai['data'];
                            $data_opd = $satker['data'];
                            $col_opd = collect($data_opd);
                            $opd = $col_opd->firstWhere('id', $data_pegawai['opd_id']);

                            $nama_lengkap = $data_pegawai['nama_non_gelar'];
                            $tmp_nama = explode(' ', $nama_lengkap);
                            $singkat = '';

                            foreach ($tmp_nama as $i => $key) {
                                if ($i > 0)
                                    $singkat = $singkat . substr($key, 0, 1);
                            }

                            $nama = $tmp_nama[0] . ' ' . $singkat;
                            $instansi = DB::table('instansi')->where('id', 1)->first();

                            if ($data_pegawai['jenis_asn'] == 'pns')
                                session(['status_asn' => 1]);
                            else if ($data_pegawai['jenis_asn'] == 'pppk')
                                session(['status_asn' => 2]);

                            $pegawai = array(
                                'nip' => $data_pegawai['nip'],
                                'nik' => $data_pegawai['nik'],
                                'nama_lengkap' => $nama_lengkap,
                                'nama' => $nama,
                                'telp' => $data_pegawai['hp'],
                                'email' => $data_pegawai['email'],
                                'tmp_lahir' => $data_pegawai['tempat_lahir'],
                                'tgl_lahir' => $data_pegawai['tgl_lahir'],
                                'jk' => simpegJK($data_pegawai['jk']),
                                'agama' => $data_pegawai['agama'],
                                // 'marital' => $data_pegawai['status_kepegawaian'],
                                'marital' => null,
                                'alamat' => $data_pegawai['alamat'],
                                'jabatan' => $data_pegawai['jabatan'],
                                'pangkat' => konversiGolongan($data_pegawai['golongan_id'], $data_pegawai['jenis_asn']),
                                'instansi' => $instansi->nama,
                                'satker_nama' => $opd['opd'],
                                'satker_telp' => $opd['telp'],
                                'satker_alamat' => $opd['alamat'],
                            );

                            if (session('status_asn') == 1)
                                $pangkat = DB::table('pangkat')->where('pangkat', '<>', 'PPPK')->where('pangkat', '<>', 'Non-ASN')->get();
                            else if (session('status_asn') == 2)
                                $pangkat = DB::table('pangkat')->where('pangkat', '=', 'PPPK')->get();

                            if ($jadwal->registrasi_lengkap)
                                return view('frontend.daftar.2group1', compact('jadwal', 'pangkat', 'agama', 'instansi', 'pegawai'));

                            return view('frontend.daftar.2group1s', compact('jadwal', 'instansi', 'pegawai'));
                        }
                    }
                } catch (\Exception $ex) {
                    $notifikasi = 'Terjadi kesalahan, mohon cek kembali NIP Pegawai!';

                    return redirect()->back()->with('error', $notifikasi);
                }
            } else {
                $instansi = DB::table('instansi')->where('id', 1)->get();
                $pegawai = array(
                    'nip' => session('nip'),
                );
                if ($jadwal->registrasi_lengkap)
                    return view('frontend.daftar.2group2', compact('jadwal', 'pangkat', 'agama', 'instansi', 'pegawai'));

                return view('frontend.daftar.2group2s', compact('jadwal', 'instansi', 'pegawai'));
            }
        } else if (session('instansi') == 2) {
            $instansi = DB::table('instansi')->where('group', 2)->orderBy('sort')->get();
            $pegawai = array(
                'nip' => session('nip'),
            );
            if ($jadwal->registrasi_lengkap)
                return view('frontend.daftar.2group2', compact('jadwal', 'pangkat', 'agama', 'instansi', 'pegawai'));

            return view('frontend.daftar.2group2s', compact('jadwal', 'instansi', 'pegawai'));
        }

        $pegawai = array(
            'nip' => session('nip'),
        );

        if ($jadwal->registrasi_lengkap)
            return view('frontend.daftar.2group3', compact('jadwal', 'pangkat', 'agama', 'pegawai'));

        return view('frontend.daftar.2group3s', compact('jadwal', 'pegawai'));
    }

    public function poststep2(Request $request)
    {
        if (session('status_asn') == 1 || session('status_asn') == 2) {
            $validator = $request->validate([
                'foto' => 'mimetypes:image/jpeg,image/png|max:512',
                'nip' => 'required|min:18|max:18',
                'ktp' => 'required|min:16|max:16',
                'nama_lengkap' => 'required',
                'nama_panggil' => 'required',
                'alamat' => 'required',
                'jk' => 'required',
                'tmp_lahir' => 'required',
                'tgl_lahir' => 'required|date',
                'agama' => 'required',
                'marital' => 'required',
                'hp' => 'required',
                'email' => 'required|email',
                'pangkat' => 'required',
                'jabatan' => 'required',
                'instansi' => 'required',
                'satker_nama' => 'required',
                'satker_alamat' => 'required',
                'pendidikan' => 'required',
                //'satker_telp' => 'required',
            ]);
        } else {
            $validator = $request->validate([
                'foto' => 'mimetypes:image/jpeg,image/png|max:512',
                'ktp' => 'required|min:16|max:16',
                'nama_lengkap' => 'required',
                'nama_panggil' => 'required',
                'alamat' => 'required',
                'jk' => 'required',
                'tmp_lahir' => 'required',
                'tgl_lahir' => 'required|date',
                'agama' => 'required',
                'marital' => 'required',
                'hp' => 'required',
                'email' => 'required|email',
                'pangkat' => 'required',
                'jabatan' => 'required',
                'instansi' => 'required',
                'satker_nama' => 'required',
                'satker_alamat' => 'required',
                'pendidikan' => 'required',
                //'satker_telp' => 'required',
            ]);
        }

        $time = time();
        $request->session()->put('peserta', $request->except('foto'));

        if (isset($request->foto)) {
            $foto = $request->file('foto');
            $nama_file = $time . "." . $foto->getClientOriginalExtension();
            $path = $request->foto->storeAs('public/files/photo/peserta/temp', $nama_file);
            $request->session()->put('foto_temp', $path);
        }

        return redirect()->route('jadwal.daftar.step3');
    }

    public function poststep2simple(Request $request)
    {
        // session status_asn
        DB::beginTransaction();
        try {
            $jadwal = DB::table('v_front_jadwal')->where('id', session('jadwal_id'))->first();
            // $peserta = DB::table('peserta')
            //     ->where('diklat_jadwal_id', $jadwal->id)
            //     // ->where('verifikasi', 1)
            //     // ->where('batal', 0)
            //     ->lockForUpdate()
            //     ->get();

            $jumlahPeserta = DB::table('peserta')->where('diklat_jadwal_id', $jadwal->id)->count();

            if ($jadwal->status_registrasi == true && $jadwal->kuota <= $jumlahPeserta) {
                DB::rollBack();
                return view('frontend.daftar.tutup', compact('jadwal'));
            }

            $token = str_random(40);
            $nip = session('nip');

            $jadwal = DB::table('v_front_jadwal')->where('id', session('jadwal_id'))->first();
            $bulan = date('m');
            $tahun = date('Y');
            $created_at = date('Y-m-d H:i:s');

            $result = DB::table('peserta')
                ->whereMonth('created_at', '=', $bulan)
                ->whereYear('created_at', '=', $tahun)
                ->count();

            $kode = "R" . sprintf("%s%02s%04s", $tahun, $bulan, ++$result);
            $status_asn = session('status_asn');

            $id = DB::table('peserta')->insertGetId([
                'kode' => $kode,
                'nip' => $nip,
                'nama_lengkap' => $request->nama_lengkap,
                'nama_panggil' => $request->nama_panggil,
                'jk' => $request->jk,
                'hp' => $request->hp,
                'email' => $request->email,
                'jabatan' => $request->jabatan,
                'instansi' => $request->instansi,
                'satker_nama' => $request->satker_nama ?? '-',
                'diklat_jadwal_id' => $jadwal->id,
                'token' => $token,
                'status_asn' => $status_asn,
                'sebagai' => 'Peserta',
                'pendidikan' => $request->pendidikan,
                'created_at' => $created_at,
            ]);

            DB::commit();

            $url = \URL::signedRoute('jadwal.konfirmasi', $id);
            // Mail::to($request->email)->send(new DaftarMailable($request->nama_lengkap, $url));
            $job = new EmailDaftarHadirJob($request->nama_lengkap, $request->email, $jadwal, $url);
            $this->dispatch($job);

            $request->session()->flush();

            $peserta = DB::table('peserta')->find($id);

            return view('frontend.daftar.finish_simple', compact('jadwal', 'peserta'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function step3()
    {
        $request = \Request::session();

        if (!$request->has('peserta'))
            abort(404);

        $jadwal = DB::table('v_front_jadwal')->where('id', session('jadwal_id'))->first();
        $pangkat = DB::table('pangkat')->get();
        $agama = DB::table('agama')->get();

        return view('frontend.daftar.3', compact('jadwal', 'pangkat', 'agama'));
    }

    public function poststep3(Request $request)
    {
        DB::beginTransaction();
        try {
            $jadwal = DB::table('v_front_jadwal')->where('id', session('jadwal_id'))->first();
            // $peserta = DB::table('peserta')
            //     ->where('diklat_jadwal_id', $jadwal->id)
            //     // ->where('verifikasi', 1)
            //     // ->where('batal', 0)
            //     ->lockForUpdate()
            //     ->get();

            $jumlahPeserta = DB::table('peserta')->where('diklat_jadwal_id', $jadwal->id)->count();

            if ($jadwal->status_registrasi == true && $jadwal->kuota <= $jumlahPeserta) {
                DB::rollBack();
                return view('frontend.daftar.tutup', compact('jadwal'));
            }

            $token = str_random(40);
            $destination = null;

            // update skpk
            if (!empty(session('foto_temp'))) {
                $file = session('foto_temp');
                $filename = pathinfo($file, PATHINFO_FILENAME);
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $destination = 'public/files/photo/peserta/' . $filename . '.' . $extension;
                Storage::move($file, $destination);
            }

            $jadwal = DB::table('v_front_jadwal')->where('id', session('jadwal_id'))->first();
            $bulan = date('m');
            $tahun = date('Y');
            $created_at = date('Y-m-d H:i:s');

            $result = DB::table('peserta')
                ->whereMonth('created_at', '=', $bulan)
                ->whereYear('created_at', '=', $tahun)
                ->count();

            $kode = "R" . sprintf("%s%02s%04s", $tahun, $bulan, ++$result);
            $status_asn = session('status_asn');

            $id = DB::table('peserta')->insertGetId([
                'kode' => $kode,
                'nip' => $request->nip,
                'ktp' => $request->ktp,
                'nama_lengkap' => $request->nama_lengkap,
                'nama_panggil' => $request->nama_panggil,
                'tmp_lahir' => $request->tmp_lahir,
                'tgl_lahir' => $request->tgl_lahir,
                'jk' => $request->jk,
                'alamat' => $request->alamat,
                'agama_id' => $request->agama,
                'hp' => $request->hp,
                'marital' => $request->marital,
                'email' => $request->email,
                'jabatan' => $request->jabatan,
                'pangkat_id' => $request->pangkat,
                'foto' => $destination,
                'instansi' => $request->instansi,
                'satker_nama' => $request->satker_nama,
                'satker_alamat' => $request->satker_alamat,
                'satker_telp' => $request->satker_telp,
                'diklat_jadwal_id' => $jadwal->id,
                'token' => $token,
                'status_asn' => $status_asn,
                'pendidikan' => $request->pendidikan,
                'created_at' => $created_at,
            ]);

            DB::commit();

            $url = \URL::signedRoute('jadwal.konfirmasi', $id);
            // Mail::to($request->email)->send(new DaftarMailable($request->nama_lengkap, $url));
            $job = new EmailKonfirmasiJob($request->nama_lengkap, $request->email, $jadwal, $url);
            $this->dispatch($job);

            $request->session()->forget([
                'jadwal_id',
                'nip',
                'instansi',
                'peserta',
                'foto_temp',
                'status_asn',
            ]);

            $peserta = DB::table('peserta')->find($id);

            return view('frontend.daftar.finish', compact('jadwal', 'peserta'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function konfirmasi(Request $request, $id)
    {
        if (!$request->hasValidSignature()) {
            abort(404);
        }

        $peserta = DB::table('peserta')->find($id);

        if (is_null($peserta)) {
            abort(404);
        }

        $jadwal = DB::table('v_jadwal_detail')->find($peserta->diklat_jadwal_id);

        if ($jadwal->registrasi_lengkap) {
            if (!$peserta->konfirmasi) {
                DB::table('peserta')->where('id', $id)->update([
                    'konfirmasi' => true
                ]);
                // Mail::to($peserta->email)->send(new VerifikasiWaitMailable($peserta->nama_lengkap, $jadwal));
                $url = route('jadwal.detail', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama)]);
                $job = new EmailVerifikasiWaitJob($peserta->nama_lengkap, $peserta->email, $jadwal, $url);
                $this->dispatch($job);
            }

            return view('frontend.daftar.konfirmasi', compact('jadwal', 'peserta'));
        }

        if ($jadwal->is_konfirmasi) {
            if (!$peserta->konfirmasi) {
                DB::table('peserta')->where('id', $id)->update([
                    'verifikasi' => true,
                    'konfirmasi' => true
                ]);
            }

            return view('frontend.daftar.daftarhadir-open', compact('jadwal', 'peserta'));
        }

        return view('frontend.daftar.daftarhadir-close', compact('jadwal', 'peserta'));
    }

    public function testFinish()
    {
        $peserta = DB::table('peserta')->where('id', 1)->first();
        $jadwal = DB::table('v_front_jadwal')->where('id', $peserta->diklat_jadwal_id)->first();
        return view('frontend.daftar.finish', compact('jadwal', 'peserta'));
    }

    public function wi()
    {
        $wi = DB::table('fasilitator')->where('internal', true)->orderBy('nama')->get();
        $jadwal = [];
        $tahun = $this->tahun;

        return view('frontend.wi', compact('wi', 'tahun', 'jadwal'));
    }

    public function postWi(Request $request)
    {
        $validator = $request->validate([
            'widyaiswara' => 'required',
            'bulan' => 'required',
        ]);
        $sql = "SELECT * FROM `v_spwi_query`";
        $where = array();
        $where[] = " `fid`=" . $request->widyaiswara;
        $where[] = " MONTH(`tanggal`)=" . $request->bulan;
        $where[] = " `tahun`=" . $this->tahun;

        if (count($where) > 0)
            $sql .= " WHERE" . implode(" AND", $where);

        $sql .= ' ORDER BY tanggal ASC';
        $jadwal = DB::select($sql);
        return view('frontend.wi_cari', compact('jadwal'));
    }

    public function tautan($id, $hash)
    {
        $jadwal = DB::table('v_front_jadwal')->where('id', $id)->where('tahun', $this->tahun)->first();
        $shortCode = substr(md5($jadwal->nama), 0, 6);


        if (empty($jadwal))
            abort(404);

        if ($hash != $shortCode)
            abort(404);

        $tautan = DB::table('tautan')->where('jadwal_id', $jadwal->id)->where('is_active', 1)->get();

        return view('frontend.tautan', compact('jadwal', 'tautan'));
    }

    public function datatablePeserta(Request $request, $jadwal_id)
    {
        $query = DB::table('peserta')
            ->where('diklat_jadwal_id', $jadwal_id)
            ->where('verifikasi', 1)
            ->where('batal', 0)
            ->select('id', 'nip', 'nama_lengkap', 'instansi', 'satker_nama');

        return datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('nip_masking', function ($row) {
                if (!empty($row->nip) && strlen($row->nip) == 18) {
                    return substr($row->nip, 0, 2) . str_repeat('*', 10) . substr($row->nip, -6);
                }
                return '-';
            })
            ->addColumn('instansi_render', function ($row) {
                return $row->satker_nama . ' ' . strtoupper($row->instansi);
            })
            ->rawColumns(['nip_masking'])
            ->make(true);
    }
}
