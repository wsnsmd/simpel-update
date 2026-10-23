<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCetakTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cetak', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->string('nama');
            $table->unsignedTinyInteger('modal_id');
            $table->string('template');
            $table->string('filename');
            $table->string('papersize')->default('folio');
            $table->string('paperorientation')->default('potrait');
            $table->string('group')->default('umum');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cetak');
    }
}
