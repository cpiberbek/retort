<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('area_sanitasis', function (Blueprint $table) {
            // Menambahkan kolom sub_area (bisa disesuaikan tipe datanya, misal string)
            $table->string('sub_area')->nullable()->after('area');
        });
    }

    public function down()
    {
        Schema::table('area_sanitasis', function (Blueprint $table) {
            // Menghapus kolom jika di-rollback
            $table->dropColumn('sub_area');
        });
    }
};
