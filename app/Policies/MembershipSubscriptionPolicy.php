<?php

namespace App\Policies;

use App\Models\MembershipSubscription;
use App\Models\User;

class MembershipSubscriptionPolicy
{
    /**
     * RBAC Rule: Staff can Create and Read. Only Admins can Update/Delete.
     * Gate::before() in AuthServiceProvider already grants Admins blanket access.
     */

    // Staff and Admin can view the list
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    // Staff and Admin can view individual subscriptions
    public function view(User $user, MembershipSubscription $subscription): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    // Staff and Admin can create subscriptions (walk-ins, registrations)
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    // Admin only for updates
    public function update(User $user, MembershipSubscription $subscription): bool
    {
        return $user->isAdmin();
    }

    // Admin only for deletion
    public function delete(User $user, MembershipSubscription $subscription): bool
    {
        return $user->isAdmin();
    }
}