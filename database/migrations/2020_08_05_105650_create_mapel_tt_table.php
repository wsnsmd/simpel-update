<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMapelTtTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mapel_tt', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('jid');
            $table->string('tempat');
            $table->date('tanggal');
            $table->string('nama')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('nip')->nullable();
            $table->string('pangkat')->nullable();
            $table->boolean('an')->default(false);
            $table->string('paraf1_nama')->nullable();
            $table->string('paraf1_jabatan')->nullable();
            $table->string('paraf2_nama')->nullable();
            $table->string('paraf2_jabatan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mapel_tt');
    }
}
