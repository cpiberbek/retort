<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_packaging', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('nama_packaging');
            $table->timestamps();
            $table->softDeletes();
            $table->uuid('updated_by')->nullable();
            $table->uuid('plant_uuid')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_packaging');
    }
};
