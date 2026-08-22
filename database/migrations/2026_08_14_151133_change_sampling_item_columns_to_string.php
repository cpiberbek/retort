<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('samplings', function (Blueprint $table) {
            $table->string('jumlah')->nullable()->change();
            $table->string('jamur')->nullable()->change();
            $table->string('lendir')->nullable()->change();
            $table->string('klip_tajam')->nullable()->change();
            $table->string('pin_hole')->nullable()->change();
            $table->string('air_trap_pvdc')->nullable()->change();
            $table->string('air_trap_produk')->nullable()->change();
            $table->string('keriput')->nullable()->change();
            $table->string('bengkok')->nullable()->change();
            $table->string('non_kode')->nullable()->change();
            $table->string('over_lap')->nullable()->change();
            $table->string('kecil')->nullable()->change();
            $table->string('terjepit')->nullable()->change();
            $table->string('double_klip')->nullable()->change();
            $table->string('seal_halus')->nullable()->change();
            $table->string('basah')->nullable()->change();
            $table->string('dll')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('samplings', function (Blueprint $table) {
            $table->decimal('jumlah', 8, 2)->nullable()->change();
            $table->decimal('jamur', 8, 2)->nullable()->change();
            $table->decimal('lendir', 8, 2)->nullable()->change();
            $table->decimal('klip_tajam', 8, 2)->nullable()->change();
            $table->decimal('pin_hole', 8, 2)->nullable()->change();
            $table->decimal('air_trap_pvdc', 8, 2)->nullable()->change();
            $table->decimal('air_trap_produk', 8, 2)->nullable()->change();
            $table->decimal('keriput', 8, 2)->nullable()->change();
            $table->decimal('bengkok', 8, 2)->nullable()->change();
            $table->decimal('non_kode', 8, 2)->nullable()->change();
            $table->decimal('over_lap', 8, 2)->nullable()->change();
            $table->decimal('kecil', 8, 2)->nullable()->change();
            $table->decimal('terjepit', 8, 2)->nullable()->change();
            $table->decimal('double_klip', 8, 2)->nullable()->change();
            $table->decimal('seal_halus', 8, 2)->nullable()->change();
            $table->decimal('basah', 8, 2)->nullable()->change();
            $table->decimal('dll', 8, 2)->nullable()->change();
        });
    }
};