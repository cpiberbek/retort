<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productivities', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('username');
            $table->string('username_updated')->nullable();
            $table->date('date');
            $table->string('plant');
            $table->decimal('tonase_bulanan', 12, 2);
            $table->unsignedInteger('total_manpower');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['plant', 'date']);
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('productivities');
    }
};
