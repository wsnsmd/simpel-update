<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTipeToDiklatJadwalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('diklat_jadwal', function (Blueprint $table) {
            //
            $table->string('tipe')->default('Pelatihan')->after('nama');
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
            //
            $table->dropColumn('tipe');
        });
    }
}
