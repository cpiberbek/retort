<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packings', function (Blueprint $table) {
            if (!Schema::hasColumn('packings', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('data_kemasan');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packings', function (Blueprint $table) {
            if (Schema::hasColumn('packings', 'keterangan')) {
                $table->dropColumn('keterangan');
            }
        });
    }
};
