<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issue_complain', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->string('username');
            $table->string('username_updated')->nullable();

            $table->date('date');
            $table->string('judul_isu');
            $table->enum('jenis', ['progress', 'penyelesaian', 'update']);
            $table->text('detail');

            $table->string('plant');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issue_complain');
    }
};