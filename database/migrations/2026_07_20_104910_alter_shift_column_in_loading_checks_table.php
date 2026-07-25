<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loading_checks', function (Blueprint $table) {
            $table->string('shift')->change();
        });
    }

    public function down(): void
    {
        Schema::table('loading_checks', function (Blueprint $table) {
            $table->enum('shift', ['Pagi', 'Malam'])->change();
        });
}
};
