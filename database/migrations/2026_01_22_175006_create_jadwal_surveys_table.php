<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJadwalSurveysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jadwal_surveys', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedInteger('jadwal_id');

            $table->string('survey_code');
            $table->string('survey_name');
            $table->text('params');
            $table->boolean('is_mandatory')->default(true);

            $table->timestamps();

            $table->foreign('jadwal_id')
                ->references('id')
                ->on('diklat_jadwal')
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
        Schema::dropIfExists('jadwal_surveys');
    }
}
