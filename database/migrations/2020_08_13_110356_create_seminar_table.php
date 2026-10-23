<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeminarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seminar', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('jid')->comment('Jadwal ID');
            $table->unsignedInteger('cid')->comment('Fasilitator ID');
            $table->unsignedInteger('pid')->comment('Fasilitator ID');
            $table->string('kelompok')->comment('Nama Kelompok');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seminar');
    }
}
