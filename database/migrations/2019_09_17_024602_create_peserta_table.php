<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePesertaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('peserta', function (Blueprint $table) {
            $table->increments('id');
            $table->char('kode', 13)->unique();
            $table->char('nip', 18);
            $table->char('ktp', 16);
            $table->string('nama_lengkap');
            $table->string('nama_panggil')->nullable();
            $table->string('tmp_lahir');
            $table->date('tgl_lahir');
            $table->char('jk', 1);
            $table->string('alamat')->nullable();
            $table->unsignedTinyInteger('agama_id')->nullable();
            $table->string('hp');
            $table->unsignedTinyInteger('marital')->nullable();
            $table->string('email');
            $table->string('jabatan');
            $table->unsignedTinyInteger('pangkat_id');
            $table->string('foto')->nullable();
            $table->string('instansi');
            $table->string('satker_nama');
            $table->string('satker_alamat')->nullable();
            $table->string('satker_telp')->nullable();
            $table->unsignedInteger('diklat_jadwal_id');
            $table->boolean('verifikasi')->default(false);            
            $table->boolean('batal')->default(false);
            $table->string('batal_ket')->nullable();
            $table->boolean('konfirmasi')->default(false);
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
        Schema::dropIfExists('peserta');
    }
}
