<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNilaiTables extends Migration
{
    public function up()
    {
        // ── 1. Template komponen nilai (per jenis & tahun peraturan) ─────
        Schema::create('nilai_template', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama');                          // "PKA 2023 (Klasikal)"
            $table->text('deskripsi')->nullable();
            $table->unsignedSmallInteger('tahun')->nullable();
            $table->unsignedInteger('jenis_diklat_id')->nullable();
            $table->enum('mode', ['klasikal', 'blended', 'distance'])
                  ->default('klasikal');
            $table->decimal('passing_grade_aspek', 5, 2)->default(70.01)
                  ->comment('Nilai minimum per aspek untuk lulus (sesuai peraturan: >70.00)');
            $table->string('created_by')->nullable();
            $table->timestamps();
        });

        // ── 2. Komponen nilai (hierarki: aspek induk → sub-komponen) ────
        //
        // Struktur PKA:
        //   [induk] Evaluasi Akademik           bobot=0.10  penilai=operator   fase=null
        //     [sub] Analisis Kepemimpinan Kinerja  bobot=0.04  penilai=operator   fase=null
        //     [sub] Analisis Manajemen Kinerja      bobot=0.04  penilai=operator   fase=null
        //     [sub] Quiz Smart Governance           bobot=0.02  penilai=operator   fase=null
        //   [induk] Studi Lapangan              bobot=0.20  penilai=operator   fase=null
        //     [sub] Laporan Kelompok               bobot=0.10  penilai=operator   fase=null
        //     [sub] Laporan Individu               bobot=0.10  penilai=operator   fase=null
        //   [induk] Rancangan Aksi Perubahan    bobot=0.20  penilai=penguji    fase=rancangan
        //     [sub] Ketepatan Rencana              bobot=0.04  penilai=penguji    fase=rancangan
        //     ... dst
        //   [induk] Implementasi Aksi Perubahan bobot=0.30  penilai=penguji    fase=akhir
        //     [sub] Capaian Perubahan              bobot=0.05  penilai=penguji    fase=akhir
        //     ... dst
        //   [induk] Sikap & Perilaku            bobot=0.20  penilai=operator   fase=null
        //     [sub] Kerja sama & Prakarsa          bobot=0.04  penilai=operator   fase=null
        //     ... dst
        Schema::create('nilai_komponen', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('template_id');
            $table->unsignedInteger('parent_id')->nullable()
                  ->comment('null = aspek induk, isi id = sub-komponen');
            $table->string('nama');
            $table->decimal('bobot', 6, 4)
                  ->comment('Desimal 0-1: 0.04 = 4%, 0.20 = 20%');
            $table->enum('penilai', ['operator', 'penguji'])
                  ->default('operator')
                  ->comment('operator=admin/panitia, penguji=via token seminar');
            $table->enum('fase', ['rancangan', 'akhir'])->nullable()
                  ->comment('null=bukan seminar, rancangan=seminar rancangan, akhir=seminar akhir');
            $table->unsignedTinyInteger('urutan')->default(1);
            $table->text('keterangan')->nullable()
                  ->comment('Panduan penilaian / rubrik singkat');

            $table->foreign('template_id')
                  ->references('id')->on('nilai_template')
                  ->onDelete('cascade');

            $table->index(['template_id', 'parent_id']);
        });

        // ── 3. Setup penilaian per jadwal ───────────────────────────────
        Schema::create('diklat_nilai_setup', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('diklat_jadwal_id')->unique();
            $table->unsignedInteger('template_id');
            $table->decimal('passing_grade_aspek', 5, 2)->default(70.01)
                  ->comment('Nilai minimum per aspek untuk lulus (default dari template)');
            $table->boolean('is_locked')->default(false);
            $table->timestamp('locked_at')->nullable();
            $table->string('locked_by')->nullable();
            $table->timestamps();

            $table->foreign('template_id')
                  ->references('id')->on('nilai_template');
        });

        // ── 4. Token akses penguji (1 token per kelompok seminar) ───────
        //    Token digenerate dari halaman seminar jadwal
        //    Penguji (fasilitator.pid) buka /nilai/penguji/{token}
        //    → otomatis tahu kelompok & peserta yang dinilai
        Schema::create('nilai_penguji_token', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('seminar_id')
                  ->comment('FK ke tabel seminar (sudah ada)');
            $table->string('token', 32)->unique();
            $table->timestamp('token_expired_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->foreign('seminar_id')
                  ->references('id')->on('seminar')
                  ->onDelete('cascade');
        });

        // ── 5. Nilai per peserta per sub-komponen ───────────────────────
        //    Nilai hanya disimpan di level sub-komponen (leaf node)
        //    Nilai aspek induk dihitung otomatis dari sub-komponennya
        //    OPERATOR bisa input/koreksi SEMUA komponen
        //    PENGUJI hanya bisa input komponen dengan penilai=penguji
        Schema::create('peserta_nilai', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('diklat_jadwal_id');
            $table->unsignedInteger('peserta_id');
            $table->unsignedInteger('komponen_id')
                  ->comment('Sub-komponen (leaf), bukan aspek induk');
            $table->decimal('nilai', 5, 2)->nullable()
                  ->comment('0.00 - 100.00');
            $table->text('catatan')->nullable()
                  ->comment('Rekod/catatan dari penguji saat seminar');
            $table->string('input_by')->nullable()
                  ->comment('Format: operator:{nama} atau penguji:{seminar_id}:{nama_penguji}');
            $table->timestamp('input_at')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();

            $table->unique(['peserta_id', 'komponen_id'], 'uq_peserta_komponen');

            $table->index(['diklat_jadwal_id', 'peserta_id']);

            $table->foreign('komponen_id')
                  ->references('id')->on('nilai_komponen')
                  ->onDelete('cascade');
        });

        // ── 6. Rekap per aspek (dihitung otomatis) ─────────────────────
        //    Sesuai peraturan: kelulusan dinilai PER ASPEK (bukan hanya nilai akhir)
        //    Setiap aspek induk punya status kualifikasi sendiri
        Schema::create('peserta_nilai_aspek', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('diklat_jadwal_id');
            $table->unsignedInteger('peserta_id');
            $table->unsignedInteger('komponen_id')
                  ->comment('ID aspek INDUK');
            $table->decimal('nilai_aspek', 5, 2)->nullable()
                  ->comment('Nilai aspek ini (sudah berbobot kontribusinya)');
            $table->decimal('nilai_mentah', 5, 2)->nullable()
                  ->comment('Nilai rata-rata sub-komponen sebelum dikali bobot');
            $table->string('kualifikasi')->nullable()
                  ->comment('Sangat Memuaskan/Memuaskan/Baik/Kurang Baik/Tidak Memenuhi Kualifikasi');
            $table->boolean('is_lulus_aspek')->default(false)
                  ->comment('true jika nilai_mentah > passing_grade_aspek');
            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();

            $table->unique(['peserta_id', 'komponen_id'], 'uq_peserta_aspek');
            $table->index(['diklat_jadwal_id', 'peserta_id']);
        });

        // ── 7. Rekap nilai akhir per peserta ────────────────────────────
        //    Status kelulusan mengikuti aturan peraturan:
        //    - Lulus: semua aspek >= passing_grade_aspek
        //    - Ditunda: 1 aspek Kurang Baik (60.01-70.00), boleh remedial
        //    - Tidak Lulus: 1 aspek < 60.01 atau absen > 18 JP
        Schema::create('peserta_nilai_rekap', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('diklat_jadwal_id');
            $table->unsignedInteger('peserta_id');
            $table->decimal('nilai_akhir', 5, 2)->nullable()
                  ->comment('Total nilai berbobot semua aspek');
            $table->string('kualifikasi')->nullable()
                  ->comment('Kualifikasi nilai akhir');
            $table->enum('status_kelulusan', ['lulus', 'ditunda', 'tidak_lulus', 'belum'])->default('belum');
            $table->unsignedSmallInteger('ranking')->nullable();

            // Remedial (1 kali kesempatan, nilai max 85.00)
            $table->boolean('is_remedial')->default(false);
            $table->decimal('nilai_remedial', 5, 2)->nullable();
            $table->string('kualifikasi_remedial')->nullable();
            $table->enum('status_remedial', ['lulus', 'tidak_lulus'])->nullable();
            $table->timestamp('remedial_at')->nullable();
            $table->string('remedial_by')->nullable();

            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();

            $table->unique(['diklat_jadwal_id', 'peserta_id']);
            $table->index('diklat_jadwal_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('peserta_nilai_rekap');
        Schema::dropIfExists('peserta_nilai_aspek');
        Schema::dropIfExists('peserta_nilai');
        Schema::dropIfExists('nilai_penguji_token');
        Schema::dropIfExists('diklat_nilai_setup');
        Schema::dropIfExists('nilai_komponen');
        Schema::dropIfExists('nilai_template');
    }
}
