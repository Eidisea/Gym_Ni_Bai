<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    // Using the constants you already smartly defined in the Role model
    $roles = [Role::ADMIN, Role::STAFF, Role::CUSTOMER];

    foreach ($roles as $roleName) {
        // Changed 'name' to 'role_name' to match your schema
        Role::firstOrCreate(['role_name' => $roleName]);
    }
}
}