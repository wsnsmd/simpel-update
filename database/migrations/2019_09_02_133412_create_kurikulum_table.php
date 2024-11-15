<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKurikulumTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kurikulum', function (Blueprint $table) {
            $table->Increments('id');
            $table->unsignedTinyInteger('diklat_jenis_id');
            $table->string('nama')->unique();
            $table->unsignedTinyInteger('jenis_belajar')->comment('1. Klasikal Penuh; 2. Blended Learning'); // 1. Klasikal Penuh 2. Blended Learning
            $table->smallInteger('total_jp');

            $table->foreign('diklat_jenis_id')->references('id')->on('diklat_jenis');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kurikulum');
    }
}
