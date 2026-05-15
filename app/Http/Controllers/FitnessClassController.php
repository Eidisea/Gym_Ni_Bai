<?php

namespace App\Http\Controllers;

use App\Models\FitnessClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FitnessClassController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', FitnessClass::class);

        $showArchived = $request->get('archived', false);
        $search = $request->get('search');

        $query = FitnessClass::withCount('schedules');

        if ($showArchived) {
            $query->onlyTrashed();
        }

        // Search functionality
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('class_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $fitnessClasses = $query->get();

        return view('fitness_classes.index', compact('fitnessClasses', 'showArchived', 'search'));
    }

    public function create()
    {
        Gate::authorize('create', FitnessClass::class);

        return view('fitness_classes.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('create', FitnessClass::class);

        $validated = $request->validate([
            'class_name'   => ['required', 'string', 'max:100'],
            'description'  => ['required', 'string'],
            'max_participants' => ['required', 'integer', 'min:1'],
        ]);

        FitnessClass::create($validated);

        return redirect()->route('fitness-classes.index')
            ->with('success', 'Fitness class created successfully.');
    }

    public function show($id)
    {
        Gate::authorize('viewAny', FitnessClass::class);

        $fitnessClass = FitnessClass::withTrashed()->findOrFail($id);
        $fitnessClass->load(['schedules.trainerProfile']);

        return view('fitness_classes.show', compact('fitnessClass'));
    }

    public function edit(FitnessClass $fitnessClass)
    {
        Gate::authorize('update', $fitnessClass);

        return view('fitness_classes.edit', compact('fitnessClass'));
    }

    public function update(Request $request, FitnessClass $fitnessClass)
    {
        Gate::authorize('update', $fitnessClass);

        $validated = $request->validate([
            'class_name'   => ['required', 'string', 'max:100'],
            'description'  => ['required', 'string'],
            'max_participants' => ['required', 'integer', 'min:1'],
        ]);

        $fitnessClass->update($validated);

        return redirect()->route('fitness-classes.index')
            ->with('success', 'Fitness class updated successfully.');
    }

    public function destroy(FitnessClass $fitnessClass)
    {
        Gate::authorize('delete', $fitnessClass);

        // Guard: DB enforces ON DELETE RESTRICT on class_schedules
        if ($fitnessClass->schedules()->exists()) {
            return redirect()->route('fitness-classes.index')
                ->with('error', 'Cannot delete a class that has existing schedules.');
        }

        $fitnessClass->delete();

        return redirect()->route('fitness-classes.index')
            ->with('success', 'Fitness class deleted successfully.');
    }

    public function archive(Request $request, $id)
    {
        Gate::authorize('admin-only');

        $fitnessClass = FitnessClass::findOrFail($id);

        $validated = $request->validate([
            'archive_reason' => ['required', 'string', 'max:255'],
        ]);

        $fitnessClass->update([
            'archived_at' => now(),
            'archived_by' => auth()->id(),
            'archive_reason' => $validated['archive_reason'],
            'last_active_date' => now()->toDateString(),
        ]);

        $fitnessClass->delete();

        return redirect()->route('fitness-classes.index')
            ->with('success', 'Fitness class archived successfully.');
    }

    public function restore($id)
    {
        Gate::authorize('admin-only');

        $fitnessClass = FitnessClass::onlyTrashed()->findOrFail($id);
        $fitnessClass->restore();

        $fitnessClass->update([
            'archived_at' => null,
            'archived_by' => null,
            'archive_reason' => null,
        ]);

        return redirect()->route('fitness-classes.index')
            ->with('success', 'Fitness class restored successfully.');
    }
}
