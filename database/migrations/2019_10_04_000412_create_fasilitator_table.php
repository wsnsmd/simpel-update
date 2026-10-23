<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFasilitatorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fasilitator', function (Blueprint $table) {
            $table->Increments('id');
            $table->char('nip', 18)->nullable();
            $table->string('nama');
            $table->unsignedInteger('pangkat_id')->nullable();
            $table->date('tmt_pangkat')->nullable();            
            $table->string('jabatan');
            $table->date('tmt_jabatan')->nullable();
            $table->string('instansi')->nullable();
            $table->string('satker_nama')->nullable();
            $table->string('foto')->nullable();
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
        Schema::dropIfExists('fasilitator');
    }
}
