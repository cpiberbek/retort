<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packaging_inspections', function (Blueprint $table) {
            $table->string('verified_by')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('packaging_inspections', function (Blueprint $table) {
            $table->unsignedBigInteger('verified_by')->nullable()->change();
        });
    }
};
