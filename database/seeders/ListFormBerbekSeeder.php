<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ListFormBerbekSeeder extends Seeder
{
    public function run(): void
    {
        $cikande2Uuid = '9c3f416a-ad95-47d4-8cbf-a322e1c5122b';
        $berbekUuid = '026529af-0039-4fe7-a8af-ba1143c1ab9d';

        $data = DB::table('list_forms')
            ->where('plant', $cikande2Uuid)
            ->get();

        foreach ($data as $item) {
            DB::table('list_forms')->insert([
                'uuid' => (string) Str::uuid(),
                'plant' => $berbekUuid,
                'username' => 'superadmin',
                'laporan' => $item->laporan,
                'no_dokumen' => $item->no_dokumen,
                'last_revisi' => null,
                'last_updated' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}