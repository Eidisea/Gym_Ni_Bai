<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use App\Models\ClassBooking;
use App\Models\MembershipSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $customerId = Auth::user()->customerProfile->customer_id;

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
        $customerId = Auth::user()->customerProfile->customer_id;

        // Guard: active membership required to book a class
        $hasActiveMembership = MembershipSubscription::where('customer_id', $customerId)
            ->where('status', MembershipSubscription::STATUS_ACTIVE)
            ->where('end_date', '>=', now()->toDateString())
            ->exists();

        if (!$hasActiveMembership) {
            return redirect()->route('customer.billing.index')
                ->with('error', 'You need an active membership to book classes.');
        }

        if ($schedule->available_slots <= 0) {
            return redirect()->back()->with('error', 'This class is full.');
        }

        $existingBooking = ClassBooking::where('customer_id', $customerId)
            ->where('schedule_id', $schedule->schedule_id)
            ->where('status', 'confirmed')
            ->first();

        if ($existingBooking) {
            return redirect()->back()->with('error', 'You are already booked for this class.');
        }

        ClassBooking::create([
            'customer_id' => $customerId,
            'schedule_id' => $schedule->schedule_id,
            'status' => 'confirmed',
        ]);

        // Decrement available slots
        $schedule->decrement('available_slots');

        // Load the booking with relationships for email
        $booking = ClassBooking::with([
            'customerProfile',
            'schedule.fitnessClass',
            'schedule.trainerProfile'
        ])->where('customer_id', $customerId)
          ->where('schedule_id', $schedule->schedule_id)
          ->latest()
          ->first();

        // Send booking confirmation email
        \Mail::to(Auth::user()->email)->send(new \App\Mail\BookingConfirmed($booking));

        return redirect()->route('customer.dashboard')->with('success', 'Class booked successfully!');
    }

    public function myBookings()
    {
        $customerId = Auth::user()->customerProfile->customer_id;

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
        $customerId = (int) Auth::user()->customerProfile->customer_id;

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

        // Update status first and save — do NOT soft-delete so the record
        // remains visible in the Past & Cancelled tab and re-booking works cleanly.
        $booking->status = ClassBooking::STATUS_CANCELLED;
        $booking->save();

        // Give the slot back
        $booking->schedule->increment('available_slots');

        // Load relationships needed for the cancellation email
        $booking->load([
            'customerProfile',
            'schedule.fitnessClass',
            'schedule.trainerProfile',
        ]);

        \Mail::to(Auth::user()->email)->send(new \App\Mail\ClassCancelled($booking));

        return redirect()->back()->with('success', 'Booking successfully cancelled.');
    }
}