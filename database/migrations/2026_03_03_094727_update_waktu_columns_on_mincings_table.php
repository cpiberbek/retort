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
        // 1. Hapus kolom lama yang formatnya 'time'
        Schema::table('mincings', function (Blueprint $table) {
            $table->dropColumn([
                'waktu_mixing_premix_awal',
                'waktu_mixing_premix_akhir',
                'waktu_bowl_cutter_awal',
                'waktu_bowl_cutter_akhir',
                'waktu_mixing' // Kita drop sekalian karena tipe datanya berubah dari time ke integer
            ]);
        });

        // 2. Tambahkan kolom baru dengan format 'integer'
        Schema::table('mincings', function (Blueprint $table) {
            $table->integer('waktu_mixing_premix')->nullable()->after('suhu_sebelum_grinding');
            $table->integer('waktu_bowl_cutter')->nullable()->after('waktu_mixing_premix');
            $table->integer('waktu_mixing')->nullable()->after('suhu_akhir_emulsi_gel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Hapus kolom integer yang baru jika di-rollback
        Schema::table('mincings', function (Blueprint $table) {
            $table->dropColumn([
                'waktu_mixing_premix',
                'waktu_bowl_cutter',
                'waktu_mixing'
            ]);
        });

        // 2. Kembalikan kolom time yang lama
        Schema::table('mincings', function (Blueprint $table) {
            $table->time('waktu_mixing_premix_awal')->nullable()->after('suhu_sebelum_grinding');
            $table->time('waktu_mixing_premix_akhir')->nullable()->after('waktu_mixing_premix_awal');
            $table->time('waktu_bowl_cutter_awal')->nullable()->after('waktu_mixing_premix_akhir');
            $table->time('waktu_bowl_cutter_akhir')->nullable()->after('waktu_bowl_cutter_awal');
            $table->time('waktu_mixing')->nullable()->after('suhu_akhir_emulsi_gel');
        });
    }
};