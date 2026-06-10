<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class AdditionPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'can access form spv',
            'can access cikande',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }
    }
}