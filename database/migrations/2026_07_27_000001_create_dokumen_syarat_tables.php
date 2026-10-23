<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDokumenSyaratTables extends Migration
{
    public function up()
    {
        // Definisi dokumen yang disyaratkan per jadwal diklat
        // Contoh: Surat Tugas, Lembar Komitmen, Pas Foto 3x4, dll.
        Schema::create('jadwal_dokumen_syarat', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('diklat_jadwal_id');
            $table->string('nama')->comment('Nama dokumen, cth: Surat Tugas, Lembar Komitmen');
            $table->text('keterangan')->nullable()->comment('Petunjuk untuk peserta');
            $table->string('format_izin')->default('pdf,jpg,jpeg,png')
                  ->comment('Ekstensi yang diizinkan, dipisah koma');
            $table->unsignedInteger('max_size_kb')->default(2048)
                  ->comment('Ukuran maksimal file dalam KB');
            $table->boolean('wajib')->default(true)
                  ->comment('true = wajib diupload sebelum daftar bisa diproses');
            $table->unsignedTinyInteger('urutan')->default(1)
                  ->comment('Urutan tampil di form pendaftaran dan dashboard');
            $table->timestamps();

            $table->foreign('diklat_jadwal_id')
                  ->references('id')->on('diklat_jadwal')
                  ->onDelete('cascade');

            $table->index('diklat_jadwal_id');
        });

        // File yang sudah diupload oleh peserta untuk setiap syarat
        Schema::create('peserta_dokumen', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('peserta_id');
            $table->unsignedInteger('dokumen_syarat_id');
            $table->string('file_path');
            $table->string('file_original_name')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->boolean('verified_by_admin')->default(false);
            $table->string('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->timestamps();

            $table->foreign('peserta_id')
                  ->references('id')->on('peserta')
                  ->onDelete('cascade');

            $table->foreign('dokumen_syarat_id')
                  ->references('id')->on('jadwal_dokumen_syarat')
                  ->onDelete('cascade');

            // Satu peserta hanya boleh punya satu file per syarat
            $table->unique(['peserta_id', 'dokumen_syarat_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('peserta_dokumen');
        Schema::dropIfExists('jadwal_dokumen_syarat');
    }
}
