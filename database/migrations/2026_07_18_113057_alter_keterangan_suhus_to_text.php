<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('suhus', function (Blueprint $table) {
            $table->text('keterangan')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('suhus', function (Blueprint $table) {
            $table->string('keterangan', 255)->nullable()->change();
        });
    }
};
