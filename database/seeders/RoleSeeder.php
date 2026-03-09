<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'superadmin',
            'admin',
            'Manager',
            'SPV QC',
            'foreman_produksi',
            'Forelady',
            'QC Inspector',
            'engineer',
            'warehouse',
            'Laboran',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}