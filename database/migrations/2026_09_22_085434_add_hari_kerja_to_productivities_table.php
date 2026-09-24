<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productivities', function (Blueprint $table) {
            $table->unsignedTinyInteger('hari_kerja')->after('date')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('productivities', function (Blueprint $table) {
            $table->dropColumn('hari_kerja');
        });
    }
};
