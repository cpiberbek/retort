<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sampels', function (Blueprint $table) {
            $table->boolean('is_audit')->default(false)->after('uuid');
            $table->char('source_uuid', 36)->nullable()->after('is_audit')->index();
        });
    }

    public function down(): void
    {
        Schema::table('sampels', function (Blueprint $table) {
            if (Schema::hasColumn('sampels', 'source_uuid')) {
                $table->dropIndex(['source_uuid']);
                $table->dropColumn('source_uuid');
            }

            if (Schema::hasColumn('sampels', 'is_audit')) {
                $table->dropColumn('is_audit');
            }
        });
    }
};
