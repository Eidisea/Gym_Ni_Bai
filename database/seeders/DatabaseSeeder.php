<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // STRICT ORDER OF OPERATIONS
        $this->call([
            RoleSeeder::class,       // 1. Create Roles first
            UserSeeder::class,       // 2. Create Users (depends on Roles)
        ]);
    }
}