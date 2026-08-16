<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaporanSeminarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:peserta');
    }

    // =========================================================================
    // Halaman utama — daftar pelatihan + slot upload laporan
    // GET /peserta/laporan-seminar
    // =========================================================================

    public function index()
    {
        $akun = Auth::guard('peserta')->user();
        $nip  = session('peserta_nip', $akun->nip);

        // Pelatihan aktif peserta yang memiliki setup nilai + komponen penguji (seminar)
        $jadwalList = DB::table('peserta as p')
            ->join('diklat_jadwal as j', 'j.id', '=', 'p.diklat_jadwal_id')
            ->join('diklat_nilai_setup as ns', 'ns.diklat_jadwal_id', '=', 'j.id')
            ->join('nilai_template as nt', 'nt.id', '=', 'ns.template_id')
            ->where('p.nip', $nip)
            ->where('p.verifikasi', true)
            ->where('p.batal', false)
            ->select(
                'p.id as peserta_id',
                'j.id as jadwal_id',
                'j.nama as jadwal_nama',
                'j.tgl_awal',
                'j.tgl_akhir',
                'ns.is_locked',
                'ns.template_id'
            )
            ->orderByDesc('j.tgl_awal')
            ->get();

        foreach ($jadwalList as $jdw) {
            // Cek apakah template punya komponen penguji per fase
            $jdw->ada_rancangan = DB::table('nilai_komponen')
                ->where('template_id', $jdw->template_id)
                ->where('fase', 'rancangan')
                ->where('penilai', 'penguji')
                ->whereNull('parent_id')
                ->exists();

            $jdw->ada_akhir = DB::table('nilai_komponen')
                ->where('template_id', $jdw->template_id)
                ->where('fase', 'akhir')
                ->where('penilai', 'penguji')
                ->whereNull('parent_id')
                ->exists();

            // Laporan yang sudah diupload peserta ini
            $laporan = DB::table('seminar_laporan')
                ->where('peserta_id', $jdw->peserta_id)
                ->where('diklat_jadwal_id', $jdw->jadwal_id)
                ->get()
                ->keyBy('fase');

            $jdw->laporan_rancangan = $laporan->get('rancangan');
            $jdw->laporan_akhir     = $laporan->get('akhir');
        }

        // Saring hanya pelatihan yang punya seminar (rancangan atau akhir)
        $jadwalList = $jadwalList->filter(function ($j) {
            return $j->ada_rancangan || $j->ada_akhir;
        })->values();

        return view('peserta.laporan_seminar.index', compact('jadwalList'));
    }

    // =========================================================================
    // Simpan atau perbarui laporan (upsert)
    // POST /peserta/laporan-seminar/{jadwalId}/{fase}
    // =========================================================================

    public function upsert(Request $request, $jadwalId, $fase)
    {
        if (!in_array($fase, ['rancangan', 'akhir'])) {
            abort(404);
        }

        $akun = Auth::guard('peserta')->user();
        $nip  = session('peserta_nip', $akun->nip);

        // Pastikan peserta ini terdaftar di jadwal tersebut
        $peserta = DB::table('peserta')
            ->where('nip', $nip)
            ->where('diklat_jadwal_id', $jadwalId)
            ->where('verifikasi', true)
            ->where('batal', false)
            ->first();

        if (!$peserta) {
            abort(403, 'Akses tidak diizinkan.');
        }

        // Pastikan jadwal punya setup nilai + komponen penguji untuk fase ini
        $setup = DB::table('diklat_nilai_setup')
            ->where('diklat_jadwal_id', $jadwalId)
            ->first();

        if (!$setup) {
            return back()->withErrors(['Setup penilaian belum dikonfigurasi.']);
        }

        // Jika nilai sudah dikunci, tidak boleh ubah laporan
        if ($setup->is_locked) {
            return back()->withErrors(['Data penilaian sudah dikunci. Laporan tidak dapat diubah.']);
        }

        $adaFase = DB::table('nilai_komponen')
            ->where('template_id', $setup->template_id)
            ->where('fase', $fase)
            ->where('penilai', 'penguji')
            ->whereNull('parent_id')
            ->exists();

        if (!$adaFase) {
            return back()->withErrors(['Fase seminar ini tidak tersedia untuk pelatihan ini.']);
        }

        $request->validate([
            'judul' => 'required|string|max:300',
            'url'   => [
                'required',
                'url',
                'max:2000',
                // Hanya izinkan domain yang umum digunakan
                function ($attribute, $value, $fail) {
                    $allowedDomains = [
                        'drive.google.com',
                        'docs.google.com',
                        'onedrive.live.com',
                        '1drv.ms',
                        'sharepoint.com',
                        'dropbox.com',
                        'notion.so',
                        'canva.com',
                    ];
                    $host = parse_url($value, PHP_URL_HOST);
                    $host = strtolower($host ?: '');

                    $valid = false;
                    foreach ($allowedDomains as $domain) {
                        if ($host === $domain || substr($host, -(strlen($domain) + 1)) === '.' . $domain) {
                            $valid = true;
                            break;
                        }
                    }

                    if (!$valid) {
                        $fail('URL harus berasal dari layanan yang diizinkan: Google Drive, OneDrive, Dropbox, Notion, atau Canva.');
                    }
                },
            ],
        ], [
            'judul.required' => 'Judul laporan wajib diisi.',
            'url.required'   => 'Tautan laporan wajib diisi.',
            'url.url'        => 'Format URL tidak valid. Pastikan dimulai dengan https://',
        ]);

        // Temukan seminar_id berdasarkan peserta dan jadwal
        $seminarAnggota = DB::table('seminar_anggota as sa')
            ->join('seminar as s', 's.id', '=', 'sa.sid')
            ->where('sa.peid', $peserta->id)
            ->where('s.jid', $jadwalId)
            ->select('s.id as seminar_id')
            ->first();

        $seminarId = $seminarAnggota ? $seminarAnggota->seminar_id : null;

        $now = now();

        DB::table('seminar_laporan')->updateOrInsert(
            [
                'peserta_id'       => $peserta->id,
                'diklat_jadwal_id' => $jadwalId,
                'fase'             => $fase,
            ],
            [
                'seminar_id'  => $seminarId,
                'judul'       => trim($request->judul),
                'url'         => trim($request->url),
                'uploaded_by' => $akun->nama_lengkap,
                'updated_at'  => $now,
                'created_at'  => $now,
            ]
        );

        $label = $fase === 'rancangan' ? 'Seminar Rancangan' : 'Seminar Akhir';
        return back()->with('notifikasi', 'Laporan ' . $label . ' berhasil disimpan.');
    }

    // =========================================================================
    // Hapus laporan
    // DELETE /peserta/laporan-seminar/{jadwalId}/{fase}
    // =========================================================================

    public function destroy($jadwalId, $fase)
    {
        if (!in_array($fase, ['rancangan', 'akhir'])) {
            abort(404);
        }

        $akun = Auth::guard('peserta')->user();
        $nip  = session('peserta_nip', $akun->nip);

        $peserta = DB::table('peserta')
            ->where('nip', $nip)
            ->where('diklat_jadwal_id', $jadwalId)
            ->first();

        if (!$peserta) abort(403);

        // Cek lock
        $setup = DB::table('diklat_nilai_setup')
            ->where('diklat_jadwal_id', $jadwalId)->first();

        if ($setup && $setup->is_locked) {
            return back()->withErrors(['Data sudah dikunci. Laporan tidak dapat dihapus.']);
        }

        DB::table('seminar_laporan')
            ->where('peserta_id', $peserta->id)
            ->where('diklat_jadwal_id', $jadwalId)
            ->where('fase', $fase)
            ->delete();

        return back()->with('notifikasi', 'Laporan berhasil dihapus.');
    }
}
