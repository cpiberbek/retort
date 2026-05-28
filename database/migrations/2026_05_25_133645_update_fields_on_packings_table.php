<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packings', function (Blueprint $table) {
            // Hapus kolom lama
            $table->dropColumn(['berat_toples', 'berat_pouch']);
            
            // Tambahkan kolom baru
            $table->integer('jumlah_produk')->nullable()->after('kondisi_segel');
            $table->decimal('berat_pcs', 10, 2)->nullable()->after('jumlah_produk');
            $table->decimal('berat_pack', 10, 2)->nullable()->after('berat_pcs');
        });
    }

    public function down(): void
    {
        Schema::table('packings', function (Blueprint $table) {
            $table->decimal('berat_toples', 10, 2)->nullable();
            $table->decimal('berat_pouch', 10, 2)->nullable();
            $table->dropColumn(['jumlah_produk', 'berat_pcs', 'berat_pack']);
        });
    }
};