<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSertifikatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sertifikat', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('tsid');
            $table->boolean('is_generate');
            $table->boolean('is_upload');
            $table->unsignedInteger('diklat_jadwal_id')->unique();
            $table->boolean('barcode')->default(false);
            $table->boolean('kualifikasi')->default(false);
            $table->boolean('import')->default(false);
            $table->string('format_nomor')->nullable;
            $table->string('tempat')->nullable;
            $table->date('tanggal')->nullable;
            $table->string('jabatan')->nullable;
            $table->string('nama')->nullable;
            $table->string('pangkat')->nullable;
            $table->string('nip')->nullable;
            $table->text('spesimen')->nullable;
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
        Schema::dropIfExists('sertifikat');
    }
}
