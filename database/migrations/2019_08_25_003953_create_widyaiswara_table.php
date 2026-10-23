<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWidyaiswaraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('widyaiswara', function (Blueprint $table) {
            $table->Increments('id');
            $table->char('nip', 18)->nullable();
            $table->string('nama');
            $table->unsignedInteger('pangkat_id')->nullable();
            $table->date('tmt_pangkat')->nullable();            
            $table->string('jabatan');
            $table->date('tmt_jabatan')->nullable();
            $table->string('unitkerja')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
            
            // $table->foreign('pangkat_id')->references('id')->on('pangkat');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('widyaiswara');
    }
}
