<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSertifikatTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sertifikat_template', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama');
            $table->string('file');
            $table->text('preview')->nullable();
            $table->string('spesimen_kiri')->nullable();
            $table->string('spesimen_bawah')->nullable();
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
        Schema::dropIfExists('sertifikat_template');
    }
}
