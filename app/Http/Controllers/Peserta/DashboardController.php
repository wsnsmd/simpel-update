<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Peserta;
use App\PesertaDokumen;
use App\JadwalDokumenSyarat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    const PER_PAGE = 10;

    public function __construct()
    {
        $this->middleware('auth:peserta');
    }

    // =========================================================================
    // Dashboard utama — dengan paging manual (Laravel 5.6 + DB::table)
    // =========================================================================

    public function index(Request $request)
    {
        $akun = Auth::guard('peserta')->user();
        $nip = session('peserta_nip', $akun->nip);
        $nama = session('peserta_name', $akun->nama_lengkap);

        // --- Statistik (query semua, tidak dipaging) ---
        $stats = DB::table('peserta')
            ->join('diklat_jadwal', 'diklat_jadwal.id', '=', 'peserta.diklat_jadwal_id')
            ->where('peserta.nip', $nip)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN peserta.verifikasi = 1 AND peserta.batal = 0 THEN 1 ELSE 0 END) as aktif
            ')
            ->first();

        $totalSertifikat = DB::table('sertifikat_peserta')
            ->join('sertifikat', 'sertifikat.id', '=', 'sertifikat_peserta.sertifikat_id')
            ->join('peserta', 'peserta.id', '=', 'sertifikat_peserta.peserta_id')
            ->where('peserta.nip', $nip)
            ->where('sertifikat.is_final', true)
            ->count();

        $totalSurveyDone = DB::table('peserta_survey_status')
            ->join('peserta', 'peserta.id', '=', 'peserta_survey_status.peserta_id')
            ->where('peserta.nip', $nip)
            ->where('peserta_survey_status.is_completed', true)
            ->count();

        // --- Paging manual ---
        $page = max(1, (int) $request->get('page', 1));
        $perPage = self::PER_PAGE;
        $offset = ($page - 1) * $perPage;

        $totalRows = DB::table('peserta')
            ->where('nip', $nip)
            ->count();

        $totalPages = (int) ceil($totalRows / $perPage);
        $page = min($page, max(1, $totalPages)); // clamp

        $riwayat = DB::table('peserta')
            ->join('diklat_jadwal', 'diklat_jadwal.id', '=', 'peserta.diklat_jadwal_id')
            ->where('peserta.nip', $nip)
            ->select(
                'peserta.id',
                'peserta.kode',
                'peserta.nama_lengkap',
                'peserta.foto',
                'peserta.verifikasi',
                'peserta.konfirmasi',
                'peserta.batal',
                'peserta.batal_ket',
                'diklat_jadwal.id                as jadwal_id',
                'diklat_jadwal.nama              as diklat_nama',
                'diklat_jadwal.tgl_awal',
                'diklat_jadwal.tgl_akhir',
                'diklat_jadwal.registrasi_lengkap'
            )
            ->orderByDesc('diklat_jadwal.tgl_awal')
            ->skip($offset)
            ->take($perPage)
            ->get();

        $pesertaIds = $riwayat->pluck('id')->toArray();
        $jadwalIds = $riwayat->pluck('jadwal_id')->unique()->toArray();

        // Sertifikat final untuk halaman ini
        $sertifikat = DB::table('sertifikat_peserta')
            ->join('sertifikat', 'sertifikat.id', '=', 'sertifikat_peserta.sertifikat_id')
            ->join('peserta', 'peserta.id', '=', 'sertifikat_peserta.peserta_id')
            ->whereIn('sertifikat_peserta.peserta_id', $pesertaIds)
            ->where('sertifikat.is_final', true)
            ->select(
                'sertifikat_peserta.id       as sertifikat_peserta_id',
                'sertifikat_peserta.peserta_id',
                'sertifikat_peserta.nomor',
                'sertifikat_peserta.upload',
                'sertifikat.id               as sertifikat_id',
                'sertifikat.is_upload',
                'sertifikat.diklat_jadwal_id',
                'peserta.email'
            )
            ->get()
            ->keyBy('peserta_id');

        // Status survey untuk halaman ini
        $survey = DB::table('peserta_survey_status')
            ->whereIn('peserta_id', $pesertaIds)
            ->select('peserta_id', 'is_completed')
            ->get()
            ->keyBy('peserta_id');

        // Syarat dokumen untuk jadwal di halaman ini
        $syaratPerJadwal = empty($jadwalIds) ? collect() :
            JadwalDokumenSyarat::whereIn('diklat_jadwal_id', $jadwalIds)
                ->orderBy('urutan')
                ->get()
                ->groupBy('diklat_jadwal_id');

        // Dokumen yang sudah diupload untuk peserta di halaman ini
        $dokumenPeserta = empty($pesertaIds) ? collect() :
            PesertaDokumen::whereIn('peserta_id', $pesertaIds)
                ->get()
                ->groupBy('peserta_id');

        // Foto untuk hero — ambil satu dari semua riwayat (tidak dipaging)
        $fotoRow = DB::table('peserta')
            ->where('nip', $nip)
            ->whereNotNull('foto')
            ->where('foto', '!=', '')
            ->select('foto')
            ->orderByDesc('created_at')
            ->first();

        return view('peserta.dashboard', compact(
            'akun',
            'nip',
            'nama',
            'stats',
            'totalSertifikat',
            'totalSurveyDone',
            'riwayat',
            'sertifikat',
            'survey',
            'syaratPerJadwal',
            'dokumenPeserta',
            'fotoRow',
            'page',
            'totalPages',
            'totalRows',
            'perPage'
        ));
    }

    // =========================================================================
    // Download sertifikat
    // =========================================================================

    public function downloadSertifikat($pesertaId)
    {
        $akun = Auth::guard('peserta')->user();
        $nip = session('peserta_nip', $akun->nip);
        $peserta = DB::table('peserta')->where('id', $pesertaId)->where('nip', $nip)->first();

        if (!$peserta)
            abort(403);

        $sp = DB::table('sertifikat_peserta')
            ->join('sertifikat', 'sertifikat.id', '=', 'sertifikat_peserta.sertifikat_id')
            ->join('peserta', 'peserta.id', '=', 'sertifikat_peserta.peserta_id')
            ->where('sertifikat_peserta.peserta_id', $pesertaId)
            ->where('sertifikat.is_final', true)
            ->select(
                'sertifikat_peserta.id',
                'sertifikat_peserta.upload',
                'sertifikat.diklat_jadwal_id',
                'sertifikat.is_upload',
                'peserta.email'
            )
            ->first();

        if (!$sp)
            return back()->withErrors(['download' => 'Sertifikat belum tersedia.']);

        if ($sp->is_upload && $sp->upload && Storage::exists($sp->upload)) {
            return Storage::response($sp->upload, 'sertifikat-' . $peserta->kode . '.pdf');
        }

        return redirect()->away(route('sertifikat.show', [
            'peserta' => $pesertaId,
            'jadwal' => $sp->diklat_jadwal_id,
            'sertifikat' => $sp->id,
            'email' => str_slug($peserta->email),
        ]));
    }

    // =========================================================================
    // Upload foto
    // Syarat: sertifikat belum terbit DAN diklat_jadwal.registrasi_lengkap = 1
    // =========================================================================

    public function uploadFoto(Request $request, $pesertaId)
    {
        $akun = Auth::guard('peserta')->user();
        $peserta = Peserta::where('id', $pesertaId)
            ->where('nip', session('peserta_nip', $akun->nip))
            ->first();

        if (!$peserta)
            abort(403);

        // Cek syarat: registrasi_lengkap = 1
        $jadwal = DB::table('diklat_jadwal')
            ->where('id', $peserta->diklat_jadwal_id)
            ->select('registrasi_lengkap')
            ->first();

        if (!$jadwal || !$jadwal->registrasi_lengkap) {
            return back()->withErrors(['foto' => 'Foto tidak dapat diubah untuk diklat ini.']);
        }

        // Cek syarat: sertifikat belum terbit
        $sudahSertifikat = DB::table('sertifikat_peserta')
            ->join('sertifikat', 'sertifikat.id', '=', 'sertifikat_peserta.sertifikat_id')
            ->where('sertifikat_peserta.peserta_id', $pesertaId)
            ->where('sertifikat.is_final', true)
            ->exists();

        if ($sudahSertifikat) {
            return back()->withErrors(['foto' => 'Foto tidak dapat diubah karena sertifikat sudah terbit.']);
        }

        $validator = Validator::make($request->all(), [
            'foto' => 'required|file|mimetypes:image/jpeg,image/png|max:1024',
        ], [
            'foto.required' => 'Pilih file foto terlebih dahulu.',
            'foto.mimetypes' => 'Format harus JPG atau PNG.',
            'foto.max' => 'Maksimal 1 MB.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        if ($peserta->foto && Storage::exists($peserta->foto)) {
            Storage::delete($peserta->foto);
        }

        $file = $request->file('foto');
        $path = $file->storeAs(
            'public/files/photo/peserta',
            $peserta->kode . '_foto_' . time() . '.' . $file->getClientOriginalExtension()
        );

        $peserta->foto = $path;
        $peserta->save();

        return back()->with('flash_success', 'Foto berhasil diperbarui.');
    }

    // =========================================================================
    // Upload dokumen persyaratan
    // =========================================================================

    public function uploadDokumen(Request $request, $pesertaId, $syaratId)
    {
        $akun = Auth::guard('peserta')->user();
        $peserta = Peserta::where('id', $pesertaId)
            ->where('nip', session('peserta_nip', $akun->nip))
            ->first();

        if (!$peserta)
            abort(403);

        $existing = PesertaDokumen::where('peserta_id', $pesertaId)
            ->where('dokumen_syarat_id', $syaratId)
            ->first();

        if ($existing && $existing->verified_by_admin) {
            return back()->withErrors([
                'dokumen_' . $syaratId => 'Dokumen ini sudah diverifikasi dan tidak dapat diganti.',
            ]);
        }

        $syarat = JadwalDokumenSyarat::findOrFail($syaratId);

        if ($syarat->diklat_jadwal_id !== $peserta->diklat_jadwal_id) {
            abort(403);
        }

        $maxKb = $syarat->max_size_kb;
        $mimes = $syarat->mimes_rule;

        $validator = Validator::make($request->all(), [
            'dokumen' => "required|file|mimes:{$mimes}|max:{$maxKb}",
        ], [
            'dokumen.required' => 'Pilih file yang akan diunggah.',
            'dokumen.mimes' => 'Format tidak sesuai. Diizinkan: ' . strtoupper(str_replace(',', ', ', $mimes)) . '.',
            'dokumen.max' => 'Ukuran melebihi batas ' . round($maxKb / 1024, 1) . ' MB.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $file = $request->file('dokumen');
        $originalName = $file->getClientOriginalName();
        $storedName = $peserta->kode . '_syarat' . $syaratId . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/files/dokumen_peserta', $storedName);

        if ($existing && $existing->file_path && Storage::exists($existing->file_path)) {
            Storage::delete($existing->file_path);
        }

        PesertaDokumen::updateOrCreate(
            ['peserta_id' => $pesertaId, 'dokumen_syarat_id' => $syaratId],
            [
                'file_path' => $path,
                'file_original_name' => $originalName,
                'uploaded_at' => now(),
                'verified_by_admin' => false,
                'verified_by' => null,
                'verified_at' => null,
                'catatan_admin' => null,
            ]
        );

        return back()->with('flash_success', "Dokumen \"{$syarat->nama}\" berhasil diunggah.");
    }
}
