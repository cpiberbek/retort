<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packings', function (Blueprint $table) {
            $table->json('suhu')->nullable()->change();
        });

        DB::table('packings')
            ->whereNotNull('suhu')
            ->get()
            ->each(function ($packing) {
                $value = number_format((float) $packing->suhu, 2, '.', '');

                DB::table('packings')
                    ->where('id', $packing->id)
                    ->update([
                        'suhu' => json_encode([$value]),
                    ]);
            });
    }

    public function down(): void
    {
        //dont rollback 
    }
};
