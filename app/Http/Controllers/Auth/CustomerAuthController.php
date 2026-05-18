<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CustomerProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class CustomerAuthController extends Controller
{
    // =========================================================================
    // SHOW LOGIN
    // =========================================================================
    public function showLogin(): View
    {
        return view('auth.customer.login');
    }

    // =========================================================================
    // LOGIN
    // =========================================================================
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Ensure only Customer role accounts can log in here
        $user = User::where('email', $credentials['email'])
            ->whereHas('role', fn($q) => $q->where('role_name', Role::CUSTOMER))
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'email' => 'These credentials do not match our records.',
            ])->onlyInput('email');
        }

        if (! $user->is_active) {
            return back()->withErrors([
                'email' => 'Your account has been deactivated. Please contact support.',
            ])->onlyInput('email');
        }

        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        // Load the profile relationship to ensure name accessor works
        $user->load('customerProfile');

        return redirect()->intended(route('customer.dashboard'));
    }

    // =========================================================================
    // SHOW REGISTER
    // =========================================================================
    public function showRegister(): View
    {
        return view('auth.customer.register');
    }

    // =========================================================================
    // REGISTER — wrapped in DB::transaction
    // Inserts into: users + customer_profiles atomically
    // =========================================================================
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name'    => ['required', 'string', 'max:50'],
            'last_name'     => ['required', 'string', 'max:50'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'      => ['required', 'confirmed', Password::defaults()],
            'phone_number'  => ['required', 'string', 'max:20', 'unique:customer_profiles,phone_number'],
            'date_of_birth' => ['required', 'date', 'before:today'],
        ]);

        $user = DB::transaction(function () use ($validated) {
            // Step 1: Resolve the 'Customer' role ID
            $customerRole = Role::where('role_name', Role::CUSTOMER)->firstOrFail();

            // Step 2: Create the User record
            $user = User::create([
                'role_id'  => $customerRole->role_id,
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'is_active' => true,
            ]);

            // Step 3: Create the linked CustomerProfile record
            CustomerProfile::create([
                'user_id'       => $user->user_id,
                'first_name'    => $validated['first_name'],
                'last_name'     => $validated['last_name'],
                'phone_number'  => $validated['phone_number'],
                'date_of_birth' => $validated['date_of_birth'],
            ]);

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        // Load the profile relationship to ensure name accessor works
        $user->load('customerProfile');

        return redirect(route('customer.dashboard'));
    }

    // =========================================================================
    // LOGOUT
    // =========================================================================
    public function logout(Request $request): RedirectResponse
    {
        $role = Auth::user()->role->role_name;

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($role === 'Customer') {
            return redirect()->route('home');
        } else {
            return redirect()->route('management.login');
        }
    }
}