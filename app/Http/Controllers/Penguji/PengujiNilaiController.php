<?php

namespace App\Http\Controllers\Penguji;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Diklat\NilaiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PengujiNilaiController extends Controller
{
    // =========================================================================
    // Halaman utama penguji
    // GET /nilai/penguji/{token}
    // =========================================================================

    public function index($token)
    {
        $data = $this->resolveToken($token);
        if (!$data) {
            return view('penguji.nilai.invalid', [
                'pesan' => 'Link tidak valid atau sudah kadaluarsa. Silakan hubungi panitia.'
            ]);
        }

        list($tokenRow, $seminar, $penguji, $jadwal) = $data;

        DB::table('nilai_penguji_token')->where('id', $tokenRow->id)
            ->update(['last_login_at' => now()]);

        $setup = DB::table('diklat_nilai_setup')
            ->where('diklat_jadwal_id', $jadwal->id)->first();

        // Daftar peserta kelompok ini
        $pesertaList = DB::table('seminar_anggota as sa')
            ->join('peserta as p', 'p.id', '=', 'sa.peid')
            ->where('sa.sid', $seminar->id)
            ->select('p.id', 'p.nip', 'p.nama_lengkap', 'p.instansi', 'p.jabatan')
            ->orderBy('p.nama_lengkap')->get();

        // Status nilai per peserta per fase
        $statusNilai = array();
        if ($setup) {
            $komRancangan = DB::table('nilai_komponen')
                ->where('template_id', $setup->template_id)
                ->where('fase', 'rancangan')->where('penilai', 'penguji')
                ->whereNotNull('parent_id')->pluck('id')->toArray();

            $komAkhir = DB::table('nilai_komponen')
                ->where('template_id', $setup->template_id)
                ->where('fase', 'akhir')->where('penilai', 'penguji')
                ->whereNotNull('parent_id')->pluck('id')->toArray();

            foreach ($pesertaList as $p) {
                $cntR = !empty($komRancangan)
                    ? DB::table('peserta_nilai')
                        ->where('peserta_id', $p->id)
                        ->whereIn('komponen_id', $komRancangan)
                        ->whereNotNull('nilai')->count()
                    : 0;
                $cntA = !empty($komAkhir)
                    ? DB::table('peserta_nilai')
                        ->where('peserta_id', $p->id)
                        ->whereIn('komponen_id', $komAkhir)
                        ->whereNotNull('nilai')->count()
                    : 0;

                $statusNilai[$p->id] = [
                    'rancangan_selesai' => $cntR > 0 && $cntR >= count($komRancangan),
                    'akhir_selesai'     => $cntA > 0 && $cntA >= count($komAkhir),
                    'rancangan_total'   => count($komRancangan),
                    'akhir_total'       => count($komAkhir),
                ];
            }
        }

        return view('penguji.nilai.index', compact(
            'tokenRow', 'seminar', 'penguji', 'jadwal',
            'pesertaList', 'statusNilai', 'setup'
        ));
    }

    // =========================================================================
    // Form input nilai seminar rancangan atau akhir
    // GET /nilai/penguji/{token}/peserta/{pesertaId}/{fase}
    // =========================================================================

    public function formNilai($token, $pesertaId, $fase)
    {
        if (!in_array($fase, ['rancangan', 'akhir'])) abort(404);

        $data = $this->resolveToken($token);
        if (!$data) {
            return view('penguji.nilai.invalid', ['pesan' => 'Link tidak valid atau kadaluarsa.']);
        }

        list($tokenRow, $seminar, $penguji, $jadwal) = $data;

        $setup = DB::table('diklat_nilai_setup')
            ->where('diklat_jadwal_id', $jadwal->id)->first();

        if (!$setup) {
            return view('penguji.nilai.invalid', [
                'pesan' => 'Setup penilaian belum dikonfigurasi. Hubungi panitia.'
            ]);
        }

        if ($setup->is_locked) {
            return view('penguji.nilai.invalid', [
                'pesan' => 'Data penilaian sudah dikunci oleh admin.'
            ]);
        }

        // Validasi peserta ada di kelompok
        $peserta = DB::table('seminar_anggota as sa')
            ->join('peserta as p', 'p.id', '=', 'sa.peid')
            ->where('sa.sid', $seminar->id)->where('p.id', $pesertaId)
            ->select('p.id', 'p.nip', 'p.nama_lengkap', 'p.instansi', 'p.jabatan')
            ->first();

        if (!$peserta) {
            return view('penguji.nilai.invalid', [
                'pesan' => 'Peserta tidak ditemukan dalam kelompok Anda.'
            ]);
        }

        // Sub-komponen untuk fase ini (penilai=penguji)
        $subKomponen = DB::table('nilai_komponen')
            ->where('template_id', $setup->template_id)
            ->where('fase', $fase)->where('penilai', 'penguji')
            ->whereNotNull('parent_id')
            ->orderBy('urutan')->get();

        // Aspek induk dari sub-komponen ini
        $aspekIds = $subKomponen->pluck('parent_id')->unique()->toArray();
        $aspekList = DB::table('nilai_komponen')
            ->whereIn('id', $aspekIds)->orderBy('urutan')->get()
            ->map(function ($a) use ($subKomponen) {
                $a->sub = $subKomponen->where('parent_id', $a->id)->values();
                return $a;
            });

        // Nilai yang sudah ada
        $nilaiMap   = array();
        $catatanMap = array();
        if ($subKomponen->count() > 0) {
            $existing = DB::table('peserta_nilai')
                ->where('peserta_id', $pesertaId)
                ->whereIn('komponen_id', $subKomponen->pluck('id')->toArray())->get();
            foreach ($existing as $n) {
                $nilaiMap[$n->komponen_id]   = $n->nilai;
                $catatanMap[$n->komponen_id] = $n->catatan;
            }
        }

        // Ambil catatan umum yang sudah ada
        $kolomCatatan   = $fase === 'rancangan' ? 'catatan_rancangan' : 'catatan_akhir';
        $rekapPeserta   = DB::table('peserta_nilai_rekap')
            ->where('diklat_jadwal_id', $jadwal->id)
            ->where('peserta_id', $pesertaId)->first();
        $catatanUmum    = $rekapPeserta ? $rekapPeserta->{$kolomCatatan} : null;
        $catatanRancanganUmum = ($fase === 'akhir' && $rekapPeserta)
            ? $rekapPeserta->catatan_rancangan : null;

        // Rekod seminar rancangan — tampil saat penguji input seminar akhir
        $rekorRancangan = collect();
        if ($fase === 'akhir') {
            $subRancangan = DB::table('nilai_komponen')
                ->where('template_id', $setup->template_id)
                ->where('fase', 'rancangan')->where('penilai', 'penguji')
                ->whereNotNull('parent_id')->orderBy('urutan')->get();

            if ($subRancangan->count() > 0) {
                $nilaiR   = DB::table('peserta_nilai')
                    ->where('peserta_id', $pesertaId)
                    ->whereIn('komponen_id', $subRancangan->pluck('id')->toArray())
                    ->pluck('nilai', 'komponen_id');
                $catatanR = DB::table('peserta_nilai')
                    ->where('peserta_id', $pesertaId)
                    ->whereIn('komponen_id', $subRancangan->pluck('id')->toArray())
                    ->pluck('catatan', 'komponen_id');

                // Group sub-komponen rancangan per aspek induknya
                $aspekRancanganIds = $subRancangan->pluck('parent_id')->unique()->toArray();
                $aspekRancangan = DB::table('nilai_komponen')
                    ->whereIn('id', $aspekRancanganIds)->orderBy('urutan')->get()
                    ->map(function ($a) use ($subRancangan, $nilaiR, $catatanR) {
                        $a->sub = $subRancangan->where('parent_id', $a->id)
                            ->map(function ($s) use ($nilaiR, $catatanR) {
                                $s->nilai   = isset($nilaiR[$s->id])   ? $nilaiR[$s->id]   : null;
                                $s->catatan = isset($catatanR[$s->id]) ? $catatanR[$s->id] : null;
                                return $s;
                            })->values();
                        return $a;
                    });

                $rekorRancangan = $aspekRancangan;
            }
        }

        return view('penguji.nilai.form', compact(
            'tokenRow', 'seminar', 'penguji', 'jadwal', 'peserta',
            'fase', 'aspekList', 'nilaiMap', 'catatanMap',
            'rekorRancangan', 'setup',
            'catatanUmum', 'catatanRancanganUmum'
        ));
    }

    // =========================================================================
    // Simpan nilai dari penguji
    // POST /nilai/penguji/{token}/peserta/{pesertaId}/{fase}
    // =========================================================================

    public function saveNilai(Request $request, $token, $pesertaId, $fase)
    {
        $data = $this->resolveToken($token);
        if (!$data) abort(403, 'Token tidak valid.');

        list($tokenRow, $seminar, $penguji, $jadwal) = $data;

        $setup = DB::table('diklat_nilai_setup')
            ->where('diklat_jadwal_id', $jadwal->id)->first();
        if (!$setup || $setup->is_locked) {
            return back()->withErrors(['Data sudah dikunci oleh admin.']);
        }

        // Validasi peserta ada di kelompok
        if (!DB::table('seminar_anggota')
            ->where('sid', $seminar->id)->where('peid', $pesertaId)->exists()) {
            abort(403);
        }

        $request->validate([
            'nilai'        => 'required|array',
            'nilai.*'      => 'nullable|numeric|min:0|max:100',
            'catatan_umum' => 'nullable|string|max:1000',
        ]);

        $subKomponen = DB::table('nilai_komponen')
            ->where('template_id', $setup->template_id)
            ->where('fase', $fase)->where('penilai', 'penguji')
            ->whereNotNull('parent_id')->orderBy('urutan')->get();

        $now       = now();
        $inputBy   = 'penguji:' . $seminar->id . ':' . $penguji->nama;

        foreach ($subKomponen as $k) {
            if (!isset($request->nilai[$k->id]) || $request->nilai[$k->id] === '') continue;

            DB::table('peserta_nilai')->updateOrInsert(
                ['peserta_id' => $pesertaId, 'komponen_id' => $k->id],
                [
                    'diklat_jadwal_id' => $jadwal->id,
                    'nilai'     => $request->nilai[$k->id],
                    'catatan'   => isset($request->catatan[$k->id]) ? $request->catatan[$k->id] : null,
                    'input_by'  => $inputBy,
                    'input_at'  => $now,
                    'updated_at'=> $now,
                    'created_at'=> $now,
                ]
            );
        }

        // Simpan catatan umum per fase ke rekap peserta
        $kolomCatatan = $fase === 'rancangan' ? 'catatan_rancangan' : 'catatan_akhir';
        DB::table('peserta_nilai_rekap')->updateOrInsert(
            ['diklat_jadwal_id' => $jadwal->id, 'peserta_id' => $pesertaId],
            [
                $kolomCatatan => $request->catatan_umum,
                'updated_at'  => $now,
                'created_at'  => $now,
            ]
        );

        // Hitung ulang rekap
        $nilaiController = new NilaiController();
        $nilaiController->hitungRekap($jadwal->id, $pesertaId, $setup);

        $label = $fase === 'rancangan' ? 'Seminar Rancangan' : 'Seminar Akhir';
        return redirect()
            ->route('penguji.nilai.index', $token)
            ->with('notifikasi', 'Nilai ' . $label . ' untuk ' . $jadwal->nama . ' berhasil disimpan.');
    }

    // =========================================================================
    // Helper: Validasi & resolve token
    // =========================================================================

    private function resolveToken($token)
    {
        $tokenRow = DB::table('nilai_penguji_token')
            ->where('token', $token)->where('is_active', true)->first();

        if (!$tokenRow) return null;

        if ($tokenRow->token_expired_at &&
            Carbon::parse($tokenRow->token_expired_at)->isPast()) {
            return null;
        }

        $seminar    = DB::table('seminar')->where('id', $tokenRow->seminar_id)->first();
        if (!$seminar) return null;

        $penguji    = DB::table('fasilitator')->where('id', $seminar->pid)->first();
        $jadwal     = DB::table('v_jadwal_detail')->where('id', $seminar->jid)->first();

        return [$tokenRow, $seminar, $penguji, $jadwal];
    }
}
