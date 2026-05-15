<?php

namespace Database\Factories;

use App\Models\ClassSchedule;
use App\Models\FitnessClass;
use App\Models\TrainerProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class ClassScheduleFactory extends Factory
{
    protected $model = ClassSchedule::class;

    public function definition(): array
    {
        $start = Carbon::now()->addDays(rand(1, 14))->setHour(rand(7, 18))->setMinute(0);
        
        return [
            'class_id' => FitnessClass::factory(),
            'trainer_id' => TrainerProfile::factory(),
            'start_time' => $start,
            'end_time' => (clone $start)->addHours(1),
            'available_slots' => rand(5, 20),
        ];
    }
}