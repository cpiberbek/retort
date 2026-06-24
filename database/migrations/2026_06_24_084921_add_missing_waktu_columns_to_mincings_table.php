<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mincings', function (Blueprint $table) {
            $table->integer('waktu_mixing_premix')->nullable();
            $table->integer('waktu_bowl_cutter')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mincings', function (Blueprint $table) {
            $table->dropColumn(['waktu_mixing_premix', 'waktu_bowl_cutter']);
        });
    }
};
