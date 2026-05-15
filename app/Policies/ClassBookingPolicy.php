<?php

namespace App\Policies;

use App\Models\ClassBooking;
use App\Models\User;

class ClassBookingPolicy
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

    // Staff and Admin can view individual bookings
    public function view(User $user, ClassBooking $booking): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    // Staff and Admin can create bookings (walk-ins, phone bookings)
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    // Admin only for updates
    public function update(User $user, ClassBooking $booking): bool
    {
        return $user->isAdmin();
    }

    // Admin only for deletion
    public function delete(User $user, ClassBooking $booking): bool
    {
        return $user->isAdmin();
    }
}