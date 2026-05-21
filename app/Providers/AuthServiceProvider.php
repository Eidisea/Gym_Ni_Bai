<?php

namespace App\Providers;

use App\Models\FitnessClass;
use App\Models\MembershipPlan;
use App\Models\CustomerProfile;
use App\Models\StaffProfile;
use App\Models\TrainerProfile;
use App\Models\ClassSchedule;
use App\Models\MembershipSubscription;
use App\Models\ClassBooking;
use App\Models\PaymentTransaction;
use App\Policies\FitnessClassPolicy;
use App\Policies\MembershipPlanPolicy;
use App\Policies\CustomerProfilePolicy;
use App\Policies\StaffProfilePolicy;
use App\Policies\TrainerProfilePolicy;
use App\Policies\ClassSchedulePolicy;
use App\Policies\MembershipSubscriptionPolicy;
use App\Policies\ClassBookingPolicy;
use App\Policies\PaymentTransactionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Map models to their Policies.
     */
    protected $policies = [
        MembershipPlan::class => MembershipPlanPolicy::class,
        FitnessClass::class   => FitnessClassPolicy::class,
        CustomerProfile::class => CustomerProfilePolicy::class,
        StaffProfile::class    => StaffProfilePolicy::class,
        TrainerProfile::class  => TrainerProfilePolicy::class,
        ClassSchedule::class   => ClassSchedulePolicy::class,
        MembershipSubscription::class => MembershipSubscriptionPolicy::class,
        ClassBooking::class    => ClassBookingPolicy::class,
        PaymentTransaction::class => PaymentTransactionPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // =====================================================================
        // BASELINE GATE: Admin bypass
        // Gate::before() runs before ALL other gates and policies.
        // If the user is an Admin, they are unconditionally authorized.
        // =====================================================================
        Gate::before(function ($user, string $ability) {
            if ($user->isAdmin()) {
                return true; // Admin has full access to everything
            }
        });

        // =====================================================================
        // GATE: admin-only
        // Used directly in controllers for tables that Staff cannot touch at all.
        // Usage: Gate::authorize('admin-only');
        // =====================================================================
        Gate::define('admin-only', function ($user) {
            return $user->isAdmin();
        });

        // =====================================================================
        // GATE: management-access (for read operations accessible to both Admin and Staff)
        // =====================================================================
        Gate::define('management-access', function ($user) {
            return $user->isAdmin() || $user->isStaff();
        });

        // =====================================================================
        // GATE: staff-or-admin (for read operations accessible to both)
        // =====================================================================
        Gate::define('staff-or-admin', function ($user) {
            return $user->isAdmin() || $user->isStaff();
        });
    }
}