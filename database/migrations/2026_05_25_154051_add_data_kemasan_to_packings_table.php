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
        Schema::table('packings', function (Blueprint $table) {
            // Hapus kolom kemasan individu yang lama
            $table->dropColumn(['no_lot', 'tgl_kedatangan', 'nama_supplier', 'keterangan']);

            // Tambahkan wadah kolom dinamis baru berbentuk JSON
            $table->json('data_kemasan')->nullable()->after('berat_pack');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packings', function (Blueprint $table) {
            $table->string('no_lot')->nullable();
            $table->date('tgl_kedatangan')->nullable();
            $table->string('nama_supplier')->nullable();
            $table->text('keterangan')->nullable();
            $table->dropColumn('data_kemasan');
        });
    }
};