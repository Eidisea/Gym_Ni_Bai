<?php

namespace App\Policies;

use App\Models\MembershipPlan;
use App\Models\User;

class MembershipPlanPolicy
{
    /**
     * NOTE: Gate::before() in AuthServiceProvider already grants Admins
     * blanket access before these methods are ever evaluated.
     * These methods define what non-admin (Staff, Trainer, Customer) can do.
     */

    // Staff and above can view the list
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    // Staff and above can view a single plan
    public function view(User $user, MembershipPlan $plan): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    // Admin only
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    // Admin only
    public function update(User $user, MembershipPlan $plan): bool
    {
        return $user->isAdmin();
    }

    // Admin only
    public function delete(User $user, MembershipPlan $plan): bool
    {
        return $user->isAdmin();
    }
}