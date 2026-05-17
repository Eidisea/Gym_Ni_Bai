<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch the 3 roles dynamically
        $adminRole = Role::where('role_name', Role::ADMIN)->first();
        $staffRole = Role::where('role_name', Role::STAFF)->first();
        $customerRole = Role::where('role_name', Role::CUSTOMER)->first();

        // 1. Create Admin
        User::firstOrCreate(
            ['email' => 'admin@gymnibai.com'],
            [
                'password' => Hash::make('password123'),
                'role_id' => $adminRole->role_id,
            ]
        );

        // 2. Create Staff
        User::firstOrCreate(
            ['email' => 'staff@gymnibai.com'],
            [
                'password' => Hash::make('password123'),
                'role_id' => $staffRole->role_id,
            ]
        );

        // 3. Create Customer
        User::firstOrCreate(
            ['email' => 'customer@gmail.com'],
            [
                'password' => Hash::make('password123'),
                'role_id' => $customerRole->role_id,
            ]
        );
    }
}