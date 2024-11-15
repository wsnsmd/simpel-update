<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMapelJadwalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mapel_jadwal', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('jid');
            $table->unsignedInteger('mid');
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_akhir')->nullable();
            $table->smallInteger('jp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mapel_jadwal');
    }
}
