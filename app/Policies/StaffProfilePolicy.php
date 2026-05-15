<?php

namespace App\Policies;

use App\Models\StaffProfile;
use App\Models\User;

class StaffProfilePolicy
{
    // Staff cannot access StaffProfile records at all — Admin only
    // Gate::before() handles Admin bypass automatically
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, StaffProfile $profile): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, StaffProfile $profile): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, StaffProfile $profile): bool
    {
        return $user->isAdmin();
    }
}