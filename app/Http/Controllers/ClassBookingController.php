<?php

namespace App\Http\Controllers;

use App\Models\ClassBooking;
use App\Models\ClassSchedule;
use App\Models\CustomerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ClassBookingController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', ClassBooking::class);

        $search = $request->get('search');
        $filter = $request->get('filter');
        $sort = $request->get('sort', 'date_desc');

        // Eager-load relationships with withTrashed() for archive-aware display
        $query = ClassBooking::with([
            'customerProfile' => fn($q) => $q->withTrashed(),
            'schedule' => fn($q) => $q->withTrashed()->with([
                'fitnessClass' => fn($fq) => $fq->withTrashed(),
                'trainerProfile' => fn($tq) => $tq->withTrashed()
            ]),
            'transactions'
        ]);

        // Search functionality
        $query->when($search, function($q) use ($search) {
            $q->where(function($subQuery) use ($search) {
                $subQuery->whereHas('customerProfile', function($customerQuery) use ($search) {
                    $customerQuery->where('first_name', 'like', "%{$search}%")
                                  ->orWhere('last_name', 'like', "%{$search}%");
                })
                ->orWhereHas('schedule.fitnessClass', function($classQuery) use ($search) {
                    $classQuery->where('class_name', 'like', "%{$search}%");
                })
                ->orWhere('status', 'like', "%{$search}%");
            });
        });

        // Filter by status
        $query->when($filter === 'confirmed', function($q) {
            $q->where('status', 'confirmed');
        });

        $query->when($filter === 'attended', function($q) {
            $q->where('status', 'attended');
        });

        $query->when($filter === 'cancelled', function($q) {
            $q->where('status', 'cancelled');
        });

        $query->when($filter === 'no_show', function($q) {
            $q->where('status', 'no_show');
        });

        // Sorting
        $query->when($sort === 'date_desc', function($q) {
            $q->orderBy('booked_at', 'desc');
        });

        $query->when($sort === 'date_asc', function($q) {
            $q->orderBy('booked_at', 'asc');
        });

        $query->when($sort === 'customer_name', function($q) {
            $q->join('customer_profiles', 'class_bookings.customer_id', '=', 'customer_profiles.customer_id')
              ->orderBy('customer_profiles.last_name', 'asc')
              ->orderBy('customer_profiles.first_name', 'asc')
              ->select('class_bookings.*');
        });

        $query->when($sort === 'class_name', function($q) {
            $q->join('class_schedules', 'class_bookings.schedule_id', '=', 'class_schedules.schedule_id')
              ->join('fitness_classes', 'class_schedules.class_id', '=', 'fitness_classes.class_id')
              ->orderBy('fitness_classes.class_name', 'asc')
              ->select('class_bookings.*');
        });

        $bookings = $query->paginate(20)->withQueryString();

        return view('class_bookings.index', compact('bookings', 'search', 'filter', 'sort'));
    }

    public function create()
    {
        Gate::authorize('create', ClassBooking::class);

        $customers = CustomerProfile::orderBy('last_name')->get();
        $schedules = ClassSchedule::with(['fitnessClass', 'trainerProfile'])
            ->where('start_time', '>', now())
            ->orderBy('start_time')
            ->get();

        return view('class_bookings.create', compact('customers', 'schedules'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', ClassBooking::class);

        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customer_profiles,customer_id'],
            'schedule_id' => ['required', 'exists:class_schedules,schedule_id'],
        ]);

        // Check if schedule has available slots
        $schedule = ClassSchedule::find($validated['schedule_id']);
        if ($schedule->isFullyBooked()) {
            return back()->withErrors(['schedule_id' => 'This class is fully booked.']);
        }

        // Check for duplicate booking
        $existingBooking = ClassBooking::where('customer_id', $validated['customer_id'])
            ->where('schedule_id', $validated['schedule_id'])
            ->where('status', 'confirmed')
            ->first();

        if ($existingBooking) {
            return back()->withErrors(['schedule_id' => 'Customer is already booked for this class.']);
        }

        // Auto-set status to 'confirmed' for new bookings
        $validated['status'] = 'confirmed';
        $validated['booked_at'] = now();
        ClassBooking::create($validated);

        return redirect()->route('class-bookings.index')
            ->with('success', 'Class booking created successfully.');
    }

    public function show(ClassBooking $classBooking)
    {
        Gate::authorize('view', $classBooking);

        // Eager-load all related data with withTrashed() for archive-aware display
        $classBooking->load([
            'customerProfile' => fn($q) => $q->withTrashed()->with('user'),
            'schedule' => fn($q) => $q->with([
                'fitnessClass' => fn($fq) => $fq->withTrashed(),
                'trainerProfile' => fn($tq) => $tq->withTrashed()
            ]),
            'transactions' => fn($q) => $q->with([
                'cashPayment' => fn($cq) => $cq->with(['staffProfile' => fn($sq) => $sq->withTrashed()]),
                'cardPayment',
                'ewalletPayment'
            ])
        ]);

        return view('class_bookings.show', compact('classBooking'));
    }

    public function edit(ClassBooking $classBooking)
    {
        Gate::authorize('update', $classBooking);

        $customers = CustomerProfile::orderBy('last_name')->get();
        $schedules = ClassSchedule::with(['fitnessClass', 'trainerProfile'])
            ->orderBy('start_time')
            ->get();

        return view('class_bookings.edit', compact('classBooking', 'customers', 'schedules'));
    }

    public function update(Request $request, ClassBooking $classBooking)
    {
        Gate::authorize('update', $classBooking);

        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customer_profiles,customer_id'],
            'schedule_id' => ['required', 'exists:class_schedules,schedule_id'],
            'status' => ['required', 'in:confirmed,cancelled,completed'],
        ]);

        $classBooking->update($validated);

        return redirect()->route('class-bookings.show', $classBooking)
            ->with('success', 'Class booking updated successfully.');
    }

    public function destroy(ClassBooking $classBooking)
    {
        // Bookings should not be deleted for attendance tracking and analytics
        // Use status changes instead (confirmed → cancelled)
        return redirect()->route('class-bookings.index')
            ->with('error', 'Bookings cannot be deleted. Use "Cancel Booking" to change status instead.');
    }

    /**
     * Cancel a booking (change status to cancelled)
     */
    public function cancel(ClassBooking $classBooking)
    {
        Gate::authorize('update', $classBooking);

        if ($classBooking->status === 'cancelled') {
            return redirect()->route('class-bookings.show', $classBooking)
                ->with('error', 'Booking is already cancelled.');
        }

        if ($classBooking->status === 'completed') {
            return redirect()->route('class-bookings.show', $classBooking)
                ->with('error', 'Cannot cancel a completed booking.');
        }

        $classBooking->update([
            'status' => 'cancelled',
        ]);

        return redirect()->route('class-bookings.show', $classBooking)
            ->with('success', 'Booking cancelled successfully.');
    }
}
