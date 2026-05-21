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
            ->whereNull('cancellation_reason')  // exclude cancelled schedules
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

    public function store(Request $request, $scheduleId)
    {
        $customerProfile = Auth::user()->customerProfile;
        
        if (!$customerProfile) {
            return redirect()->route('customer.profile.edit')
                ->with('error', 'Please complete your customer profile first.');
        }

        $customerId = $customerProfile->customer_id;

        // Manually load the schedule using the schedule_id
        $schedule = ClassSchedule::where('schedule_id', $scheduleId)->first();

        // Log initial state for debugging
        try {
            Log::info("Booking attempt started", [
                'schedule_id' => $scheduleId,
                'customer_id' => $customerId,
                'schedule_found' => $schedule ? 'yes' : 'no',
                'initial_available_slots' => $schedule ? $schedule->available_slots : 'N/A',
                'schedule_exists' => $schedule ? $schedule->exists : false,
                'schedule_deleted_at' => $schedule ? $schedule->deleted_at : 'N/A'
            ]);
        } catch (\Exception $logError) {
            // Ignore logging errors
        }

        // Verify the schedule exists and is not soft-deleted
        if (!$schedule || $schedule->trashed()) {
            try {
                Log::warning("Schedule not available", [
                    'schedule_id' => $scheduleId,
                    'schedule_found' => $schedule ? 'yes' : 'no',
                    'trashed' => $schedule ? $schedule->trashed() : 'unknown'
                ]);
            } catch (\Exception $logError) {
                // Ignore logging errors
            }
            return redirect()->back()->with('error', 'This class schedule is no longer available.');
        }

        // Guard against booking a cancelled schedule
        if ($schedule->cancellation_reason) {
            return redirect()->back()->with('error', 'This class has been cancelled and is no longer available for booking.');
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
            DB::transaction(function () use ($schedule, $customerId) {

                // Re-fetch with a pessimistic lock so concurrent requests queue up
                // Use withTrashed() to avoid the SoftDeletes scope silently 404-ing,
                // then verify it's not actually deleted ourselves
                $locked = ClassSchedule::withTrashed()
                    ->lockForUpdate()
                    ->findOrFail($schedule->schedule_id);

                if ($locked->trashed()) {
                    throw new \Exception('This class schedule is no longer available.');
                }

                // Compute remaining slots dynamically from actual confirmed booking count
                // available_slots is the TOTAL capacity (immutable) — never decremented
                $confirmedBookings = ClassBooking::where('schedule_id', $locked->schedule_id)
                    ->where('status', 'confirmed')
                    ->count();

                try {
                    Log::info("Inside transaction", [
                        'schedule_id'      => $locked->schedule_id,
                        'total_capacity'   => $locked->available_slots,
                        'confirmed_booked' => $confirmedBookings,
                        'slots_remaining'  => $locked->available_slots - $confirmedBookings,
                        'customer_id'      => $customerId,
                    ]);
                } catch (\Exception $logError) {
                    // Ignore logging errors
                }

                if ($confirmedBookings >= $locked->available_slots) {
                    throw new \Exception('This class is full.');
                }

                // Check for existing confirmed booking
                $existingBooking = ClassBooking::where('customer_id', $customerId)
                    ->where('schedule_id', $locked->schedule_id)
                    ->where('status', 'confirmed')
                    ->first();

                if ($existingBooking) {
                    throw new \Exception('You are already booked for this class.');
                }

                ClassBooking::create([
                    'customer_id' => $customerId,
                    'schedule_id' => $locked->schedule_id,
                    'status'      => 'confirmed',
                ]);

                try {
                    Log::info("Booking successful", [
                        'schedule_id'           => $locked->schedule_id,
                        'customer_id'           => $customerId,
                        'slots_remaining_after' => $locked->available_slots - $confirmedBookings - 1,
                    ]);
                } catch (\Exception $logError) {
                    // Ignore logging errors
                }
            });

        } catch (\Exception $e) {
            // Log the error for debugging
            try {
                Log::error("Booking failed", [
                    'error' => $e->getMessage(),
                    'schedule_id' => $schedule->schedule_id,
                    'customer_id' => $customerId
                ]);
            } catch (\Exception $logError) {
                // Ignore logging errors
            }
            
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
            try {
                Log::error('SMTP Email failed to send: ' . $e->getMessage());
            } catch (\Exception $logError) {
                // Ignore logging errors
            }
        }

        return redirect()->route('customer.bookings.index')->with('success', 'Class booked successfully!');
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

    public function cancelBooking(Request $request, $bookingId)
    {
        // Manually load the booking
        $booking = ClassBooking::where('booking_id', $bookingId)->first();

        if (!$booking) {
            return redirect()->back()->with('error', 'Booking not found.');
        }

        $customerProfile = Auth::user()->customerProfile;
        
        if (!$customerProfile) {
            return redirect()->route('customer.profile.edit')
                ->with('error', 'Please complete your customer profile first.');
        }

        $customerId = $customerProfile->customer_id;

        // Ensure both values are compared as the same type
        if ((string) $booking->customer_id !== (string) $customerId) {
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
            // Update status to cancelled — the slot automatically becomes
            // available again because remaining slots are computed from booking count
            $booking->status = ClassBooking::STATUS_CANCELLED;
            $booking->save();
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
            try {
                Log::error('SMTP Cancellation Email failed to send: ' . $e->getMessage());
            } catch (\Exception $logError) {
                // Ignore logging errors
            }
        }

        return redirect()->back()->with('success', 'Booking successfully cancelled.');
    }
}