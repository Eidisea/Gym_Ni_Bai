<?php

namespace App\Policies;

use App\Models\TrainerProfile;
use App\Models\User;

class TrainerProfilePolicy
{
    // Admin only — same rationale as StaffProfile
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, TrainerProfile $profile): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, TrainerProfile $profile): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, TrainerProfile $profile): bool
    {
        return $user->isAdmin();
    }
}