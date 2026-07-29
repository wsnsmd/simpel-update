<?php

namespace App\Http\Controllers\Diklat;

use App\Http\Controllers\Controller;
use App\JadwalDokumenSyarat;
use App\PesertaDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DokumenSyaratController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:isUser');
        $this->user = Auth::user();
    }

    // =========================================================================
    // Halaman daftar syarat dokumen untuk satu jadwal diklat
    // =========================================================================

    public function index($jadwalId)
    {
        $jadwal = DB::table('v_jadwal_detail')->where('id', $jadwalId)->first();
        if (!$jadwal) abort(404);
        $daftarSyarat = JadwalDokumenSyarat::where('diklat_jadwal_id', $jadwalId)
                    ->orderBy('urutan')
                    ->get();

        return view('backend.diklat.dokumen_syarat.index', compact('jadwal', 'daftarSyarat'));
    }

    // =========================================================================
    // Simpan syarat baru
    // =========================================================================

    public function store(Request $request, $jadwalId)
    {
        $request->validate([
            'nama'         => 'required|string|max:100',
            'keterangan'   => 'nullable|string|max:500',
            'format_izin'  => 'required|string',
            'max_size_kb'  => 'required|integer|min:100|max:10240',
            'wajib'        => 'required|boolean',
            'urutan'       => 'required|integer|min:1',
        ]);

        JadwalDokumenSyarat::create([
            'diklat_jadwal_id' => $jadwalId,
            'nama'             => $request->nama,
            'keterangan'       => $request->keterangan,
            'format_izin'      => $request->format_izin,
            'max_size_kb'      => $request->max_size_kb,
            'wajib'            => $request->wajib,
            'urutan'           => $request->urutan,
        ]);

        return back()->with('notifikasi', 'Syarat dokumen berhasil ditambahkan.');
    }

    // =========================================================================
    // Update syarat yang ada
    // =========================================================================

    public function update(Request $request, $id)
    {
        $syarat = JadwalDokumenSyarat::findOrFail($id);

        $request->validate([
            'nama'        => 'required|string|max:100',
            'keterangan'  => 'nullable|string|max:500',
            'format_izin' => 'required|string',
            'max_size_kb' => 'required|integer|min:100|max:10240',
            'wajib'       => 'required|boolean',
            'urutan'      => 'required|integer|min:1',
        ]);

        $syarat->update($request->only(['nama', 'keterangan', 'format_izin', 'max_size_kb', 'wajib', 'urutan']));

        return back()->with('notifikasi', 'Syarat dokumen berhasil diperbarui.');
    }

    // =========================================================================
    // Hapus syarat (sekaligus hapus semua file upload peserta)
    // =========================================================================

    public function destroy($id)
    {
        $syarat   = JadwalDokumenSyarat::findOrFail($id);
        $dokumens = PesertaDokumen::where('dokumen_syarat_id', $id)->get();

        foreach ($dokumens as $d) {
            if ($d->file_path && Storage::exists($d->file_path)) {
                Storage::delete($d->file_path);
            }
        }

        $syarat->delete();

        return back()->with('notifikasi', 'Syarat dokumen berhasil dihapus.');
    }

    // =========================================================================
    // Daftar dokumen yang sudah diupload peserta untuk syarat tertentu
    // =========================================================================

    public function listUpload($syaratId)
    {
        $syarat   = JadwalDokumenSyarat::with('jadwal')->findOrFail($syaratId);
        $jadwal   = DB::table('v_jadwal_detail')->where('id', $syarat->diklat_jadwal_id)->first();
        if (!$jadwal) abort(404);

        $dokumens = PesertaDokumen::with('peserta')
                    ->where('dokumen_syarat_id', $syaratId)
                    ->orderByDesc('uploaded_at')
                    ->get();

        return view('backend.diklat.dokumen_syarat.list_upload', compact('syarat', 'dokumens', 'jadwal'));
    }

    // =========================================================================
    // Preview / download file yang diupload peserta
    // =========================================================================

    public function previewFile(Request $request, $dokumenId)
    {
        $dokumen = PesertaDokumen::findOrFail($dokumenId);

        if (!$dokumen->file_path || !Storage::exists($dokumen->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $path = Storage::path($dokumen->file_path);
        $ext  = strtolower(pathinfo($dokumen->file_path, PATHINFO_EXTENSION));

        // Fallback ekstensi dari nama file asli
        if (!$ext && $dokumen->file_original_name) {
            $ext = strtolower(pathinfo($dokumen->file_original_name, PATHINFO_EXTENSION));
        }

        $mimeMap = [
            'jpg'  => 'image/jpeg', 'jpeg' => 'image/jpeg',
            'png'  => 'image/png',  'gif'  => 'image/gif',
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];
        $mime = isset($mimeMap[$ext]) ? $mimeMap[$ext] : mime_content_type($path);

        if ($request->get('download')) {
            return response()->download($path, $dokumen->file_original_name, [
                'Content-Type' => $mime,
            ]);
        }

        // Serve inline — hapus header X-Frame-Options agar bisa tampil di iframe
        return response()->file($path, [
            'Content-Type'           => $mime,
            'Content-Disposition'    => 'inline; filename="' . $dokumen->file_original_name . '"',
            'X-Frame-Options'        => 'SAMEORIGIN',
            'Cache-Control'          => 'no-store',
        ]);
    }

    // =========================================================================
    // Info file untuk modal preview inline (JSON)
    // =========================================================================

    public function previewInfo($dokumenId)
    {
        $dokumen = PesertaDokumen::with('peserta', 'dokumenSyarat')->findOrFail($dokumenId);

        if (!$dokumen->file_path || !Storage::exists($dokumen->file_path)) {
            return response()->json(['error' => 'File tidak ditemukan.'], 404);
        }

        $storagePath = Storage::path($dokumen->file_path);

        // Deteksi ekstensi: coba dari file_original_name dulu, fallback ke file_path
        $extFromOriginal = $dokumen->file_original_name
            ? strtolower(pathinfo($dokumen->file_original_name, PATHINFO_EXTENSION))
            : '';
        $extFromPath = strtolower(pathinfo($dokumen->file_path, PATHINFO_EXTENSION));
        $ext = $extFromOriginal ?: $extFromPath;

        // Fallback: deteksi via mime type fisik jika ekstensi tidak jelas
        if (!$ext || $ext === 'tmp') {
            if (function_exists('mime_content_type')) {
                $mime = mime_content_type($storagePath);
                $mimeToExt = [
                    'application/pdf'  => 'pdf',
                    'image/jpeg'       => 'jpg',
                    'image/png'        => 'png',
                    'image/gif'        => 'gif',
                    'application/msword' => 'doc',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                ];
                $ext = isset($mimeToExt[$mime]) ? $mimeToExt[$mime] : $ext;
            }
        }

        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif']);
        $isPdf   = ($ext === 'pdf');
        $isDoc   = in_array($ext, ['doc', 'docx']);

        // Log untuk debugging
        \Log::info('previewInfo', [
            'id'              => $dokumenId,
            'file_path'       => $dokumen->file_path,
            'file_original'   => $dokumen->file_original_name,
            'ext_original'    => $extFromOriginal,
            'ext_path'        => $extFromPath,
            'ext_final'       => $ext,
            'is_pdf'          => $isPdf,
            'is_image'        => $isImage,
            'storage_exists'  => file_exists($storagePath),
        ]);

        return response()->json([
            'id'           => $dokumen->id,
            'nama_peserta' => $dokumen->peserta ? $dokumen->peserta->nama_lengkap : '-',
            'nip'          => $dokumen->peserta ? $dokumen->peserta->nip : '-',
            'nama_dokumen' => $dokumen->dokumenSyarat ? $dokumen->dokumenSyarat->nama : '-',
            'file_name'    => $dokumen->file_original_name,
            'ext'          => $ext,
            'is_image'     => $isImage,
            'is_pdf'       => $isPdf,
            'is_doc'       => $isDoc,
            'url'          => route('backend.diklat.dokumen_peserta.file', $dokumenId),
            'download_url' => route('backend.diklat.dokumen_peserta.file', $dokumenId) . '?download=1',
            'uploaded_at'  => $dokumen->uploaded_at
                ? \Carbon\Carbon::parse($dokumen->uploaded_at)->format('d M Y H:i')
                : '-',
        ]);
    }

    // =========================================================================
    // Download ZIP semua dokumen untuk satu jenis syarat
    // =========================================================================

    public function downloadZipBySyarat($syaratId)
    {
        $syarat = JadwalDokumenSyarat::with('jadwal')->findOrFail($syaratId);

        $dokumens = PesertaDokumen::with('peserta')
            ->where('dokumen_syarat_id', $syaratId)
            ->get();

        if ($dokumens->isEmpty()) {
            return back()->with('notifikasi', 'Belum ada dokumen yang diupload untuk syarat ini.');
        }

        return $this->streamZip(
            $dokumens,
            function ($d) {
                // Nama file: NIP_NamaPeserta_NamaFile.ext
                $nip  = $d->peserta ? $d->peserta->nip : 'unknown';
                $nama = $d->peserta ? str_replace(' ', '_', $d->peserta->nama_lengkap) : 'peserta';
                $ext  = pathinfo($d->file_path, PATHINFO_EXTENSION);
                return $nip . '_' . $nama . '.' . $ext;
            },
            // Nama ZIP: NamaSyarat_NamaJadwal.zip
            $this->safeFilename($syarat->nama . '_' . $syarat->jadwal->nama) . '.zip'
        );
    }

    // =========================================================================
    // Download ZIP semua dokumen untuk satu peserta (semua jenis syarat)
    // =========================================================================

    public function downloadZipByPeserta($jadwalId, $pesertaId)
    {
        $jadwal  = DB::table('v_jadwal_detail')->where('id', $jadwalId)->first();
        $peserta = DB::table('peserta')->where('id', $pesertaId)->first();

        if (!$jadwal || !$peserta) abort(404);

        $syaratIds = JadwalDokumenSyarat::where('diklat_jadwal_id', $jadwalId)
            ->pluck('id')->toArray();

        $dokumens = PesertaDokumen::with('dokumenSyarat')
            ->where('peserta_id', $pesertaId)
            ->whereIn('dokumen_syarat_id', $syaratIds)
            ->get();

        if ($dokumens->isEmpty()) {
            return back()->with('notifikasi', 'Peserta ini belum mengupload dokumen apapun.');
        }

        return $this->streamZip(
            $dokumens,
            function ($d) {
                // Nama file: NamaSyarat_NamaFile.ext
                $syaratNama = $d->dokumenSyarat ? str_replace(' ', '_', $d->dokumenSyarat->nama) : 'dokumen';
                $ext        = pathinfo($d->file_path, PATHINFO_EXTENSION);
                return $syaratNama . '.' . $ext;
            },
            $this->safeFilename($peserta->nip . '_' . $peserta->nama_lengkap) . '.zip'
        );
    }

    // =========================================================================
    // Download ZIP semua dokumen satu jadwal (dikelompokkan per jenis syarat)
    // =========================================================================

    public function downloadZipAll($jadwalId)
    {
        $jadwal = DB::table('v_jadwal_detail')->where('id', $jadwalId)->first();
        if (!$jadwal) abort(404);

        $daftarSyarat = JadwalDokumenSyarat::where('diklat_jadwal_id', $jadwalId)
            ->orderBy('urutan')->get();

        $syaratIds = $daftarSyarat->pluck('id')->toArray();

        $dokumens = PesertaDokumen::with(['peserta', 'dokumenSyarat'])
            ->whereIn('dokumen_syarat_id', $syaratIds)
            ->get();

        if ($dokumens->isEmpty()) {
            return back()->with('notifikasi', 'Belum ada dokumen yang diupload untuk pelatihan ini.');
        }

        // Kelompokkan dalam sub-folder per jenis syarat
        return $this->streamZip(
            $dokumens,
            function ($d) {
                $folder = $d->dokumenSyarat
                    ? $this->safeFilename($d->dokumenSyarat->nama)
                    : 'lainnya';
                $nip    = $d->peserta ? $d->peserta->nip : 'unknown';
                $nama   = $d->peserta ? str_replace(' ', '_', $d->peserta->nama_lengkap) : 'peserta';
                $ext    = pathinfo($d->file_path, PATHINFO_EXTENSION);
                return $folder . '/' . $nip . '_' . $nama . '.' . $ext;
            },
            $this->safeFilename('Dokumen_' . $jadwal->nama) . '.zip'
        );
    }

    // =========================================================================
    // Helper: stream ZIP ke browser tanpa menyimpan file sementara
    // =========================================================================

    private function streamZip($dokumens, callable $namaFileFn, $zipName)
    {
        // Kumpulkan file yang benar-benar ada di storage
        $files = array();
        foreach ($dokumens as $d) {
            $storagePath = Storage::path($d->file_path);
            if ($d->file_path && file_exists($storagePath)) {
                $files[] = array(
                    'path'  => $storagePath,
                    'entry' => $namaFileFn($d),
                );
            }
        }

        if (empty($files)) {
            return back()->with('notifikasi', 'File tidak ditemukan di storage.');
        }

        // Tulis ZIP ke file temp lalu stream ke browser
        $tempPath = sys_get_temp_dir() . '/' . uniqid('dokumen_') . '.zip';

        $zip = new \ZipArchive();
        if ($zip->open($tempPath, \ZipArchive::CREATE) !== true) {
            abort(500, 'Gagal membuat file ZIP.');
        }

        foreach ($files as $f) {
            $zip->addFile($f['path'], $f['entry']);
        }

        $zip->close();

        return response()->download($tempPath, $zipName, [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    // Helper: bersihkan nama file dari karakter tidak aman
    private function safeFilename($name)
    {
        $name = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $name);
        $name = preg_replace('/_+/', '_', $name);
        return trim($name, '_');
    }

    // =========================================================================
    // Rekapitulasi dokumen per peserta untuk satu jadwal diklat
    // =========================================================================

    public function rekap($jadwalId)
    {
        $jadwal = DB::table('v_jadwal_detail')->where('id', $jadwalId)->first();
        if (!$jadwal) abort(404);

        // Semua syarat dokumen jadwal ini
        $daftarSyarat = JadwalDokumenSyarat::where('diklat_jadwal_id', $jadwalId)
            ->orderBy('urutan')
            ->get();

        // Semua peserta aktif (tidak batal)
        $pesertaList = DB::table('peserta')
            ->where('diklat_jadwal_id', $jadwalId)
            ->where('batal', false)
            ->select('id', 'kode', 'nip', 'nama_lengkap', 'instansi', 'verifikasi', 'konfirmasi')
            ->orderBy('nama_lengkap')
            ->get();

        $pesertaIds   = $pesertaList->pluck('id')->toArray();
        $syaratIds    = $daftarSyarat->pluck('id')->toArray();

        // Semua dokumen yang sudah diupload — dikelompokkan per peserta
        $dokumenMap = array();
        if (!empty($pesertaIds) && !empty($syaratIds)) {
            $semuaDokumen = PesertaDokumen::whereIn('peserta_id', $pesertaIds)
                ->whereIn('dokumen_syarat_id', $syaratIds)
                ->get();

            foreach ($semuaDokumen as $d) {
                $dokumenMap[$d->peserta_id][$d->dokumen_syarat_id] = $d;
            }
        }

        // Hitung statistik per syarat (untuk header kolom)
        $statPerSyarat = array();
        foreach ($daftarSyarat as $s) {
            $uploaded = 0;
            $verified = 0;
            $rejected = 0;
            foreach ($pesertaIds as $pid) {
                if (isset($dokumenMap[$pid][$s->id])) {
                    $d = $dokumenMap[$pid][$s->id];
                    $uploaded++;
                    if ($d->verified_by_admin) $verified++;
                    elseif ($d->verified_at)   $rejected++;
                }
            }
            $statPerSyarat[$s->id] = array(
                'uploaded' => $uploaded,
                'verified' => $verified,
                'rejected' => $rejected,
                'missing'  => count($pesertaIds) - $uploaded,
            );
        }

        return view('backend.diklat.dokumen_syarat.rekap', compact(
            'jadwal', 'daftarSyarat', 'pesertaList', 'dokumenMap', 'statPerSyarat'
        ));
    }

    // =========================================================================
    // Verifikasi / tolak dokumen peserta
    // =========================================================================

    public function verifikasi(Request $request, $dokumenId)
    {
        $dokumen = PesertaDokumen::findOrFail($dokumenId);

        $request->validate([
            'aksi'          => 'required|in:terima,tolak',
            'catatan_admin' => 'nullable|string|max:300',
        ]);

        $dokumen->update([
            'verified_by_admin' => $request->aksi === 'terima',
            'verified_by'       => $this->user->name,
            'verified_at'       => now(),
            'catatan_admin'     => $request->catatan_admin,
        ]);

        $pesan = $request->aksi === 'terima' ? 'Dokumen diterima.' : 'Dokumen ditolak, peserta perlu upload ulang.';

        return back()->with('notifikasi', $pesan);
    }
}
