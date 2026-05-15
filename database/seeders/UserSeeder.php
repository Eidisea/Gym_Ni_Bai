<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('role_name', 'Admin')->first()->role_id;
        $staffRole = Role::where('role_name', 'Staff')->first()->role_id;
        $customerRole = Role::where('role_name', 'Customer')->first()->role_id;

        // 1 Admin
        User::create([
            'role_id' => $adminRole,
            'email' => 'admin@gymnibai.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        // 2 Staff Members
        for ($i = 1; $i <= 2; $i++) {
            User::create([
                'role_id' => $staffRole,
                'email' => "staff{$i}@gymnibai.com",
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]);
        }

        // 10 Customers
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'role_id' => $customerRole,
                'email' => "customer{$i}@example.com",
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]);
        }
    }
}