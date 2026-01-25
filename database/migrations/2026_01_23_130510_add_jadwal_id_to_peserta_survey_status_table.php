<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJadwalIdToPesertaSurveyStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('peserta_survey_status', function (Blueprint $table) {
            $table->unsignedInteger('jadwal_id')->after('id');
            $table->text('response_json')->nullable()->after('completed_at');
            $table->text('meta_json')->nullable()->after('response_json');

            $table->foreign('jadwal_id')
                ->references('id')
                ->on('diklat_jadwal')
                ->onDelete('cascade');
            $table->unique(['jadwal_id', 'peserta_id', 'survey_code'], 'uniq_jadwal_peserta_survey');
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
            $table->dropForeign(['jadwal_id']);
            $table->dropUnique('uniq_jadwal_peserta_survey');
            $table->dropColumn('jadwal_id', 'response_json', 'meta_json');
        });
    }
}
