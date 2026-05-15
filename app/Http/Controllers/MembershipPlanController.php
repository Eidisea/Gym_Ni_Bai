<?php

namespace App\Http\Controllers;

use App\Models\MembershipPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MembershipPlanController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', MembershipPlan::class);

        $showArchived = $request->get('archived', false);

        $query = MembershipPlan::withCount('subscriptions');

        if ($showArchived) {
            $query->onlyTrashed();
        }

        $plans = $query->get();

        return view('membership_plans.index', compact('plans', 'showArchived'));
    }

    public function create()
    {
        Gate::authorize('create', MembershipPlan::class);

        return view('membership_plans.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('create', MembershipPlan::class);

        $validated = $request->validate([
            'plan_name'     => ['required', 'string', 'max:100'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'base_price'    => ['required', 'numeric', 'min:0'],
        ]);

        MembershipPlan::create($validated);

        return redirect()->route('membership-plans.index')
            ->with('success', 'Membership plan created successfully.');
    }

    public function show($id)
    {
        Gate::authorize('viewAny', MembershipPlan::class);

        $membershipPlan = MembershipPlan::withTrashed()->findOrFail($id);
        $membershipPlan->load(['subscriptions.customerProfile']);

        return view('membership_plans.show', compact('membershipPlan'));
    }

    public function edit(MembershipPlan $membershipPlan)
    {
        Gate::authorize('update', $membershipPlan);

        return view('membership_plans.edit', compact('membershipPlan'));
    }

    public function update(Request $request, MembershipPlan $membershipPlan)
    {
        Gate::authorize('update', $membershipPlan);

        $validated = $request->validate([
            'plan_name'     => ['required', 'string', 'max:100'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'base_price'    => ['required', 'numeric', 'min:0'],
        ]);

        $membershipPlan->update($validated);

        return redirect()->route('membership-plans.index')
            ->with('success', 'Membership plan updated successfully.');
    }

    public function destroy(MembershipPlan $membershipPlan)
    {
        Gate::authorize('delete', $membershipPlan);

        // Guard: DB enforces ON DELETE RESTRICT, but give a friendly error
        if ($membershipPlan->subscriptions()->exists()) {
            return redirect()->route('membership-plans.index')
                ->with('error', 'Cannot delete a plan that has active subscriptions.');
        }

        $membershipPlan->delete();

        return redirect()->route('membership-plans.index')
            ->with('success', 'Membership plan deleted successfully.');
    }

    public function archive(Request $request, $id)
    {
        Gate::authorize('admin-only');

        $membershipPlan = MembershipPlan::findOrFail($id);

        $validated = $request->validate([
            'archive_reason' => ['required', 'string', 'max:255'],
        ]);

        $membershipPlan->update([
            'archived_at' => now(),
            'archived_by' => auth()->id(),
            'archive_reason' => $validated['archive_reason'],
            'last_active_date' => now()->toDateString(),
        ]);

        $membershipPlan->delete();

        return redirect()->route('membership-plans.index')
            ->with('success', 'Membership plan archived successfully.');
    }

    public function restore($id)
    {
        Gate::authorize('admin-only');

        $membershipPlan = MembershipPlan::onlyTrashed()->findOrFail($id);
        $membershipPlan->restore();

        $membershipPlan->update([
            'archived_at' => null,
            'archived_by' => null,
            'archive_reason' => null,
        ]);

        return redirect()->route('membership-plans.index')
            ->with('success', 'Membership plan restored successfully.');
    }
}
