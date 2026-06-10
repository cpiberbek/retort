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
        Schema::table('stuffings', function (Blueprint $table) {
            // 1. Tambahkan kolom JSON untuk menampung array dinamis
            $table->json('data_stuffing')->nullable()->after('exp_date');

            // 2. Drop kolom-kolom lama yang kini digantikan oleh JSON
            // Catatan: 'cek_vakum' tidak di-drop karena sudah dihapus di migrasi sebelumnya.
            $table->dropColumn([
                'kode_mesin',
                'jam_mulai',
                'suhu',
                'sensori',
                'kecepatan_stuffing',
                'panjang_pcs',
                'berat_pcs',
                'kebersihan_seal',
                'kekuatan_seal',
                'diameter_klip',
                'print_kode',
                'lebar_cassing',
                'catatan'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stuffings', function (Blueprint $table) {
            // 1. Hapus kolom JSON jika di-rollback
            $table->dropColumn('data_stuffing');

            // 2. Kembalikan kolom-kolom lama jika di-rollback
            $table->string('kode_mesin');
            $table->time('jam_mulai');
            $table->decimal('suhu', 8, 2)->nullable();
            $table->string('sensori')->nullable();
            $table->decimal('kecepatan_stuffing', 8, 2)->nullable();
            $table->decimal('panjang_pcs', 8, 2)->nullable();
            $table->decimal('berat_pcs', 8, 2)->nullable();
            $table->string('kebersihan_seal')->nullable();
            $table->string('kekuatan_seal')->nullable();
            $table->decimal('diameter_klip', 8, 2)->nullable();
            $table->string('print_kode')->nullable();
            $table->decimal('lebar_cassing', 8, 2)->nullable();
            $table->string('catatan')->nullable();
        });
    }
};