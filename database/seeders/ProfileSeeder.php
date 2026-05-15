<?php

namespace Database\Seeders;

use App\Models\CustomerProfile;
use App\Models\Role;
use App\Models\StaffProfile;
use App\Models\TrainerProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProfileSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Staff Profiles - Only for users without existing profiles
        $staffUsers = User::whereHas('role', fn($q) => $q->where('role_name', 'Staff'))
            ->whereDoesntHave('staffProfile')
            ->get();
        foreach ($staffUsers as $staff) {
            StaffProfile::create([
                'user_id' => $staff->user_id,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'department' => $faker->randomElement(['Front Desk', 'Customer Service']),
            ]);
        }

        // Customer Profiles - Only for users without existing profiles
        $customerUsers = User::whereHas('role', fn($q) => $q->where('role_name', 'Customer'))
            ->whereDoesntHave('customerProfile')
            ->get();
        foreach ($customerUsers as $customer) {
            CustomerProfile::create([
                'user_id' => $customer->user_id,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'phone_number' => $faker->unique()->numerify('+63 9## ### ####'),
                'date_of_birth' => $faker->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d'),
            ]);
        }

        // Standalone Trainer Profiles (No User ID!)
        $specializations = ['Yoga', 'Weightlifting', 'CrossFit', 'Cardio'];
        for ($i = 0; $i < 4; $i++) {
            TrainerProfile::create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'specialization' => $specializations[$i],
            ]);
        }
    }
}