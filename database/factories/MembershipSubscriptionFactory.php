<?php

namespace Database\Factories;

use App\Models\MembershipSubscription;
use App\Models\CustomerProfile;
use App\Models\MembershipPlan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class MembershipSubscriptionFactory extends Factory
{
    protected $model = MembershipSubscription::class;

    public function definition(): array
    {
        $startDate = Carbon::now()->subDays(rand(0, 10));
        $plan = MembershipPlan::inRandomOrder()->first();
        
        return [
            'customer_id' => CustomerProfile::factory(),
            'plan_id' => $plan ? $plan->plan_id : MembershipPlan::factory(),
            'start_date' => $startDate->toDateString(),
            'end_date' => (clone $startDate)->addDays($plan ? $plan->duration_days : 30)->toDateString(),
            'status' => 'Active',
        ];
    }
}