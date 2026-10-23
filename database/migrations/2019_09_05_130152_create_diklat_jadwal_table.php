<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiklatJadwalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diklat_jadwal', function (Blueprint $table) {
            $table->Increments('id');
            $table->unsignedTinyInteger('diklat_jenis_id');
            $table->unsignedInteger('kurikulum_id');
            $table->unsignedInteger('lokasi_id');
            $table->string('nama');
            $table->smallInteger('kuota');
            $table->date('tgl_awal');
            $table->date('tgl_akhir');
            $table->unsignedInteger('tahun');
            $table->string('kelas')->comment('Dari tabel pemda');
            $table->boolean('registrasi')->default(false)->comment('0-Internal, 1-Online');
            $table->date('reg_awal')->nullable();
            $table->date('reg_akhir')->nullable();
            $table->string('panitia_nama');
            $table->string('panitia_telp');
            $table->string('panitia_email');
            $table->text('deskripsi')->nullable();
            $table->text('syarat')->nullable();
            $table->text('lampiran')->nullable();
            $table->unsignedTinyInteger('status')->default(true)->comment('0-Ditunda, 1-Dilaksanakan');
            $table->string('var_1')->nullable();
            $table->string('var_2')->nullable();
            $table->string('var_3')->nullable();
            $table->timestamps();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('usergroup')->nullable();

            $table->foreign('diklat_jenis_id')->references('id')->on('diklat_jenis');
            $table->foreign('kurikulum_id')->references('id')->on('kurikulum');
            $table->foreign('lokasi_id')->references('id')->on('lokasi');

            $table->index('tahun');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('diklat_jadwal');
    }
}
