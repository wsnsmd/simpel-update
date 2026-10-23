<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJenisLayananToDiklatJadwalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('diklat_jadwal', function (Blueprint $table) {
            $table->string('jenis_layanan')->nullable()->after('status');
            // sesuaikan after('nama_diklat') dengan struktur tabel Anda
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('diklat_jadwal', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
