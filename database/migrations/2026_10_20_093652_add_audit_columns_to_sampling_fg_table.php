<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sampling_fgs', function (Blueprint $table) {
            if (!Schema::hasColumn('sampling_fgs', 'is_audit')) {
                $table->boolean('is_audit')->default(false)->after('uuid');
            }

            if (!Schema::hasColumn('sampling_fgs', 'source_uuid')) {
                $table->char('source_uuid', 36)->nullable()->after('is_audit');
                $table->index('source_uuid');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sampling_fgs', function (Blueprint $table) {
            if (Schema::hasColumn('sampling_fgs', 'source_uuid')) {
                $table->dropIndex(['source_uuid']);
                $table->dropColumn('source_uuid');
            }

            if (Schema::hasColumn('sampling_fgs', 'is_audit')) {
                $table->dropColumn('is_audit');
            }
        });
    }
};