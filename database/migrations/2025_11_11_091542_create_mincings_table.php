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
        Schema::create('mincings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('username');
            $table->string('username_updated')->nullable();
            $table->date('date');
            $table->string('plant');
            $table->string('shift');
            $table->string('nama_produk');
            $table->string('kode_produksi');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai')->nullable();
            $table->longText('non_premix')->nullable();
            $table->longText('premix')->nullable();
            $table->string('daging')->nullable();
            $table->decimal('suhu_sebelum_grinding', 8, 2)->nullable();
            $table->time('waktu_mixing_premix_awal')->nullable();
            $table->time('waktu_mixing_premix_akhir')->nullable();
            $table->time('waktu_bowl_cutter_awal')->nullable();
            $table->time('waktu_bowl_cutter_akhir')->nullable();
            $table->time('waktu_aging_emulsi_awal')->nullable();
            $table->time('waktu_aging_emulsi_akhir')->nullable();
            $table->decimal('suhu_akhir_emulsi_gel', 8, 2)->nullable();
            $table->time('waktu_mixing')->nullable();
            $table->time('waktu_mixing_start')->nullable();
            $table->time('waktu_mixing_end')->nullable();
            $table->decimal('suhu_akhir_mixing', 8, 2)->nullable();
            $table->decimal('suhu_akhir_emulsi', 8, 2)->nullable();
            $table->string('catatan')->nullable();
            $table->string('nama_produksi');
            $table->string('status_produksi')->nullable();
            $table->timestamp('tgl_update_produksi')->nullable();
            $table->string('nama_spv')->nullable();
            $table->string('status_spv')->nullable();
            $table->string('catatan_spv')->nullable();
            $table->timestamp('tgl_update_spv')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mincings');
    }
};
