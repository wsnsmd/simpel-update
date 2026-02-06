<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

use App;
use DB;
use Storage;
use App\Jobs\UploadSimpegJob;

class SertifikatController extends Controller
{
    //
    public function show($peserta, $jadwal, $sertifikat, $email)
    {
        $cekSertifikat = DB::table('sertifikat_peserta')->where('id', $sertifikat)->first();

        if (is_null($cekSertifikat))
            abort(404);

        $cek = DB::table('v_sertifikat')
            ->where('id', $peserta)
            ->where('sertifikat_id', $cekSertifikat->sertifikat_id)
            ->first();

        if (is_null($cek)) {
            abort(404);
        }

        if (
            !($cek->id == $peserta
                && $cek->diklat_jadwal_id == $jadwal
                && $cek->spid == $sertifikat
                && str_slug($cek->email) == $email
            )
        ) {
            abort(404);
        }

        $sertPeserta = DB::table('v_sertifikat')
            ->select(
                'id',
                'nip',
                'nama_lengkap',
                'tmp_lahir',
                'tgl_lahir',
                'jabatan',
                'foto',
                'instansi',
                'satker_nama',
                'sebagai',
                'diklat_jadwal_id',
                'pangkat',
                'golongan',
                'nomor',
                'kualifikasi',
                'status',
                'sertifikat_id',
                'spesimen_kiri',
                'spesimen_bawah',
                'spesimen2_kiri',
                'spesimen2_bawah',
                'upload',
                'simpeg_at',
                'simpeg_queued_at',
                'simpeg_failed_at',
            )
            ->where('spid', $sertifikat)
            ->first();

        if (is_null($sertPeserta)) {
            abort(404);
        }

        $sertifikatRow = DB::table('sertifikat')
            ->select(
                'tempat',
                'tanggal',
                'jabatan',
                'nama',
                'pangkat',
                'nip',
                'jabatan2',
                'nama2',
                'pangkat2',
                'nip2',
                'diklat_jadwal_id',
                'spesimen',
                'spesimen2',
                'tsid',
                'is_upload',
                'fasilitasi',
                'barcode'
            )
            ->where('id', $sertPeserta->sertifikat_id)
            ->first();

        if (is_null($sertifikatRow)) {
            abort(404);
        }

        $jadwalRow = DB::table('v_jadwal_detail')
            ->select(
                'id',
                'nama',
                'tahun',
                'tipe',
                'tgl_awal',
                'tgl_akhir',
                'kelas',
                'total_jp',
                'lokasi',
                'lokasi_kota',
                'kurikulum_id',
                'jenis_layanan',
                'usergroup',
            )
            ->where('id', $sertifikatRow->diklat_jadwal_id)
            ->first();

        if (is_null($jadwalRow)) {
            abort(404);
        }

        // 1) ambil survei wajib untuk jadwal
        $mandatorySurveys = DB::table('jadwal_surveys')
            ->where('jadwal_id', $jadwalRow->id)
            ->where('is_mandatory', 1)
            ->get();

        if ($mandatorySurveys->count() > 0) {
            // 2) ambil yang sudah diisi peserta
            $doneCodes = DB::table('peserta_survey_status')
                ->where('jadwal_id', $jadwal)
                ->where('peserta_id', $peserta)
                ->pluck('survey_code')
                ->toArray();

            // 3) cari yang belum diisi
            $missing = $mandatorySurveys->filter(function ($s) use ($doneCodes) {
                return !in_array($s->survey_code, $doneCodes);
            })->values();

            if ($missing->count() > 0) {
                // tambahkan fill_url untuk UX
                $retryUrl = url()->current();
                $missingSurveys = $missing->map(function ($s) use ($cek, $jadwalRow, $sertPeserta) {
                    // params dari DB text JSON, kalau invalid ya fallback []
                    $params = [];
                    if (!empty($s->params)) {
                        $decoded = json_decode($s->params, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $params = $decoded;
                        }
                    }

                    // payload wajib sesuai format app survei kamu
                    $data = array_merge([
                        'peserta_id' => (string) $cek->id,
                        'nama_lengkap' => (string) $sertPeserta->nama_lengkap,
                        'jk' => (string) ($cek->jk ?? ''), // kalau ada di view; jika tidak ada, bisa ambil dari peserta table
                        'pendidikan' => (string) ($cek->pendidikan ?? 'Sarjana (S1)'), // sesuaikan sumbernya
                        'pekerjaan' => (string) (($cek->status_asn ?? null) ? 'ASN' : 'Non-ASN'),
                        'instansi' => (string) $sertPeserta->instansi,
                        'jadwal_id' => (string) $jadwalRow->id,
                        'jadwal_nama' => (string) $jadwalRow->nama,
                        'bidang' => (string) strtoupper(($jadwalRow->usergroup ?? '-')),
                        'jenis_layanan' => (string) ($jadwalRow->jenis_layanan ?? ''),
                        'survey_code' => (string) $s->survey_code,
                        'source_app' => 'SIMPel',
                    ], $params);

                    return (object) [
                        'survey_name' => $s->survey_name,
                        'survey_code' => $s->survey_code,
                        'fill_url' => $this->buildSurveyFillUrl($data),
                    ];
                });

                // opsional: ambil info jadwal untuk ditampilkan
                $jadwalInfo = DB::table('v_jadwal_detail')
                    ->select('id', 'nama')
                    ->where('id', $jadwal)
                    ->first();

                return view('frontend.survey', [
                    'missing' => $missingSurveys,
                    'jadwal' => $jadwalRow,
                    'sertPeserta' => $sertPeserta,
                    'retryUrl' => $retryUrl,
                ]);
            }
        }

        if ($sertifikatRow->is_upload) {
            return Storage::response($sertPeserta->upload);
        }

        $kurikulum = DB::table('mapel')
            ->select('nama', 'jpk', 'jpe')
            ->where('kurikulum_id', $jadwalRow->kurikulum_id)
            ->get();

        $template = DB::table('sertifikat_template')->where('id', $sertifikatRow->tsid)->first();
        if (is_null($template)) {
            abort(404);
        }

        if (is_null($sertPeserta->foto)) {
            $sertPeserta->foto = 'media/avatars/avatar8.jpg';
        }

        // 1. Filter Instansi: Hanya Pemerintah Provinsi Kalimantan Timur
        $instansi = strtoupper($sertPeserta->instansi);
        $pesertaRow = DB::table('peserta')
            ->where('id', $peserta)
            ->first();
        $isAsn = in_array($pesertaRow->status_asn, [1, 2]);
        $isPemprov = str_contains($instansi, 'PEMERINTAH PROVINSI KALIMANTAN TIMUR');

        // 2. Filter Status Antrean: Pastikan simpe_queued masih 0 (belum pernah masuk antrean)
        $isNotQueued = DB::table('sertifikat_peserta')
            ->where('id', $sertifikat)
            ->whereNull('simpeg_at')
            ->whereNull('simpeg_queued_at')
            ->exists();

        if ($isPemprov && $isNotQueued && $isAsn) {
            // 3. Tandai langsung di database agar request berikutnya tidak masuk ke sini (Mencegah Loop)
            DB::table('sertifikat_peserta')
                ->where('id', $sertifikat)
                ->update(['simpeg_queued_at' => now()]);

            // 4. Persiapkan variabel untuk Job
            $simasn = DB::table('sertifikat_simasn')
                ->where('sertifikat_id', $sertPeserta->sertifikat_id)
                ->first();

            $pes = $sertPeserta;
            $jenis = $simasn->jenis; // misal: Diklat, Workshop, dll
            $kategori = $simasn->kategori;
            $sub = $simasn->sub_kategori;

            // Gunakan URL saat ini sebagai sumber sertifikat untuk di-upload oleh Job
            $url_sertifikat = url()->current();

            // 5. Dispatch Job dengan Delay
            $job = new UploadSimpegJob($pes, $jadwal, $sertifikat, $jenis, $kategori, $sub, $url_sertifikat);
            $job->delay(now()->addSeconds(5));
            dispatch($job);
        }

        ini_set('memory_limit', '1024M');
        set_time_limit(30);

        $papersize = 'a4';
        $paperorientation = 'landscape';
        $filename = 'Sertifikat - ' . $sertPeserta->nomor;

        $view = view('report.dom.sertifikat.' . $template->file, [
            'sertPeserta' => $sertPeserta,
            'sertifikat' => $sertifikatRow,
            'jadwal' => $jadwalRow,
            'kurikulum' => $kurikulum,
        ]);

        $pdf = App::make('dompdf.wrapper');
        $pdf->setOptions([
            'dpi' => '120',
            'isRemoteEnabled' => true,
            'chroot' => realpath(base_path()),
        ]);
        $pdf->loadHTML($view);
        $pdf->setPaper($papersize, $paperorientation);

        return $pdf->stream($filename . '.pdf');
    }

    public function cek(Request $request)
    {
        if ($request->has('hash')) {
            $hash = $request->hash;
            $sertifikat_peserta = DB::table('sertifikat_peserta')
                ->whereRaw('SHA1(nomor) = ?', [$hash])
                ->first();
            if (is_null($sertifikat_peserta))
                return redirect()->route('sertifikat.cek')->with('error', 'Data sertifikat tidak ditemukan!');

            $peserta = DB::table('v_sertifikat')
                ->where('spid', $sertifikat_peserta->id)
                ->first();
            $sertifikat = DB::table('sertifikat')
                ->where('id', $sertifikat_peserta->sertifikat_id)
                ->first();
            $jadwal = DB::table('v_jadwal_detail')
                ->select('nama', 'tahun', 'tipe', 'tgl_awal', 'tgl_akhir', 'kelas', 'total_jp', 'lokasi', 'lokasi_kota', 'kurikulum_id')
                ->where('id', $sertifikat->diklat_jadwal_id)
                ->first();
            return view('frontend.sertifikat-cek', compact('peserta', 'sertifikat_peserta', 'jadwal'));
        }

        return view('frontend.sertifikat-cek');
    }

    public function postCek(Request $request)
    {
        $request->validate([
            'nomor' => 'required',
            'captcha' => 'required|captcha',
        ]);
        $nomor = str_replace(' ', '', $request->nomor);
        $sertifikat_peserta = DB::table('sertifikat_peserta')
            ->where(DB::raw("REPLACE(nomor, ' ', '')"), $nomor)
            ->first();

        if (is_null($sertifikat_peserta))
            return redirect()->back()->with('error', 'Data sertifikat tidak ditemukan!');

        $peserta = DB::table('v_sertifikat')
            ->where('spid', $sertifikat_peserta->id)
            ->first();
        $sertifikat = DB::table('sertifikat')
            ->where('id', $sertifikat_peserta->sertifikat_id)
            ->first();
        $jadwal = DB::table('v_jadwal_detail')
            ->select('nama', 'tahun', 'tipe', 'tgl_awal', 'tgl_akhir', 'kelas', 'total_jp', 'lokasi', 'lokasi_kota', 'kurikulum_id')
            ->where('id', $sertifikat->diklat_jadwal_id)
            ->first();
        return view('frontend.sertifikat-cek', compact('peserta', 'sertifikat_peserta', 'jadwal'));
    }

    private function buildSurveyFillUrl(array $data): string
    {
        // Wajib ada survey_code
        if (empty($data['survey_code'])) {
            throw new \InvalidArgumentException('survey_code is required');
        }

        // Pastikan source_app ada
        $data['source_app'] = $data['source_app'] ?? 'SIMPel';

        // Secret key harus sama dengan app survei
        $secretKey = config('simpel.survey_app.secret_key');

        // Token dibuat dari data (tanpa token), diurutkan berdasarkan key
        $dataToHash = $data;
        unset($dataToHash['token']); // jaga-jaga kalau kepanggil ulang
        ksort($dataToHash);

        $stringToHash = http_build_query($dataToHash);
        $token = hash_hmac('sha256', $stringToHash, $secretKey);

        // Payload final = data + token
        $payload = array_merge($dataToHash, [
            'token' => $token,
        ]);

        // Base URL aplikasi survei (bukan url() SIMPel)
        $base = rtrim(config('simpel.survey_app.base_url'), '/');

        if ($base === '') {
            throw new \InvalidArgumentException('SURVEY_BASE_URL belum diset');
        }

        // URL akhir: {SURVEY_BASE_URL}/s/{survey_code}?{payload}
        return $base . '/s/' . $data['survey_code'] . '?' . http_build_query($payload);
    }


}
