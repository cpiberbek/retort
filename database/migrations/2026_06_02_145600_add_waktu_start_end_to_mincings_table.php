<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mincings', function (Blueprint $table) {
            // PREMIX
            $table->time('waktu_mixing_premix_start')->nullable();

            $table->time('waktu_mixing_premix_end')
                ->nullable();

            // BOWL CUTTER
            $table->time('waktu_bowl_cutter_start')
                ->nullable();


            $table->time('waktu_bowl_cutter_end')
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('mincings', function (Blueprint $table) {
            $table->dropColumn([
                'waktu_mixing_premix_start',
                'waktu_mixing_premix_end',
                'waktu_bowl_cutter_start',
                'waktu_bowl_cutter_end',
            ]);
        });
    }
};
