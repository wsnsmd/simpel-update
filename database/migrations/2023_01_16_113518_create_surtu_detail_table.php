<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSurtuDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surtu_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sid');
            $table->string('nip');
            $table->string('nama');
            $table->string('pangkat');
            $table->string('jabatan');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('surtu_detail');
    }
}
