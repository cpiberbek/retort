<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemeriksaan_kekuatan_magnet_traps', function (Blueprint $table) {
            $table->boolean('is_audit')
                ->default(false)
                ->after('plant_uuid');

            $table->uuid('source_uuid')
                ->nullable()
                ->after('is_audit');
        });
    }

    public function down(): void
    {
        Schema::table('pemeriksaan_kekuatan_magnet_traps', function (Blueprint $table) {
            $table->dropColumn([
                'is_audit',
                'source_uuid',
            ]);
        });
    }
};