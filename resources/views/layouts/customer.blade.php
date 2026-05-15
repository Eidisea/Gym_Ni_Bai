<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Gym Ni Bai') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-slate-50">
    <nav class="bg-slate-900 border-b border-slate-800" x-data="{ open: false, profileOpen: false }">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <a href="{{ route('customer.dashboard') }}" class="text-xl font-bold text-white">
                            Gym Ni Bai
                        </a>
                    </div>

                    <div class="hidden md:flex md:items-center md:space-x-8">
                        <a href="{{ route('customer.dashboard') }}" class="{{ request()->routeIs('customer.dashboard') ? 'text-indigo-400 font-semibold' : 'text-slate-300 hover:text-white' }} text-sm font-medium transition">
                            Home
                        </a>
                        <a href="{{ route('customer.classes.index') }}" class="{{ request()->routeIs('customer.classes.*') ? 'text-indigo-400 font-semibold' : 'text-slate-300 hover:text-white' }} text-sm font-medium transition">
                            Find Classes
                        </a>
                        <a href="{{ route('customer.bookings.index') }}" class="{{ request()->routeIs('customer.bookings.*') ? 'text-indigo-400 font-semibold' : 'text-slate-300 hover:text-white' }} text-sm font-medium transition">
                            My Bookings
                        </a>
                        <a href="{{ route('customer.billing.index') }}" class="{{ request()->routeIs('customer.billing.*') ? 'text-indigo-400 font-semibold' : 'text-slate-300 hover:text-white' }} text-sm font-medium transition">
                            My Billing
                        </a>
                    </div>

                    <div class="hidden md:flex md:items-center">
                        <div class="relative">
                            <button @click="profileOpen = !profileOpen" class="flex items-center text-sm font-medium text-gray-300 hover:text-white focus:outline-none transition">
                                <span>{{ Auth::user()->name }}</span>
                                <svg class="ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="profileOpen" @click.away="profileOpen = false" x-transition class="absolute right-0 mt-2 w-48 bg-slate-800 rounded-md shadow-lg py-1 border border-slate-700 z-50">
                                <a href="{{ route('customer.profile.edit') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-slate-700 hover:text-white">
                                    Profile Settings
                                </a>
                                <form method="POST" action="{{ route('customer.logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-slate-700 hover:text-white">
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center md:hidden">
                        <button @click="open = !open" class="text-gray-300 hover:text-white focus:outline-none">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div x-show="open" @click.away="open = false" x-transition class="md:hidden border-t border-slate-800">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <a href="{{ route('customer.dashboard') }}" class="{{ request()->routeIs('customer.dashboard') ? 'bg-slate-800 text-white' : 'text-gray-300 hover:bg-slate-800 hover:text-white' }} block px-3 py-2 text-base font-medium rounded-md">
                        Home
                    </a>
                    <a href="{{ route('customer.classes.index') }}" class="{{ request()->routeIs('customer.classes.*') ? 'bg-slate-800 text-white' : 'text-gray-300 hover:bg-slate-800 hover:text-white' }} block px-3 py-2 text-base font-medium rounded-md">
                        Find Classes
                    </a>
                    <a href="{{ route('customer.bookings.index') }}" class="{{ request()->routeIs('customer.bookings.*') ? 'bg-slate-800 text-white' : 'text-gray-300 hover:bg-slate-800 hover:text-white' }} block px-3 py-2 text-base font-medium rounded-md">
                        My Bookings
                    </a>
                    <a href="{{ route('customer.billing.index') }}" class="{{ request()->routeIs('customer.billing.*') ? 'bg-slate-800 text-white' : 'text-gray-300 hover:bg-slate-800 hover:text-white' }} block px-3 py-2 text-base font-medium rounded-md">
                        My Billing
                    </a>
                </div>
                <div class="pt-4 pb-3 border-t border-slate-800">
                    <div class="px-5">
                        <div class="text-base font-medium text-white">{{ Auth::user()->name }}</div>
                        <div class="text-sm font-medium text-gray-400">{{ Auth::user()->email }}</div>
                    </div>
                    <div class="mt-3 px-2 space-y-1">
                        <a href="{{ route('customer.profile.edit') }}" class="block px-3 py-2 text-base font-medium text-gray-300 hover:bg-slate-800 hover:text-white rounded-md">
                            Profile Settings
                        </a>
                        <form method="POST" action="{{ route('customer.logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-3 py-2 text-base font-medium text-gray-300 hover:bg-slate-800 hover:text-white rounded-md">
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <main class="flex-grow bg-slate-50">
            @yield('content')
        </main>

        <footer class="bg-white border-t border-slate-200 mt-auto">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <p class="text-center text-sm text-slate-500">&copy; {{ date('Y') }} Gym Ni Bai. All rights reserved.</p>
            </div>
        </footer>
</body>
</html>