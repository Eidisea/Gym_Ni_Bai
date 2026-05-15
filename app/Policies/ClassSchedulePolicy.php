<?php

namespace App\Policies;

use App\Models\ClassSchedule;
use App\Models\User;

class ClassSchedulePolicy
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

    // Staff and Admin can view individual schedules
    public function view(User $user, ClassSchedule $schedule): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    // Staff and Admin can create schedules
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    // Admin only for updates
    public function update(User $user, ClassSchedule $schedule): bool
    {
        return $user->isAdmin();
    }

    // Admin only for deletion
    public function delete(User $user, ClassSchedule $schedule): bool
    {
        return $user->isAdmin();
    }
}