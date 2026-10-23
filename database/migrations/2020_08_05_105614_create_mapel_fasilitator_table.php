<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMapelFasilitatorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mapel_fasilitator', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('jid');
            $table->unsignedInteger('mid');
            $table->unsignedInteger('fid');
            $table->string('nip');
            $table->string('nama');
            $table->string('pangkat');
            $table->string('jabatan');
            $table->smallInteger('butir');
            $table->unsignedInteger('nomor');
            $table->smallInteger('tahun');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mapel_fasilitator');
    }
}
