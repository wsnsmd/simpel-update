<?php

namespace App\Http\Controllers\Diklat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NilaiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // =========================================================================
    // TEMPLATE — CRUD
    // =========================================================================

    public function templateIndex()
    {
        $templates = DB::table('nilai_template')
            ->leftJoin('diklat_jenis', 'diklat_jenis.id', '=', 'nilai_template.jenis_diklat_id')
            ->select('nilai_template.*', 'diklat_jenis.nama as jenis_nama')
            ->orderByDesc('nilai_template.id')->get()
            ->map(function ($t) {
                $t->jumlah_aspek = DB::table('nilai_komponen')
                    ->where('template_id', $t->id)->whereNull('parent_id')->count();
                $t->total_bobot  = DB::table('nilai_komponen')
                    ->where('template_id', $t->id)->whereNull('parent_id')->sum('bobot');
                $t->dipakai      = DB::table('diklat_nilai_setup')
                    ->where('template_id', $t->id)->count();
                return $t;
            });

        $jenisDiklat = DB::table('diklat_jenis')->orderBy('nama')->get();

        return view('backend.diklat.nilai.template_index',
            compact('templates', 'jenisDiklat'));
    }

    public function templateStore(Request $request)
    {
        $request->validate([
            'nama'                 => 'required|string|max:200',
            'mode'                 => 'required|in:klasikal,blended,distance',
            'passing_grade_aspek'  => 'required|numeric|min:0|max:100',
        ]);

        $id = DB::table('nilai_template')->insertGetId([
            'nama'                => $request->nama,
            'deskripsi'           => $request->deskripsi,
            'tahun'               => $request->tahun,
            'jenis_diklat_id'     => $request->jenis_diklat_id ?: null,
            'mode'                => $request->mode,
            'passing_grade_aspek' => $request->passing_grade_aspek,
            'created_by'          => Auth::user()->name,
            'created_at'          => now(), 'updated_at' => now(),
        ]);

        return redirect()
            ->route('backend.diklat.nilai.template.komponen', $id)
            ->with('notifikasi', 'Template dibuat. Silakan definisikan komponen penilaian.');
    }

    public function templateUpdate(Request $request, $id)
    {
        $request->validate([
            'nama'                => 'required|string|max:200',
            'passing_grade_aspek' => 'required|numeric|min:0|max:100',
        ]);

        DB::table('nilai_template')->where('id', $id)->update([
            'nama'                => $request->nama,
            'deskripsi'           => $request->deskripsi,
            'tahun'               => $request->tahun,
            'jenis_diklat_id'     => $request->jenis_diklat_id ?: null,
            'mode'                => $request->mode,
            'passing_grade_aspek' => $request->passing_grade_aspek,
            'updated_at'          => now(),
        ]);

        return back()->with('notifikasi', 'Template berhasil diperbarui.');
    }

    public function templateClone($id)
    {
        $tpl = DB::table('nilai_template')->where('id', $id)->first();
        if (!$tpl) abort(404);

        $newId = DB::table('nilai_template')->insertGetId([
            'nama'                => $tpl->nama . ' (Salinan)',
            'deskripsi'           => $tpl->deskripsi,
            'tahun'               => $tpl->tahun,
            'jenis_diklat_id'     => $tpl->jenis_diklat_id,
            'mode'                => $tpl->mode,
            'passing_grade_aspek' => $tpl->passing_grade_aspek,
            'created_by'          => Auth::user()->name,
            'created_at'          => now(), 'updated_at' => now(),
        ]);

        // Clone aspek induk & sub-komponen
        $aspekList = DB::table('nilai_komponen')
            ->where('template_id', $id)->whereNull('parent_id')
            ->orderBy('urutan')->get();

        foreach ($aspekList as $aspek) {
            $newAspekId = DB::table('nilai_komponen')->insertGetId([
                'template_id' => $newId,
                'parent_id'   => null,
                'nama'        => $aspek->nama,
                'bobot'       => $aspek->bobot,
                'penilai'     => $aspek->penilai,
                'fase'        => $aspek->fase,
                'urutan'      => $aspek->urutan,
                'keterangan'  => $aspek->keterangan,
            ]);

            $subList = DB::table('nilai_komponen')
                ->where('template_id', $id)->where('parent_id', $aspek->id)
                ->orderBy('urutan')->get();

            foreach ($subList as $sub) {
                DB::table('nilai_komponen')->insert([
                    'template_id' => $newId,
                    'parent_id'   => $newAspekId,
                    'nama'        => $sub->nama,
                    'bobot'       => $sub->bobot,
                    'penilai'     => $sub->penilai,
                    'fase'        => $sub->fase,
                    'urutan'      => $sub->urutan,
                    'keterangan'  => $sub->keterangan,
                ]);
            }
        }

        return redirect()
            ->route('backend.diklat.nilai.template.komponen', $newId)
            ->with('notifikasi', 'Template berhasil disalin.');
    }

    public function templateDestroy($id)
    {
        $dipakai = DB::table('diklat_nilai_setup')->where('template_id', $id)->count();
        if ($dipakai > 0) {
            return back()->withErrors([$dipakai . ' jadwal menggunakan template ini.']);
        }
        DB::table('nilai_template')->where('id', $id)->delete();
        return back()->with('notifikasi', 'Template dihapus.');
    }

    // =========================================================================
    // KOMPONEN — CRUD
    // =========================================================================

    public function komponenIndex($templateId)
    {
        $template = DB::table('nilai_template')->where('id', $templateId)->first();
        if (!$template) abort(404);

        $aspekList = DB::table('nilai_komponen')
            ->where('template_id', $templateId)->whereNull('parent_id')
            ->orderBy('urutan')->get()
            ->map(function ($a) use ($templateId) {
                $a->sub = DB::table('nilai_komponen')
                    ->where('template_id', $templateId)->where('parent_id', $a->id)
                    ->orderBy('urutan')->get();
                return $a;
            });

        $totalBobot = $aspekList->sum('bobot');

        return view('backend.diklat.nilai.komponen_index',
            compact('template', 'aspekList', 'totalBobot'));
    }

    public function komponenStoreAspek(Request $request, $templateId)
    {
        $request->validate([
            'nama'       => 'required|string|max:200',
            'bobot'      => 'required|numeric|min:0.001|max:1',
            'penilai'    => 'required|in:operator,penguji',
            'fase'       => 'nullable|in:rancangan,akhir',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $urutan = DB::table('nilai_komponen')
            ->where('template_id', $templateId)->whereNull('parent_id')
            ->max('urutan') + 1;

        DB::table('nilai_komponen')->insert([
            'template_id' => $templateId,
            'parent_id'   => null,
            'nama'        => $request->nama,
            'bobot'       => $request->bobot,
            'penilai'     => $request->penilai,
            'fase'        => $request->fase ?: null,
            'urutan'      => $urutan,
            'keterangan'  => $request->keterangan,
        ]);

        return back()->with('notifikasi', 'Aspek berhasil ditambahkan.');
    }

    public function komponenStoreSub(Request $request, $templateId, $aspekId)
    {
        $aspek = DB::table('nilai_komponen')
            ->where('id', $aspekId)->where('template_id', $templateId)->first();
        if (!$aspek) abort(404);

        $request->validate([
            'nama'       => 'required|string|max:200',
            'bobot'      => 'required|numeric|min:0.001|max:1',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $urutan = DB::table('nilai_komponen')
            ->where('template_id', $templateId)->where('parent_id', $aspekId)
            ->max('urutan') + 1;

        DB::table('nilai_komponen')->insert([
            'template_id' => $templateId,
            'parent_id'   => $aspekId,
            'nama'        => $request->nama,
            'bobot'       => $request->bobot,
            // Sub-komponen inherit penilai & fase dari aspek induk
            'penilai'     => $aspek->penilai,
            'fase'        => $aspek->fase,
            'urutan'      => $urutan,
            'keterangan'  => $request->keterangan,
        ]);

        return back()->with('notifikasi', 'Sub-komponen berhasil ditambahkan.');
    }

    public function komponenUpdate(Request $request, $id)
    {
        $k = DB::table('nilai_komponen')->where('id', $id)->first();
        if (!$k) abort(404);

        $rules = [
            'nama'       => 'required|string|max:200',
            'bobot'      => 'required|numeric|min:0.001|max:1',
            'keterangan' => 'nullable|string|max:500',
        ];
        if (!$k->parent_id) {
            // Aspek induk bisa ubah penilai & fase
            $rules['penilai'] = 'required|in:operator,penguji';
            $rules['fase']    = 'nullable|in:rancangan,akhir';
        }
        $request->validate($rules);

        $data = [
            'nama'       => $request->nama,
            'bobot'      => $request->bobot,
            'keterangan' => $request->keterangan,
        ];
        if (!$k->parent_id) {
            $data['penilai'] = $request->penilai;
            $data['fase']    = $request->fase ?: null;

            // Sinkronkan sub-komponen
            DB::table('nilai_komponen')->where('parent_id', $id)->update([
                'penilai' => $request->penilai,
                'fase'    => $request->fase ?: null,
            ]);
        }

        DB::table('nilai_komponen')->where('id', $id)->update($data);
        return back()->with('notifikasi', 'Komponen berhasil diperbarui.');
    }

    public function komponenDestroy($id)
    {
        // Cek apakah sudah ada nilai yang diinput
        $sub = DB::table('nilai_komponen')->where('parent_id', $id)->pluck('id')->toArray();
        $cekNilai = DB::table('peserta_nilai')
            ->where('komponen_id', $id)
            ->orWhereIn('komponen_id', $sub)->count();

        if ($cekNilai > 0) {
            return back()->withErrors(['Komponen sudah memiliki data nilai dan tidak dapat dihapus.']);
        }

        DB::table('nilai_komponen')->where('parent_id', $id)->delete();
        DB::table('nilai_komponen')->where('id', $id)->delete();
        return back()->with('notifikasi', 'Komponen dihapus.');
    }

    // =========================================================================
    // SETUP PENILAIAN PER JADWAL
    // =========================================================================

    public function setupIndex($jadwalId)
    {
        $jadwal    = DB::table('v_jadwal_detail')->where('id', $jadwalId)->first();
        if (!$jadwal) abort(404);

        $setup     = DB::table('diklat_nilai_setup')
            ->where('diklat_jadwal_id', $jadwalId)->first();
        $templates = DB::table('nilai_template')->orderByDesc('id')->get();

        $aspekList = collect();
        if ($setup) {
            $aspekList = DB::table('nilai_komponen')
                ->where('template_id', $setup->template_id)->whereNull('parent_id')
                ->orderBy('urutan')->get()
                ->map(function ($a) use ($setup) {
                    $a->sub = DB::table('nilai_komponen')
                        ->where('template_id', $setup->template_id)
                        ->where('parent_id', $a->id)->orderBy('urutan')->get();
                    return $a;
                });
        }

        // Token penguji per kelompok seminar
        $seminarList = DB::table('seminar as s')
            ->join('fasilitator as fc', 'fc.id', '=', 's.cid')
            ->join('fasilitator as fp', 'fp.id', '=', 's.pid')
            ->where('s.jid', $jadwalId)
            ->select('s.id', 's.kelompok', 'fc.nama as coach', 'fp.nama as penguji')
            ->orderBy('s.kelompok')->get();

        $tokenMap = DB::table('nilai_penguji_token')
            ->whereIn('seminar_id', $seminarList->pluck('id')->toArray())
            ->get()->keyBy('seminar_id');

        return view('backend.diklat.nilai.setup_index', compact(
            'jadwal', 'setup', 'templates', 'aspekList', 'seminarList', 'tokenMap'
        ));
    }

    public function setupStore(Request $request, $jadwalId)
    {
        $request->validate([
            'template_id'          => 'required|integer',
            'passing_grade_aspek'  => 'required|numeric|min:0|max:100',
        ]);

        $existing = DB::table('diklat_nilai_setup')
            ->where('diklat_jadwal_id', $jadwalId)->first();

        if ($existing && $existing->is_locked) {
            return back()->withErrors(['Data sudah dikunci.']);
        }

        if ($existing) {
            DB::table('diklat_nilai_setup')->where('diklat_jadwal_id', $jadwalId)->update([
                'template_id'         => $request->template_id,
                'passing_grade_aspek' => $request->passing_grade_aspek,
                'updated_at'          => now(),
            ]);
        } else {
            DB::table('diklat_nilai_setup')->insert([
                'diklat_jadwal_id'    => $jadwalId,
                'template_id'         => $request->template_id,
                'passing_grade_aspek' => $request->passing_grade_aspek,
                'is_locked'           => false,
                'created_at'          => now(), 'updated_at' => now(),
            ]);
        }

        return back()->with('notifikasi', 'Setup penilaian disimpan.');
    }

    public function setupLock($jadwalId)
    {
        $setup = DB::table('diklat_nilai_setup')
            ->where('diklat_jadwal_id', $jadwalId)->first();
        if (!$setup || $setup->is_locked) abort(400);

        DB::table('diklat_nilai_setup')->where('diklat_jadwal_id', $jadwalId)->update([
            'is_locked'  => true,
            'locked_at'  => now(),
            'locked_by'  => Auth::user()->name,
            'updated_at' => now(),
        ]);

        return back()->with('notifikasi', 'Data nilai dikunci. Sertifikat dapat diterbitkan.');
    }

    // =========================================================================
    // TOKEN PENGUJI
    // =========================================================================

    public function generateToken(Request $request, $jadwalId)
    {
        $request->validate(['seminar_id' => 'required|integer']);

        $seminar = DB::table('seminar')
            ->where('id', $request->seminar_id)->where('jid', $jadwalId)->first();
        if (!$seminar) abort(404);

        if (DB::table('nilai_penguji_token')->where('seminar_id', $seminar->id)->exists()) {
            return back()->withErrors(['Token untuk kelompok ini sudah ada.']);
        }

        $jadwal = DB::table('diklat_jadwal')->where('id', $jadwalId)->first();

        do {
            $token = bin2hex(random_bytes(16));
        } while (DB::table('nilai_penguji_token')->where('token', $token)->exists());

        DB::table('nilai_penguji_token')->insert([
            'seminar_id'       => $seminar->id,
            'token'            => $token,
            'token_expired_at' => Carbon::parse($jadwal->tgl_akhir)->endOfDay(),
            'is_active'        => true,
            'created_by'       => Auth::user()->name,
            'created_at'       => now(), 'updated_at' => now(),
        ]);

        return back()->with('notifikasi', 'Token berhasil digenerate.');
    }

    public function revokeToken($tokenId)
    {
        DB::table('nilai_penguji_token')->where('id', $tokenId)
            ->update(['is_active' => false, 'updated_at' => now()]);
        return back()->with('notifikasi', 'Token dinonaktifkan.');
    }

    public function activateToken($tokenId)
    {
        DB::table('nilai_penguji_token')->where('id', $tokenId)
            ->update(['is_active' => true, 'updated_at' => now()]);
        return back()->with('notifikasi', 'Token diaktifkan kembali.');
    }

    public function regenerateToken($tokenId)
    {
        $existing = DB::table('nilai_penguji_token')->where('id', $tokenId)->first();
        if (!$existing) abort(404);

        // Ambil tgl_akhir jadwal lewat seminar.jid → diklat_jadwal
        $seminar = DB::table('seminar')->where('id', $existing->seminar_id)->first();
        $tglAkhir = null;
        if ($seminar) {
            $jadwal = DB::table('diklat_jadwal')->where('id', $seminar->jid)->first();
            if ($jadwal) {
                $tglAkhir = \Carbon\Carbon::parse($jadwal->tgl_akhir)->endOfDay();
            }
        }

        // Generate token baru yang unik
        do {
            $token = bin2hex(random_bytes(16));
        } while (DB::table('nilai_penguji_token')->where('token', $token)->exists());

        DB::table('nilai_penguji_token')->where('id', $tokenId)->update([
            'token'            => $token,
            'is_active'        => true,
            'last_login_at'    => null,
            'token_expired_at' => $tglAkhir,
            'updated_at'       => now(),
        ]);

        return back()->with('notifikasi', 'Token berhasil di-regenerate. Link lama tidak berlaku lagi.');
    }

    // =========================================================================
    // INPUT NILAI OPERATOR
    // Operator dapat input/koreksi SEMUA komponen (akademik, penguji, sikap)
    // =========================================================================

    public function inputNilai($jadwalId)
    {
        $jadwal = DB::table('v_jadwal_detail')->where('id', $jadwalId)->first();
        if (!$jadwal) abort(404);

        $setup = DB::table('diklat_nilai_setup')
            ->where('diklat_jadwal_id', $jadwalId)->first();
        if (!$setup) {
            return redirect()->route('backend.diklat.nilai.setup', $jadwalId)
                ->withErrors(['Setup penilaian belum dikonfigurasi.']);
        }

        // Semua aspek & sub-komponen (operator bisa input semua)
        $aspekList = DB::table('nilai_komponen')
            ->where('template_id', $setup->template_id)->whereNull('parent_id')
            ->orderBy('urutan')->get()
            ->map(function ($a) use ($setup) {
                $a->sub = DB::table('nilai_komponen')
                    ->where('template_id', $setup->template_id)
                    ->where('parent_id', $a->id)->orderBy('urutan')->get();
                return $a;
            });

        $pesertaList = DB::table('peserta')
            ->where('diklat_jadwal_id', $jadwalId)
            ->where('batal', false)->where('verifikasi', true)
            ->select('id', 'nip', 'nama_lengkap', 'instansi')
            ->orderBy('nama_lengkap')->get();

        // Map nilai yang sudah ada [peserta_id][komponen_id]
        $nilaiMap = array();
        $inputByMap = array();
        $pesIds = $pesertaList->pluck('id')->toArray();
        $allKomIds = DB::table('nilai_komponen')
            ->where('template_id', $setup->template_id)
            ->whereNotNull('parent_id')->pluck('id')->toArray();

        if (!empty($pesIds) && !empty($allKomIds)) {
            $existing = DB::table('peserta_nilai')
                ->whereIn('peserta_id', $pesIds)
                ->whereIn('komponen_id', $allKomIds)->get();
            foreach ($existing as $n) {
                $nilaiMap[$n->peserta_id][$n->komponen_id]    = $n->nilai;
                $inputByMap[$n->peserta_id][$n->komponen_id]  = $n->input_by;
            }
        }

        return view('backend.diklat.nilai.input_nilai', compact(
            'jadwal', 'setup', 'aspekList', 'pesertaList', 'nilaiMap', 'inputByMap'
        ));
    }

    // AJAX: simpan satu nilai (dipanggil dari tabel inline)
    public function saveNilai(Request $request, $jadwalId)
    {
        $setup = DB::table('diklat_nilai_setup')
            ->where('diklat_jadwal_id', $jadwalId)->first();
        if (!$setup || $setup->is_locked) {
            return response()->json(['error' => 'Nilai sudah dikunci.'], 403);
        }

        $request->validate([
            'peserta_id'  => 'required|integer',
            'komponen_id' => 'required|integer',
            'nilai'       => 'required|numeric|min:0|max:100',
            'catatan'     => 'nullable|string|max:300',
        ]);

        $now = now();
        DB::table('peserta_nilai')->updateOrInsert(
            ['peserta_id' => $request->peserta_id, 'komponen_id' => $request->komponen_id],
            [
                'diklat_jadwal_id' => $jadwalId,
                'nilai'            => $request->nilai,
                'catatan'          => $request->catatan,
                'input_by'         => 'operator:' . Auth::user()->name,
                'input_at'         => $now,
                'updated_by'       => Auth::user()->name,
                'updated_at'       => $now,
                'created_at'       => $now,
            ]
        );

        $this->hitungRekap($jadwalId, $request->peserta_id, $setup);

        // Kembalikan nilai aspek untuk update UI
        $komponen  = DB::table('nilai_komponen')->where('id', $request->komponen_id)->first();
        $nilaiAspek = $this->hitungNilaiAspek($jadwalId, $request->peserta_id, $komponen->parent_id);

        return response()->json([
            'success'     => true,
            'nilai_aspek' => $nilaiAspek,
        ]);
    }

    // =========================================================================
    // REKAP NILAI
    // =========================================================================

    public function rekapNilai($jadwalId)
    {
        $jadwal = DB::table('v_jadwal_detail')->where('id', $jadwalId)->first();
        if (!$jadwal) abort(404);

        $setup = DB::table('diklat_nilai_setup')
            ->where('diklat_jadwal_id', $jadwalId)->first();

        $pesertaList = DB::table('peserta')
            ->where('diklat_jadwal_id', $jadwalId)
            ->where('batal', false)->where('verifikasi', true)
            ->select('id', 'nip', 'nama_lengkap', 'instansi')
            ->orderBy('nama_lengkap')->get();

        $aspekList = collect();
        $rekapAspekMap = array();
        $rekapAkhirMap = array();

        if ($setup) {
            $aspekList = DB::table('nilai_komponen')
                ->where('template_id', $setup->template_id)->whereNull('parent_id')
                ->orderBy('urutan')->get();

            $pesIds = $pesertaList->pluck('id')->toArray();

            // Rekap per aspek
            if (!empty($pesIds)) {
                $aspekRekaps = DB::table('peserta_nilai_aspek')
                    ->where('diklat_jadwal_id', $jadwalId)
                    ->whereIn('peserta_id', $pesIds)->get();
                foreach ($aspekRekaps as $r) {
                    $rekapAspekMap[$r->peserta_id][$r->komponen_id] = $r;
                }

                // Rekap akhir
                $akhirRekaps = DB::table('peserta_nilai_rekap')
                    ->where('diklat_jadwal_id', $jadwalId)
                    ->whereIn('peserta_id', $pesIds)->get();
                foreach ($akhirRekaps as $r) {
                    $rekapAkhirMap[$r->peserta_id] = $r;
                }
            }
        }

        return view('backend.diklat.nilai.rekap_nilai', compact(
            'jadwal', 'setup', 'pesertaList', 'aspekList',
            'rekapAspekMap', 'rekapAkhirMap'
        ));
    }

    public function inputRemedial(Request $request, $jadwalId, $pesertaId)
    {
        $setup = DB::table('diklat_nilai_setup')
            ->where('diklat_jadwal_id', $jadwalId)->first();
        if (!$setup || !$setup->is_locked) abort(400);

        $rekap = DB::table('peserta_nilai_rekap')
            ->where('diklat_jadwal_id', $jadwalId)
            ->where('peserta_id', $pesertaId)->first();

        if (!$rekap || $rekap->status_kelulusan !== 'ditunda') {
            return back()->withErrors(['Peserta tidak dalam status remedial.']);
        }

        $request->validate([
            'nilai_remedial' => 'required|numeric|min:0|max:85',
        ], [
            'nilai_remedial.max' => 'Nilai remedial maksimal 85.00 sesuai peraturan.',
        ]);

        $nilaiRemedial  = $request->nilai_remedial;
        $kualRemedial   = $this->getKualifikasi($nilaiRemedial);
        $statusRemedial = $nilaiRemedial > $setup->passing_grade_aspek ? 'lulus' : 'tidak_lulus';

        DB::table('peserta_nilai_rekap')
            ->where('diklat_jadwal_id', $jadwalId)->where('peserta_id', $pesertaId)
            ->update([
                'is_remedial'         => true,
                'nilai_remedial'      => $nilaiRemedial,
                'kualifikasi_remedial'=> $kualRemedial,
                'status_remedial'     => $statusRemedial,
                'remedial_at'         => now(),
                'remedial_by'         => Auth::user()->name,
                'updated_at'          => now(),
            ]);

        return back()->with('notifikasi', 'Nilai remedial berhasil disimpan.');
    }

    public function exportExcel($jadwalId)
    {
        $jadwal = DB::table('v_jadwal_detail')->where('id', $jadwalId)->first();
        if (!$jadwal) abort(404);

        $setup = DB::table('diklat_nilai_setup')
            ->where('diklat_jadwal_id', $jadwalId)->first();
        if (!$setup) abort(404);

        $aspekList = DB::table('nilai_komponen')
            ->where('template_id', $setup->template_id)
            ->whereNull('parent_id')
            ->orderBy('urutan')->get();

        $pesertaList = DB::table('peserta')
            ->where('diklat_jadwal_id', $jadwalId)
            ->where('batal', false)->where('verifikasi', true)
            ->select('id', 'nip', 'nama_lengkap', 'instansi', 'jabatan')
            ->orderBy('nama_lengkap')->get();

        $pesIds = $pesertaList->pluck('id')->toArray();

        $rekapAspek = array();
        $rekapAkhir = array();
        if (!empty($pesIds)) {
            foreach (DB::table('peserta_nilai_aspek')
                ->where('diklat_jadwal_id', $jadwalId)
                ->whereIn('peserta_id', $pesIds)->get() as $r) {
                $rekapAspek[$r->peserta_id][$r->komponen_id] = $r;
            }
            foreach (DB::table('peserta_nilai_rekap')
                ->where('diklat_jadwal_id', $jadwalId)
                ->whereIn('peserta_id', $pesIds)->get() as $r) {
                $rekapAkhir[$r->peserta_id] = $r;
            }
        }

        $Coordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::class;
        $Fill       = \PhpOffice\PhpSpreadsheet\Style\Fill::class;
        $Alignment  = \PhpOffice\PhpSpreadsheet\Style\Alignment::class;
        $Border     = \PhpOffice\PhpSpreadsheet\Style\Border::class;

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Nilai');

        // Hitung total kolom & kolom terakhir
        $totalCols  = 5 + $aspekList->count() + 4;
        $lastColStr = $Coordinate::stringFromColumnIndex($totalCols);

        // ── Baris 1-3: Judul ─────────────────────────────────────────
        $sheet->mergeCells('A1:' . $lastColStr . '1');
        $sheet->setCellValue('A1', 'REKAP PENILAIAN PESERTA');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal($Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:' . $lastColStr . '2');
        $sheet->setCellValue('A2', $jadwal->nama);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A2')->getAlignment()
            ->setHorizontal($Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A3:' . $lastColStr . '3');
        $sheet->setCellValue('A3',
            'Passing Grade per Aspek: > ' . $setup->passing_grade_aspek .
            ' | Dicetak: ' . now()->format('d M Y H:i'));
        $sheet->getStyle('A3')->getFont()->setSize(9)->setItalic(true)
            ->getColor()->setARGB('FF666666');
        $sheet->getStyle('A3')->getAlignment()
            ->setHorizontal($Alignment::HORIZONTAL_CENTER);

        // ── Baris 4: Header kolom ─────────────────────────────────────
        $row = 4;
        $col = 1;

        // Kolom tetap
        $fixedHeaders = ['No', 'NIP', 'Nama Lengkap', 'Instansi', 'Jabatan'];
        foreach ($fixedHeaders as $h) {
            $colStr = $Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue($colStr . $row, $h);
            $sheet->getStyle($colStr . $row)->getFont()->setBold(true)
                ->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle($colStr . $row)->getFill()
                ->setFillType($Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF1E3A5F');
            $sheet->getStyle($colStr . $row)->getAlignment()
                ->setHorizontal($Alignment::HORIZONTAL_CENTER)
                ->setVertical($Alignment::VERTICAL_CENTER)
                ->setWrapText(true);
            $col++;
        }

        // Kolom aspek
        foreach ($aspekList as $aspek) {
            $colStr = $Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue($colStr . $row,
                $aspek->nama . "\n(" . round($aspek->bobot * 100) . '%)');
            $sheet->getStyle($colStr . $row)->getFont()->setBold(true)
                ->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle($colStr . $row)->getFill()
                ->setFillType($Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF1E3A5F');
            $sheet->getStyle($colStr . $row)->getAlignment()
                ->setHorizontal($Alignment::HORIZONTAL_CENTER)
                ->setVertical($Alignment::VERTICAL_CENTER)
                ->setWrapText(true);
            $col++;
        }

        // Kolom akhir
        foreach (['Nilai Akhir', 'Kualifikasi', 'Status Kelulusan', 'Ranking'] as $h) {
            $colStr = $Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue($colStr . $row, $h);
            $sheet->getStyle($colStr . $row)->getFont()->setBold(true)
                ->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle($colStr . $row)->getFill()
                ->setFillType($Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF1E3A5F');
            $sheet->getStyle($colStr . $row)->getAlignment()
                ->setHorizontal($Alignment::HORIZONTAL_CENTER)
                ->setVertical($Alignment::VERTICAL_CENTER)
                ->setWrapText(true);
            $col++;
        }
        $sheet->getRowDimension(4)->setRowHeight(45);

        // ── Baris 5+: Data peserta ────────────────────────────────────
        $kualColor = [
            'Sangat Memuaskan'           => 'FF22C55E',
            'Memuaskan'                  => 'FF3B82F6',
            'Baik'                       => 'FF84CC16',
            'Kurang Baik'                => 'FFEF4444',
            'Tidak Memenuhi Kualifikasi' => 'FF991B1B',
        ];
        $statusMap = [
            'lulus'       => 'Lulus',
            'ditunda'     => 'Ditunda Kelulusan',
            'tidak_lulus' => 'Tidak Lulus',
            'belum'       => 'Belum Lengkap',
        ];
        $statusColor = [
            'lulus'       => 'FF22C55E',
            'ditunda'     => 'FFEF4444',
            'tidak_lulus' => 'FF991B1B',
            'belum'       => 'FF94A3B8',
        ];

        foreach ($pesertaList as $i => $p) {
            $row++;
            $col = 1;

            // No
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->getStyle('A' . $row)->getAlignment()
                ->setHorizontal($Alignment::HORIZONTAL_CENTER);
            $col++;

            // NIP
            $sheet->setCellValueExplicit('B' . $row, $p->nip ?: '-',
                \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $col++;

            // Nama, Instansi, Jabatan
            $sheet->setCellValue('C' . $row, $p->nama_lengkap);
            $sheet->setCellValue('D' . $row, $p->instansi ?: '-');
            $sheet->setCellValue('E' . $row, $p->jabatan ?: '-');
            $col = 6;

            // Nilai per aspek
            foreach ($aspekList as $aspek) {
                $colStr = $Coordinate::stringFromColumnIndex($col);
                $ra     = isset($rekapAspek[$p->id][$aspek->id])
                    ? $rekapAspek[$p->id][$aspek->id] : null;

                if ($ra && $ra->nilai_mentah !== null) {
                    $sheet->setCellValue($colStr . $row, round($ra->nilai_mentah, 2));
                    $argb = isset($kualColor[$ra->kualifikasi])
                        ? $kualColor[$ra->kualifikasi] : 'FF64748B';
                    $sheet->getStyle($colStr . $row)->getFont()->setBold(true)
                        ->getColor()->setARGB('FFFFFFFF');
                    $sheet->getStyle($colStr . $row)->getFill()
                        ->setFillType($Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($argb);
                    $sheet->getStyle($colStr . $row)->getAlignment()
                        ->setHorizontal($Alignment::HORIZONTAL_CENTER);
                } else {
                    $sheet->setCellValue($colStr . $row, '-');
                    $sheet->getStyle($colStr . $row)->getAlignment()
                        ->setHorizontal($Alignment::HORIZONTAL_CENTER);
                }
                $col++;
            }

            $rk = isset($rekapAkhir[$p->id]) ? $rekapAkhir[$p->id] : null;

            // Nilai akhir
            $colStr = $Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue($colStr . $row, $rk ? round($rk->nilai_akhir, 2) : '-');
            $sheet->getStyle($colStr . $row)->getFont()->setBold(true);
            $sheet->getStyle($colStr . $row)->getAlignment()
                ->setHorizontal($Alignment::HORIZONTAL_CENTER);
            $col++;

            // Kualifikasi
            $colStr = $Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue($colStr . $row, $rk ? $rk->kualifikasi : '-');
            $col++;

            // Status kelulusan
            $colStr      = $Coordinate::stringFromColumnIndex($col);
            $statusLabel = $rk ? ($statusMap[$rk->status_kelulusan] ?? '-') : '-';
            $sheet->setCellValue($colStr . $row, $statusLabel);
            if ($rk && isset($statusColor[$rk->status_kelulusan])) {
                $sheet->getStyle($colStr . $row)->getFont()->setBold(true)
                    ->getColor()->setARGB('FFFFFFFF');
                $sheet->getStyle($colStr . $row)->getFill()
                    ->setFillType($Fill::FILL_SOLID)
                    ->getStartColor()->setARGB($statusColor[$rk->status_kelulusan]);
                $sheet->getStyle($colStr . $row)->getAlignment()
                    ->setHorizontal($Alignment::HORIZONTAL_CENTER);
            }
            $col++;

            // Ranking
            $colStr = $Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue($colStr . $row, $rk ? $rk->ranking : '-');
            $sheet->getStyle($colStr . $row)->getFont()->setBold(true);
            $sheet->getStyle($colStr . $row)->getAlignment()
                ->setHorizontal($Alignment::HORIZONTAL_CENTER);

            // Border seluruh baris
            $rangeStr = 'A' . $row . ':' . $lastColStr . $row;
            $sheet->getStyle($rangeStr)->getBorders()->getAllBorders()
                ->setBorderStyle($Border::BORDER_THIN)
                ->getColor()->setARGB('FFDDDDDD');

            // Alternating row
            if ($i % 2 === 1) {
                $sheet->getStyle('A' . $row . ':E' . $row)->getFill()
                    ->setFillType($Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF8FAFF');
            }
        }

        // ── Baris keterangan ─────────────────────────────────────────
        $row += 2;
        $sheet->mergeCells('A' . $row . ':' . $lastColStr . $row);
        $sheet->setCellValue('A' . $row,
            'Keterangan: Sangat Memuaskan (>90) | Memuaskan (>80) | ' .
            'Baik (>70) | Kurang Baik (>60) | Tidak Memenuhi Kualifikasi (<=60)');
        $sheet->getStyle('A' . $row)->getFont()->setSize(8)->setItalic(true)
            ->getColor()->setARGB('FF666666');

        // ── Lebar kolom ───────────────────────────────────────────────
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(22);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(28);
        $sheet->getColumnDimension('E')->setWidth(22);
        for ($c = 6; $c <= 5 + $aspekList->count(); $c++) {
            $sheet->getColumnDimension($Coordinate::stringFromColumnIndex($c))->setWidth(14);
        }
        $lastA = 5 + $aspekList->count();
        $sheet->getColumnDimension($Coordinate::stringFromColumnIndex($lastA + 1))->setWidth(12);
        $sheet->getColumnDimension($Coordinate::stringFromColumnIndex($lastA + 2))->setWidth(22);
        $sheet->getColumnDimension($Coordinate::stringFromColumnIndex($lastA + 3))->setWidth(20);
        $sheet->getColumnDimension($Coordinate::stringFromColumnIndex($lastA + 4))->setWidth(10);

        // Freeze header
        $sheet->freezePane('F5');

        // ── Output XLSX ───────────────────────────────────────────────
        $filename = 'rekap_nilai_' . str_slug($jadwal->nama) . '_' . date('Ymd') . '.xlsx';
        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }


    // =========================================================================
    // HELPER: Hitung Rekap
    // =========================================================================

    public function hitungRekap($jadwalId, $pesertaId, $setup)
    {
        $allKomponen = DB::table('nilai_komponen')
            ->where('template_id', $setup->template_id)->get();

        $nilaiPeserta = DB::table('peserta_nilai')
            ->where('peserta_id', $pesertaId)
            ->where('diklat_jadwal_id', $jadwalId)
            ->pluck('nilai', 'komponen_id');

        $aspekList = $allKomponen->filter(function ($k) { return is_null($k->parent_id); });

        $nilaiAkhir     = 0;
        $adaKurangBaik  = false;
        $adaTidakMemenuhi = false;
        $now = now();

        foreach ($aspekList as $aspek) {
            $subList = $allKomponen->where('parent_id', $aspek->id);

            if ($subList->count() > 0) {
                // Hitung rata-rata sub-komponen berbobot dalam aspek ini
                $totalBobotSub = $subList->sum('bobot');
                $nilaiMentah   = 0;
                foreach ($subList as $sub) {
                    $ns = isset($nilaiPeserta[$sub->id]) ? floatval($nilaiPeserta[$sub->id]) : 0;
                    // Bobot relatif dalam aspek: bobot_sub / total_bobot_sub
                    $nilaiMentah += $totalBobotSub > 0
                        ? ($ns * ($sub->bobot / $totalBobotSub))
                        : 0;
                }
            } else {
                $nilaiMentah = isset($nilaiPeserta[$aspek->id])
                    ? floatval($nilaiPeserta[$aspek->id]) : 0;
            }

            $nilaiMentah    = round($nilaiMentah, 2);
            $kualifikasi    = $this->getKualifikasi($nilaiMentah);
            $isLulusAspek   = $nilaiMentah > $setup->passing_grade_aspek;
            $nilaiAkhir    += $nilaiMentah * $aspek->bobot;

            if ($nilaiMentah <= 60) $adaTidakMemenuhi = true;
            elseif ($nilaiMentah <= 70) $adaKurangBaik = true;

            DB::table('peserta_nilai_aspek')->updateOrInsert(
                ['peserta_id' => $pesertaId, 'komponen_id' => $aspek->id],
                [
                    'diklat_jadwal_id' => $jadwalId,
                    'nilai_mentah'     => $nilaiMentah,
                    'nilai_aspek'      => round($nilaiMentah * $aspek->bobot, 4),
                    'kualifikasi'      => $kualifikasi,
                    'is_lulus_aspek'   => $isLulusAspek,
                    'calculated_at'    => $now,
                    'updated_at'       => $now,
                    'created_at'       => $now,
                ]
            );
        }

        $nilaiAkhir = round($nilaiAkhir, 2);
        $kualAkhir  = $this->getKualifikasi($nilaiAkhir);

        // Status kelulusan sesuai peraturan LAN
        if ($adaTidakMemenuhi) {
            $status = 'tidak_lulus';
        } elseif ($adaKurangBaik) {
            $status = 'ditunda';
        } else {
            $status = 'lulus';
        }

        DB::table('peserta_nilai_rekap')->updateOrInsert(
            ['diklat_jadwal_id' => $jadwalId, 'peserta_id' => $pesertaId],
            [
                'nilai_akhir'      => $nilaiAkhir,
                'kualifikasi'      => $kualAkhir,
                'status_kelulusan' => $status,
                'calculated_at'    => $now,
                'updated_at'       => $now,
                'created_at'       => $now,
            ]
        );

        // Update ranking
        $rekaps = DB::table('peserta_nilai_rekap')
            ->where('diklat_jadwal_id', $jadwalId)
            ->orderByDesc('nilai_akhir')->get();
        foreach ($rekaps as $i => $r) {
            DB::table('peserta_nilai_rekap')->where('id', $r->id)
                ->update(['ranking' => $i + 1]);
        }
    }

    private function hitungNilaiAspek($jadwalId, $pesertaId, $aspekId)
    {
        $aspek = DB::table('peserta_nilai_aspek')
            ->where('diklat_jadwal_id', $jadwalId)
            ->where('peserta_id', $pesertaId)
            ->where('komponen_id', $aspekId)->first();
        return $aspek ? $aspek->nilai_mentah : null;
    }

    private function getKualifikasi($nilai)
    {
        if ($nilai > 90)       return 'Sangat Memuaskan';
        if ($nilai > 80)       return 'Memuaskan';
        if ($nilai > 70)       return 'Baik';
        if ($nilai > 60)       return 'Kurang Baik';
        return 'Tidak Memenuhi Kualifikasi';
    }
}
