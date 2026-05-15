<?php

namespace App\Http\Controllers;

use App\Models\TrainerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class TrainerProfileController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', TrainerProfile::class);

        $showArchived = $request->input('archived', false);
        $search = $request->input('search');
        $filter = $request->input('filter');
        $sort = $request->input('sort', 'name_asc');

        $query = TrainerProfile::withCount('schedules');

        if ($showArchived) {
            $query->onlyTrashed();
        }

        // Search functionality
        $query->when($search, function($q) use ($search) {
            $q->where(function($subQuery) use ($search) {
                $subQuery->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('specialization', 'like', "%{$search}%");
            });
        });

        // Filter by status
        $query->when($filter === 'active', function($q) {
            $q->whereNull('deleted_at');
        });

        $query->when($filter === 'archived', function($q) {
            $q->whereNotNull('deleted_at')->withTrashed();
        });

        // Sorting
        $query->when($sort === 'name_asc', function($q) {
            $q->orderBy('last_name', 'asc')->orderBy('first_name', 'asc');
        });

        $query->when($sort === 'name_desc', function($q) {
            $q->orderBy('last_name', 'desc')->orderBy('first_name', 'desc');
        });

        $query->when($sort === 'specialization', function($q) {
            $q->orderBy('specialization', 'asc');
        });

        $query->when($sort === 'newest', function($q) {
            $q->orderBy('created_at', 'desc');
        });

        $profiles = $query->paginate(20)->withQueryString();

        return view('trainer_profiles.index', compact('profiles', 'showArchived', 'search', 'filter', 'sort'));
    }

    public function create()
    {
        Gate::authorize('create', TrainerProfile::class);

        return view('trainer_profiles.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('create', TrainerProfile::class);

        $validated = $request->validate([
            'first_name'     => ['required', 'string', 'max:50'],
            'last_name'      => ['required', 'string', 'max:50'],
            'specialization' => ['required', 'string', 'max:100'],
        ]);

        TrainerProfile::create($validated);

        return redirect()->route('trainer-profiles.index')
            ->with('success', 'Trainer profile created successfully.');
    }

    public function show(TrainerProfile $trainerProfile)
    {
        Gate::authorize('viewAny', TrainerProfile::class);

        $trainerProfile = TrainerProfile::withTrashed()->findOrFail($trainerProfile->id);
        $trainerProfile->load(['schedules.fitnessClass']);

        return view('trainer_profiles.show', compact('trainerProfile'));
    }

    public function edit(TrainerProfile $trainerProfile)
    {
        Gate::authorize('update', $trainerProfile);

        return view('trainer_profiles.edit', compact('trainerProfile'));
    }

    public function update(Request $request, TrainerProfile $trainerProfile)
    {
        Gate::authorize('update', $trainerProfile);

        $validated = $request->validate([
            'first_name'     => ['required', 'string', 'max:50'],
            'last_name'      => ['required', 'string', 'max:50'],
            'specialization' => ['required', 'string', 'max:100'],
        ]);

        $trainerProfile->update($validated);

        return redirect()->route('trainer-profiles.show', $trainerProfile)
            ->with('success', 'Trainer profile updated successfully.');
    }

    public function destroy(TrainerProfile $trainerProfile)
    {
        Gate::authorize('delete', $trainerProfile);

        $trainerProfile->delete();

        return redirect()->route('trainer-profiles.index')
            ->with('success', 'Trainer profile deleted.');
    }

    /**
     * Archive the specified trainer profile.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function archive(Request $request, $id)
    {
        Gate::authorize('admin-only');

        $trainerProfile = TrainerProfile::findOrFail($id);

        $validated = $request->validate([
            'archive_reason' => ['required', 'string', 'max:255'],
        ]);

        $trainerProfile->update([
            'archived_at' => now(),
            'archived_by' => Auth::id(),
            'archive_reason' => $validated['archive_reason'],
            'last_active_date' => now()->toDateString(),
        ]);

        $trainerProfile->delete();

        return redirect()->route('trainer-profiles.index')
            ->with('success', 'Trainer profile archived successfully.');
    }

    /**
     * Restore the specified archived trainer profile.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        Gate::authorize('admin-only');

        $trainerProfile = TrainerProfile::onlyTrashed()->findOrFail($id);
        $trainerProfile->restore();

        $trainerProfile->update([
            'archived_at' => null,
            'archived_by' => null,
            'archive_reason' => null,
        ]);

        return redirect()->route('trainer-profiles.index')
            ->with('success', 'Trainer profile restored successfully.');
    }
}
