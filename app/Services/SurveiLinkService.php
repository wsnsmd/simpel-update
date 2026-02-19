<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SurveiLinkService
{
    /**
     * Generate URL download yang aman dengan Signature HMAC
     *
     * @param mixed $jadwalId ID Jadwal (Gunakan 'all' untuk semua data)
     * @param string $surveyCode Kode survei (contoh: 'skm')
     * @return string
     */
    public static function generateSecureDownloadUrl($jadwalId, string $surveyCode): string
    {
        // 1. Ambil data dari config (pastikan sudah diset di config/services.php)
        $baseUrl = config('simpel.survey_app.base_url');
        $secret = config('simpel.survey_app.secret_key');

        // Validasi awal untuk mencegah error jika config kosong
        if (empty($baseUrl) || empty($secret)) {
            Log::error("Gagal generate link survei: Config base_url atau shared_secret kosong.");
            return "#";
        }

        // 2. Pastikan format jadwalId konsisten (misal: 'all' atau ID angka)
        $jadwalIdStr = (string) $jadwalId;

        // 3. Buat Tanda Tangan Digital (Signature) menggunakan HMAC SHA256
        // Urutan parameter harus SAMA dengan yang ada di Aplikasi Survei
        $signature = hash_hmac('sha256', $jadwalIdStr . $surveyCode, $secret);

        // 4. Susun URL lengkap
        // Format: {base_url}/integration/simpel/download/{jadwal_id}/{survey_code}?signature={hash}
        return sprintf(
            "%s/integration/simpel/download/%s/%s?signature=%s",
            rtrim($baseUrl, '/'),
            $jadwalIdStr,
            $surveyCode,
            $signature
        );
    }
}
