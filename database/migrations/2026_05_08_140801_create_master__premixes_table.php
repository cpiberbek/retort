<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_premixes', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')->unique();

            $table->string('nama_premix');
            $table->string('kode_internal')->nullable();

            $table->enum('satuan', [
                'kg',
                'gr',
                'liter',
                'sak'
            ]);

            $table->uuid('plant_uuid');
            $table->uuid('created_by');
            $table->uuid('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_premixes');
    }
};
