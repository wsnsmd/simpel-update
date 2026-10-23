<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeminarLaporanTable extends Migration
{
    public function up()
    {
        Schema::create('seminar_laporan', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('peserta_id');
            $table->unsignedBigInteger('seminar_id');          // FK ke tabel seminar (kelompok)
            $table->unsignedInteger('diklat_jadwal_id');
            $table->enum('fase', ['rancangan', 'akhir']);
            $table->string('judul', 300);
            $table->string('url', 2000);                       // Google Drive / OneDrive / dst
            $table->string('uploaded_by')->nullable();         // nama peserta
            $table->timestamps();

            // Satu peserta hanya bisa punya 1 laporan per fase per jadwal
            $table->unique(['peserta_id', 'diklat_jadwal_id', 'fase'], 'uq_laporan_peserta_fase');
        });
    }

    public function down()
    {
        Schema::dropIfExists('seminar_laporan');
    }
}
