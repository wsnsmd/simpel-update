<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCatatanUmumToPesertaNilaiRekap extends Migration
{
    public function up()
    {
        Schema::table('peserta_nilai_rekap', function (Blueprint $table) {
            // Catatan umum penguji saat seminar rancangan
            $table->text('catatan_rancangan')->nullable()
                  ->after('nilai_akhir')
                  ->comment('Catatan/masukan umum penguji saat seminar rancangan');

            // Catatan umum penguji saat seminar akhir
            $table->text('catatan_akhir')->nullable()
                  ->after('catatan_rancangan')
                  ->comment('Catatan/masukan umum penguji saat seminar akhir');
        });
    }

    public function down()
    {
        Schema::table('peserta_nilai_rekap', function (Blueprint $table) {
            $table->dropColumn(['catatan_rancangan', 'catatan_akhir']);
        });
    }
}
