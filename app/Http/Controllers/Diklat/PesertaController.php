<?php

namespace App\Http\Controllers\Diklat;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Jobs\EmailVerifikasiStatusJob;
use App\Mail\DaftarMailable;
use App\Mail\VerifikasiStatusMailable;
use App\Jobs\EmailKonfirmasiJob;
use Gate;
use Session;
use Storage;
use ZipArchive;

class PesertaController extends Controller
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

    }

    public function create($id, $slug)
    {
        $sertifikat = DB::table('sertifikat')
                        ->where('diklat_jadwal_id', $id)
                        ->first();
        if($sertifikat)
            abort(403);

        $this->checkAuth($id);

        $jadwal = DB::table('diklat_jadwal')->where('id', $id)->first();
        $pangkat = DB::table('pangkat')->get();
        $agama = DB::table('agama')->get();
        $instansi = DB::table('instansi')->get();

        if(!$jadwal->registrasi_lengkap)
            return view('backend.diklat.peserta.create_simple', compact('jadwal', 'instansi'));

        return view('backend.diklat.peserta.create', compact('jadwal', 'pangkat', 'agama', 'instansi'));
    }

    public function store(Request $request, $id)
    {
        $validator = $request->validate([
            'foto' => 'mimetypes:image/jpeg,image/png|max:512',
            // 'nip' => 'required|min:18|max:18',
            // 'ktp' => 'required|min:16|max:16',
            'nama_lengkap' => 'required',
            // 'nama_panggil' => 'required',
            // 'alamat' => 'required',
            'jk' => 'required',
            'tmp_lahir' => 'required',
            'tgl_lahir' => 'required|date',
            // 'agama' => 'required',
            // 'marital' => 'required',
            'hp' => 'required',
            'email' => 'required|email',
            // 'pangkat' => 'required',
            'jabatan' => 'required',
            'instansi' => 'required',
            'satker_nama' => 'required',
            // 'satker_alamat' => 'required',
            // 'satker_telp' => 'required',
            // 'verifikasi' => 'required',
            // 'batal' => 'required',
            // 'batal_ket' => 'required_if:batal,==,1'
        ]);

        try
        {
            $this->checkAuth($id);

            $jadwal = DB::table('diklat_jadwal')->where('id', $id)->first();
            $bulan = date('m');
            $tahun = date('Y');
            $created_at = date('Y-m-d H:i:s');
            $path = null;

            $result = DB::table('peserta')
                        ->whereMonth('created_at', '=', $bulan)
                        ->whereYear('created_at', '=', $tahun)
                        ->count();

            $kode = "R" . sprintf("%s%02s%04s", $tahun, $bulan, ++$result);

            if(isset($request->foto))
            {
                $foto = $request->file('foto');
                $nama_file = time()."_".$foto->getClientOriginalName();
                $path = $request->foto->storeAs('public/files/photo/peserta', $nama_file);
            }

            DB::table('peserta')->insert([
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
                'foto' => $path,
                'instansi' => $request->instansi,
                'satker_nama' => $request->satker_nama,
                'satker_alamat' => $request->satker_alamat,
                'satker_telp' => $request->satker_telp,
                'diklat_jadwal_id' => $jadwal->id,
                'verifikasi' => true,
                'konfirmasi' => true,
                // 'batal' => $request->batal,
                // 'batal_ket' => $request->batal_ket,
                'status_asn' => $request->status_asn,
                'created_at' => $created_at,
            ]);

            $notifikasi = 'Data peserta berhasil ditambahkan!';

            if(isset($request->add))
                return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'peserta'])
                        ->with([
                            'success' => $notifikasi,
                            'page' => 'peserta'
                        ]);

            return redirect()->back()->with('success', $notifikasi);
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data peserta gagal ditambahkan!';
            return redirect()->back()->with('error', $notifikasi);
        }
    }

    public function storeSimple(Request $request, $id)
    {
        $validator = $request->validate([
            'status_asn' => 'required',
            'nama_lengkap' => 'required',
            'jk' => 'required',
            'hp' => 'required',
            'email' => 'required|email',
            'jabatan' => 'required',
            'instansi' => 'required',
            'sebagai' => 'required',
        ]);

        try
        {
            $this->checkAuth($id);

            $jadwal = DB::table('diklat_jadwal')->where('id', $id)->first();
            $peserta = DB::table('peserta')
                    ->where('email', $request->email)
                    ->where('diklat_jadwal_id', $jadwal->id)
                    ->first();

            if(!empty($peserta))
            {
                $notifikasi = 'Email Anda telah terdaftar untuk mengikuti kegiatan ini!';
                return redirect()->back()->with('error', $notifikasi);
            }

            $bulan = date('m');
            $tahun = date('Y');
            $created_at = date('Y-m-d H:i:s');

            $result = DB::table('peserta')
                        ->whereMonth('created_at', '=', $bulan)
                        ->whereYear('created_at', '=', $tahun)
                        ->count();

            $kode = "R" . sprintf("%s%02s%04s", $tahun, $bulan, ++$result);

            DB::table('peserta')->insert([
                'kode' => $kode,
                'nip' => $request->nip,
                'nama_lengkap' => $request->nama_lengkap,
                'jk' => $request->jk,
                'hp' => $request->hp,
                'email' => $request->email,
                'jabatan' => $request->jabatan,
                'instansi' => $request->instansi,
                'diklat_jadwal_id' => $jadwal->id,
                'verifikasi' => true,
                'konfirmasi' => true,
                'status_asn' => $request->status_asn,
                'sebagai' => $request->sebagai,
                'created_at' => $created_at,
            ]);

            $notifikasi = 'Data peserta berhasil ditambahkan!';

            if(isset($request->add))
                return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'peserta'])
                        ->with([
                            'success' => $notifikasi,
                            'page' => 'peserta'
                        ]);

            return redirect()->back()->with('success', $notifikasi);
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data peserta gagal ditambahkan!';
            return redirect()->back()->with('error', $notifikasi);
        }
    }

    public function show($id, $slug)
    {

    }

    public function edit($jadwal, $slug, $id)
    {
        $sertifikat = DB::table('sertifikat')
                        ->where('diklat_jadwal_id', $jadwal)
                        ->first();
        if($sertifikat)
            abort(403);

        $this->checkAuth($jadwal);

        $jadwal = DB::table('diklat_jadwal')->where('id', $jadwal)->first();
        $peserta = DB::table('peserta')->where('id', $id)->first();
        $pangkat = DB::table('pangkat')->get();
        $agama = DB::table('agama')->get();
        $instansi = DB::table('instansi')->get();

        if(!$jadwal->registrasi_lengkap)
            return view('backend.diklat.peserta.edit_simple', compact('jadwal', 'peserta', 'instansi'));

        return view('backend.diklat.peserta.edit', compact('jadwal', 'peserta', 'pangkat', 'agama', 'instansi'));
    }

    public function update(Request $request, $id)
    {
        $validator = $request->validate([
            'foto' => 'mimetypes:image/jpeg,image/png|max:512',
            // 'nip' => 'required|min:18|max:18',
            // 'ktp' => 'required|min:16|max:16',
            'nama_lengkap' => 'required',
            // 'nama_panggil' => 'required',
            // 'alamat' => 'required',
            'status_asn' => 'required',
            'jk' => 'required',
            'tmp_lahir' => 'required',
            'tgl_lahir' => 'required|date',
            // 'agama' => 'required',
            // 'marital' => 'required',
            'hp' => 'required',
            'email' => 'required|email',
            // 'pangkat' => 'required',
            'jabatan' => 'required',
            'instansi' => 'required',
            'satker_nama' => 'required',
            // 'satker_alamat' => 'required',
            // 'satker_telp' => 'required',
            'verifikasi' => 'required',
            'batal' => 'required',
            'batal_ket' => 'required_if:batal,==,1'
        ]);

        try
        {
            $peserta = DB::table('peserta')->where('id', $id)->first();
            $this->checkAuth($peserta->diklat_jadwal_id);
            $jadwal = DB::table('diklat_jadwal')->where('id', $peserta->diklat_jadwal_id)->first();
            $bulan = date('m');
            $tahun = date('Y');
            $updated_at = date('Y-m-d H:i:s');
            $path = null;

            if(isset($request->foto))
            {
                $foto = $request->file('foto');
                $nama_file = time()."_".$foto->getClientOriginalName();
                $path = $request->foto->storeAs('public/files/photo/peserta', $nama_file);
            }
            else
            {
                $path = $request->foto_lama;
            }

            DB::table('peserta')->where('id', $id)->update([
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
                'foto' => $path,
                'instansi' => $request->instansi,
                'satker_nama' => $request->satker_nama,
                'satker_alamat' => $request->satker_alamat,
                'satker_telp' => $request->satker_telp,
                'verifikasi' => $request->verifikasi,
                'batal' => $request->batal,
                'batal_ket' => $request->batal_ket,
                'status_asn' => $request->status_asn,
                'updated_at' => $updated_at,
            ]);

            if(isset($request->foto))
                \Storage::delete($request->foto_lama);

            $notifikasi = 'Data peserta berhasil diubah!';

            return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'peserta'])
                    ->with([
                        'success' => $notifikasi,
                        'page' => 'peserta'
                    ]);
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data peserta gagal diubah!';
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function updateSimple(Request $request, $id)
    {
        $validator = $request->validate([
            'status_asn' => 'required',
            'nama_lengkap' => 'required',
            'jk' => 'required',
            'hp' => 'required',
            'email' => 'required|email',
            'jabatan' => 'required',
            'instansi' => 'required',
            'sebagai' => 'required',
        ]);

        try
        {
            $peserta = DB::table('peserta')->where('id', $id)->first();
            $this->checkAuth($peserta->diklat_jadwal_id);
            $jadwal = DB::table('diklat_jadwal')->where('id', $peserta->diklat_jadwal_id)->first();
            $updated_at = date('Y-m-d H:i:s');

            DB::table('peserta')->where('id', $id)->update([
                'nip' => $request->nip,
                'nama_lengkap' => $request->nama_lengkap,
                'jk' => $request->jk,
                'hp' => $request->hp,
                'email' => $request->email,
                'jabatan' => $request->jabatan,
                'instansi' => $request->instansi,
                'status_asn' => $request->status_asn,
                'sebagai' => $request->sebagai,
                'updated_at' => $updated_at,
            ]);

            $notifikasi = 'Data peserta berhasil diubah!';

            return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'peserta'])
                    ->with([
                        'success' => $notifikasi,
                        'page' => 'peserta'
                    ]);
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data peserta gagal diubah!';
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $peserta = DB::table('peserta')->where('id', $id)->first();
        $this->checkAuth($peserta->diklat_jadwal_id);
        $jadwal = DB::table('diklat_jadwal')->where('id', $peserta->diklat_jadwal_id)->first();

        $delete = DB::table('peserta')->where('id', $id)->delete();

        if($delete)
        {
            $notifikasi = 'Data peserta berhasil dihapus!';
            return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'peserta'])
                    ->with([
                        'success' => $notifikasi,
                        'page' => 'peserta'
                    ]);
        }

        $notifikasi = 'Data peserta gagal dihapus!';
        return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'peserta'])
                ->with([
                    'error' => $notifikasi,
                    'page' => 'peserta'
                ]);
    }

    public function verifikasi(Request $request, $id)
    {
        $peserta = DB::table('peserta')->where('id', $id)->first();
        $this->checkAuth($peserta->diklat_jadwal_id);
        $jadwal = DB::table('v_jadwal_detail')->where('id', $peserta->diklat_jadwal_id)->first();
        $verifikasi = $request->setuju;

        try
        {
            switch($verifikasi)
            {
                case 1:
                    $notifikasi = 'Data verifikasi peserta telah disetujui!';
                    $status = 'Disetujui';
                    break;
                case 2:
                    $notifikasi = 'Data verifikasi peserta telah ditolak!';
                    $status = 'Ditolak';
                    break;
                default:
                    $verifikasi = 0;
                    $notifikasi = 'Data verifikasi peserta tidak ada perubahan!';
                    break;
            }

            DB::table('peserta')->where('id', $id)->update([
                'verifikasi' => $verifikasi,
            ]);

            if($jadwal->registrasi && $verifikasi != 0)
            {
                // Mail::to($peserta->email)->send(new VerifikasiStatusMailable($peserta->nama_lengkap, $jadwal, $status));
                $job = new EmailVerifikasiStatusJob($peserta->nama_lengkap, $peserta->email, $jadwal, $status);
                $this->dispatch($job);
            }

            return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'peserta'])
                    ->with([
                        'success' => $notifikasi,
                        'page' => 'peserta'
                    ]);
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data peserta gagal diverifikasi!';
            return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'peserta'])
                    ->with([
                        'error' => $notifikasi,
                        'page' => 'peserta'
                    ]);
        }
    }

    public function konfirmasi(Request $request, $id)
    {
        $peserta = DB::table('peserta')->where('id', $id)->first();
        $this->checkAuth($peserta->diklat_jadwal_id);
        $jadwal = DB::table('v_jadwal_detail')->where('id', $peserta->diklat_jadwal_id)->first();
        $konfirmasi = $request->konfirmasi;

        try
        {
            switch($konfirmasi)
            {
                case 1:
                    DB::table('peserta')->where('id', $id)->update([
                        'konfirmasi' => true,
                    ]);
                    $notifikasi = 'Konfirmasi manual peserta telah berhasil!';
                    break;
                case 2:
                    $url = \URL::signedRoute('jadwal.konfirmasi', $id);
                    // Mail::to($peserta->email)->send(new DaftarMailable($peserta->nama_lengkap, $url));
                    $job = new EmailKonfirmasiJob($peserta->nama_lengkap, $peserta->email, $jadwal, $url);
                    $this->dispatch($job);
                    $notifikasi = 'Email konfirmasi telah dikirim ulang ke peserta!';
                    break;
            }

            return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'peserta'])
                    ->with([
                        'success' => $notifikasi,
                        'page' => 'peserta'
                    ]);
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Konfirmasi manual peserta gagal!';
            return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'peserta'])
                    ->with([
                        'error' => $notifikasi,
                        'page' => 'peserta'
                    ]);
        }
    }

    public function batal(Request $request, $id)
    {
        $peserta = DB::table('peserta')->where('id', $id)->first();
        $this->checkAuth($peserta->diklat_jadwal_id);
        $jadwal = DB::table('diklat_jadwal')->where('id', $peserta->diklat_jadwal_id)->first();
        $verifikasi = $request->setuju;

        try
        {
            DB::table('peserta')->where('id', $id)->update([
                'batal' => true,
                'batal_ket' => $request->batal_ket,
            ]);

            $notifikasi = 'Data batal peserta telah tersimpan!';

            return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'peserta'])
                    ->with([
                        'success' => $notifikasi,
                        'page' => 'peserta'
                    ]);
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data batal peserta gagal disimpan!';
            return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'peserta'])
                    ->with([
                        'error' => $notifikasi,
                        'page' => 'peserta'
                    ]);
        }
    }

    public function export(Request $request)
    {
        $this->checkAuth($request->jadwal_id);

        $export = $request->export;

        switch($export)
        {
            case 1:
                $filename = './templates/excel/peserta.xlsx';

				$peserta = DB::table('v_peserta')
								->where('diklat_jadwal_id', $request->jadwal_id)
								->get();

				// Create new Spreadsheet object
                // $spreadsheet = new Spreadsheet();
                $spreadsheet = IOFactory::load($filename);

				// Set document properties
				// $spreadsheet->getProperties()->setCreator('BPSDM Prov. Kaltim')
				// 			->setLastModifiedBy('BPSDM Prov. Kaltim')
				// 			->setTitle('Office XLS Document')
				// 			->setSubject('Office XLS Document')
				// 			->setCategory('Pelatihan');

				// $spreadsheet->setActiveSheetIndex(0)
				// 			->setCellValue('A1', 'nama {diisi dengan nama peserta}')
				// 			->setCellValue('B1', 'no_identitas {Diisi dengan NIP/NRP/NPP/NIK}')
				// 			->setCellValue('C1', 'jenis_kelamin (Pilih Satu)')
				// 			->setCellValue('D1', 'agama (Pilih Satu)')
				// 			->setCellValue('E1', 'tempat_lahir {Diisi nama kota lahir}')
				// 			->setCellValue('F1', 'tgl_lahir ("dd-mm-yyyy")')
				// 			->setCellValue('G1', 'email {Diisi dengan alamat email }')
				// 			->setCellValue('H1', 'no_hp / telp kantor {diisi nomor telepon}')
				// 			->setCellValue('I1', 'jenis_peserta (Pilih Satu)')
				// 			->setCellValue('J1', 'gol (Pilih Satu)')
				// 			->setCellValue('K1', 'pangkat (Pilih Satu)')
				// 			->setCellValue('L1', 'jabatan {Diisi jabatan terakhir}')
				// 			->setCellValue('M1', 'pola_penyelenggaraan (Pilih Satu)')
				// 			->setCellValue('N1', 'sumber_anggaran (Pilih Satu)')
				// 			->setCellValue('O1', 'Instansi (Pilih Satu)')
                //             ->setCellValue('P1', 'Alamat Instansi');

                // $spreadsheet->setActiveSheetIndex(0)
				// 			->setCellValue('A2', '{contoh : Ahmad Bustomi S.Kom}}')
				// 			->setCellValue('B2', '{NIP/NRP/NPP/NIK}')
				// 			->setCellValue('C2', 'Wanita')
				// 			->setCellValue('D2', 'Islam')
				// 			->setCellValue('E2', '{contoh : Jakarta}')
				// 			->setCellValue('F2', '{tanggal format"dd-mm-yyyy" contoh: 13-06-2020}')
				// 			->setCellValue('G2', '{email peserta, contoh: lan@lan.go.id}')
				// 			->setCellValue('H2', '{no telp}')
				// 			->setCellValue('I2', 'PNS')
				// 			->setCellValue('J2', 'IV/B')
				// 			->setCellValue('K2', 'Pembina Tingkat I')
				// 			->setCellValue('L2', '{Jabatan terakhir}')
				// 			->setCellValue('M2', 'Mandiri')
				// 			->setCellValue('N2', 'APBN')
				// 			->setCellValue('O2', 'Lembaga Administrasi Negara')
                //             ->setCellValue('P2', '{Alamat Instansi}')
                //             ->setCellValue('Q2', '<= Baris tidak perlu dihapus, daftar dapat langsung ditambahkan di bawahnya dengan meng copy baris ini');

				$row = 3;

				foreach ($peserta as $p)
				{
					// $spreadsheet->getActiveSheet()->getStyle('G'.$row)
					// 			->setQuotePrefix(true);

					// $spreadsheet->getActiveSheet()->getStyle('G'.$row)
					// 			->getNumberFormat()
					// 			->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

					$spreadsheet->setActiveSheetIndex(0)
								->setCellValue('A'.$row, $p->nama_lengkap)
                                ->setCellValueExplicit('B'.$row, $p->nip, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)
								->setCellValue('C'.$row, ($p->jk == 'P' ? 'Wanita' : 'Pria'))
								->setCellValue('D'.$row, $p->agama)
								->setCellValue('E'.$row, $p->tmp_lahir)
								->setCellValue('F'.$row, date("d-m-Y", strtotime($p->tgl_lahir)))
								->setCellValue('G'.$row, $p->email)
								->setCellValue('H'.$row, $p->hp)
								->setCellValue('I'.$row, 'PNS')
								->setCellValue('J'.$row, $p->golongan)
								->setCellValue('K'.$row, $p->pangkat)
								->setCellValue('L'.$row, $p->jabatan)
								->setCellValue('M'.$row, 'Mandiri')
								->setCellValue('N'.$row, 'APBD')
								->setCellValue('O'.$row, $p->instansi)
								->setCellValue('P'.$row, $p->satker_alamat);
					$row++;
				}

				// Rename worksheet
				// $spreadsheet->getActiveSheet()->setTitle('Report Excel '.date('d-m-Y H'));
				// $spreadsheet->getActiveSheet()->setTitle('Daftar Peserta');

				// for($col='A'; $col<='R';$col++)
				// {
				// 	$spreadsheet->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
				// 	$spreadsheet->getActiveSheet()->getStyle($col.'1')->getFont()->setBold(true);
				// }

				// $spreadsheet->getActiveSheet()->getPageSetup()
				// 			->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
				// $spreadsheet->getActiveSheet()->getPageSetup()
				// 			->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

				// $spreadsheet->getActiveSheet()->getPageMargins()->setTop(1);
				// $spreadsheet->getActiveSheet()->getPageMargins()->setRight(1);
				// $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(1);
				// $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(1);

				// $spreadsheet->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);

				// // Set active sheet index to the first sheet, so Excel opens this as the first sheet
				// $spreadsheet->setActiveSheetIndex(0);

				// Redirect output to a client’s web browser (Xlsx)
                // header('Content-Type: application/vnd.ms-excel');
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="pelatihan-'. time() . '.xlsx"');
				header('Cache-Control: max-age=0');
				// If you're serving to IE 9, then the following may be needed
				// header('Cache-Control: max-age=1');

				// If you're serving to IE over SSL, then the following may be needed
				// header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
				// header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
				// header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
				// header('Pragma: public'); // HTTP/1.0

				$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
				$writer->save('php://output');
                exit;
            case 2:
                $jadwal = DB::table('v_jadwal_detail')->where('id', $request->jadwal_id)->first();
                $peserta = DB::table('v_peserta')
								->where('diklat_jadwal_id', $request->jadwal_id)
								->get();

				// Create new Spreadsheet object
				$spreadsheet = new Spreadsheet();

				// Set document properties
				$spreadsheet->getProperties()->setCreator('BPSDM Prov. Kaltim')
							->setLastModifiedBy('BPSDM Prov. Kaltim')
							->setTitle('Office XLS Document')
							->setSubject('Office XLS Document')
							->setCategory('Pelatihan');

				$spreadsheet->setActiveSheetIndex(0)
                            ->setCellValue('A1', 'firstname')
                            ->setCellValue('B1', 'lastname')
                            ->setCellValue('C1', 'username')
                            ->setCellValue('D1', 'password')
                            ->setCellValue('E1', 'email');

				$no = 1;
                $row = 2;

                foreach ($peserta as $p)
				{
					$spreadsheet->setActiveSheetIndex(0)
								->setCellValue('A'.$row, $p->nama_lengkap)
								->setCellValue('B'.$row, $jadwal->nama)
								->setCellValueExplicit('C'.$row, $p->nip, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)
								->setCellValueExplicit('D'.$row, $p->nip, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)
								->setCellValue('E'.$row, $p->email);
					$row++;
				}

				// Redirect output to a client’s web browser (Xlsx)
				header('Content-Type: text/csv');
				header('Content-Disposition: attachment;filename="moodle-'. time() . '.csv"');
				header('Cache-Control: max-age=0');
				// If you're serving to IE 9, then the following may be needed
				header('Cache-Control: max-age=1');

				// If you're serving to IE over SSL, then the following may be needed
				header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
				header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
				header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
				header('Pragma: public'); // HTTP/1.0

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
                $writer->setDelimiter(';');
				$writer->setEnclosure('');
				$writer->setLineEnding("\r\n");
				$writer->setSheetIndex(0);
				$writer->save('php://output');
                exit;
            case 3:
                $jadwal = DB::table('v_jadwal_detail')->where('id', $request->jadwal_id)->first();
                $peserta = DB::table('v_peserta')
								->where('diklat_jadwal_id', $request->jadwal_id)
                                ->get();

                $zip = new ZipArchive;
                $fileName = storage_path() . DIRECTORY_SEPARATOR . 'foto-' . time() . '.zip';

                $res = $zip->open($fileName, ZipArchive::CREATE | ZipArchive::OVERWRITE);

                if ($res)
                {
                    foreach ($peserta as $p) {
                        if(!empty($p->foto))
                        {
                            $foto = Storage::url($p->foto);
                            $ext = pathinfo($foto, PATHINFO_EXTENSION);
                            $filefoto = $p->nama_lengkap . '.' . $ext;
                            $zip->addFile(public_path($foto), $filefoto);
                        }
                    }

                    $zip->close();
                }

                return response()->download($fileName)->deleteFileAfterSend(true);
        }
        exit;
    }

    public function import(Request $request, $id)
    {
        $this->checkAuth($id);

        $jadwal = DB::table('v_jadwal_detail')->find($id);

        $level = $this->checkLevel();

        $from = [];

        switch($level)
        {
            case 'admin':
                $from = DB::table('v_jadwal_detail')
                            ->where('id', '<>', $id)
                            ->where('tahun', $this->tahun)
                            ->orderby('nama', 'asc')
                            ->get();
                break;

            case 'user':
                $from = DB::table('v_jadwal_detail')
                            ->where('id', '<>', $id)
                            ->where('tahun', $this->tahun)
                            ->where('usergroup', $this->user->usergroup)
                            ->orderby('nama', 'asc')
                            ->get();
                break;

            case 'kontribusi':
                $instansi = DB::table('instansi')->where('id', $this->user->instansi_id)->first();
                $from = DB::table('v_jadwal_detail')
                            ->where('id', '<>', $id)
                            ->where('tahun', $this->tahun)
                            ->where('kelas', $instansi->nama)
                            ->orderby('nama', 'asc')
                            ->get();
                break;
        }

        return view('backend.diklat.peserta.import', compact('jadwal', 'from'));
    }

    public function importPreview(Request $request, $id)
    {
        $this->checkAuth($id);

        $peserta = DB::table('v_peserta')
                    ->where('diklat_jadwal_id', $request->jadwal_from)
                    ->get();

        $jadwal = DB::table('v_jadwal_detail')->find($id);

        return view('backend.diklat.peserta.import_preview', compact('jadwal', 'peserta'));
    }

    public function importSimpan(Request $request, $id)
    {
        $this->checkAuth($id);

        $pid = $request->pid;

        $created_at = date('Y-m-d H:i:s');
        $bulan = date('m');
        $tahun = date('Y');

        $result = DB::table('peserta')
                    ->whereMonth('created_at', '=', $bulan)
                    ->whereYear('created_at', '=', $tahun)
                    ->count();

        try
        {
            DB::beginTransaction();

            foreach($pid as $p)
            {
                $kode = "R" . sprintf("%s%02s%04s", $tahun, $bulan, ++$result);

                $peserta = DB::table('peserta')->find($p);

                DB::table('peserta')->insert([
                    'kode' => $kode,
                    'nip' => $peserta->nip,
                    'ktp' => $peserta->ktp,
                    'nama_lengkap' => $peserta->nama_lengkap,
                    'nama_panggil' => $peserta->nama_panggil,
                    'tmp_lahir' => $peserta->tmp_lahir,
                    'tgl_lahir' => $peserta->tgl_lahir,
                    'jk' => $peserta->jk,
                    'alamat' => $peserta->alamat,
                    'agama_id' => $peserta->agama_id,
                    'hp' => $peserta->hp,
                    'marital' => $peserta->marital,
                    'email' => $peserta->email,
                    'jabatan' => $peserta->jabatan,
                    'pangkat_id' => $peserta->pangkat_id,
                    'foto' => $peserta->foto,
                    'instansi' => $peserta->instansi,
                    'satker_nama' => $peserta->satker_nama,
                    'satker_alamat' => $peserta->satker_alamat,
                    'satker_telp' => $peserta->satker_telp,
                    'diklat_jadwal_id' => $id,
                    'verifikasi' => $peserta->verifikasi,
                    'konfirmasi' => true,
                    'created_at' => $created_at,
                ]);
            }

            DB::commit();

            $jadwal = DB::table('v_jadwal_detail')->find($id);

            $notifikasi = 'Import peserta berhasil!';

            return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'peserta'])
                    ->with([
                        'success' => $notifikasi,
                        'page' => 'peserta'
                    ]);
        }
        catch(\Exception $e)
        {
            $jadwal = DB::table('v_jadwal_detail')->find($id);

            $notifikasi = 'Import peserta gagal!';

            return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'peserta'])
                    ->with([
                        'error' => $notifikasi,
                        'page' => 'peserta'
                    ]);
        }
    }

    public function konfirmasiJadwal(Request $request)
    {
      $jadwal = DB::table('diklat_jadwal')->where('id', $request->jadwal_id)->first();
      $updated_at = date('Y-m-d H:i:s');

      DB::table('diklat_jadwal')->where('id', $request->jadwal_id)->update([
          'is_konfirmasi' => $request->status,
          'updated_at' => $updated_at,
      ]);

      $notifikasi = 'Konfirmasi kehadiran berhasil ditutup!';

      if($request->status)
        $notifikasi = 'Konfirmasi kehadiran berhasil dibuka!';

      return redirect()->route('backend.diklat.jadwal.detail', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'peserta'])
              ->with([
                  'success' => $notifikasi,
                  'page' => 'peserta'
              ]);
    }

    public function checkAuth($id)
    {
        if($this->isAdmin())
            return true;

        $jadwal = DB::table('v_jadwal_detail')->where('id', $id)
                    ->where('tahun', $this->tahun)
                    ->first();

        if(empty($jadwal))
        {
            abort(404);
        }

        if(Gate::allows('isCreator', $jadwal) && Auth::user()->instansi_id == 1)
        {
            return true;
        }
        else
        {
            if(Gate::allows('isKelasKontribusi', $jadwal) && $jadwal->status_jadwal < 3)
                return true;
        }

        abort(403);
    }
}
