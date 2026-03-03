<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('area_suhus', function (Blueprint $table) {
            $table->decimal('standar_min', 10, 2)->nullable()->after('area');
            $table->decimal('standar_max', 10, 2)->nullable()->after('standar_min');
            $table->string('standar')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('area_suhus', function (Blueprint $table) {
            $table->dropColumn(['standar_min', 'standar_max']);
        });
    }
};
