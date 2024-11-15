<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSurtuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surtu', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('jid');
            $table->string('tipe');
            $table->string('keterangan');
            $table->text('dasar')->nullable();
            $table->text('untuk')->nullable();
            $table->string('tempat')->nullable();
            $table->date('tanggal')->nullable();
            $table->string('nama')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('nip')->nullable();
            $table->string('pangkat')->nullable();
            $table->boolean('an')->default(false);
            $table->string('paraf1_nama')->nullable();
            $table->string('paraf1_jabatan')->nullable();
            $table->string('paraf2_nama')->nullable();
            $table->string('paraf2_jabatan')->nullable();
            $table->unsignedInteger('nourut');
            $table->string('nomor');
            $table->unsignedInteger('tahun');
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
        Schema::dropIfExists('surtu');
    }
}
