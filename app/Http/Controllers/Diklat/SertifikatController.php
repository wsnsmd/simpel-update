<?php

namespace App\Http\Controllers\Diklat;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Diklat\JadwalController;
use App\Mail\KirimSertifikatMailable;
use App\Jobs\UploadSimpegJob;
use App\Jobs\KirimEmailSertifikatJob;
use GuzzleHttp\Client;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use App;
use DB;
use Session;
use Storage;

class SertifikatController extends Controller
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

    public function store(Request $request)
    {
        $is_generate = $request->is_generate;

        if($is_generate)
        {
            $validator = $request->validate([
                'tsid' => 'required',
                'jadwal_id' => 'required',
                'is_generate' => 'required',
                'is_upload' => 'required',
                'is_import' => 'required',
                'tempat' => 'required',
                'tanggal' => 'required|date',
                'barcode' => 'required_if:is_generate,1',
                'kualifikasi' => 'required_if:is_generate,1',
                // 'format_nomor' => 'required_if:is_generate,1',
                'jabatan' => 'required_if:is_generate,1',
                'nip' => 'required_if:is_generate,1|min:18|max:18',
                'nama' => 'required_if:is_generate,1',
                'pangkat' => 'required_if:is_generate,1',
                'spesimen' => 'nullable|mimes:png|max:512'
            ]);
        }
        else
        {
            $validator = $request->validate([
                'jadwal_id' => 'required',
                'is_generate' => 'required',
                'is_upload' => 'required|gt:0',
                'is_import' => 'required',
                'tempat' => 'required',
                'tanggal' => 'required|date',
            ]);
        }

        $input = $request->all();
        $spesimen_path = null;
        $spesimen2_path = null;

        DB::beginTransaction();

        try
        {
            $created_at = date('Y-m-d H:i:s');
            if($request->has('spesimen'))
            {
                $file = $input['spesimen'];
                $nama_file = time()."_".$file->getClientOriginalName();
                $spesimen_path = $input['spesimen']->storeAs('public/files/spesimen', $nama_file);
            }
            if($request->has('spesimen2'))
            {
                $file = $input['spesimen2'];
                $nama_file2 = time()."_".$file->getClientOriginalName();
                $spesimen2_path = $input['spesimen2']->storeAs('public/files/spesimen', $nama_file2);
            }

            DB::table('sertifikat')->insert([
                'diklat_jadwal_id' => $input['jadwal_id'],
                'tsid' => $input['tsid'],
                'fasilitasi' => $input['fasilitasi'],
                'is_generate' => $input['is_generate'],
                'is_upload' => $input['is_upload'],
                'barcode' => $input['barcode'],
                'kualifikasi' => $input['kualifikasi'],
                'import' => $input['is_import'],
                'format_nomor' => $input['format_nomor'],
                'tempat' => $input['tempat'],
                'tanggal' => $input['tanggal'],
                'jabatan' => $input['jabatan'],
                'nip' => $input['nip'],
                'nama' => $input['nama'],
                'pangkat' => $input['pangkat'],
                'jabatan2' => $input['jabatan2'],
                'nip2' => $input['nip2'],
                'nama2' => $input['nama2'],
                'pangkat2' => $input['pangkat2'],
                'spesimen' => $spesimen_path,
                'spesimen2' => $spesimen2_path,
                'created_at' => $created_at
            ]);
        }
        catch (\Exception $e)
        {
            DB::rollback();
            throw $e;
        }

        DB::commit();

        $notifikasi = 'Data sertifikat berhasil dibuat!';

        $jadwal = DB::table('v_jadwal_detail')->where('id', $input['jadwal_id'])->first();

        return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'sertifikat'])
                    ->with([
                        'success' => $notifikasi,
                    ]);
    }

    public function buatPeserta(Request $request, $id)
    {
        $peserta = DB::table('v_sertifikat_peserta')
                    ->where('diklat_jadwal_id', $id)
                    ->get();

        $jadwal = DB::table('v_jadwal_detail')->find($id);

        $sertifikat = DB::table('sertifikat')->where('diklat_jadwal_id', $id)->first();

        return view('backend.diklat.sertifikat.buat_peserta', compact('jadwal', 'peserta', 'sertifikat'));
    }

    public function buatPesertaSimpan(Request $request, $id)
    {
        $pid = $request->pid;
        $pid_nomor = $request->pid_nomor;

        $created_at = date('Y-m-d H:i:s');
        $jadwal = DB::table('v_jadwal_detail')->find($id);
        $sertifikat = DB::table('sertifikat')->where('diklat_jadwal_id', $id)->first();

        $bulan = date('m', strtotime($sertifikat->tanggal));
        $tahun = date('Y', strtotime($sertifikat->tanggal));
        $search = array('{N}', '{m}', '{y}');
        $format = $sertifikat->format_nomor;

        $result = DB::table('sertifikat_peserta')
                    ->where('tahun', $tahun)
                    ->where('bidang', $jadwal->usergroup)
                    ->max('no');

        $kiri = null;
        $bawah = null;
        $kiri2 = null;
        $bawah2 = null;
        if(!is_null($sertifikat->tsid))
        {
            $template = DB::table('sertifikat_template')->where('id', $sertifikat->tsid)->first();
            $kiri = $template->spesimen_kiri;
            $bawah = $template->spesimen_bawah;
            $kiri2 = $template->spesimen2_kiri;
            $bawah2 = $template->spesimen2_bawah;
        }

        if(is_null($pid))
        {
            $notifikasi = 'Tidak ada data peserta yang dipilih!';
            return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'sertifikat'])
                        ->with([
                            'success' => $notifikasi,
                        ]);
        }

        DB::beginTransaction();

        try
        {
            foreach($pid as $p)
            {
                $peserta = DB::table('v_peserta')
                            ->where('id', $p)
                            ->where('diklat_jadwal_id', $id)
                            ->exists();

                if($peserta)
                {
                    $no = ++$result;
                    $kualifikasi =  $request['pid_kualifikasi_' . $p];
                    $status =  $request['pid_status_' . $p];
                    if(!$sertifikat->is_generate && $sertifikat->is_upload)
                        $no_sertifikat = $request['pid_nomor_' . $p];
                    else
                    {
                        $nomor = sprintf("%05s", $no);
                        $replace = array($nomor, $bulan, $tahun);
                        $no_sertifikat = str_replace($search, $replace, $format);
                    }

                    DB::table('sertifikat_peserta')->insert([
                        'no' => $no,
                        'peserta_id' => $p,
                        'sertifikat_id' => $sertifikat->id,
                        'kualifikasi' => $kualifikasi,
                        'status' => $status,
                        'nomor' => $no_sertifikat,
                        'tahun' => $tahun,
                        'bidang' => $jadwal->usergroup,
                        'spesimen_kiri' => $kiri,
                        'spesimen_bawah' => $bawah,
                        'spesimen2_kiri' => $kiri2,
                        'spesimen2_bawah' => $bawah2,
                        'created_at' => $created_at,
                    ]);
                }
                else
                {
                    throw new \Exception('Simpan data sertifikat gagal, data peserta tidak sesuai!');
                }
            }
        }
        catch (\Exception $e)
        {
            DB::rollback();
            throw $e;
        }

        DB::commit();

        $notifikasi = 'Data sertifikat peserta berhasil dibuat!';

        return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'sertifikat'])
                    ->with([
                        'success' => $notifikasi,
                    ]);
    }

    public function cetak(Request $request, $id)
    {
        $sertPeserta = DB::table('v_sertifikat')
                        ->select('nip', 'nama_lengkap', 'tmp_lahir', 'tgl_lahir', 'jabatan', 'foto', 'instansi', 'satker_nama', 'sebagai', 'diklat_jadwal_id', 'pangkat', 'golongan', 'nomor', 'kualifikasi', 'status', 'sertifikat_id', 'spesimen_kiri', 'spesimen_bawah', 'spesimen2_kiri', 'spesimen2_bawah')
                        ->where('spid', $id)
                        ->first();

        $sertifikat = DB::table('sertifikat')
                        ->select('tempat', 'tanggal', 'jabatan', 'nama', 'pangkat', 'nip', 'jabatan2', 'nama2', 'pangkat2', 'nip2', 'diklat_jadwal_id', 'spesimen', 'spesimen2', 'tsid', 'fasilitasi')
                        ->where('id', $sertPeserta->sertifikat_id)
                        ->first();

        $jadwal = DB::table('v_jadwal_detail')
                        ->select('nama', 'tahun', 'tipe', 'tgl_awal', 'tgl_akhir', 'kelas', 'total_jp', 'lokasi', 'lokasi_kota', 'kurikulum_id')
                        ->where('id', $sertifikat->diklat_jadwal_id)
                        ->first();

        $kurikulum = DB::table('mapel')
                        ->select('nama', 'jpk', 'jpe')
                        ->where('kurikulum_id', $jadwal->kurikulum_id)
                        ->get();

        $template = DB::table('sertifikat_template')->where('id', $sertifikat->tsid)->first();

        // if(!is_null($sertPeserta->foto))
        // {
        //     $sertPeserta->foto = asset(\Storage::url($sertPeserta->foto));
        // }
        // else
        // {
        //     $sertPeserta->foto = asset('media/avatars/avatar8.jpg');
        // }

        if(is_null($sertPeserta->foto))
        {
            $sertPeserta->foto = 'media/avatars/avatar8.jpg';
        }

        // $temp = [];
        // $data = [];
        // $temp['sertifikat'] = $sertifikat;
        // $temp['peserta'] = $sertPeserta;
        // $temp['jadwal'] = $jadwal;
        // $data['json'] = json_encode($temp);

        // return view('report.sertifikat.template1', $data);
        // dd($sertifikat);

        ini_set('memory_limit', '1024M');
        set_time_limit(30);

        $paperorientation = 'landscape';
        //$filename = 'Surat Tugas - ' . $fasilitator->nama . '-' . time();
        $filename = 'Sertifikat - ' . $sertPeserta->nomor;

        $view = view('report.dom.sertifikat.' . $template->file, compact('sertPeserta', 'sertifikat', 'jadwal', 'kurikulum'));
        // return $view;
        $pdf = App::make('dompdf.wrapper');
        $pdf->setOptions(['dpi' => '120', 'isRemoteEnabled' => true, 'chroot' => realpath(base_path()) ]);
        $pdf->loadHTML($view);
        $pdf->setPaper($template->papersize, $paperorientation);

        return $pdf->stream($filename.'.pdf');
    }

    public function getSpesimenPos($id)
    {
        $data = DB::table('sertifikat_peserta')->where('id', $id)->first();
        return response()->json($data, 200);
    }

    public function postSpesimenPos(Request $request, $id)
    {
        $validator = $request->validate([
            'jadwal_id' => 'required',
            'kiri' => 'required|numeric|between:0.0,20.0',
            'bawah' => 'required|numeric|between:0.0,20.0',
            'kiri2' => 'numeric|between:0.0,20.0',
            'bawah2' => 'numeric|between:0.0,20.0',
        ]);

        $input = $request->all();

        DB::beginTransaction();

        try
        {
            $updated_at = date('Y-m-d H:i:s');

            if($request->has('spesimen_semua'))
            {
                $sp = DB::table('sertifikat_peserta')->where('id', $id)->first();
                DB::table('sertifikat_peserta')->where('sertifikat_id', $sp->sertifikat_id)->update([
                    'spesimen_kiri' => $input['kiri'],
                    'spesimen_bawah' => $input['bawah'],
                    'spesimen2_kiri' => $input['kiri2'],
                    'spesimen2_bawah' => $input['bawah2'],
                    'updated_at' => $updated_at
                ]);
            }
            else
            {
                DB::table('sertifikat_peserta')->where('id', $id)->update([
                    'spesimen_kiri' => $input['kiri'],
                    'spesimen_bawah' => $input['bawah'],
                    'spesimen2_kiri' => $input['kiri2'],
                    'spesimen2_bawah' => $input['bawah2'],
                    'updated_at' => $updated_at
                ]);
            }
        }
        catch (\Exception $e)
        {
            DB::rollback();
            throw $e;
        }

        DB::commit();

        $notifikasi = 'Data posisi spesimen berhasil diubah!';

        $jadwal = DB::table('v_jadwal_detail')->where('id', $input['jadwal_id'])->first();

        return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'sertifikat'])
                    ->with([
                        'success' => $notifikasi,
                    ]);
    }

    public function emailTemplate(Request $request, $id)
    {
        $jadwal = DB::table('v_jadwal_detail')->find($id);
        $sertifikat = DB::table('sertifikat')->where('diklat_jadwal_id', $id)->first();
        $email = DB::table('sertifikat_email')->where('sertifikat_id', $sertifikat->id)->first();

        return view('backend.diklat.sertifikat.email', compact('jadwal', 'email', 'sertifikat'));
    }

    public function emailTemplateSimpan(Request $request, $id)
    {
        $validator = $request->validate([
            'konten' => 'required',
            'bcc' => 'nullable|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
        ]);

        $jadwal = DB::table('v_jadwal_detail')->find($id);
        $sertifikat = DB::table('sertifikat')->where('diklat_jadwal_id', $id)->first();

        DB::beginTransaction();

        try
        {
            $at = date('Y-m-d H:i:s');

            DB::table('sertifikat_email')->updateOrInsert(
                ['sertifikat_id' => $sertifikat->id],
                ['sertifikat_id' => $sertifikat->id, 'konten' => $request->konten, 'bcc' => $request->bcc]
            );
        }
        catch (\Exception $e)
        {
            DB::rollback();
            throw $e;
        }

        DB::commit();

        $notifikasi = 'Template email berhasil disimpan!';

        return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'sertifikat'])
                    ->with([
                        'success' => $notifikasi,
                    ]);
    }

    public function kirimEmail(Request $request, $id)
    {
        $sertifikat = DB::table('sertifikat')->where('diklat_jadwal_id', $id)->first();
        $jadwal = DB::table('v_jadwal_detail')->find($id);
        $email = DB::table('sertifikat_email')->where('sertifikat_id', $sertifikat->id)->first();
        $search = array('http://{sertifikat}', '{nama}');

        if($request->has('email_alamat'))
        {
            $request->validate([
                'email_alamat' => ['required', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/']
            ]);
            $peserta = DB::table('v_sertifikat')
                        ->where('sertifikat_id', $sertifikat->id)
                        ->first();
            if($sertifikat->is_upload && is_null($peserta->upload))
            {
                $notifikasi = 'Email sertifikat gagal dikirim, pastikan data sudah benar!';
                return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'sertifikat'])
                        ->with([
                            'error' => $notifikasi,
                            'page' => 'peserta'
                        ]);
            }

            $url_sertifikat = route('sertifikat.show', [
                'peserta' => $peserta->id,
                'jadwal' => $peserta->diklat_jadwal_id,
                'sertifikat' => $peserta->spid,
                'email' => str_slug($peserta->email)
            ]);
            $replace = array($url_sertifikat, $peserta->nama_lengkap);
            $konten = str_replace($search, $replace, $email->konten);
            $job = new KirimEmailSertifikatJob($request->email_alamat, $peserta->nama_lengkap, $jadwal, $konten, $sertifikat);
            $this->dispatch($job);

            $notifikasi = 'Email sertifikat berhasil dikirim!';

            return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'sertifikat'])
                            ->with([
                                'success' => $notifikasi,
                            ]);
        }
        else
        {
            $peserta = DB::table('v_sertifikat')
                        ->where('sertifikat_id', $sertifikat->id)
                        ->whereNull('email_at')
                        ->get();
            try
            {
                foreach($peserta as $pes)
                {
                    if($sertifikat->is_upload && is_null($pes->upload))
                        continue;

                    $url_sertifikat = route('sertifikat.show', [
                        'peserta' => $pes->id,
                        'jadwal' => $pes->diklat_jadwal_id,
                        'sertifikat' => $pes->spid,
                        'email' => str_slug($pes->email)
                    ]);
                    $replace = array($url_sertifikat, $pes->nama_lengkap);
                    $konten = str_replace($search, $replace, $email->konten);
                    if(!is_null($email->bcc))
                    {
                        $job = new KirimEmailSertifikatJob($pes->email, $pes->nama_lengkap, $jadwal, $konten, $sertifikat, $email->bcc);
                        $this->dispatch($job);
                    }
                    else
                    {
                        $job = new KirimEmailSertifikatJob($pes->email, $pes->nama_lengkap, $jadwal, $konten, $sertifikat);
                        $this->dispatch($job);
                    }

                    $at = date('Y-m-d H:i:s');
                    DB::table('sertifikat_peserta')->where('id', $pes->spid)->update([
                        'email_at' => $at
                    ]);
                }

                $notifikasi = 'Email sertifikat berhasil dikirim!';

                return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'sertifikat'])
                            ->with([
                                'success' => $notifikasi,
                            ]);
            }
            catch(\Exception $e)
            {
                $notifikasi = 'Email sertifikat gagal dikirim!';
                return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'sertifikat'])
                        ->with([
                            'error' => $notifikasi,
                            'page' => 'peserta'
                        ]);
            }
        }
    }

    public function postUpload(Request $request, $id)
    {
        $validator = $request->validate([
            'jadwal_id' => 'required',
            'file' => 'required|mimetypes:application/pdf|max:1024'
        ]);

        $input = $request->all();
        $sertifikat = DB::table('sertifikat_peserta')->where('id', $id)->first();
        $file_old = null;

        DB::beginTransaction();

        try
        {
            $updated_at = date('Y-m-d H:i:s');
            if(!is_null($sertifikat->upload))
                $file_old = $sertifikat->upload;

            $file = $input['file'];
            $nama_file = time()."_".$file->getClientOriginalName();
            $path = $input['file']->storeAs('public/files/sertifikat/upload', $nama_file);

            DB::table('sertifikat_peserta')->where('id', $id)->update([
                'upload' => $path,
                'updated_at' => $updated_at
            ]);
        }
        catch (\Exception $e)
        {
            DB::rollback();
            throw $e;
        }

        DB::commit();

        if(!is_null($file_old))
            Storage::delete($file_old);

        $notifikasi = 'Upload sertifikat berhasil!';

        $jadwal = DB::table('v_jadwal_detail')->where('id', $input['jadwal_id'])->first();

        return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'sertifikat'])
                    ->with([
                        'success' => $notifikasi,
                    ]);
    }

    public function kirimSimpeg(Request $request)
    {
        $validator = $request->validate([
            'jadwal_id' => 'required',
            'jenis' => 'required',
            'kategori' => 'required',
            'sub_kategori' => 'required_if:jenis,==,2',
        ]);

        $input = $request->all();
        $jadwal = DB::table('v_jadwal_detail')->where('id', $input['jadwal_id'])->first();
        $sertifikat = DB::table('sertifikat')->where('diklat_jadwal_id', $input['jadwal_id'])->first();
        $peserta = DB::table('v_sertifikat')
                    ->where('sertifikat_id', $sertifikat->id)
                    ->whereNull('simpeg_at')
                    ->get();
        $instansi = DB::table('instansi')->find(1);
        $jenis = $input['jenis'];
        $kategori = $input['kategori'];
        $sub = null;
        if(!empty($input['sub_kategori']))
            $sub = $input['sub_kategori'];

        try
        {
            foreach($peserta as $pes)
            {
                if($sertifikat->is_upload && is_null($pes->upload))
                    continue;

                if($pes->instansi != $instansi->nama || !is_null($pes->simpeg_at) || $pes->status_asn != 1)
                    continue;

                $url_sertifikat = route('sertifikat.show', [
                    'peserta' => $pes->id,
                    'jadwal' => $pes->diklat_jadwal_id,
                    'sertifikat' => $pes->spid,
                    'email' => str_slug($pes->email)
                ]);

                $job = new UploadSimpegJob($pes, $jadwal, $sertifikat, $jenis, $kategori, $sub, $url_sertifikat);
                $this->dispatch($job);

                // $at = date('Y-m-d H:i:s');
                // DB::table('sertifikat_peserta')->where('id', $pes->spid)->update([
                //     'simpeg_at' => $at
                // ]);
            }

            $notifikasi = 'Data SIMASN berhasil dikirim!';

            return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'sertifikat'])
                        ->with([
                            'success' => $notifikasi,
                        ]);
        }
        catch(\Exception $e)
        {
            dd($e);
            $notifikasi = 'Data SIMASN gagal dikirim!';
            return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'sertifikat'])
                    ->with([
                        'error' => $notifikasi,
                        'page' => 'peserta'
                    ]);
        }
    }

    public function destroy($id)
    {
        $sertifikat = DB::table('sertifikat')->where('id', '=', $id)->first();
        $jadwal = DB::table('v_jadwal_detail')->where('id', $sertifikat->diklat_jadwal_id)->first();

        DB::beginTransaction();

        try
        {
            $email = DB::table('sertifikat_email')->where('sertifikat_id', '=', $id)->delete();
            $peserta = DB::table('sertifikat_peserta')->where('sertifikat_id', '=', $id)->delete();
            $sertifikat = DB::table('sertifikat')->where('id', '=', $id)->delete();
        }
        catch (\Exception $e)
        {
            DB::rollback();
            $notifikasi = 'Hapus sertifikat gagal!';
            return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'sertifikat'])
                    ->with([
                        'error' => $notifikasi,
                    ]);
        }

        DB::commit();

        $notifikasi = 'Hapus sertifikat berhasil!';

        return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'sertifikat'])
                    ->with([
                        'success' => $notifikasi,
                    ]);
    }

    public function simasnKategori($id)
    {
        $client = new Client(['http_errors' => false, 'verify' => false]);
        $headers = [
            'Authorization' => 'Bearer ' . env('SIMASN_BEARER')
        ];

        $request = $client->get(env('SIMASN_KATEGORI'), ['headers' => $headers, 'timeout' => 120]);

        if($request->getStatusCode() == 200)
        {
            $result = $request->getBody();
            $json = json_decode($result->getContents(), true);
            $data = $json['data'];
            $selected = [];

            foreach($data as $d)
            {
                if($d['jenis_diklat_id'] === $id)
                    array_push($selected, $d);
            }

            return response()->json($selected);
        }

        return response()->json(['error' => 'Unable to communicate'], 500);
    }

    public function simasnSubKategori()
    {
        $client = new Client(['http_errors' => false, 'verify' => false]);
        $headers = [
            'Authorization' => 'Bearer ' . env('SIMASN_BEARER')
        ];

        $request = $client->get(env('SIMASN_SUBKATEGORI'), ['headers' => $headers, 'timeout' => 120]);

        if($request->getStatusCode() == 200)
        {
            $result = $request->getBody();
            $json = json_decode($result->getContents(), true);
            $data = $json['data'];
            return response()->json($data);
        }

        return response()->json(['error' => 'Unable to communicate'], 500);
    }

    public function templatePeserta(Request $request)
    {
        $sertifikat = DB::table('sertifikat')->where('id', $request->sertifikat_id)->first();
        $jadwal = DB::table('v_jadwal_detail')->where('id', $sertifikat->diklat_jadwal_id)->first();
        $peserta = DB::table('v_peserta')
                        ->where('diklat_jadwal_id', $sertifikat->diklat_jadwal_id)
                        ->orderBy('nama_lengkap')
                        ->get();

        $kolom = 'UNIT KERJA';

        if (strpos($jadwal->nama, 'DPRD') !== false)
            $kolom = "PARTAI"; // Jika mengandung "DPRD"

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();

        // Set document properties
        $spreadsheet->getProperties()->setCreator('BPSDM Prov. Kaltim')
                    ->setLastModifiedBy('BPSDM Prov. Kaltim')
                    ->setTitle('Office XLS Document')
                    ->setSubject('Office XLS Document')
                    ->setCategory('Pelatihan');

        $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'NO')
                    ->setCellValue('B1', 'PESERTA_ID')
                    ->setCellValue('C1', 'NAMA')
                    ->setCellValue('D1', $kolom)
                    ->setCellValue('E1', 'INSTANSI')
                    ->setCellValue('F1', 'NOMOR SERTIFIKAT');

        $no = 1;
        $row = 2;

        foreach ($peserta as $p)
        {
            $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue('A'.$row, $no++)
                        ->setCellValue('B'.$row, $p->id)
                        ->setCellValue('C'.$row, $p->nama_lengkap)
                        ->setCellValue('D'.$row, $p->satker_nama)
                        ->setCellValue('E'.$row, $p->instansi)
                        ->setCellValue('F'.$row, '');
            $row++;
        }

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="template-peserta-'. time() . '.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');


        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    public function importPeserta(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required',
            'import_file' => 'required|mimes:xlsx,xls',
        ]);

        $created_at = date('Y-m-d H:i:s');
        $jadwal = DB::table('v_jadwal_detail')->find($request->jadwal_id);
        $sertifikat = DB::table('sertifikat')->where('diklat_jadwal_id', $request->jadwal_id)->first();
        $tahun = date('Y', strtotime($sertifikat->tanggal));

        $kiri = null;
        $bawah = null;
        if(!is_null($sertifikat->tsid))
        {
            $template = DB::table('sertifikat_template')->where('id', $sertifikat->tsid)->first();
            $kiri = $template->spesimen_kiri;
            $bawah = $template->spesimen_bawah;
        }

        $result = DB::table('sertifikat_peserta')
                        ->where('tahun', $tahun)
                        ->where('bidang', $jadwal->usergroup)
                        ->max('no');

        // Mulai transaksi
        DB::beginTransaction();

        try {
            $spreadsheet = IOFactory::load($request->file('import_file')->getRealPath());
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            // Baca setiap baris (selain header)
            foreach ($sheetData as $index => $row) {
                // Skip header (dianggap sebagai baris pertama)
                if ($index == 1) continue; // index 1 adalah header

                $peserta = DB::table('v_peserta')
                                ->where('id', $row['B'])
                                ->where('diklat_jadwal_id', $request->jadwal_id)
                                ->exists();

                if($peserta)
                {
                    $no = ++$result;

                    DB::table('sertifikat_peserta')->insert([
                        'no' => $no,
                        'peserta_id' => $row['B'],
                        'sertifikat_id' => $sertifikat->id,
                        'nomor' => $row['F'],
                        'tahun' => $tahun,
                        'bidang' => $jadwal->usergroup,
                        'spesimen_kiri' => $kiri,
                        'spesimen_bawah' => $bawah,
                        'created_at' => $created_at,
                    ]);
                }
                else
                {
                    throw new \Exception('Simpan data sertifikat gagal, data peserta tidak sesuai!');
                }
            }

            // Semua sukses, commit transaksi
            DB::commit();

            return redirect()->back()->with('success', 'Data peserta berhasil diimpor!');
        } catch (\Exception $e) {
            // Rollback jika ada kesalahan
            DB::rollBack();

            return redirect()->back()->with('error', 'Terjadi masalah saat mengimpor data: ' . $e->getMessage());
        }
    }

    public function edit($id, $jadwal)
    {
        $jadwalController = new JadwalController();
        $jadwalController->tahun = $this->tahun;
        $jadwalController->checkAuth($jadwal);

        $sertifikat = DB::table('sertifikat')
                        ->where('id', $id)
                        ->where('diklat_jadwal_id', $jadwal)
                        ->first();
        $jadwal = DB::table('diklat_jadwal')->where('id', $jadwal)->first();
        $template = DB::table('sertifikat_template')->where('is_tampil', true)->orderBy('nama')->get();
        $detail = 'Edit Sertifikat';

        if(is_null($sertifikat) || $sertifikat->is_final)
            abort(404);

        return view('backend.diklat.sertifikat.edit', compact('jadwal', 'sertifikat', 'template', 'detail'));
    }

    public function update($id, Request $request)
    {
        $is_generate = $request->is_generate;
        $sertifikat = DB::table('sertifikat')
                            ->where('id', $id)
                            ->first();

        if($is_generate)
        {
            $validator = $request->validate([
                'tsid' => 'required',
                'is_generate' => 'required',
                'is_upload' => 'required',
                'is_import' => 'required',
                'is_final' => 'required',
                'tempat' => 'required',
                'tanggal' => 'required|date',
                'barcode' => 'required_if:is_generate,1',
                'kualifikasi' => 'required_if:is_generate,1',
                // 'format_nomor' => 'required_if:is_generate,1',
                'jabatan' => 'required_if:is_generate,1',
                'nip' => 'required_if:is_generate,1|min:18|max:18',
                'nama' => 'required_if:is_generate,1',
                'pangkat' => 'required_if:is_generate,1',
                'spesimen' => 'nullable|mimes:png|max:512'
            ]);
        }
        else
        {
            $validator = $request->validate([
                'is_generate' => 'required',
                'is_upload' => 'required|gt:0',
                'is_import' => 'required',
                'is_final' => 'required',
                'tempat' => 'required',
                'tanggal' => 'required|date',
            ]);
        }

        $input = $request->all();
        $spesimen_path = null;
        $spesimen2_path = null;

        DB::beginTransaction();

        try
        {
            $at = date('Y-m-d H:i:s');
            if($request->has('spesimen'))
            {
                if ($sertifikat->spesimen)
                    Storage::delete($sertifikat->spesimen);

                $file = $input['spesimen'];
                $nama_file = time()."_".$file->getClientOriginalName();
                $spesimen_path = $input['spesimen']->storeAs('public/files/spesimen', $nama_file);
            }
            if($request->has('spesimen2'))
            {
                if ($sertifikat->spesimen2)
                    Storage::delete($sertifikat->spesimen2);

                $file = $input['spesimen2'];
                $nama_file2 = time()."_".$file->getClientOriginalName();
                $spesimen2_path = $input['spesimen2']->storeAs('public/files/spesimen', $nama_file2);
            }

            $sertifikatData = [
                'tsid' => $input['tsid'],
                'fasilitasi' => $input['fasilitasi'],
                'is_generate' => $input['is_generate'],
                'is_upload' => $input['is_upload'],
                'is_final' => $input['is_final'],
                'barcode' => $input['barcode'],
                'kualifikasi' => $input['kualifikasi'],
                'import' => $input['is_import'],
                'format_nomor' => $input['format_nomor'],
                'tempat' => $input['tempat'],
                'tanggal' => $input['tanggal'],
                'jabatan' => $input['jabatan'],
                'nip' => $input['nip'],
                'nama' => $input['nama'],
                'pangkat' => $input['pangkat'],
                'jabatan2' => $input['jabatan2'],
                'nip2' => $input['nip2'],
                'nama2' => $input['nama2'],
                'pangkat2' => $input['pangkat2'],
                'updated_at' => $at,
            ];

            if (!empty($spesimen_path)) {
                $sertifikatData['spesimen'] = $spesimen_path; // Tambahkan spesimen jika ada
            }

            if (!empty($spesimen2_path)) {
                $sertifikatData['spesimen2'] = $spesimen2_path; // Tambahkan spesimen2 jika ada
            }

            // Simpan data sertifikat
            DB::table('sertifikat')->where('id', $id)->update($sertifikatData);
        }
        catch (\Exception $e)
        {
            DB::rollback();
            throw $e;
        }

        DB::commit();

        $notifikasi = 'Data sertifikat berhasil disimpan!';

        $jadwal = DB::table('v_jadwal_detail')->where('id', $sertifikat->diklat_jadwal_id)->first();

        return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'sertifikat'])
                    ->with([
                        'success' => $notifikasi,
                    ]);
    }

    function get_content($URL){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $URL);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
  }
}
