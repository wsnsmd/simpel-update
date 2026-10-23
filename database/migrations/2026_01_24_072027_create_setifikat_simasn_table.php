<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSetifikatSimasnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sertifikat_simasn', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sertifikat_id');

            $table->string('jenis');
            $table->string('kategori');
            $table->string('sub_kategori');

            $table->timestamps();

            $table->foreign('sertifikat_id')
                ->references('id')
                ->on('sertifikat')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sertifikat_simasn');
    }
}
