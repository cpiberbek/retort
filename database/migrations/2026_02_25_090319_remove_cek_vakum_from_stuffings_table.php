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
            // Menghapus kolom cek_vakum
            $table->dropColumn('cek_vakum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stuffings', function (Blueprint $table) {
            // Mengembalikan kolom jika migrasi di-rollback
            $table->string('cek_vakum')->nullable();
        });
    }
};