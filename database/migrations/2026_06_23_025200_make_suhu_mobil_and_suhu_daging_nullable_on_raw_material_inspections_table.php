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
        Schema::table('raw_material_inspections', function (Blueprint $table) {
            $table->decimal('suhu_mobil', 5, 2)->nullable()->change();
            $table->decimal('suhu_daging', 5, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raw_material_inspections', function (Blueprint $table) {
            $table->decimal('suhu_mobil', 5, 2)->nullable(false)->change();
            $table->decimal('suhu_daging', 5, 2)->nullable(false)->change();
        });
    }
};
