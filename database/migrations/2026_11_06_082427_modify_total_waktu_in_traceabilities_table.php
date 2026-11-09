<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('traceabilities', function (Blueprint $table) {
            $table->string('total_waktu', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('traceabilities', function (Blueprint $table) {
            $table->decimal('total_waktu', 8, 2)->nullable()->change();
        });
    }
};
