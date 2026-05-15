<?php

namespace Database\Seeders;

use App\Models\FitnessClass;
use App\Models\MembershipPlan;
use Illuminate\Database\Seeder;

class ConfigSeeder extends Seeder
{
    public function run(): void
    {
        // Membership Plans
        MembershipPlan::create([
            'plan_name' => '1-Day Walk-In',
            'duration_days' => 1,
            'base_price' => 150.00
        ]);

        MembershipPlan::create([
            'plan_name' => '1-Month Pro',
            'duration_days' => 30,
            'base_price' => 1500.00
        ]);

        MembershipPlan::create([
            'plan_name' => 'Annual VIP',
            'duration_days' => 365,
            'base_price' => 12000.00
        ]);

        // Fitness Classes
        FitnessClass::create([
            'class_name' => 'HIIT Bootcamp',
            'description' => 'High-intensity interval training to burn fat fast.',
            'max_participants' => 15
        ]);

        FitnessClass::create([
            'class_name' => 'Morning Vinyasa Yoga',
            'description' => 'A flowing yoga class to build strength and flexibility.',
            'max_participants' => 20
        ]);

        FitnessClass::create([
            'class_name' => 'Powerlifting 101',
            'description' => 'Learn the fundamentals of the squat, bench, and deadlift.',
            'max_participants' => 10
        ]);
    }
}