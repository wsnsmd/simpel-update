<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSimpegQueuedAtAndFailedAtToSertifikatPeserta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sertifikat_peserta', function (Blueprint $table) {
            $table->timestamp('simpeg_queued_at')->nullable()->after('simpeg_at');   // atau sesuaikan kolom sebelumnya
            $table->timestamp('simpeg_failed_at')->nullable()->after('simpeg_queued_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sertifikat_peserta', function (Blueprint $table) {
            $table->dropColumn([
                'simpeg_queued_at',
                'simpeg_failed_at'
            ]);
        });
    }
}
