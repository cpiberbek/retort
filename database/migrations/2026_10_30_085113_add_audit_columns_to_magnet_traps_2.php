<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('magnet_traps', 'is_audit')) {
            Schema::table('magnet_traps', function (Blueprint $table) {
                $table->boolean('is_audit')->default(false)->after('uuid');
            });
        }

        if (!Schema::hasColumn('magnet_traps', 'source_uuid')) {
            Schema::table('magnet_traps', function (Blueprint $table) {
                $table->uuid('source_uuid')->nullable()->after('is_audit');
            });
        }
    }

    public function down(): void
    {
        $columns = [];

        if (Schema::hasColumn('magnet_traps', 'is_audit')) {
            $columns[] = 'is_audit';
        }

        if (Schema::hasColumn('magnet_traps', 'source_uuid')) {
            $columns[] = 'source_uuid';
        }

        if (!empty($columns)) {
            Schema::table('magnet_traps', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};