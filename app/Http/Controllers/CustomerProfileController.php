<?php

namespace App\Http\Controllers;

use App\Models\CustomerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class CustomerProfileController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', CustomerProfile::class);

        $showArchived = $request->input('archived', false);
        $search = $request->input('search');
        $filter = $request->input('filter');
        $sort = $request->input('sort', 'name_asc');

        $query = CustomerProfile::with('user.role');

        // Archived filter
        if ($showArchived) {
            $query->onlyTrashed();
        }

        // Search functionality
        $query->when($search, function($q) use ($search) {
            $q->where(function($subQuery) use ($search) {
                $subQuery->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('phone_number', 'like', "%{$search}%")
                         ->orWhereHas('user', function($userQuery) use ($search) {
                             $userQuery->where('email', 'like', "%{$search}%");
                         });
            });
        });

        // Filter by status
        $query->when($filter === 'active', function($q) {
            $q->whereNull('archived_at');
        });

        $query->when($filter === 'archived', function($q) {
            $q->whereNotNull('archived_at');
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

        $query->when($sort === 'oldest', function($q) {
            $q->orderBy('created_at', 'asc');
        });

        $query->when($sort === 'recent_activity', function($q) {
            $q->orderBy('last_active_date', 'desc');
        });

        $profiles = $query->paginate(20)->withQueryString();

        return view('customer_profiles.index', compact('profiles', 'showArchived', 'search', 'filter', 'sort'));
    }

    /**
     * Display the specified customer profile.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(int $id)
    {
        Gate::authorize('viewAny', CustomerProfile::class);

        $customerProfile = CustomerProfile::withTrashed()->findOrFail($id);
        $customerProfile->load([
            'user.role',
            'subscriptions.plan',
            'bookings.schedule.fitnessClass',
        ]);

        return view('customer_profiles.show', compact('customerProfile'));
    }

    public function edit(CustomerProfile $customerProfile)
    {
        Gate::authorize('update', $customerProfile);

        return view('customer_profiles.edit', compact('customerProfile'));
    }

    public function update(Request $request, CustomerProfile $customerProfile)
    {
        Gate::authorize('update', $customerProfile);

        $validated = $request->validate([
            'first_name'    => ['required', 'string', 'max:50'],
            'last_name'     => ['required', 'string', 'max:50'],
            'phone_number'  => ['required', 'string', 'max:20',
                'unique:customer_profiles,phone_number,' . $customerProfile->customer_id . ',customer_id',
            ],
            'date_of_birth' => ['required', 'date', 'before:today'],
        ]);

        $customerProfile->update($validated);

        return redirect()->route('customer-profiles.show', $customerProfile)
            ->with('success', 'Customer profile updated successfully.');
    }

    public function destroy(CustomerProfile $customerProfile)
    {
        Gate::authorize('delete', $customerProfile);

        // Cascades to user record via DB ON DELETE CASCADE on users→customer_profiles
        // But we delete the user, which cascades to profile — or delete profile only
        $customerProfile->delete();

        return redirect()->route('customer-profiles.index')
            ->with('success', 'Customer profile deleted.');
    }

    /**
     * Archive a customer profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function archive(Request $request, int $id)
    {
        Gate::authorize('admin-only');

        $customerProfile = CustomerProfile::findOrFail($id);

        $validated = $request->validate([
            'archive_reason' => ['required', 'string', 'max:255'],
        ]);

        $customerProfile->update([
            'archived_at' => now(),
            'archived_by' => Auth::id(),
            'archive_reason' => $validated['archive_reason'],
            'last_active_date' => now()->toDateString(),
        ]);

        $customerProfile->delete();

        return redirect()->route('customer-profiles.index')
            ->with('success', 'Customer profile archived successfully.');
    }

    /**
     * Restore an archived customer profile.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(int $id)
    {
        Gate::authorize('admin-only');

        $customerProfile = CustomerProfile::onlyTrashed()->findOrFail($id);
        $customerProfile->restore();

        $customerProfile->update([
            'archived_at' => null,
            'archived_by' => null,
            'archive_reason' => null,
        ]);

        return redirect()->route('customer-profiles.index')
            ->with('success', 'Customer profile restored successfully.');
    }

    public function dashboard()
    {
        $user = Auth::user();
        $customerProfile = $user->customerProfile;

        $activeSubscription = $customerProfile->subscriptions()
            ->with(['plan' => fn($q) => $q->withTrashed()])
            ->where('status', 'active')
            ->where('end_date', '>=', now()->toDateString())
            ->orderBy('end_date', 'desc')
            ->first();

        $upcomingBookings = $customerProfile->bookings()
            ->with(['schedule' => fn($q) => $q->withTrashed()->with([
                'fitnessClass'   => fn($q) => $q->withTrashed(),
                'trainerProfile' => fn($q) => $q->withTrashed(),
            ])])
            ->where('status', 'confirmed')
            ->whereHas('schedule', function ($q) {
                $q->where('start_time', '>=', now());
            })
            ->get()
            ->sortBy(fn($b) => $b->schedule->start_time)
            ->take(3);

        // Stats for the dashboard header bar
        $totalBookings = $customerProfile->bookings()->count();

        $daysLeft = $activeSubscription
            ? now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($activeSubscription->end_date)->startOfDay(), false)
            : null;

        return view('customer.dashboard', compact(
            'activeSubscription',
            'upcomingBookings',
            'totalBookings',
            'daysLeft',
        ));
    }

    public function editProfile()
    {
        $customerProfile = Auth::user()->customerProfile;
        return view('customer.profile', compact('customerProfile'));
    }

    public function updateProfile(Request $request)
    {
        $customerProfile = Auth::user()->customerProfile;

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'phone_number' => ['required', 'string', 'max:20', 
                'unique:customer_profiles,phone_number,' . $customerProfile->customer_id . ',customer_id'
            ],
            'date_of_birth' => ['required', 'date', 'before:today'],
        ]);

        $customerProfile->update($validated);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}