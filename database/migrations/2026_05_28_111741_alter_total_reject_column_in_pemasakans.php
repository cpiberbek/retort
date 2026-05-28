<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemasakans', function (Blueprint $table) {
            // Mengubah tipe kolom yang sudah ada dari decimal menjadi text/json
            $table->text('total_reject')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pemasakans', function (Blueprint $table) {
            // Mengembalikan ke decimal jika di-rollback
            $table->decimal('total_reject', 8, 2)->nullable()->change();
        });
    }
};