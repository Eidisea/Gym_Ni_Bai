<?php

namespace Database\Factories;

use App\Models\ClassBooking;
use App\Models\ClassSchedule;
use App\Models\CustomerProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class ClassBookingFactory extends Factory
{
    protected $model = ClassBooking::class;

    public function definition(): array
    {
        return [
            'customer_id' => CustomerProfile::factory(),
            'schedule_id' => ClassSchedule::factory(),
            'status' => 'Confirmed',
            'booking_date' => Carbon::now()->subDays(rand(0, 5))->toDateString(),
        ];
    }
}