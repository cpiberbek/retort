<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('washings', function (Blueprint $table) {
            $table->boolean('is_audit')
                ->default(false)
                ->after('username_updated');

            $table->uuid('source_uuid')
                ->nullable()
                ->after('is_audit');
        });
    }

    public function down(): void
    {
        Schema::table('washings', function (Blueprint $table) {
            $table->dropColumn([
                'is_audit',
                'source_uuid',
            ]);
        });
    }
};