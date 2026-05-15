<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Gym Ni Bai') }} - Management Portal</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-900 text-gray-100">
    <div class="min-h-screen flex">
        <!-- Left Sidebar Navigation -->
        <div class="w-56 bg-slate-800 border-r border-slate-700 flex flex-col">
            <!-- Logo/Brand -->
            <div class="flex items-center h-12 px-4 border-b border-slate-700">
                <span class="text-base font-semibold text-gray-100">Gym Ni Bai</span>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
                <!-- Dashboard -->
                <a href="{{ route('management.dashboard') }}" 
                   class="flex items-center px-2 py-1.5 font-medium rounded transition-colors
                          {{ request()->routeIs('management.dashboard') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                    Dashboard
                </a>

                <!-- Customer Management -->
                <div class="space-y-0.5">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-2 py-1.5">
                        Customer Management
                    </div>
                    <a href="{{ route('management.customer-profiles.index') }}" 
                       class="flex items-center px-2 py-1.5 font-medium rounded transition-colors
                              {{ request()->routeIs('management.customer-profiles.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                        Customer Profiles
                    </a>
                    <a href="{{ route('membership-subscriptions.index') }}" 
                       class="flex items-center px-2 py-1.5 font-medium rounded transition-colors
                              {{ request()->routeIs('membership-subscriptions.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                        Memberships
                    </a>
                </div>

                <!-- Class Management -->
                <div class="space-y-0.5">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-2 py-1.5">
                        Class Management
                    </div>
                    <a href="{{ route('fitness-classes.index') }}" 
                       class="flex items-center px-2 py-1.5 font-medium rounded transition-colors
                              {{ request()->routeIs('fitness-classes.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                        Fitness Classes
                    </a>
                    <a href="{{ route('class-schedules.index') }}" 
                       class="flex items-center px-2 py-1.5 font-medium rounded transition-colors
                              {{ request()->routeIs('class-schedules.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                        Class Schedules
                    </a>
                    <a href="{{ route('class-bookings.index') }}" 
                       class="flex items-center px-2 py-1.5 font-medium rounded transition-colors
                              {{ request()->routeIs('class-bookings.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                        Class Bookings
                    </a>
                </div>

                <!-- Staff Management (Admin Only) -->
                @can('admin-only')
                <div class="space-y-0.5">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-2 py-1.5">
                        Staff Management
                    </div>
                    <a href="{{ route('staff-profiles.index') }}" 
                       class="flex items-center px-2 py-1.5 font-medium rounded transition-colors
                              {{ request()->routeIs('staff-profiles.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                        Staff Profiles
                    </a>
                    <a href="{{ route('trainer-profiles.index') }}" 
                       class="flex items-center px-2 py-1.5 font-medium rounded transition-colors
                              {{ request()->routeIs('trainer-profiles.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                        Trainer Profiles
                    </a>
                    <a href="{{ route('roles.index') }}" 
                       class="flex items-center px-2 py-1.5 font-medium rounded transition-colors
                              {{ request()->routeIs('roles.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                        Roles & Permissions
                    </a>
                    <a href="{{ route('membership-plans.index') }}" 
                       class="flex items-center px-2 py-1.5 font-medium rounded transition-colors
                              {{ request()->routeIs('membership-plans.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                        Membership Plans
                    </a>
                </div>
                @endcan

                <!-- Financial Management -->
                <div class="space-y-0.5">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-2 py-1.5">
                        Financial Management
                    </div>
                    <a href="{{ route('payment-transactions.index') }}" 
                       class="flex items-center px-2 py-1.5 font-medium rounded transition-colors
                              {{ request()->routeIs('payment-transactions.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                        Payment Transactions
                    </a>
                    <a href="{{ route('payments.cash-process') }}" 
                       class="flex items-center px-2 py-1.5 font-medium rounded transition-colors
                              {{ request()->routeIs('payments.cash-process') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                        Process Cash Payment
                    </a>
                </div>
            </nav>

            <!-- User Profile & Logout -->
            <div class="border-t border-slate-700 p-3">
                <div class="flex items-center space-x-2 mb-2">
                    <div class="w-7 h-7 bg-indigo-600 rounded-full flex items-center justify-center">
                        <span class="text-xs font-medium text-white">{{ substr(auth()->user()->email, 0, 1) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-100 truncate">{{ auth()->user()->email }}</p>
                        <p class="text-xs text-gray-400">{{ auth()->user()->role->role_name }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('management.logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-2 py-1.5 text-xs font-medium text-gray-300 rounded hover:bg-slate-700 hover:text-white transition-colors">
                        Sign Out
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-slate-800 border-b border-slate-700 px-4 py-2.5">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-base font-semibold text-gray-100">@yield('title', 'Dashboard')</h1>
                        <p class="text-xs text-gray-400">@yield('subtitle', 'Manage your gym operations')</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-xs text-gray-400">{{ now()->format('M j, Y') }}</span>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mx-4 mt-3 p-2.5 bg-green-900/50 border border-green-700 rounded text-sm">
                    <span class="text-green-100">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mx-4 mt-3 p-2.5 bg-red-900/50 border border-red-700 rounded text-sm">
                    <span class="text-red-100">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
