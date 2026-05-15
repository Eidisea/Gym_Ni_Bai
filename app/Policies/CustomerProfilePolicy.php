<?php

namespace App\Policies;

use App\Models\CustomerProfile;
use App\Models\User;

class CustomerProfilePolicy
{
    // Gate::before() already handles Admin — these define Staff behavior
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function view(User $user, CustomerProfile $profile): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function update(User $user, CustomerProfile $profile): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    // Staff CANNOT delete — Admin only (Gate::before handles admin)
    public function delete(User $user, CustomerProfile $profile): bool
    {
        return $user->isAdmin();
    }
}