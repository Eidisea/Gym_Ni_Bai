<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['Admin', 'Staff', 'Trainer', 'Customer'];

        foreach ($roles as $role) {
            Role::create(['role_name' => $role]);
        }
    }
}