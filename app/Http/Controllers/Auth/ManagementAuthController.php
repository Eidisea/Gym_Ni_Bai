<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\StaffProfile;
use App\Models\TrainerProfile;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ManagementAuthController extends Controller
{
    // =========================================================================
    // SHOW LOGIN
    // =========================================================================
    public function showLogin(): View
    {
        return view('auth.management.login');
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

        // Ensure only Admin or Staff role accounts can log in here
        $user = User::where('email', $credentials['email'])
            ->whereHas('role', fn($q) => $q->whereIn('role_name', [Role::ADMIN, Role::STAFF]))
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

        return redirect()->intended(route('management.dashboard'));
    }

    // =========================================================================
    // SHOW REGISTER
    // =========================================================================
    public function showRegister(): View
    {
        $roles = Role::whereIn('role_name', [Role::ADMIN, Role::STAFF])->get();
        
        return view('auth.management.register', compact('roles'));
    }

    // =========================================================================
    // REGISTER — wrapped in DB::transaction
    // Creates users + staff_profiles atomically (NO TRAINER CREATION HERE)
    // =========================================================================
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name'     => ['required', 'string', 'max:50'],
            'last_name'      => ['required', 'string', 'max:50'],
            'email'          => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'       => ['required', 'confirmed', Password::defaults()],
            'role_id'        => ['required', 'exists:roles,role_id'],
            'department'     => ['nullable', 'string', 'max:50'],
        ]);

        $user = DB::transaction(function () use ($validated) {
            // Step 1: Create the User record
            $user = User::create([
                'role_id'   => $validated['role_id'],
                'email'     => $validated['email'],
                'password'  => Hash::make($validated['password']),
                'is_active' => true,
            ]);

            // Step 2: Create staff profile (Admin and Staff both get staff profiles)
            $role = Role::find($validated['role_id']);
            
            if ($role->role_name === Role::STAFF || $role->role_name === Role::ADMIN) {
                StaffProfile::create([
                    'user_id'    => $user->user_id,
                    'first_name' => $validated['first_name'],
                    'last_name'  => $validated['last_name'],
                    'department' => $validated['department'] ?? 'General',
                ]);
            }

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('management.dashboard'));
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

        if (in_array($role, ['Admin', 'Staff'])) {
            return redirect()->route('management.login');
        } else {
            return redirect()->route('customer.login');
        }
    }
}