<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use App\Models\ClassBooking;
use App\Models\MembershipSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // ADDED for Transactions
use Illuminate\Support\Facades\Log; // ADDED for silent error logging
use Illuminate\Support\Facades\Mail; // ADDED for sending emails

class CustomerBookingController extends Controller
{
    public function index()
    {
        $schedules = ClassSchedule::select('schedule_id', 'class_id', 'trainer_id', 'start_time', 'end_time', 'available_slots')
            ->with([
                'fitnessClass' => fn($q) => $q->withTrashed(),
                'trainerProfile' => fn($q) => $q->withTrashed()
            ])
            ->where('start_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->get();

        $customerProfile = Auth::user()->customerProfile;
        
        if (!$customerProfile) {
            return redirect()->route('customer.profile.edit')
                ->with('error', 'Please complete your customer profile first.');
        }

        $customerId = $customerProfile->customer_id;

        $userBookings = ClassBooking::where('customer_id', $customerId)
            ->where('status', 'confirmed')
            ->pluck('schedule_id')
            ->toArray();

        $hasActiveMembership = MembershipSubscription::where('customer_id', $customerId)
            ->where('status', MembershipSubscription::STATUS_ACTIVE)
            ->where('end_date', '>=', now()->toDateString())
            ->exists();

        return view('customer.classes', compact('schedules', 'userBookings', 'hasActiveMembership'));
    }

    public function store(Request $request, ClassSchedule $schedule)
    {
        $customerProfile = Auth::user()->customerProfile;
        
        if (!$customerProfile) {
            return redirect()->route('customer.profile.edit')
                ->with('error', 'Please complete your customer profile first.');
        }

        $customerId = $customerProfile->customer_id;

        // Log initial state for debugging
        Log::info("Booking attempt started", [
            'schedule_id' => $schedule->schedule_id,
            'customer_id' => $customerId,
            'initial_available_slots' => $schedule->available_slots,
            'schedule_exists' => $schedule->exists,
            'schedule_deleted_at' => $schedule->deleted_at
        ]);

        // Verify the schedule exists and is not soft-deleted
        if (!$schedule || $schedule->trashed()) {
            Log::warning("Schedule not available", [
                'schedule_id' => $schedule->schedule_id ?? 'null',
                'trashed' => $schedule->trashed() ?? 'unknown'
            ]);
            return redirect()->back()->with('error', 'This class schedule is no longer available.');
        }

        // Guard: active membership required to book a class
        $hasActiveMembership = MembershipSubscription::where('customer_id', $customerId)
            ->where('status', MembershipSubscription::STATUS_ACTIVE)
            ->where('end_date', '>=', now()->toDateString())
            ->exists();

        if (!$hasActiveMembership) {
            return redirect()->route('customer.billing.index')
                ->with('error', 'You need an active membership to book classes.');
        }

        try {
            // Simple transaction without complex re-querying
            DB::transaction(function () use ($schedule, $customerId) {
                
                // Refresh the schedule model to get the latest data from database
                $schedule->refresh();
                
                // Log current state
                Log::info("Inside transaction", [
                    'schedule_id' => $schedule->schedule_id,
                    'available_slots' => $schedule->available_slots,
                    'customer_id' => $customerId
                ]);
                
                // Check if the schedule is available for booking
                if ($schedule->available_slots <= 0) {
                    throw new \Exception('This class is full.');
                }

                // Check for existing booking
                $existingBooking = ClassBooking::where('customer_id', $customerId)
                    ->where('schedule_id', $schedule->schedule_id)
                    ->where('status', 'confirmed')
                    ->first();

                if ($existingBooking) {
                    throw new \Exception('You are already booked for this class.');
                }

                // Create the booking
                ClassBooking::create([
                    'customer_id' => $customerId,
                    'schedule_id' => $schedule->schedule_id,
                    'status' => 'confirmed',
                ]);

                // Decrement available slots securely
                $schedule->decrement('available_slots');
                
                Log::info("Booking successful", [
                    'schedule_id' => $schedule->schedule_id,
                    'customer_id' => $customerId,
                    'remaining_slots' => $schedule->available_slots - 1
                ]);
            });

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error("Booking failed", [
                'error' => $e->getMessage(),
                'schedule_id' => $schedule->schedule_id,
                'customer_id' => $customerId
            ]);
            
            // If the transaction throws an error (like "This class is full"), catch it and send it to the view
            return redirect()->back()->with('error', $e->getMessage());
        }

        // Load the booking with relationships for email
        $booking = ClassBooking::with([
            'customerProfile',
            'schedule.fitnessClass',
            'schedule.trainerProfile'
        ])->where('customer_id', $customerId)
          ->where('schedule_id', $schedule->schedule_id)
          ->latest()
          ->first();

        // FIX 3: SMTP Crash Protection
        // Wrap the email in a try-catch. If the university Wi-Fi blocks the SMTP port, 
        // the user still gets their booking, and the system just quietly logs the email failure.
        try {
            Mail::to(Auth::user()->email)->send(new \App\Mail\BookingConfirmed($booking));
        } catch (\Exception $e) {
            Log::error('SMTP Email failed to send: ' . $e->getMessage());
        }

        return redirect()->route('customer.dashboard')->with('success', 'Class booked successfully!');
    }

    public function myBookings()
    {
        $customerProfile = Auth::user()->customerProfile;
        
        if (!$customerProfile) {
            return redirect()->route('customer.profile.edit')
                ->with('error', 'Please complete your customer profile first.');
        }

        $customerId = $customerProfile->customer_id;

        $upcoming = ClassBooking::with([
            'schedule' => fn($q) => $q->withTrashed()->with([
                'fitnessClass' => fn($q) => $q->withTrashed(),
                'trainerProfile' => fn($q) => $q->withTrashed()
            ])
        ])
        ->where('customer_id', $customerId)
        ->where('status', '!=', 'cancelled')
        ->whereHas('schedule', function($q) {
            $q->where('start_time', '>=', now());
        })
        ->get()
        ->sortBy(function($booking) {
            return $booking->schedule->start_time;
        });

        $past = ClassBooking::withTrashed()
            ->with([
                'schedule' => fn($q) => $q->withTrashed()->with([
                    'fitnessClass' => fn($q) => $q->withTrashed(),
                    'trainerProfile' => fn($q) => $q->withTrashed()
                ])
            ])
            ->where('customer_id', $customerId)
            ->where(function($q) {
                $q->where('status', 'cancelled')
                  ->orWhereHas('schedule', function($subQ) {
                      $subQ->where('start_time', '<', now());
                  });
            })
            ->get()
            ->sortByDesc(function($booking) {
                return $booking->schedule->start_time;
            });

        return view('customer.my_bookings', compact('upcoming', 'past'));
    }

    public function cancelBooking(Request $request, ClassBooking $booking)
    {
        $customerProfile = Auth::user()->customerProfile;
        
        if (!$customerProfile) {
            return redirect()->route('customer.profile.edit')
                ->with('error', 'Please complete your customer profile first.');
        }

        $customerId = (int) $customerProfile->customer_id;

        if ((int) $booking->customer_id !== $customerId) {
            abort(403, 'Unauthorized action. This booking does not belong to your profile.');
        }

        // Guard: already cancelled
        if ($booking->status === ClassBooking::STATUS_CANCELLED) {
            return redirect()->back()->with('error', 'This booking has already been cancelled.');
        }

        // Enforce the 2-hour cancellation window defined in the model
        if (!$booking->canBeCancelled()) {
            return redirect()->back()->with('error', 'Bookings cannot be cancelled within 2 hours of the class start time.');
        }

        // Use transaction here as well to ensure DB consistency when freeing up slots
        DB::transaction(function () use ($booking) {
            // Update status first and save — do NOT soft-delete so the record
            // remains visible in the Past & Cancelled tab and re-booking works cleanly.
            $booking->status = ClassBooking::STATUS_CANCELLED;
            $booking->save();

            // Give the slot back - but only if the schedule still exists
            if ($booking->schedule) {
                $booking->schedule->increment('available_slots');
            }
        });

        // Load relationships needed for the cancellation email
        $booking->load([
            'customerProfile',
            'schedule.fitnessClass',
            'schedule.trainerProfile',
        ]);

        // FIX 3: SMTP Crash Protection (for cancellations)
        try {
            Mail::to(Auth::user()->email)->send(new \App\Mail\ClassCancelled($booking));
        } catch (\Exception $e) {
            Log::error('SMTP Cancellation Email failed to send: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Booking successfully cancelled.');
    }
}