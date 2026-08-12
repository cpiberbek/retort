<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packaging_inspection_items', function (Blueprint $table) {
            $table->decimal('condition_weight', 10, 2)->nullable()->after('condition_dimension');
        });
    }

    public function down(): void
    {
        Schema::table('packaging_inspection_items', function (Blueprint $table) {
            $table->dropColumn('condition_weight');
        });
    }
};
