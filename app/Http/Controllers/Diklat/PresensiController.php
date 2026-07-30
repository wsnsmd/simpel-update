<?php

namespace App\Http\Controllers\Diklat;

use App\Http\Controllers\Controller;
use App\PresensiSesi;
use App\PresensiPeserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PresensiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // =========================================================================
    // Daftar sesi presensi per jadwal
    // =========================================================================

    public function index($jadwalId)
    {
        $jadwal = DB::table('v_jadwal_detail')->where('id', $jadwalId)->first();
        if (!$jadwal) abort(404);

        $sesi = PresensiSesi::where('diklat_jadwal_id', $jadwalId)
            ->withCount('presensiPeserta')
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get();

        // Hitung total peserta aktif untuk jadwal ini
        $totalPeserta = DB::table('peserta')
            ->where('diklat_jadwal_id', $jadwalId)
            ->where('batal', false)
            ->where('verifikasi', true)
            ->count();

        return view('backend.diklat.presensi.index', compact('jadwal', 'sesi', 'totalPeserta'));
    }

    // =========================================================================
    // Simpan sesi baru
    // =========================================================================

    public function store(Request $request, $jadwalId)
    {
        $jadwal = DB::table('v_jadwal_detail')->where('id', $jadwalId)->first();
        if (!$jadwal) abort(404);

        $request->validate([
            'nama_materi'     => 'required|string|max:200',
            'widyaiswara'     => 'nullable|string|max:100',
            'jp'              => 'required|integer|min:1|max:20',
            'tanggal'         => 'required|date',
            'jam_mulai'       => 'required',
            'jam_selesai'     => 'required|after:jam_mulai',
            'batas_terlambat' => 'required|integer|min:0|max:120',
        ], [
            'nama_materi.required'  => 'Nama materi wajib diisi.',
            'jp.required'           => 'Jumlah JP wajib diisi.',
            'tanggal.required'      => 'Tanggal wajib diisi.',
            'jam_mulai.required'    => 'Jam mulai wajib diisi.',
            'jam_selesai.required'  => 'Jam selesai wajib diisi.',
            'jam_selesai.after'     => 'Jam selesai harus setelah jam mulai.',
        ]);

        // Generate token unik
        $token = $this->generateToken();

        // Token expired = tanggal + jam_selesai
        $tokenExpiredAt = Carbon::parse(
            $request->tanggal . ' ' . $request->jam_selesai
        );

        $sesi = PresensiSesi::create([
            'diklat_jadwal_id' => $jadwalId,
            'nama_materi'      => $request->nama_materi,
            'widyaiswara'      => $request->widyaiswara,
            'jp'               => $request->jp,
            'tanggal'          => $request->tanggal,
            'jam_mulai'        => $request->jam_mulai,
            'jam_selesai'      => $request->jam_selesai,
            'batas_terlambat'  => $request->batas_terlambat ?? 30,
            'token'            => $token,
            'token_expired_at' => $tokenExpiredAt,
            'aktif'            => true,
        ]);

        // Auto-buat record presensi alpha untuk semua peserta aktif
        $pesertaList = DB::table('peserta')
            ->where('diklat_jadwal_id', $jadwalId)
            ->where('batal', false)
            ->where('verifikasi', true)
            ->pluck('id');

        $now = now();
        $inserts = [];
        foreach ($pesertaList as $pid) {
            $inserts[] = [
                'presensi_sesi_id' => $sesi->id,
                'peserta_id'       => $pid,
                'status'           => 'alpha',
                'created_at'       => $now,
                'updated_at'       => $now,
            ];
        }
        if (!empty($inserts)) {
            DB::table('presensi_peserta')->insert($inserts);
        }

        return back()->with('notifikasi', 'Sesi presensi berhasil ditambahkan.');
    }

    // =========================================================================
    // Update sesi
    // =========================================================================

    public function update(Request $request, $sesiId)
    {
        $sesi = PresensiSesi::findOrFail($sesiId);

        $request->validate([
            'nama_materi'     => 'required|string|max:200',
            'widyaiswara'     => 'nullable|string|max:100',
            'jp'              => 'required|integer|min:1|max:20',
            'tanggal'         => 'required|date',
            'jam_mulai'       => 'required',
            'jam_selesai'     => 'required',
            'batas_terlambat' => 'required|integer|min:0|max:120',
        ]);

        $tokenExpiredAt = Carbon::parse(
            $request->tanggal . ' ' . $request->jam_selesai
        );

        $sesi->update([
            'nama_materi'      => $request->nama_materi,
            'widyaiswara'      => $request->widyaiswara,
            'jp'               => $request->jp,
            'tanggal'          => $request->tanggal,
            'jam_mulai'        => $request->jam_mulai,
            'jam_selesai'      => $request->jam_selesai,
            'batas_terlambat'  => $request->batas_terlambat,
            'token_expired_at' => $tokenExpiredAt,
        ]);

        return back()->with('notifikasi', 'Sesi berhasil diperbarui.');
    }

    // =========================================================================
    // Hapus sesi
    // =========================================================================

    public function destroy($sesiId)
    {
        $sesi = PresensiSesi::findOrFail($sesiId);
        $sesi->delete();

        return back()->with('notifikasi', 'Sesi presensi berhasil dihapus.');
    }

    // =========================================================================
    // Regenerate token QR
    // =========================================================================

    public function regenerateToken($sesiId)
    {
        $sesi = PresensiSesi::findOrFail($sesiId);
        $sesi->update(['token' => $this->generateToken()]);

        return back()->with('notifikasi', 'Token QR berhasil diperbarui.');
    }

    // =========================================================================
    // Tampilan QR code sesi (fullscreen untuk diproyeksikan)
    // =========================================================================

    public function showQr($sesiId)
    {
        $sesi   = PresensiSesi::with('jadwal')->findOrFail($sesiId);
        $jadwal = DB::table('v_jadwal_detail')->where('id', $sesi->diklat_jadwal_id)->first();

        return view('backend.diklat.presensi.qr', compact('sesi', 'jadwal'));
    }

    // =========================================================================
    // Rekap presensi per sesi
    // =========================================================================

    public function rekap($sesiId)
    {
        $sesi   = PresensiSesi::findOrFail($sesiId);
        $jadwal = DB::table('v_jadwal_detail')->where('id', $sesi->diklat_jadwal_id)->first();

        $presensi = PresensiPeserta::with('peserta')
            ->where('presensi_sesi_id', $sesiId)
            ->orderBy('scan_at')
            ->get();

        $totalHadir     = $presensi->whereIn('status', ['hadir', 'terlambat'])->count();
        $totalTerlambat = $presensi->where('status', 'terlambat')->count();
        $totalAlpha     = $presensi->where('status', 'alpha')->count();
        $totalIzin      = $presensi->where('status', 'izin')->count();
        $totalSakit     = $presensi->where('status', 'sakit')->count();

        return view('backend.diklat.presensi.rekap', compact(
            'sesi', 'jadwal', 'presensi',
            'totalHadir', 'totalTerlambat', 'totalAlpha', 'totalIzin', 'totalSakit'
        ));
    }

    // =========================================================================
    // Rekap keseluruhan semua sesi per jadwal
    // =========================================================================

    public function rekapAll($jadwalId)
    {
        $jadwal = DB::table('v_jadwal_detail')->where('id', $jadwalId)->first();
        if (!$jadwal) abort(404);

        $sesiList = PresensiSesi::where('diklat_jadwal_id', $jadwalId)
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get();

        $pesertaList = DB::table('peserta')
            ->where('diklat_jadwal_id', $jadwalId)
            ->where('batal', false)
            ->where('verifikasi', true)
            ->select('id', 'nip', 'nama_lengkap', 'instansi')
            ->orderBy('nama_lengkap')
            ->get();

        $sesiIds    = $sesiList->pluck('id')->toArray();
        $pesertaIds = $pesertaList->pluck('id')->toArray();

        // Ambil semua presensi sekaligus, kelompokkan [peserta_id][sesi_id]
        $presensiMap = array();
        if (!empty($sesiIds) && !empty($pesertaIds)) {
            $semuaPresensi = DB::table('presensi_peserta')
                ->whereIn('presensi_sesi_id', $sesiIds)
                ->whereIn('peserta_id', $pesertaIds)
                ->select('presensi_sesi_id', 'peserta_id', 'status', 'scan_at')
                ->get();

            foreach ($semuaPresensi as $p) {
                $presensiMap[$p->peserta_id][$p->presensi_sesi_id] = $p;
            }
        }

        return view('backend.diklat.presensi.rekap_all', compact(
            'jadwal', 'sesiList', 'pesertaList', 'presensiMap'
        ));
    }

    // =========================================================================
    // Ubah status manual oleh admin
    // =========================================================================

    public function updateStatus(Request $request, $presensiId)
    {
        $request->validate([
            'status'      => 'required|in:hadir,terlambat,izin,sakit,alpha',
            'keterangan'  => 'nullable|string|max:200',
        ]);

        DB::table('presensi_peserta')
            ->where('id', $presensiId)
            ->update([
                'status'      => $request->status,
                'keterangan'  => $request->keterangan,
                'updated_at'  => now(),
            ]);

        return back()->with('notifikasi', 'Status presensi berhasil diperbarui.');
    }

    // =========================================================================
    // Export Excel rekap semua sesi
    // =========================================================================

    public function exportExcel($jadwalId)
    {
        $jadwal = DB::table('v_jadwal_detail')->where('id', $jadwalId)->first();
        if (!$jadwal) abort(404);

        $sesiList = PresensiSesi::where('diklat_jadwal_id', $jadwalId)
            ->orderBy('tanggal')->orderBy('jam_mulai')->get();

        $pesertaList = DB::table('peserta')
            ->where('diklat_jadwal_id', $jadwalId)
            ->where('batal', false)->where('verifikasi', true)
            ->select('id', 'nip', 'nama_lengkap', 'instansi', 'jabatan')
            ->orderBy('nama_lengkap')->get();

        $sesiIds = $sesiList->pluck('id')->toArray();
        $presensiMap = array();
        if (!empty($sesiIds)) {
            $rows = DB::table('presensi_peserta')
                ->whereIn('presensi_sesi_id', $sesiIds)
                ->get();
            foreach ($rows as $r) {
                $presensiMap[$r->peserta_id][$r->presensi_sesi_id] = $r->status;
            }
        }

        // Build CSV manual (kompatibel Laravel 5.6 + PHP 7.3 tanpa package)
        $filename = 'presensi_' . str_slug($jadwal->nama) . '_' . date('Ymd') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function() use ($jadwal, $sesiList, $pesertaList, $presensiMap) {
            $handle = fopen('php://output', 'w');
            // BOM untuk Excel UTF-8
            fputs($handle, "\xEF\xBB\xBF");

            // Baris judul
            fputcsv($handle, ['REKAP PRESENSI: ' . $jadwal->nama]);
            fputcsv($handle, ['Periode:', $jadwal->tgl_awal . ' s.d. ' . $jadwal->tgl_akhir]);
            fputcsv($handle, []);

            // Header kolom
            $headerRow = ['No', 'NIP', 'Nama Lengkap', 'Instansi', 'Jabatan'];
            foreach ($sesiList as $s) {
                $headerRow[] = $s->nama_materi . "\n" . $s->tanggal->format('d/m') . ' ' . substr($s->jam_mulai, 0, 5);
            }
            $headerRow[] = 'Hadir';
            $headerRow[] = 'Terlambat';
            $headerRow[] = 'Alpha';
            fputcsv($handle, $headerRow);

            // Data peserta
            foreach ($pesertaList as $i => $p) {
                $row = [$i + 1, $p->nip, $p->nama_lengkap, $p->instansi, $p->jabatan];
                $hadir = 0; $terlambat = 0; $alpha = 0;
                foreach ($sesiList as $s) {
                    $status = isset($presensiMap[$p->id][$s->id])
                        ? $presensiMap[$p->id][$s->id]
                        : 'alpha';
                    // Singkatan untuk Excel
                    $kode = ['hadir' => 'H', 'terlambat' => 'T', 'izin' => 'I', 'sakit' => 'S', 'alpha' => 'A'];
                    $row[] = $kode[$status] ?? '-';
                    if ($status === 'hadir') $hadir++;
                    elseif ($status === 'terlambat') $terlambat++;
                    else $alpha++;
                }
                $row[] = $hadir;
                $row[] = $terlambat;
                $row[] = $alpha;
                fputcsv($handle, $row);
            }

            // Keterangan
            fputcsv($handle, []);
            fputcsv($handle, ['Keterangan: H=Hadir, T=Terlambat, I=Izin, S=Sakit, A=Alpha/Tidak Hadir']);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // =========================================================================
    // Helper
    // =========================================================================

    private function generateToken()
    {
        do {
            $token = bin2hex(random_bytes(32));
        } while (DB::table('presensi_sesi')->where('token', $token)->exists());

        return $token;
    }
}
