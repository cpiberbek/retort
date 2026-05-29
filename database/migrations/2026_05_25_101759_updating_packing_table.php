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
            // Mengubah tipe qrcode menjadi string untuk menyimpan path file gambar
            $table->string('qrcode')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packings', function (Blueprint $table) {
            $table->string('qrcode')->nullable(false)->change(); // sesuaikan dengan kondisi awal jika sebelumnya required teks
        });
    }
};