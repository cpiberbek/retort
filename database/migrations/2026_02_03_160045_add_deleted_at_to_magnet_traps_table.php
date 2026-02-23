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
        // Tambahkan pengecekan agar tidak error jika kolom sudah ada
        if (!Schema::hasColumn('magnet_traps', 'deleted_at')) {
            Schema::table('magnet_traps', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('magnet_traps', 'deleted_at')) {
            Schema::table('magnet_traps', function (Blueprint $table) {
                $table.dropSoftDeletes(); // Menggunakan helper bawaan Laravel
            });
        }
    }
};