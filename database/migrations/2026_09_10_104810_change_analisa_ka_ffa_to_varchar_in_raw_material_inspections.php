<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('raw_material_inspections', function (Blueprint $table) {
            $table->string('analisa_ka_ffa_temp')
                ->nullable()
                ->after('keterangan');
        });

        DB::statement("
            UPDATE raw_material_inspections
            SET analisa_ka_ffa_temp = CAST(analisa_ka_ffa AS CHAR)
        ");

        Schema::table('raw_material_inspections', function (Blueprint $table) {
            $table->dropColumn('analisa_ka_ffa');
        });

        Schema::table('raw_material_inspections', function (Blueprint $table) {
            $table->renameColumn('analisa_ka_ffa_temp', 'analisa_ka_ffa');
        });
    }

    public function down(): void
    {
        Schema::table('raw_material_inspections', function (Blueprint $table) {
            $table->decimal(10, 2)->change();
        });
    }
};