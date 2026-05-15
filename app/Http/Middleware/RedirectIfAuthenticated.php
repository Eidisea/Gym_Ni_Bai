<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                $role = $user->role->role_name;

                if (in_array($role, ['Admin', 'Staff'])) {
                    return redirect()->route('management.dashboard');
                } elseif ($role === 'Customer') {
                    return redirect()->route('customer.dashboard');
                }
            }
        }

        return $next($request);
    }
}
