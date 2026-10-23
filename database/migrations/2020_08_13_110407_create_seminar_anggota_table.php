<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeminarAnggotaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seminar_anggota', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('sid')->comment('Seminar ID');
            $table->unsignedInteger('peid')->comment('Peserta ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seminar_anggota');
    }
}
