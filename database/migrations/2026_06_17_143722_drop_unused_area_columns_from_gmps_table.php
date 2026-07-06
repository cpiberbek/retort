<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('gmps', function (Blueprint $table) {
            $table->dropColumn([
                'mp_chamber', 
                'karantina_packing', 
                'filling_susun', 
                'sampling_fg'
            ]);
        });
    }

    public function down()
    {
        Schema::table('gmps', function (Blueprint $table) {
            $table->longText('mp_chamber')->nullable();
            $table->longText('karantina_packing')->nullable();
            $table->longText('filling_susun')->nullable();
            $table->longText('sampling_fg')->nullable();
        });
    }
};