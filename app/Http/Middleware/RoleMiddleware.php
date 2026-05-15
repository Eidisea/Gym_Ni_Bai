<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = Auth::user()->role->role_name;

        // Management portal access - only Admin and Staff
        if ($role === 'management' && !in_array($userRole, ['Admin', 'Staff'])) {
            if ($userRole === 'Customer') {
                return redirect()->route('customer.dashboard')
                    ->with('error', 'Unauthorized access.');
            }
            abort(403, 'Unauthorized access.');
        }

        // Customer portal access - only Customers
        if ($role === 'customer' && $userRole !== 'Customer') {
            if (in_array($userRole, ['Admin', 'Staff'])) {
                return redirect()->route('management.dashboard')
                    ->with('error', 'Unauthorized access.');
            }
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
