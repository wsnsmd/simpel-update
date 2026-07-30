<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePresensiTables extends Migration
{
    public function up()
    {
        // Sesi presensi per materi/JP
        Schema::create('presensi_sesi', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('diklat_jadwal_id');
            $table->string('nama_materi');
            $table->string('widyaiswara')->nullable();
            $table->unsignedSmallInteger('jp')->default(1)
                  ->comment('Jumlah Jam Pelajaran');
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->unsignedSmallInteger('batas_terlambat')->default(30)
                  ->comment('Menit toleransi keterlambatan');
            $table->string('token', 64)->unique()
                  ->comment('Token unik untuk QR code');
            $table->timestamp('token_expired_at')->nullable()
                  ->comment('Token expired saat jam_selesai');
            $table->boolean('aktif')->default(true);
            $table->timestamps();

            $table->foreign('diklat_jadwal_id')
                  ->references('id')->on('diklat_jadwal')
                  ->onDelete('cascade');

            $table->index(['diklat_jadwal_id', 'tanggal']);
            $table->index('token');
        });

        // Presensi per peserta per sesi
        Schema::create('presensi_peserta', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('presensi_sesi_id');
            $table->unsignedInteger('peserta_id');
            $table->timestamp('scan_at')->nullable();
            $table->enum('status', ['hadir', 'terlambat', 'izin', 'sakit', 'alpha'])
                  ->default('alpha');
            $table->text('keterangan')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->unique(['presensi_sesi_id', 'peserta_id']);

            $table->foreign('presensi_sesi_id')
                  ->references('id')->on('presensi_sesi')
                  ->onDelete('cascade');

            $table->foreign('peserta_id')
                  ->references('id')->on('peserta')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('presensi_peserta');
        Schema::dropIfExists('presensi_sesi');
    }
}
