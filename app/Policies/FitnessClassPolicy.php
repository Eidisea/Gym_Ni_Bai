<?php

namespace App\Policies;

use App\Models\FitnessClass;
use App\Models\User;

class FitnessClassPolicy
{
    // Staff can read; Admin can do everything (handled by Gate::before)
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function view(User $user, FitnessClass $fitnessClass): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    // Admin only
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    // Admin only
    public function update(User $user, FitnessClass $fitnessClass): bool
    {
        return $user->isAdmin();
    }

    // Admin only
    public function delete(User $user, FitnessClass $fitnessClass): bool
    {
        return $user->isAdmin();
    }
}