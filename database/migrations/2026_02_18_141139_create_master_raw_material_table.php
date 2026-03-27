<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('master_raw_materials', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('nama_bahan_baku');
            $table->uuid('plant_uuid')->nullable(); 
            $table->uuid('created_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    public function down(): void {
        Schema::dropIfExists('master_raw_materials');
    }
};