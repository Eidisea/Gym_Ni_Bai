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
        // Seed in the correct order to maintain referential integrity
        $this->call([
            RoleSeeder::class,        // Level 1: Roles first
            UserSeeder::class,        // Level 2: Users (depends on roles)
            ProfileSeeder::class,     // Level 3: Profiles (depends on users)
            ConfigSeeder::class,      // Level 4: Plans & Classes (independent)
            TransactionSeeder::class, // Level 5: Schedules, Subscriptions, Bookings, Transactions
        ]);
    }
}