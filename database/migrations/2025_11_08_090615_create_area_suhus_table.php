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
    Schema::create('area_suhus', function (Blueprint $table) {
        $table->id();
        $table->uuid('uuid')->unique();
        $table->string('username');
        $table->string('plant');
        $table->string('area');
        $table->decimal('standar_min', 5, 2);
        $table->decimal('standar_max', 5, 2);
        $table->decimal('rh_min', 5, 2)->nullable();
        $table->decimal('rh_max', 5, 2)->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area_suhus');
    }
};
