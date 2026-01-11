<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['admin', 'fleet_manager', 'driver', 'mechanic'] as $role) {
            Role::firstOrCreate(['name' => $role], ['guard_name' => 'web']);
        }
    }
}
