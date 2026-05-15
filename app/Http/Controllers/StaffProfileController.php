<?php

namespace App\Http\Controllers;

use App\Models\StaffProfile;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class StaffProfileController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('admin-only');

        $showArchived = $request->get('archived', false);
        $search = $request->get('search');
        $filter = $request->get('filter');
        $sort = $request->get('sort', 'name_asc');

        $query = StaffProfile::with('user');

        if ($showArchived) {
            $query->onlyTrashed();
        }

        // Search functionality
        $query->when($search, function($q) use ($search) {
            $q->where(function($subQuery) use ($search) {
                $subQuery->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('department', 'like', "%{$search}%")
                         ->orWhereHas('user', function($userQuery) use ($search) {
                             $userQuery->where('email', 'like', "%{$search}%");
                         });
            });
        });

        // Filter by status
        $query->when($filter === 'active', function($q) {
            $q->whereHas('user', function($userQuery) {
                $userQuery->where('is_active', true);
            });
        });

        $query->when($filter === 'deactivated', function($q) {
            $q->whereHas('user', function($userQuery) {
                $userQuery->where('is_active', false);
            });
        });

        // Sorting
        $query->when($sort === 'name_asc', function($q) {
            $q->orderBy('last_name', 'asc')->orderBy('first_name', 'asc');
        });

        $query->when($sort === 'name_desc', function($q) {
            $q->orderBy('last_name', 'desc')->orderBy('first_name', 'desc');
        });

        $query->when($sort === 'newest', function($q) {
            $q->orderBy('created_at', 'desc');
        });

        $staffProfiles = $query->paginate(20)->withQueryString();

        return view('staff_profiles.index', compact('staffProfiles', 'showArchived', 'search', 'filter', 'sort'));
    }

    public function create()
    {
        Gate::authorize('admin-only');

        return view('staff_profiles.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('admin-only');

        $validated = $request->validate([
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'department' => ['required', 'string', 'max:50'],
        ]);

        // Get Staff role
        $staffRole = Role::where('role_name', Role::STAFF)->firstOrFail();

        // Create user account
        $user = User::create([
            'role_id' => $staffRole->role_id,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        // Create staff profile
        StaffProfile::create([
            'user_id' => $user->user_id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'department' => $validated['department'],
        ]);

        return redirect()->route('staff-profiles.index')
            ->with('success', 'Staff profile created successfully.');
    }

    public function show($id)
    {
        Gate::authorize('admin-only');

        $staffProfile = StaffProfile::withTrashed()->findOrFail($id);
        $staffProfile->load(['user', 'cashPayments.transaction']);

        return view('staff_profiles.show', compact('staffProfile'));
    }

    public function edit(StaffProfile $staffProfile)
    {
        Gate::authorize('admin-only');

        return view('staff_profiles.edit', compact('staffProfile'));
    }

    public function update(Request $request, StaffProfile $staffProfile)
    {
        Gate::authorize('admin-only');

        $validated = $request->validate([
            'email' => ['required', 'email', 'unique:users,email,' . $staffProfile->user_id . ',user_id'],
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'department' => ['required', 'string', 'max:50'],
            'is_active' => ['required', 'boolean'],
        ]);

        // Update user account
        $staffProfile->user->update([
            'email' => $validated['email'],
            'is_active' => $validated['is_active'],
        ]);

        // Update staff profile
        $staffProfile->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'department' => $validated['department'],
        ]);

        return redirect()->route('staff-profiles.show', $staffProfile)
            ->with('success', 'Staff profile updated successfully.');
    }

    public function destroy(StaffProfile $staffProfile)
    {
        Gate::authorize('admin-only');

        // Check if staff has processed cash payments
        if ($staffProfile->cashPayments()->exists()) {
            return redirect()->route('staff-profiles.index')
                ->with('error', 'Cannot delete staff member with payment transaction history.');
        }

        $user = $staffProfile->user;
        $staffProfile->delete();
        $user->delete();

        return redirect()->route('staff-profiles.index')
            ->with('success', 'Staff profile deleted successfully.');
    }

    public function archive(Request $request, $id)
    {
        Gate::authorize('admin-only');

        $staffProfile = StaffProfile::findOrFail($id);

        $validated = $request->validate([
            'archive_reason' => ['required', 'string', 'max:255'],
        ]);

        $staffProfile->update([
            'archived_at' => now(),
            'archived_by' => auth()->id(),
            'archive_reason' => $validated['archive_reason'],
            'last_active_date' => now()->toDateString(),
        ]);

        $staffProfile->delete();

        return redirect()->route('staff-profiles.index')
            ->with('success', 'Staff profile archived successfully.');
    }

    public function restore($id)
    {
        Gate::authorize('admin-only');

        $staffProfile = StaffProfile::onlyTrashed()->findOrFail($id);
        $staffProfile->restore();

        $staffProfile->update([
            'archived_at' => null,
            'archived_by' => null,
            'archive_reason' => null,
        ]);

        return redirect()->route('staff-profiles.index')
            ->with('success', 'Staff profile restored successfully.');
    }
}
