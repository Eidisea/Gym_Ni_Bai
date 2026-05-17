<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Gym Management') }} - Management Portal</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-900 text-gray-100">
    <div class="min-h-screen flex">
        <!-- Left Sidebar Navigation -->
        <div class="w-64 bg-slate-800 border-r border-slate-700 flex flex-col">
            <!-- Logo/Brand -->
            <div class="flex items-center justify-center h-14 px-4 border-b border-slate-700">
                <span class="text-lg font-semibold text-gray-100">Gym Ni Bai</span>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 px-4 py-4 space-y-1">
                <!-- Dashboard -->
                <a href="{{ route('management.dashboard') }}" 
                   class="flex items-center px-2 py-1.5 text-sm font-medium rounded-lg transition-colors
                          {{ request()->routeIs('management.dashboard') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                    </svg>
                    Dashboard
                </a>

                <!-- Customer Management -->
                <div class="space-y-0.5">
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 py-1.5">
                        Customer Management
                    </div>
                    <a href="{{ route('management.customer-profiles.index') }}" 
                       class="flex items-center px-2 py-1.5 text-sm font-medium rounded-lg transition-colors
                              {{ request()->routeIs('management.customer-profiles.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        Customer Profiles
                    </a>
                    <a href="{{ route('management.membership-subscriptions.index') }}" 
                       class="flex items-center px-2 py-1.5 text-sm font-medium rounded-lg transition-colors
                              {{ request()->routeIs('management.membership-subscriptions.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                        Memberships
                    </a>
                </div>

                <!-- Class Management -->
                <div class="space-y-0.5">
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 py-1.5">
                        Class Management
                    </div>
                    <a href="{{ route('management.fitness-classes.index') }}" 
                       class="flex items-center px-2 py-1.5 text-sm font-medium rounded-lg transition-colors
                              {{ request()->routeIs('management.fitness-classes.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Fitness Classes
                    </a>
                    <a href="{{ route('management.class-schedules.index') }}" 
                       class="flex items-center px-2 py-1.5 text-sm font-medium rounded-lg transition-colors
                              {{ request()->routeIs('management.class-schedules.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Class Schedules
                    </a>
                    <a href="{{ route('management.class-bookings.index') }}" 
                       class="flex items-center px-2 py-1.5 text-sm font-medium rounded-lg transition-colors
                              {{ request()->routeIs('management.class-bookings.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Class Bookings
                    </a>
                </div>

                <!-- Staff Management (Admin Only) -->
                @can('admin-only')
                <div class="space-y-0.5">
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 py-1.5">
                        Staff Management
                    </div>
                    <a href="{{ route('management.staff-profiles.index') }}" 
                       class="flex items-center px-2 py-1.5 text-sm font-medium rounded-lg transition-colors
                              {{ request()->routeIs('management.staff-profiles.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Staff Profiles
                    </a>
                    <a href="{{ route('management.trainer-profiles.index') }}" 
                       class="flex items-center px-2 py-1.5 text-sm font-medium rounded-lg transition-colors
                              {{ request()->routeIs('management.trainer-profiles.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Trainer Profiles
                    </a>
                    <a href="{{ route('management.roles.index') }}" 
                       class="flex items-center px-2 py-1.5 text-sm font-medium rounded-lg transition-colors
                              {{ request()->routeIs('management.roles.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Roles & Permissions
                    </a>
                </div>
                @endcan

                <!-- Financial Management -->
                <div class="space-y-0.5">
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 py-1.5">
                        Financial Management
                    </div>
                    @can('admin-only')
                    <a href="{{ route('management.membership-plans.index') }}" 
                       class="flex items-center px-2 py-1.5 text-sm font-medium rounded-lg transition-colors
                              {{ request()->routeIs('management.membership-plans.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Membership Plans
                    </a>
                    @endcan
                    <a href="{{ route('management.payment-transactions.index') }}" 
                       class="flex items-center px-2 py-1.5 text-sm font-medium rounded-lg transition-colors
                              {{ request()->routeIs('management.payment-transactions.*') && !request()->routeIs('management.payment-transactions.cash-process') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Payment Transactions
                    </a>
                    @unless(auth()->user()->role->role_name === 'Admin')
                    <a href="{{ route('management.payment-transactions.cash-process') }}" 
                       class="flex items-center px-2 py-1.5 text-sm font-medium rounded-lg transition-colors
                              {{ request()->routeIs('management.payment-transactions.cash-process') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Process Cash Payment
                    </a>
                    @endunless

                    <!-- Reports & Analytics -->
                    <a href="{{ route('management.reports.index') }}" 
                       class="flex items-center px-2 py-1.5 text-sm font-medium rounded-lg transition-colors
                              {{ request()->routeIs('management.reports.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Reports & Analytics
                    </a>
                </div>
            </nav>

            <!-- User Profile & Logout -->
            <div class="border-t border-slate-700 p-3">
                <div class="mb-2">
                    <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-700 text-slate-200 mt-1">
                        {{ auth()->user()->role->role_name }}
                    </span>
                </div>
                <form method="POST" action="{{ route('management.logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center px-2 py-1.5 text-sm font-medium text-gray-300 rounded-lg hover:bg-slate-700 hover:text-white transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Sign Out
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-slate-800 border-b border-slate-700 px-4 py-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-lg font-semibold text-gray-100">@yield('title', 'Dashboard')</h1>
                        <p class="text-xs text-gray-400">@yield('subtitle', 'Manage your gym operations')</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-xs text-gray-400">{{ now()->format('M j, Y') }}</span>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mx-4 mt-3 p-3 bg-green-900/50 border border-green-700 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm text-green-100">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mx-4 mt-3 p-3 bg-red-900/50 border border-red-700 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm text-red-100">{{ session('error') }}</span>
                    </div>
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