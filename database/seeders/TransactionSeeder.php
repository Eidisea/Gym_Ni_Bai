<?php

namespace Database\Seeders;

use App\Models\CardPayment;
use App\Models\CashPayment;
use App\Models\ClassBooking;
use App\Models\ClassSchedule;
use App\Models\CustomerProfile;
use App\Models\EwalletPayment;
use App\Models\FitnessClass;
use App\Models\MembershipPlan;
use App\Models\MembershipSubscription;
use App\Models\PaymentTransaction;
use App\Models\StaffProfile;
use App\Models\TrainerProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $classes = FitnessClass::all();
        $trainers = TrainerProfile::all();
        $customers = CustomerProfile::all();
        $plans = MembershipPlan::all();
        $staff = StaffProfile::first(); // Grab one staff member for cash transactions

        // 1. Generate Class Schedules (Level 3)
        foreach ($classes as $class) {
            for ($i = 0; $i < 3; $i++) {
                $start = Carbon::now()->addDays(rand(1, 14))->setHour(rand(7, 18))->setMinute(0);
                ClassSchedule::create([
                    'class_id' => $class->class_id,
                    'trainer_id' => $trainers->random()->trainer_id,
                    'start_time' => $start,
                    'end_time' => (clone $start)->addHours(1),
                    'available_slots' => rand(5, min(20, $class->max_participants)),
                ]);
            }
        }

        $schedules = ClassSchedule::all();

        // 2. Process Memberships & Payments (Levels 3, 4, 5)
        // Give every customer an active membership and some class bookings
        foreach ($customers as $customer) {
            $plan = $plans->random();
            $startDate = Carbon::now()->subDays(rand(0, 10));

            // Create the Membership Subscription
            $subscription = MembershipSubscription::create([
                'customer_id' => $customer->customer_id,
                'plan_id' => $plan->plan_id,
                'start_date' => $startDate->toDateString(),
                'end_date' => (clone $startDate)->addDays($plan->duration_days)->toDateString(),
                'status' => 'active',
            ]);

            // Determine Payment Method
            $method = $faker->randomElement(['cash', 'card', 'ewallet']);

            // Create Core Payment Transaction
            $payment = PaymentTransaction::create([
                'customer_id' => $customer->customer_id,
                'subscription_id' => $subscription->subscription_id,
                'booking_id' => null, // This is for membership payment
                'amount' => $plan->base_price,
                'payment_method' => $method,
                'status' => 'completed',
                'transaction_date' => $startDate->toDateString(),
                'notes' => "Membership payment for {$plan->plan_name}",
            ]);

            // Create Polymorphic Payment Subtype
            if ($method === 'cash') {
                CashPayment::create([
                    'transaction_id' => $payment->transaction_id,
                    'staff_id' => $staff->staff_id,
                    'amount_received' => $plan->base_price + rand(0, 500), // Some change
                    'change_given' => rand(0, 500),
                ]);
            } elseif ($method === 'card') {
                CardPayment::create([
                    'transaction_id' => $payment->transaction_id,
                    'card_last_four' => str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                    'card_type' => $faker->randomElement(['visa', 'mastercard', 'amex']),
                    'authorization_code' => 'AUTH-' . rand(100000, 999999),
                    'processor_reference' => 'REF-' . strtoupper(Str::random(8)),
                ]);
            } else {
                EwalletPayment::create([
                    'transaction_id' => $payment->transaction_id,
                    'provider' => $faker->randomElement(['gcash', 'paymaya', 'grabpay']),
                    'reference_number' => 'REF-' . rand(10000000, 99999999),
                    'account_identifier' => $faker->numerify('+63 9## ### ####'),
                ]);
            }

            // 3. Generate Class Bookings (Level 4)
            // Book the customer into 1 or 2 random schedules
            $randomSchedules = $schedules->random(rand(1, 2));
            foreach ($randomSchedules as $schedule) {
                // Prevent duplicate bookings
                $booking = ClassBooking::firstOrCreate([
                    'customer_id' => $customer->customer_id,
                    'schedule_id' => $schedule->schedule_id,
                ], [
                    'status' => 'confirmed',
                    'booked_at' => Carbon::now()->subDays(rand(0, 5)),
                ]);

                // Some bookings might have separate payments (drop-in fees)
                if (rand(1, 3) === 1) { // 33% chance of separate booking payment
                    $bookingPayment = PaymentTransaction::create([
                        'customer_id' => $customer->customer_id,
                        'subscription_id' => null,
                        'booking_id' => $booking->booking_id,
                        'amount' => rand(200, 500), // Drop-in class fee
                        'payment_method' => $faker->randomElement(['cash', 'card', 'ewallet']),
                        'status' => 'completed',
                        'transaction_date' => $booking->booked_at->toDateString(),
                        'notes' => 'Drop-in class payment',
                    ]);

                    // Create payment method details for booking payment
                    if ($bookingPayment->payment_method === 'cash') {
                        CashPayment::create([
                            'transaction_id' => $bookingPayment->transaction_id,
                            'staff_id' => $staff->staff_id,
                            'amount_received' => $bookingPayment->amount,
                            'change_given' => 0,
                        ]);
                    }
                }
            }
        }
    }
}