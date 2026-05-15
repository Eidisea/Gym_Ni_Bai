<?php

namespace App\Http\Controllers;

use App\Models\MembershipSubscription;
use App\Models\MembershipPlan;
use App\Models\CustomerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MembershipSubscriptionController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', MembershipSubscription::class);

        $search = $request->input('search');
        $filter = $request->input('filter');
        $sort = $request->input('sort', 'end_date_soonest');

        // Eager-load relationships with withTrashed() for archive-aware display
        $query = MembershipSubscription::with([
            'customerProfile' => fn($q) => $q->withTrashed(),
            'plan' => fn($q) => $q->withTrashed(),
            'transactions'
        ]);

        // Search functionality
        $query->when($search, function($q) use ($search) {
            $q->where(function($subQuery) use ($search) {
                $subQuery->whereHas('customerProfile', function($customerQuery) use ($search) {
                    $customerQuery->where('first_name', 'like', "%{$search}%")
                                  ->orWhere('last_name', 'like', "%{$search}%");
                })
                ->orWhereHas('plan', function($planQuery) use ($search) {
                    $planQuery->where('plan_name', 'like', "%{$search}%");
                });
            });
        });

        // Filter by status
        $query->when($filter === 'active', function($q) {
            $q->where('status', 'active');
        });

        $query->when($filter === 'expired', function($q) {
            $q->where('status', 'expired');
        });

        $query->when($filter === 'cancelled', function($q) {
            $q->where('status', 'cancelled');
        });

        $query->when($filter === 'pending', function($q) {
            $q->where('status', 'pending');
        });

        // Sorting
        $query->when($sort === 'end_date_soonest', function($q) {
            $q->orderBy('end_date', 'asc');
        });

        $query->when($sort === 'end_date_furthest', function($q) {
            $q->orderBy('end_date', 'desc');
        });

        $query->when($sort === 'customer_name', function($q) {
            $q->join('customer_profiles', 'membership_subscriptions.customer_id', '=', 'customer_profiles.customer_id')
              ->orderBy('customer_profiles.last_name', 'asc')
              ->orderBy('customer_profiles.first_name', 'asc')
              ->select('membership_subscriptions.*');
        });

        $query->when($sort === 'newest', function($q) {
            $q->orderBy('created_at', 'desc');
        });

        $subscriptions = $query->paginate(20)->withQueryString();

        return view('membership_subscriptions.index', compact('subscriptions', 'search', 'filter', 'sort'));
    }

    public function create()
    {
        Gate::authorize('create', MembershipSubscription::class);

        $customers = CustomerProfile::orderBy('last_name')->get();
        $plans = MembershipPlan::orderBy('plan_name')->get();

        return view('membership_subscriptions.create', compact('customers', 'plans'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', MembershipSubscription::class);

        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customer_profiles,customer_id'],
            'plan_id' => ['required', 'exists:membership_plans,plan_id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', 'in:active,expired,cancelled,pending'],
        ]);

        MembershipSubscription::create($validated);

        return redirect()->route('membership-subscriptions.index')
            ->with('success', 'Membership subscription created successfully.');
    }

    public function show(MembershipSubscription $membershipSubscription)
    {
        Gate::authorize('view', $membershipSubscription);

        // Eager-load all related data with withTrashed() for archive-aware display
        $membershipSubscription->load([
            'customerProfile' => fn($q) => $q->withTrashed()->with('user'),
            'plan' => fn($q) => $q->withTrashed(),
            'transactions' => fn($q) => $q->with([
                'cashPayment' => fn($cq) => $cq->with(['staffProfile' => fn($sq) => $sq->withTrashed()]),
                'cardPayment',
                'ewalletPayment'
            ])
        ]);

        return view('membership_subscriptions.show', compact('membershipSubscription'));
    }

    public function edit(MembershipSubscription $membershipSubscription)
    {
        Gate::authorize('update', $membershipSubscription);

        $customers = CustomerProfile::orderBy('last_name')->get();
        $plans = MembershipPlan::orderBy('plan_name')->get();

        return view('membership_subscriptions.edit', compact('membershipSubscription', 'customers', 'plans'));
    }

    public function update(Request $request, MembershipSubscription $membershipSubscription)
    {
        Gate::authorize('update', $membershipSubscription);

        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customer_profiles,customer_id'],
            'plan_id' => ['required', 'exists:membership_plans,plan_id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', 'in:active,expired,cancelled,pending'],
        ]);

        $membershipSubscription->update($validated);

        return redirect()->route('membership-subscriptions.show', $membershipSubscription)
            ->with('success', 'Membership subscription updated successfully.');
    }

    public function destroy(MembershipSubscription $membershipSubscription)
    {
        // Subscriptions should not be deleted for business history and analytics
        // Use status changes instead (active → cancelled)
        return redirect()->route('membership-subscriptions.index')
            ->with('error', 'Subscriptions cannot be deleted. Use "Cancel Subscription" to change status instead.');
    }

    /**
     * Cancel a subscription (change status to cancelled)
     */
    public function cancel(MembershipSubscription $membershipSubscription)
    {
        Gate::authorize('update', $membershipSubscription);

        if ($membershipSubscription->status === 'cancelled') {
            return redirect()->route('membership-subscriptions.show', $membershipSubscription)
                ->with('error', 'Subscription is already cancelled.');
        }

        $membershipSubscription->update([
            'status' => 'cancelled',
        ]);

        return redirect()->route('membership-subscriptions.show', $membershipSubscription)
            ->with('success', 'Subscription cancelled successfully.');
    }
}
