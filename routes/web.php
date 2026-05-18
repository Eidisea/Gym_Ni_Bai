    <?php

use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\Auth\ManagementAuthController;
use App\Http\Controllers\ClassScheduleController;
use App\Http\Controllers\MembershipSubscriptionController;
use App\Http\Controllers\ClassBookingController;
use App\Http\Controllers\PaymentTransactionController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\CustomerBookingController;
use App\Http\Controllers\CustomerBillingController;
use App\Http\Controllers\StaffProfileController;
use App\Http\Controllers\TrainerProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\MembershipPlanController;
use App\Http\Controllers\FitnessClassController;
use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\ReportController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::aliasMiddleware('role', RoleMiddleware::class);

// =============================================================================
// PUBLIC LANDING PAGE
// =============================================================================
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', function () {
    return redirect()->route('customer.login');
})->name('login');

// =============================================================================
// CUSTOMER PORTAL - GUEST ROUTES
// =============================================================================
Route::middleware('guest')->group(function () {
    Route::get('gym_ni_bai-login', [CustomerAuthController::class, 'showLogin'])->name('customer.login');
    Route::post('gym_ni_bai-login', [CustomerAuthController::class, 'login'])->name('customer.login.post');
    Route::get('gym_ni_bai-register', [CustomerAuthController::class, 'showRegister'])->name('customer.register');
    Route::post('gym_ni_bai-register', [CustomerAuthController::class, 'register'])->name('customer.register.post');
});

// =============================================================================
// CUSTOMER PORTAL - AUTHENTICATED ROUTES
// =============================================================================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('gym_ni_bai-logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');
    Route::get('customer/dashboard', [CustomerProfileController::class, 'dashboard'])->name('customer.dashboard');
    Route::get('customer/profile', [CustomerProfileController::class, 'editProfile'])->name('customer.profile.edit');
    Route::put('customer/profile', [CustomerProfileController::class, 'updateProfile'])->name('customer.profile.update');
    Route::get('gym_ni_bai-classes', [CustomerBookingController::class, 'index'])->name('customer.classes.index');
    Route::post('gym_ni_bai-classes/{scheduleId}/book', [CustomerBookingController::class, 'store'])->name('customer.classes.book');
    Route::get('gym_ni_bai-my-bookings', [CustomerBookingController::class, 'myBookings'])->name('customer.bookings.index');
    Route::post('gym_ni_bai-my-bookings/{bookingId}/cancel', [CustomerBookingController::class, 'cancelBooking'])->name('customer.bookings.cancel');
    Route::get('gym_ni_bai-billing', [CustomerBillingController::class, 'index'])->name('customer.billing.index');
    Route::post('gym_ni_bai-billing/renew', [CustomerBillingController::class, 'processRenewal'])->name('customer.billing.renew');
    Route::post('gym_ni_bai-billing/cancel-subscription', [CustomerBillingController::class, 'cancelSubscription'])->name('customer.billing.cancel-subscription');
});

// =============================================================================
// MANAGEMENT PORTAL - GUEST ROUTES
// =============================================================================
Route::prefix('gym_ni_bai-management')->name('management.')->middleware('guest')->group(function () {
    Route::get('login', [ManagementAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [ManagementAuthController::class, 'login'])->name('login.post');
    Route::get('signup', [ManagementAuthController::class, 'showRegister'])->name('register');
    Route::post('signup', [ManagementAuthController::class, 'register'])->name('register.post');
});

// =============================================================================
// MANAGEMENT PORTAL - AUTHENTICATED ROUTES
// =============================================================================
Route::prefix('management')->name('management.')->middleware(['auth', 'verified', 'role:management'])->group(function () {
    
    Route::post('logout', [ManagementAuthController::class, 'logout'])->name('logout');
    
    Route::get('dashboard', function () {
        return view('management.dashboard');
    })->name('dashboard');
    
    // =========================================================================
    // API ENDPOINTS (Internal AJAX)
    // =========================================================================
    Route::get('api/customers/search', [CustomerApiController::class, 'search'])->name('api.customers.search');
    Route::get('api/customers/{customerId}/active-subscription', [CustomerApiController::class, 'getActiveSubscription'])->name('api.customers.subscription');
    
    // =========================================================================
    // CUSTOMER PROFILES
    // =========================================================================
    Route::post('customer-profiles/{customerProfile}/archive', [CustomerProfileController::class, 'archive'])->name('customer-profiles.archive');
    Route::post('customer-profiles/{customerProfile}/restore', [CustomerProfileController::class, 'restore'])->name('customer-profiles.restore');
    
    Route::get('customer-profiles', [CustomerProfileController::class, 'index'])->name('customer-profiles.index');
    Route::get('customer-profiles/{customerProfile}', [CustomerProfileController::class, 'show'])->name('customer-profiles.show');
    Route::get('customer-profiles/{customerProfile}/edit', [CustomerProfileController::class, 'edit'])->name('customer-profiles.edit');
    Route::put('customer-profiles/{customerProfile}', [CustomerProfileController::class, 'update'])->name('customer-profiles.update');
    Route::delete('customer-profiles/{customerProfile}', [CustomerProfileController::class, 'destroy'])->name('customer-profiles.destroy');
    
    // =========================================================================
    // STAFF PROFILES
    // =========================================================================
    Route::post('staff-profiles/{staffProfile}/archive', [StaffProfileController::class, 'archive'])->name('staff-profiles.archive');
    Route::post('staff-profiles/{staffProfile}/restore', [StaffProfileController::class, 'restore'])->name('staff-profiles.restore');
    
    Route::get('staff-profiles', [StaffProfileController::class, 'index'])->name('staff-profiles.index');
    Route::get('staff-profiles/create', [StaffProfileController::class, 'create'])->name('staff-profiles.create');
    Route::post('staff-profiles', [StaffProfileController::class, 'store'])->name('staff-profiles.store');
    Route::get('staff-profiles/{staffProfile}', [StaffProfileController::class, 'show'])->name('staff-profiles.show');
    Route::get('staff-profiles/{staffProfile}/edit', [StaffProfileController::class, 'edit'])->name('staff-profiles.edit');
    Route::put('staff-profiles/{staffProfile}', [StaffProfileController::class, 'update'])->name('staff-profiles.update');
    Route::delete('staff-profiles/{staffProfile}', [StaffProfileController::class, 'destroy'])->name('staff-profiles.destroy');
    
    // =========================================================================
    // TRAINER PROFILES
    // =========================================================================
    Route::post('trainer-profiles/{trainerProfile}/archive', [TrainerProfileController::class, 'archive'])->name('trainer-profiles.archive');
    Route::post('trainer-profiles/{trainerProfile}/restore', [TrainerProfileController::class, 'restore'])->name('trainer-profiles.restore');
    
    Route::get('trainer-profiles', [TrainerProfileController::class, 'index'])->name('trainer-profiles.index');
    Route::get('trainer-profiles/create', [TrainerProfileController::class, 'create'])->name('trainer-profiles.create');
    Route::post('trainer-profiles', [TrainerProfileController::class, 'store'])->name('trainer-profiles.store');
    Route::get('trainer-profiles/{trainerProfile}', [TrainerProfileController::class, 'show'])->name('trainer-profiles.show');
    Route::get('trainer-profiles/{trainerProfile}/edit', [TrainerProfileController::class, 'edit'])->name('trainer-profiles.edit');
    Route::put('trainer-profiles/{trainerProfile}', [TrainerProfileController::class, 'update'])->name('trainer-profiles.update');
    Route::delete('trainer-profiles/{trainerProfile}', [TrainerProfileController::class, 'destroy'])->name('trainer-profiles.destroy');
    
    // =========================================================================
    // MEMBERSHIP PLANS
    // =========================================================================
    Route::post('membership-plans/{membershipPlan}/archive', [MembershipPlanController::class, 'archive'])->name('membership-plans.archive');
    Route::post('membership-plans/{membershipPlan}/restore', [MembershipPlanController::class, 'restore'])->name('membership-plans.restore');
    
    Route::get('membership-plans', [MembershipPlanController::class, 'index'])->name('membership-plans.index');
    Route::get('membership-plans/create', [MembershipPlanController::class, 'create'])->name('membership-plans.create');
    Route::post('membership-plans', [MembershipPlanController::class, 'store'])->name('membership-plans.store');
    Route::get('membership-plans/{membershipPlan}', [MembershipPlanController::class, 'show'])->name('membership-plans.show');
    Route::get('membership-plans/{membershipPlan}/edit', [MembershipPlanController::class, 'edit'])->name('membership-plans.edit');
    Route::put('membership-plans/{membershipPlan}', [MembershipPlanController::class, 'update'])->name('membership-plans.update');
    Route::delete('membership-plans/{membershipPlan}', [MembershipPlanController::class, 'destroy'])->name('membership-plans.destroy');
    
    // =========================================================================
    // FITNESS CLASSES
    // =========================================================================
    Route::post('fitness-classes/{fitnessClass}/archive', [FitnessClassController::class, 'archive'])->name('fitness-classes.archive');
    Route::post('fitness-classes/{fitnessClass}/restore', [FitnessClassController::class, 'restore'])->name('fitness-classes.restore');
    
    Route::get('fitness-classes', [FitnessClassController::class, 'index'])->name('fitness-classes.index');
    Route::get('fitness-classes/create', [FitnessClassController::class, 'create'])->name('fitness-classes.create');
    Route::post('fitness-classes', [FitnessClassController::class, 'store'])->name('fitness-classes.store');
    Route::get('fitness-classes/{fitnessClass}', [FitnessClassController::class, 'show'])->name('fitness-classes.show');
    Route::get('fitness-classes/{fitnessClass}/edit', [FitnessClassController::class, 'edit'])->name('fitness-classes.edit');
    Route::put('fitness-classes/{fitnessClass}', [FitnessClassController::class, 'update'])->name('fitness-classes.update');
    Route::delete('fitness-classes/{fitnessClass}', [FitnessClassController::class, 'destroy'])->name('fitness-classes.destroy');
    
    // =========================================================================
    // CLASS SCHEDULES
    // =========================================================================
    Route::post('class-schedules/{classSchedule}/cancel', [ClassScheduleController::class, 'cancel'])->name('class-schedules.cancel');
    Route::post('class-schedules/{classSchedule}/archive', [ClassScheduleController::class, 'archive'])->name('class-schedules.archive');
    Route::post('class-schedules/{classSchedule}/restore', [ClassScheduleController::class, 'restore'])->name('class-schedules.restore');
    
    Route::resource('class-schedules', ClassScheduleController::class)->parameters([
        'class-schedules' => 'classSchedule'
    ]);
    
    // =========================================================================
    // CLASS BOOKINGS
    // =========================================================================
    Route::post('class-bookings/{classBooking}/cancel', [ClassBookingController::class, 'cancel'])->name('class-bookings.cancel');
    Route::post('class-bookings/{classBooking}/restore', [ClassBookingController::class, 'restore'])->name('class-bookings.restore');
    
    Route::resource('class-bookings', ClassBookingController::class)->parameters([
        'class-bookings' => 'classBooking'
    ]);
    
    // =========================================================================
    // MEMBERSHIP SUBSCRIPTIONS
    // =========================================================================
    Route::post('membership-subscriptions/{membershipSubscription}/cancel', [MembershipSubscriptionController::class, 'cancel'])->name('membership-subscriptions.cancel');
    Route::post('membership-subscriptions/{membershipSubscription}/restore', [MembershipSubscriptionController::class, 'restore'])->name('membership-subscriptions.restore');
    
    Route::resource('membership-subscriptions', MembershipSubscriptionController::class)->parameters([
        'membership-subscriptions' => 'membershipSubscription'
    ]);
    
    // =========================================================================
    // PAYMENT TRANSACTIONS
    // =========================================================================
    Route::get('payment-transactions/cash-process', [PaymentTransactionController::class, 'cashProcess'])->name('payment-transactions.cash-process');
    Route::post('payment-transactions/cash-process', [PaymentTransactionController::class, 'cashProcessStore'])->name('payment-transactions.cash-process.store');
    
    Route::resource('payment-transactions', PaymentTransactionController::class)->parameters([
        'payment-transactions' => 'paymentTransaction'
    ]);
    
    // =========================================================================
    // REPORTS & ANALYTICS
    // =========================================================================
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    
    // =========================================================================
    // ROLES (Admin Only)
    // =========================================================================
    Route::resource('roles', RoleController::class);
});
