<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePesertaSurveyStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('peserta_survey_status', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedInteger('peserta_id');

            $table->string('survey_code');
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->foreign('peserta_id')
                ->references('id')
                ->on('peserta')
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
        Schema::table('peserta_survey_status', function (Blueprint $table) {
            $table->dropForeign(['peserta_id']);
        });

        Schema::dropIfExists('peserta_survey_status');
    }
}
