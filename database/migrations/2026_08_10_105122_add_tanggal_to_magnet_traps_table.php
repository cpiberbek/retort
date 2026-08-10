<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('magnet_traps', function (Blueprint $table) {
            $table->date('tanggal')->nullable()->after('kode_batch');
        });
    }

    public function down()
    {
        Schema::table('magnet_traps', function (Blueprint $table) {
            $table->dropColumn('tanggal');
        });
    }
};
