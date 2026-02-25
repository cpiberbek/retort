<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('raw_material_inspections', function (Blueprint $table) {
            // Mengubah menjadi decimal dengan 5 digit total, 2 digit di belakang koma (contoh: 123.45)
            $table->decimal('analisa_ka_ffa', 5, 2)->change();
            $table->decimal('suhu_mobil', 5, 2)->change();
        });
    }

    public function down(): void
    {
        Schema::table('raw_material_inspections', function (Blueprint $table) {
            // Mengembalikan ke tipe data awal jika di-rollback
            $table->boolean('analisa_ka_ffa')->default(0)->change();
            $table->string('suhu_mobil', 255)->change();
        });
    }
};