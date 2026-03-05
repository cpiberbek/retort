<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('raw_material_inspections', function (Blueprint $table) {
            $table->string('kondisi_mobil', 255)->change();
        });
    }

    public function down(): void
    {
        Schema::table('raw_material_inspections', function (Blueprint $table) {
            $table->enum('kondisi_mobil', ['Bersih', 'Kotor', 'Bau', 'Bocor', 'Basah', 'Kering', 'Bebas Hama'])->change();
        });
    }
};
