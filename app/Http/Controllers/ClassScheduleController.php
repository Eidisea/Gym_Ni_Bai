<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use App\Models\FitnessClass;
use App\Models\TrainerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class ClassScheduleController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', ClassSchedule::class);

        $search = $request->input('search');
        $showArchived = $request->boolean('show_archived');

        // Eager-load relationships with withTrashed() for archive-aware display
        $query = ClassSchedule::with([
            'fitnessClass' => fn($q) => $q->withTrashed(),
            'trainerProfile' => fn($q) => $q->withTrashed(),
            'bookings' => function($q) {
                $q->where('status', 'confirmed');
            }
        ]);

        // Show ONLY archived records if requested, otherwise show ONLY active records
        if ($showArchived) {
            $query->onlyTrashed();
        }

        // Search functionality
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('fitnessClass', function($classQuery) use ($search) {
                    $classQuery->where('class_name', 'like', "%{$search}%");
                })
                ->orWhereHas('trainerProfile', function($trainerQuery) use ($search) {
                    $trainerQuery->where('first_name', 'like', "%{$search}%")
                                 ->orWhere('last_name', 'like', "%{$search}%");
                })
                ->orWhere('start_time', 'like', "%{$search}%");
            });
        }

        $schedules = $query->orderBy('start_time')->paginate(20)->withQueryString();

        return view('class_schedules.index', compact('schedules', 'search', 'showArchived'));
    }

    public function create()
    {
        Gate::authorize('create', ClassSchedule::class);

        $fitnessClasses = FitnessClass::orderBy('class_name')->get();
        $trainers = TrainerProfile::orderBy('last_name')->get();

        return view('class_schedules.create', compact('fitnessClasses', 'trainers'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', ClassSchedule::class);

        $validated = $request->validate([
            'class_id' => ['required', 'exists:fitness_classes,class_id'],
            'trainer_id' => ['required', 'exists:trainer_profiles,trainer_id'],
            'start_time' => ['required', 'date', 'after:now'],
            'end_time' => ['required', 'date', 'after:start_time'],
        ]);

        // Auto-fetch available_slots from the selected FitnessClass
        $fitnessClass = FitnessClass::findOrFail($validated['class_id']);
        $validated['available_slots'] = $fitnessClass->max_participants;

        ClassSchedule::create($validated);

        return redirect()->route('management.class-schedules.index')
            ->with('success', 'Class schedule created successfully.');
    }

    /**
     * Display the specified class schedule.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $classSchedule = ClassSchedule::withTrashed()->findOrFail($id);
        
        Gate::authorize('view', $classSchedule);

        // Eager-load all related data with withTrashed() for archive-aware display
        $classSchedule->load([
            'fitnessClass' => fn($q) => $q->withTrashed(),
            'trainerProfile' => fn($q) => $q->withTrashed(),
            'bookings' => fn($q) => $q->withTrashed()->with(['customerProfile' => fn($cq) => $cq->withTrashed()])
        ]);

        return view('class_schedules.show', compact('classSchedule'));
    }

    public function edit(ClassSchedule $classSchedule)
    {
        Gate::authorize('update', $classSchedule);

        // Prevent editing past schedules
        if ($classSchedule->start_time->isPast()) {
            return redirect()->route('management.class-schedules.show', $classSchedule)
                ->with('error', 'Cannot edit past class schedules.');
        }

        $fitnessClasses = FitnessClass::orderBy('class_name')->get();
        $trainers = TrainerProfile::orderBy('last_name')->get();

        return view('class_schedules.edit', compact('classSchedule', 'fitnessClasses', 'trainers'));
    }

    public function update(Request $request, ClassSchedule $classSchedule)
    {
        Gate::authorize('update', $classSchedule);

        // Prevent updating past schedules
        if ($classSchedule->start_time->isPast()) {
            return redirect()->route('management.class-schedules.show', $classSchedule)
                ->with('error', 'Cannot update past class schedules.');
        }

        $validated = $request->validate([
            'class_id' => ['required', 'exists:fitness_classes,class_id'],
            'trainer_id' => ['required', 'exists:trainer_profiles,trainer_id'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'available_slots' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $classSchedule->update($validated);

        return redirect()->route('management.class-schedules.show', $classSchedule)
            ->with('success', 'Class schedule updated successfully.');
    }

    public function destroy(ClassSchedule $classSchedule)
    {
        Gate::authorize('delete', $classSchedule);

        // Check for active bookings (confirmed, attended, or no-show)
        $activeBookings = $classSchedule->bookings()
            ->whereIn('status', ['confirmed', 'attended', 'no_show'])
            ->exists();

        if ($activeBookings) {
            return back()->with('error', 'Cannot archive this schedule. Active bookings exist. All bookings must be cancelled first.');
        }

        // Soft delete (archive) the schedule
        $classSchedule->delete();

        return redirect()->route('management.class-schedules.index')
            ->with('success', 'Class schedule archived successfully. Historical data preserved.');
    }

    /**
     * Archive a schedule with reason
     */
    /**
     * Archive a schedule with reason.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function archive(Request $request, int $id)
    {
        $classSchedule = ClassSchedule::findOrFail($id);
        
        Gate::authorize('delete', $classSchedule);

        // Check for active bookings (confirmed, attended, or no-show)
        $activeBookings = $classSchedule->bookings()
            ->whereIn('status', ['confirmed', 'attended', 'no_show'])
            ->exists();

        if ($activeBookings) {
            return back()->with('error', 'Cannot archive this schedule. Active bookings exist. All bookings must be cancelled first.');
        }

        $validated = $request->validate([
            'archive_reason' => ['required', 'string', 'max:255'],
        ]);

        $classSchedule->update([
            'archived_at' => now(),
            'archived_by' => Auth::id(),
            'archive_reason' => $validated['archive_reason'],
            'last_active_date' => now()->toDateString(),
        ]);

        $classSchedule->delete();

        return redirect()->route('management.class-schedules.index')
            ->with('success', 'Class schedule archived successfully.');
    }

    /**
     * Cancel a schedule with reason (notifies customers with bookings)
     */
    public function cancel(Request $request, int $id)
    {
        $classSchedule = ClassSchedule::withTrashed()->findOrFail($id);
        
        Gate::authorize('update', $classSchedule);

        // Validate cancellation reason
        $validated = $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:500'],
        ]);

        // Check if already in the past
        if ($classSchedule->start_time->isPast()) {
            return redirect()->route('management.class-schedules.show', $id)
                ->with('error', 'Cannot cancel past class schedules.');
        }

        // Get all confirmed bookings before cancelling
        $confirmedBookings = $classSchedule->bookings()
            ->where('status', 'confirmed')
            ->with('customerProfile.user')
            ->get();

        // Cancel all confirmed bookings
        foreach ($confirmedBookings as $booking) {
            $booking->update(['status' => 'cancelled']);
        }

        // Store cancellation reason in the dedicated column
        $classSchedule->update([
            'cancellation_reason' => $validated['cancellation_reason'],
        ]);

        // TODO: Send email notifications to customers
        // foreach ($confirmedBookings as $booking) {
        //     Mail::to($booking->customerProfile->user->email)
        //         ->send(new ClassCancelledNotification($classSchedule, $validated['cancellation_reason']));
        // }

        return redirect()->route('management.class-schedules.index')
            ->with('success', "Class schedule cancelled. {$confirmedBookings->count()} customer(s) notified.");
    }

    /**
     * Restore an archived schedule
     */
    /**
     * Restore an archived class schedule.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        $classSchedule = ClassSchedule::onlyTrashed()->findOrFail($id);
        
        Gate::authorize('update', $classSchedule);

        $classSchedule->restore();

        $classSchedule->update([
            'archived_at' => null,
            'archived_by' => null,
            'archive_reason' => null,
        ]);

        return redirect()->route('management.class-schedules.show', $id)
            ->with('success', 'Class schedule restored successfully.');
    }
}