<?php

namespace App\Policies;

use App\Models\PaymentTransaction;
use App\Models\User;

class PaymentTransactionPolicy
{
    /**
     * RBAC Rule: Staff can Create and Read (cash processing). Only Admins can Update/Delete.
     * Gate::before() in AuthServiceProvider already grants Admins blanket access.
     */

    // Staff and Admin can view the list (audit trails, reports)
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    // Staff and Admin can view individual transactions
    public function view(User $user, PaymentTransaction $transaction): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    // Staff and Admin can create transactions (cash handling, processing)
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    // Admin only for updates (status changes, corrections)
    public function update(User $user, PaymentTransaction $transaction): bool
    {
        return $user->isAdmin();
    }

    // Admin only for deletion (failed transactions only)
    public function delete(User $user, PaymentTransaction $transaction): bool
    {
        return $user->isAdmin();
    }
}