<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemeriksaan_kekuatan_magnet_traps', function (Blueprint $table) {
            $table->decimal('kekuatan_median_4', 8, 2)->nullable()->after('kekuatan_median_3');
            $table->decimal('kekuatan_median_5', 8, 2)->nullable()->after('kekuatan_median_4');
            $table->decimal('kekuatan_median_6', 8, 2)->nullable()->after('kekuatan_median_5');
        });
    }

    public function down(): void
    {
        Schema::table('pemeriksaan_kekuatan_magnet_traps', function (Blueprint $table) {
            $table->dropColumn([
                'kekuatan_median_4',
                'kekuatan_median_5',
                'kekuatan_median_6',
            ]);
        });
    }
};
