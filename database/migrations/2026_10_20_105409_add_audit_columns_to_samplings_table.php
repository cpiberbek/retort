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
        Schema::table('samplings', function (Blueprint $table) {
            $table->boolean('is_audit')->default(false)->after('uuid');
            $table->char('source_uuid', 36)->nullable()->after('is_audit')->index();
        });
    }

    public function down(): void
    {
        Schema::table('samplings', function (Blueprint $table) {
            if (Schema::hasColumn('samplings', 'source_uuid')) {
                $table->dropIndex(['source_uuid']);
                $table->dropColumn('source_uuid');
            }

            if (Schema::hasColumn('samplings', 'is_audit')) {
                $table->dropColumn('is_audit');
            }
        });
    }
};
