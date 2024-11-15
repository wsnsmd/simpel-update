<?php

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\RandomColor;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
$admin_path = config('app.admin_path');

// Authentication Routes
Route::get($admin_path . '/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post($admin_path . '/login', 'Auth\LoginController@login')->middleware('throttle:5,1');;
Route::post($admin_path . '/logout', 'Auth\LoginController@logout')->name('logout');
Route::get($admin_path . '/reload-captcha', 'Auth\LoginController@reloadCaptcha')->name('reload.captcha');
// Authentication Routes

// Frontend Routes
Route::resource('/', 'BerandaController', [
    'only' => ['index']
]);

Route::resource('/jadwal', 'JadwalController', [
    'only' => ['index']
]);

Route::post('/jadwal', 'JadwalController@cari')->name('jadwal.cari');
Route::get('/jadwal/{jadwal}/{slug}/detail', 'JadwalController@detail')->name('jadwal.detail');
Route::get('/jadwal/widyaiswara', 'JadwalController@wi')->name('jadwal.wi');
Route::post('/jadwal/widyaiswara', 'JadwalController@postWi')->name('jadwal.wi.post');
Route::post('/daftar', 'JadwalController@daftar')->name('jadwal.daftar');
Route::get('/daftar/step1', 'JadwalController@step1')->name('jadwal.daftar.step1');
Route::post('/daftar/step1', 'JadwalController@poststep1')->name('jadwal.daftar.step1');
Route::get('/daftar/step2', 'JadwalController@step2')->name('jadwal.daftar.step2');
Route::post('/daftar/step2', 'JadwalController@poststep2')->name('jadwal.daftar.step2');
Route::post('/daftar/step2simple', 'JadwalController@poststep2simple')->name('jadwal.daftar.step2.simple');
Route::get('/daftar/step3', 'JadwalController@step3')->name('jadwal.daftar.step3');
Route::post('/daftar/step3', 'JadwalController@poststep3')->name('jadwal.daftar.step3');
Route::get('/daftar/konfirmasi/{id}', 'JadwalController@konfirmasi')->name('jadwal.konfirmasi');

Route::resource('/alumni', 'AlumniController', [
    'only' => ['index']
]);

Route::post('/alumni', 'AlumniController@cari')->name('alumni.cari');

Route::get('/send/email', 'HomeController@mail');

Route::get('/sertifikat/{peserta}/{jadwal}/{sertifikat}/{email}', 'SertifikatController@show')->name('sertifikat.show');

// Route::get('/test-finish', 'JadwalController@testFinish');

Route::view('/informasi', 'frontend.informasi')->name('informasi');
// Frontend Routes

// Backend Routes
Route::group(['prefix'=>$admin_path,'as'=>$admin_path.'.'], function () {
    Route::match(['get', 'post'], '/dashboard', function(){
        return view('dashboard');
    })->name('dashboard')->middleware('auth');

    Route::group(['prefix'=>'master','as'=>'master.'], function () {
        Route::resource('agama', 'Master\AgamaController');
        Route::resource('instansi', 'Master\InstansiController');
        Route::post('instansi/sort', 'Master\InstansiController@sort')->name('instansi.sort');
        Route::resource('lokasi', 'Master\LokasiController');
        Route::resource('opd', 'Master\OpdController');
        Route::resource('pangkat', 'Master\PangkatController');
        Route::resource('tahun', 'Master\TahunController');
    });

    Route::group(['prefix'=>'diklat','as'=>'diklat.'], function () {
        // Fasilitator
        Route::resource('fasilitator', 'Diklat\FasilitatorController');

        // Jenis
        Route::resource('jenis', 'Diklat\JenisController');

        // Kurikulum
        Route::resource('kurikulum', 'Diklat\KurikulumController');
        Route::get('kurikulum/{kurikulum}/detail/{slug}', 'Diklat\KurikulumController@detail')->name('kurikulum.detail');
        Route::get('kurikulum/get/{jenisdiklat}', 'Diklat\KurikulumController@getKurikulum')->name('kurikulum.get');

        // Mata Pelatihan
        Route::post('matapelatihan/{kurikulum}', 'Diklat\MapelController@store')->name('mapel.store');
        Route::delete('matapelatihan/{matapelatihan}', 'Diklat\MapelController@destroy')->name('mapel.destroy');
        Route::patch('matapelatihan/{matapelatihan}', 'Diklat\MapelController@update')->name('mapel.update');
        Route::get('matapelatihan/{matapelatihan}', 'Diklat\MapelController@show')->name('mapel.show');
        Route::get('matapelatihan/{jadwal}/{slug}/{matapelatihan}/jadwal', 'Diklat\MapelController@jadwal')->name('mapel.jadwal');
        Route::post('matapelatihan/jadwal/{jadwal}/{mapel}', 'Diklat\MapelController@jadwalStore')->name('mapel.jadwal.store');
        Route::delete('matapelatihan/jadwal/{jadwal}/{mapel}/{id}/hapus', 'Diklat\MapelController@jadwalDestroy')->name('mapel.jadwal.destroy');
        Route::get('matapelatihan/jadwal/{id}/edit', 'Diklat\MapelController@jadwalEdit')->name('mapel.jadwal.edit');
        Route::patch('matapelatihan/jadwal/{id}/update', 'Diklat\MapelController@jadwalUpdate')->name('mapel.jadwal.update');
        Route::post('matapelatihan/jadwal/{jadwal}/{mapel}/widyaiswara', 'Diklat\MapelController@wiStore')->name('mapel.wi.store');
        Route::delete('matapelatihan/jadwal/{jadwal}/{mapel}/widyaiswara/{id}/hapus', 'Diklat\MapelController@wiDestroy')->name('mapel.wi.destroy');
        Route::get('matapelatihan/jadwal/widyaiswara/{id}/edit', 'Diklat\MapelController@wiEdit')->name('mapel.wi.edit');
        Route::patch('matapelatihan/jadwal/widyaiswara/{id}/update', 'Diklat\MapelController@wiUpdate')->name('mapel.wi.update');
        Route::get('matapelatihan/jadwal/widyaiswara/{id}/cetak', 'Diklat\MapelController@wiCetak')->name('mapel.wi.cetak');
        Route::post('matapelatihan/jadwal/{id}/wi/rekap', 'Diklat\MapelController@wiRekap')->name('mapel.wi.rekap');
        Route::get('matapelatihan/jadwal/{jadwal}/tandatangan/edit', 'Diklat\MapelController@TandaTanganEdit')->name('mapel.tt.edit');
        Route::post('matapelatihan/jadwal/{jadwal}/tandatangan/update', 'Diklat\MapelController@TandaTanganUpdate')->name('mapel.tt.update');

        // Jadwal
        Route::resource('jadwal', 'Diklat\JadwalController');
        Route::post('jadwal/index/filter/{id}', 'Diklat\JadwalController@filterindex')->name('jadwal.index.filter');
        Route::get('jadwal/{jadwal}/detail/{slug}', 'Diklat\JadwalController@detail')->name('jadwal.detail');
        Route::post('jadwal/peserta', 'Diklat\JadwalController@peserta')->name('jadwal.peserta');

        // Peserta
        Route::get('peserta/{jadwal}/{slug}/create', 'Diklat\PesertaController@create')->name('peserta.create');
        Route::post('peserta/{jadwal}', 'Diklat\PesertaController@store')->name('peserta.store');
        Route::get('peserta/{jadwal}/{slug}/{peserta}/edit', 'Diklat\PesertaController@edit')->name('peserta.edit');
        Route::patch('peserta/{peserta}', 'Diklat\PesertaController@update')->name('peserta.update');
        Route::delete('peserta/{peserta}', 'Diklat\PesertaController@destroy')->name('peserta.destroy');
        Route::post('peserta/{peserta}/verifikasi', 'Diklat\PesertaController@verifikasi')->name('peserta.verifikasi');
        Route::post('peserta/{peserta}/konfirmasi', 'Diklat\PesertaController@konfirmasi')->name('peserta.konfirmasi');
        Route::post('peserta/{peserta}/batal', 'Diklat\PesertaController@batal')->name('peserta.batal');
        Route::post('peserta/export/data', 'Diklat\PesertaController@export')->name('peserta.export');
        Route::post('peserta/{jadwal}/import', 'Diklat\PesertaController@import')->name('peserta.import');
        Route::post('peserta/{jadwal}/import/preview', 'Diklat\PesertaController@importPreview')->name('peserta.import.preview');
        Route::post('peserta/{jadwal}/import/simpan', 'Diklat\PesertaController@importSimpan')->name('peserta.import.simpan');
        Route::get('peserta/{jadwal}/{slug}/back', 'Diklat\PesertaController@backToJadwal')->name('peserta.back');
        Route::post('peserta/{jadwal}/simple', 'Diklat\PesertaController@storeSimple')->name('peserta.store.simple');
        Route::patch('peserta/{peserta}/simple', 'Diklat\PesertaController@updateSimple')->name('peserta.update.simple');
        Route::post('peserta/konfirmasi/jadwal', 'Diklat\PesertaController@konfirmasiJadwal')->name('peserta.konfirmasi.jadwal');

        // Checklist
        Route::post('checklist', 'Diklat\ChecklistController@index')->name('checklist.index');
        Route::post('checklist/load', 'Diklat\ChecklistController@loadChecklist')->name('checklist.load');
        Route::post('checklist/store', 'Diklat\ChecklistController@store')->name('checklist.store');
        Route::get('checklist/{checklist}', 'Diklat\ChecklistController@show')->name('checklist.show');
        Route::patch('checklist/{checklist}', 'Diklat\ChecklistController@update')->name('checklist.update');
        Route::delete('checklist/{checklist}', 'Diklat\ChecklistController@destroy')->name('checklist.destroy');
        Route::post('checklist/upload', 'Diklat\ChecklistController@upload')->name('checklist.upload');
        Route::post('checklist/import', 'Diklat\ChecklistController@import')->name('checklist.import');

        // Sertifikat
        Route::post('sertifikat/store', 'Diklat\SertifikatController@store')->name('sertifikat.store');
        Route::post('sertifikat/{jadwal}/peserta', 'Diklat\SertifikatController@buatPeserta')->name('sertifikat.buat.peserta');
        Route::post('sertifikat/{jadwal}/peserta/simpan', 'Diklat\SertifikatController@buatPesertaSimpan')->name('sertifikat.buat.peserta.simpan');
        Route::post('sertifikat/{id}/cetak', 'Diklat\SertifikatController@cetak')->name('sertifikat.cetak');
        Route::get('sertifikat/{id}/spesimen/posisi', 'Diklat\SertifikatController@getSpesimenPos')->name('sertifikat.spesimen.posisi');
        Route::post('sertifikat/{id}/spesimen/posisi', 'Diklat\SertifikatController@postSpesimenPos')->name('sertifikat.spesimen.posisi.update');
        Route::get('sertifikat/{jadwal}/email', 'Diklat\SertifikatController@emailTemplate')->name('sertifikat.email.template');
        Route::post('sertifikat/{jadwal}/email/simpan', 'Diklat\SertifikatController@emailTemplateSimpan')->name('sertifikat.email.template.simpan');
        Route::post('sertifikat/{jadwal}/kirim/email', 'Diklat\SertifikatController@kirimEmail')->name('sertifikat.kirim.email');
        Route::post('sertifikat/kirim/simpeg', 'Diklat\SertifikatController@kirimSimpeg')->name('sertifikat.kirim.simpeg');
        Route::post('sertifikat/{id}/upload', 'Diklat\SertifikatController@postUpload')->name('sertifikat.upload');
        Route::delete('sertifikat/{id}', 'Diklat\SertifikatController@destroy')->name('sertifikat.destroy');
        Route::get('sertifikat/simasn/kategori/{id}', 'Diklat\SertifikatController@simasnKategori')->name('sertifikat.simasn.kategori');
        Route::get('sertifikat/simasn/sub-kategori', 'Diklat\SertifikatController@simasnSubKategori')->name('sertifikat.simasn.subkategori');
        Route::post('sertifikat/peserta/template', 'Diklat\SertifikatController@templatePeserta')->name('sertifikat.template.peserta');
        Route::post('sertifikat/peserta/import', 'Diklat\SertifikatController@importPeserta')->name('sertifikat.import.peserta');
        Route::get('sertifikat/{id}/{jadwal}/edit', 'Diklat\SertifikatController@edit')->name('sertifikat.edit');
        Route::patch('sertifikat/{id}', 'Diklat\SertifikatController@update')->name('sertifikat.update');

        // Seminar
        Route::get('seminar/{jadwal}/{slug}/kelompok/create', 'Diklat\SeminarController@createKelompok')->name('seminar.kelompok.create');
        Route::post('seminar/{jadwal}/kelompok/create', 'Diklat\SeminarController@storeKelompok')->name('seminar.kelompok.store');
        Route::get('seminar/{jadwal}/{slug}/kelompok/{id}/edit', 'Diklat\SeminarController@editKelompok')->name('seminar.kelompok.edit');
        Route::patch('seminar/{jadwal}/kelompok/{id}', 'Diklat\SeminarController@updateKelompok')->name('seminar.kelompok.update');
        Route::delete('seminar/kelompok/{id}', 'Diklat\SeminarController@destroyKelompok')->name('seminar.kelompok.destroy');
        Route::get('seminar/kelompok/{seminar}/anggota', 'Diklat\SeminarController@indexAnggota')->name('seminar.anggota.index');
        Route::post('seminar/kelompok/{seminar}/anggota/create', 'Diklat\SeminarController@storeAnggota')->name('seminar.anggota.store');
        Route::get('seminar/kelompok/anggota/{id}/edit', 'Diklat\SeminarController@editAnggota')->name('seminar.anggota.edit');
        Route::patch('seminar/kelompok/anggota/{id}', 'Diklat\SeminarController@updateAnggota')->name('seminar.anggota.update');
        Route::delete('seminar/kelompok/anggota/{id}', 'Diklat\SeminarController@destroyAnggota')->name('seminar.anggota.destroy');

        Route::post('seminar/kelompok/{id}/cetakform', 'Diklat\SeminarController@printForm')->name('seminar.print.form');

        Route::get('seminar/jadwal/{jadwal}/peserta/ajax', 'Diklat\SeminarController@ajaxPeserta')->name('seminar.ajax.peserta');

        // Surat Tugas
        Route::post('surat-tugas', 'Diklat\SurtuController@index')->name('surtu.index');
        Route::post('surat-tugas/load', 'Diklat\SurtuController@loadSurtu')->name('surtu.load');
        Route::post('surat-tugas/store', 'Diklat\SurtuController@store')->name('surtu.store');
        Route::get('surat-tugas/{jadwal}/{slug}/{id}/detail', 'Diklat\SurtuController@detail')->name('surtu.detail');
        Route::post('surat-tugas/load/pegawai', 'Diklat\SurtuController@loadPegawai')->name('surtu.load.pegawai');
        Route::post('surat-tugas/pegawai/add', 'Diklat\SurtuController@addPegawai')->name('surtu.pegawai.add');
        Route::patch('surat-tugas/{id}', 'Diklat\SurtuController@update')->name('surtu.update');
        Route::delete('surat-tugas/{id}', 'Diklat\SurtuController@destroy')->name('surtu.destroy');
        Route::delete('surat-tugas/pegawai/{id}', 'Diklat\SurtuController@delPegawai')->name('surtu.pegawai.del');
        Route::post('surat-tugas/cetak/{id}', 'Diklat\SurtuController@cetak')->name('surtu.cetak');
    });

    // Route::resource('user', 'UserController');
    Route::get('user/profil', 'UserController@profil')->name('user.profil.show');
    Route::post('user/profil', 'UserController@profilupdate')->name('user.profil.update');

    // Route::resource('pengguna', 'UserController');
    Route::get('pengguna', 'UserController@index')->name('pengguna.index')->middleware('can:isAdmin');;
    Route::post('pengguna', 'UserController@store')->name('pengguna.store')->middleware('can:isAdmin');;
    Route::get('pengguna/create', 'UserController@create')->name('pengguna.create')->middleware('can:isAdmin');;
    Route::get('pengguna/{users}/edit', 'UserController@edit')->name('pengguna.edit')->middleware('can:isAdmin');;
    Route::patch('pengguna/{users}', 'UserController@update')->name('pengguna.update')->middleware('can:isAdmin');;
    Route::delete('pengguna/{users}', 'UserController@destroy')->name('pengguna.destroy')->middleware('can:isAdmin');;
    Route::get('aktifitas', 'UserController@aktifitas')->name('aktifitas')->middleware('can:isAdmin');;

    // Cetak
    Route::post('cetak', 'CetakController@index')->name('cetak.index');
    Route::post('cetak/aksi/{id}', 'CetakController@cetak')->name('cetak.cetak');
    Route::post('cetak/modal/{id}', 'CetakController@modal')->name('cetak.modal');
});
// Backend Routes

// Ajax Routes
Route::group(['prefix'=>'ajax','as'=>'ajax.'], function () {

    Route::post('caripegawai', function (Request $request) {
        $id = $request->nip;
        $client = new Client(['http_errors' => false, 'verify' => false]);

        try
        {
            $req_pegawai = $client->get(env('SIMPEG_PNS') . $id . '/?api_token=' . env('SIMPEG_KEY'));

            if($req_pegawai->getStatusCode() == 200)
            {
                $res_pegawai = $req_pegawai->getBody();
                $pegawai = json_decode($res_pegawai, true);

                $req_satker = $client->get(env('SIMPEG_SATKER') . '/?id_skpd=' . $pegawai['id_skpd'] . '&api_token=' . env('SIMPEG_KEY'));

                if($req_satker->getStatusCode() == 200)
                {
                    $res_satker = $req_satker->getBody();
                    $satker = json_decode($res_satker, true);
                    $instansi = DB::table('instansi')->where('id', 1)->first();

                    $nama_lengkap = $pegawai['nama'];
                    $tmp_nama = explode(' ', $nama_lengkap);
                    $singkat = '';

                    foreach($tmp_nama as $i => $key)
                    {
                        if($i > 0)
                            $singkat = $singkat . substr($key, 0, 1);
                    }

                    $nama = $tmp_nama[0] . ' ' . $singkat;

                    $arr_pegawai = array(
                        'nip' => $pegawai['nip_baru'],
                        'nik' => $pegawai['nik'],
                        'nama_lengkap' => $nama_lengkap,
                        'nama' => $nama,
                        'telp' => $pegawai['no_hape'],
                        'email' => $pegawai['email'],
                        'tmp_lahir' => $pegawai['tempat_lahir'],
                        'tgl_lahir' => $pegawai['tgl_lahir'],
                        'jk' => simpegJK($pegawai['id_jenis_kelamin']),
                        'agama' => $pegawai['id_agama'],
                        'marital' => $pegawai['id_status_nikah'],
                        'alamat' => $pegawai['alamat'],
                        'jabatan' => $pegawai['jabatan'],
                        'pangkat' => $pegawai['id_golongan'],
                        'instansi' => $instansi->nama,
                        'satker_nama' => $satker['unit_kerja'][0]['skpd'],
                        'satker_telp' => $satker['unit_kerja'][0]['no_telp'],
                        'satker_alamat' => $satker['unit_kerja'][0]['alamat_skpd'],
                    );

                    foreach($arr_pegawai as $key => $value) {
                        if(empty($value) && $key != 'email')
                            $arr_pegawai[$key] = '(kosong)';

                        if(empty($value) && $key === 'email')
                            $arr_pegawai[$key] = 'kosong@kosong';
                    }

                    return response()->json($arr_pegawai, 200);
                }
            }
        }
        catch(Exception $ex)
        {
            $returnData = array(
                'status' => 'error',
                'message' => 'Terjadi kesalahan, mohon cek kembali NIP Pegawai!'
            );

            return response()->json($returnData, 404);
        }
    })->name('caripegawai')->middleware('auth');

    Route::post('carifasilitator', function (Request $request) {

        $fator = DB::table('fasilitator')->select('nama')->where('nama', 'like', '%'. $request->search . '%')->get();
        return response()->json($fator, 200);

    })->name('carifasilitator')->middleware('auth');

    Route::get('widyaiswara', function (Request $request) {

        $search = $request->search;

        $data = DB::table('fasilitator')
                    ->select('id', 'nama as text')
                    ->where('nama', 'like', '%'.$search.'%')
                    ->orderby('nama', 'asc')
                    ->get();

        return response()->json($data, 200);
    })->name('widyaiswara')->middleware('auth');

    Route::get('pegawai', function (Request $request) {

        $search = $request->search;

        $data = DB::table('fasilitator')
                    ->select('id', 'nama as text')
                    ->where('nama', 'like', '%'.$search.'%')
                    ->orderby('nama', 'asc')
                    ->get();

        return response()->json($data, 200);
    })->name('pegawai')->middleware('auth');

    Route::post('carimapel', function (Request $request) {

        $mapel = DB::table('mapel')->select('nama')
                    ->where('kurikulum_id', $request->kurikulum_id)
                    ->where('nama', 'like', '%'. $request->search . '%')
                    ->get();
        return response()->json($mapel, 200);

    })->name('carimapel')->middleware('auth');

    Route::get('kalendar', function (Request $request) {
        try
        {
            $kalendar = DB::table('v_kalendar')
                        ->whereRaw("
                            (start BETWEEN '" . $request->start . "' and '". $request->end . "') or
                            (end BETWEEN '" . $request->start . "' and '". $request->end . "') or
                            ('" . $request->start . "' BETWEEN date(start) and date(end)) or
                            ('". $request->end . "' between date(start) and date(end))
                        ")
                        ->select('title', 'start', 'end')
                        ->get();
            if(count($kalendar))
            {
                $jum_color = count($kalendar);
                $color = (RandomColor::many($jum_color, array('luminosity'=>'light')));
                $data = [];

                foreach($kalendar as $index => $value)
                {
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
        }
        catch(\Exception $e)
        {
            return response()->json(null, 200);
        }
    })->name('kalendar');
});
// Ajax Routes
