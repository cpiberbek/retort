<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('master_raw_materials', function (Blueprint $table) {
            // Menambahkan kolom setelah nama_bahan_baku
            $table->string('kode_internal')->nullable()->after('nama_bahan_baku');
            $table->string('satuan', 50)->nullable()->after('kode_internal');
        });
    }

    public function down()
    {
        Schema::table('master_raw_materials', function (Blueprint $table) {
            $table->dropColumn(['kode_internal', 'satuan']);
        });
    }
};