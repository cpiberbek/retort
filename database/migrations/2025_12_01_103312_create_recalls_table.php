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
        Schema::create('recalls', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique(); 
            $table->string('username');
            $table->string('username_updated')->nullable();
            $table->date('date');
            $table->string('plant');
            $table->string('penyebab');
            $table->string('asal_informasi');
            $table->string('jenis_pangan')->nullable();
            $table->string('nama_dagang')->nullable();
            $table->decimal('berat_bersih', 8, 2)->nullable();
            $table->string('jenis_kemasan')->nullable();
            $table->string('kode_produksi')->nullable();
            $table->date('tanggal_produksi')->nullable();
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->string('no_pendaftaran')->nullable();
            $table->string('diproduksi_oleh')->nullable();
            $table->decimal('jumlah_produksi', 8, 2)->nullable();
            $table->decimal('jumlah_terkirim', 8, 2)->nullable();
            $table->decimal('jumlah_tersisa', 8, 2)->nullable();
            $table->string('tindak_lanjut')->nullable();
            $table->longText('distribusi')->nullable();
            $table->longText('neraca_penarikan')->nullable();
            $table->longText('simulasi')->nullable();
            $table->string('total_waktu')->nullable();
            $table->longText('evaluasi')->nullable();
            $table->string('nama_manager')->nullable();
            $table->string('status_manager')->nullable();
            $table->string('catatan_manager')->nullable();
            $table->timestamp('tgl_update_manager')->nullable();
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
        Schema::dropIfExists('recalls');
    }
};
