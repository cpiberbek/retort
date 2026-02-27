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
        Schema::table('release_packings', function (Blueprint $table) {
            // Hapus modifier ->nullable() dan gabungkan ke dalam array
            $table->dropColumn(['jumlah_box', 'reject']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('release_packings', function (Blueprint $table) {
            // Bagian ini sudah benar
            $table->integer('jumlah_box')->nullable();
            $table->integer('reject')->nullable();
        });
    }
};