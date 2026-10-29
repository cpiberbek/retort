<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('list_forms', function (Blueprint $table) {
            $table->json('standar_ref')->nullable()->after('last_updated');
        });
    }

    public function down()
    {
        Schema::table('list_forms', function (Blueprint $table) {
            $table->dropColumn('standar_ref');
        });
    }
};
