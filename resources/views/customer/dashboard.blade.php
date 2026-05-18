@extends('layouts.customer')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-12">

    {{-- ================================================================
         HEADER
    ================================================================ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Welcome back, {{ Auth::user()->customerProfile?->first_name ?? Auth::user()->name }}!</h1>
            <p class="mt-1 text-slate-500">Here's what's going on with your membership.</p>
        </div>
        <a href="{{ route('customer.classes.index') }}" class="flex-shrink-0 inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Book a Class
        </a>
    </div>

    {{-- ================================================================
         ONBOARDING CHECKLIST — shown only until the member has done both steps
    ================================================================ --}}
    @php
        $hasSubscription = !!$activeSubscription;
        $hasBooking      = $totalBookings > 0;
        $showOnboarding  = !$hasSubscription || !$hasBooking;
    @endphp

    @if($showOnboarding)
        <div class="mb-8 bg-indigo-50 border border-indigo-200 rounded-2xl p-6">
            <h2 class="text-base font-semibold text-indigo-900 mb-4">Get started — complete these steps</h2>
            <div class="space-y-3">
                {{-- Step 1: Membership --}}
                <div class="flex items-center gap-3">
                    @if($hasSubscription)
                        <div class="flex-shrink-0 h-7 w-7 rounded-full bg-green-500 flex items-center justify-center">
                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="text-sm text-slate-600 line-through">Choose a membership plan</p>
                    @else
                        <div class="flex-shrink-0 h-7 w-7 rounded-full border-2 border-indigo-400 flex items-center justify-center">
                            <span class="text-xs font-bold text-indigo-600">1</span>
                        </div>
                        <div class="flex items-center justify-between flex-1">
                            <p class="text-sm font-medium text-slate-900">Choose a membership plan</p>
                            <a href="{{ route('customer.billing.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">Get a plan →</a>
                        </div>
                    @endif
                </div>
                {{-- Step 2: Book a class --}}
                <div class="flex items-center gap-3">
                    @if($hasBooking)
                        <div class="flex-shrink-0 h-7 w-7 rounded-full bg-green-500 flex items-center justify-center">
                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="text-sm text-slate-600 line-through">Book your first class</p>
                    @else
                        <div class="flex-shrink-0 h-7 w-7 rounded-full border-2 border-indigo-400 flex items-center justify-center">
                            <span class="text-xs font-bold text-indigo-600">2</span>
                        </div>
                        <div class="flex items-center justify-between flex-1">
                            <p class="text-sm font-medium {{ $hasSubscription ? 'text-slate-900' : 'text-slate-400' }}">Book your first class</p>
                            @if($hasSubscription)
                                <a href="{{ route('customer.classes.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">Browse classes →</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ================================================================
         STATS BAR
    ================================================================ --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 flex items-center gap-4">
            <div class="p-2.5 bg-indigo-100 rounded-lg flex-shrink-0">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-900">{{ $totalBookings }}</p>
                <p class="text-xs text-slate-500 mt-0.5">Total Bookings</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 flex items-center gap-4">
            <div class="p-2.5 bg-green-100 rounded-lg flex-shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-900">{{ $upcomingBookings->count() }}</p>
                <p class="text-xs text-slate-500 mt-0.5">Upcoming Classes</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 flex items-center gap-4 col-span-2 sm:col-span-1">
            <div class="p-2.5 bg-amber-100 rounded-lg flex-shrink-0">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                @if($daysLeft !== null)
                    <p class="text-2xl font-bold {{ $daysLeft <= 7 ? 'text-red-600' : 'text-slate-900' }}">{{ $daysLeft }}d</p>
                    <p class="text-xs text-slate-500 mt-0.5">Membership Left</p>
                @else
                    <p class="text-2xl font-bold text-slate-400">—</p>
                    <p class="text-xs text-slate-500 mt-0.5">No Active Plan</p>
                @endif
            </div>
        </div>
    </div>

    {{-- ================================================================
         MAIN WIDGETS
    ================================================================ --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        {{-- Membership Status Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-semibold text-slate-900">Membership Status</h2>
                <div class="p-2 bg-indigo-100 rounded-lg">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            @if($activeSubscription)
                @php
                    $start    = \Carbon\Carbon::parse($activeSubscription->start_date);
                    $end      = \Carbon\Carbon::parse($activeSubscription->end_date);
                    $total    = $start->diffInDays($end) ?: 1;
                    $elapsed  = $start->diffInDays(now());
                    $progress = min(100, round(($elapsed / $total) * 100));
                    $remaining = max(0, 100 - $progress);
                @endphp

                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-5">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            Active
                        </span>
                        <span class="text-sm font-semibold text-slate-800">{{ $activeSubscription->plan?->plan_name ?? 'Archived Plan' }}</span>
                    </div>

                    {{-- Progress ring --}}
                    <div class="flex items-center gap-6 mb-5">
                        <div class="relative flex-shrink-0" style="width:80px;height:80px;">
                            <svg viewBox="0 0 36 36" class="w-full h-full -rotate-90">
                                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e2e8f0" stroke-width="3"/>
                                <circle
                                    cx="18" cy="18" r="15.9" fill="none"
                                    stroke="{{ $daysLeft <= 7 ? '#ef4444' : '#6366f1' }}"
                                    stroke-width="3"
                                    stroke-dasharray="{{ $remaining }} 100"
                                    stroke-linecap="round"/>
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-lg font-bold {{ $daysLeft <= 7 ? 'text-red-600' : 'text-slate-900' }}">{{ $daysLeft }}</span>
                                <span class="text-xs text-slate-500 leading-none">days</span>
                            </div>
                        </div>
                        <div class="space-y-1.5 text-sm">
                            <div>
                                <p class="text-xs text-slate-500">Started</p>
                                <p class="font-medium text-slate-900">{{ $activeSubscription->start_date->format('M j, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Expires</p>
                                <p class="font-medium {{ $daysLeft <= 7 ? 'text-red-600' : 'text-slate-900' }}">{{ $activeSubscription->end_date->format('M j, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Expiry warning --}}
                    @if($daysLeft <= 7)
                        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-xs text-red-700">
                            Your membership expires {{ $daysLeft === 0 ? 'today' : "in {$daysLeft} day" . ($daysLeft === 1 ? '' : 's') }}. Renew now to keep access to classes.
                        </div>
                    @endif
                </div>

                <div class="pt-4 border-t border-slate-200 flex gap-2">
                    <a href="{{ route('customer.billing.index') }}" class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                        Renew Plan
                    </a>
                    <a href="{{ route('customer.billing.index') }}" class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-medium rounded-lg transition">
                        Manage Billing
                    </a>
                </div>

            @else
                <div class="flex-1 flex flex-col items-center justify-center text-center py-8">
                    <div class="p-3 bg-orange-100 rounded-full mb-4">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-slate-900 mb-2">No Active Membership</h3>
                    <p class="text-sm text-slate-500 mb-6">Get a membership plan to start booking classes.</p>
                    <a href="{{ route('customer.billing.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                        Choose a Plan
                    </a>
                </div>
            @endif
        </div>

        {{-- Upcoming Classes Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-semibold text-slate-900">Upcoming Classes</h2>
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>

            @if($upcomingBookings->count() > 0)
                <div class="flex-1 space-y-3">
                    @foreach($upcomingBookings as $booking)
                        @php
                            $start    = \Carbon\Carbon::parse($booking->schedule->start_time);
                            $isToday  = $start->isToday();
                            $isTomorrow = $start->isTomorrow();
                        @endphp
                        <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                            {{-- Date badge --}}
                            <div class="flex-shrink-0 w-12 text-center bg-white border border-slate-200 rounded-lg py-1.5 shadow-sm">
                                <p class="text-xs font-semibold text-slate-500 uppercase leading-none">{{ $start->format('M') }}</p>
                                <p class="text-xl font-bold text-slate-900 leading-tight">{{ $start->format('j') }}</p>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <p class="font-semibold text-slate-900 text-sm truncate">{{ $booking->schedule->fitnessClass?->class_name ?? 'Archived Class' }}</p>
                                    @if($isToday)
                                        <span class="flex-shrink-0 text-xs font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 px-1.5 py-0.5 rounded">Today</span>
                                    @elseif($isTomorrow)
                                        <span class="flex-shrink-0 text-xs font-medium text-amber-600 bg-amber-50 border border-amber-200 px-1.5 py-0.5 rounded">Tomorrow</span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-500">
                                    {{ $start->format('g:i A') }}
                                    @if($booking->schedule->trainerProfile)
                                        · {{ $booking->schedule->trainerProfile->first_name }} {{ $booking->schedule->trainerProfile->last_name }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 pt-4 border-t border-slate-200">
                    <a href="{{ route('customer.bookings.index') }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 transition">
                        View All Bookings
                    </a>
                </div>

            @else
                <div class="flex-1 flex flex-col items-center justify-center text-center py-8">
                    <div class="p-3 bg-slate-100 rounded-full mb-4">
                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-slate-900 mb-2">No Upcoming Classes</h3>
                    <p class="text-sm text-slate-500 mb-6">Browse the schedule and book your next session.</p>
                    <a href="{{ route('customer.classes.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                        Browse Classes
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- ================================================================
         QUICK ACTIONS
    ================================================================ --}}
    <div class="mt-8">
        <h2 class="text-base font-semibold text-slate-700 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('customer.classes.index') }}" class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 text-center hover:border-indigo-500 hover:ring-1 hover:ring-indigo-500 transition-all group">
                <div class="p-3 bg-indigo-100 rounded-lg mx-auto w-fit mb-3 group-hover:bg-indigo-200 transition-colors">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <h3 class="font-medium text-slate-900 text-sm">Find Classes</h3>
                <p class="text-xs text-slate-500 mt-1">Browse upcoming sessions</p>
            </a>

            <a href="{{ route('customer.bookings.index') }}" class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 text-center hover:border-indigo-500 hover:ring-1 hover:ring-indigo-500 transition-all group">
                <div class="p-3 bg-blue-100 rounded-lg mx-auto w-fit mb-3 group-hover:bg-blue-200 transition-colors">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="font-medium text-slate-900 text-sm">My Bookings</h3>
                <p class="text-xs text-slate-500 mt-1">View your reservations</p>
            </a>

            <a href="{{ route('customer.billing.index') }}" class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 text-center hover:border-indigo-500 hover:ring-1 hover:ring-indigo-500 transition-all group">
                <div class="p-3 bg-green-100 rounded-lg mx-auto w-fit mb-3 group-hover:bg-green-200 transition-colors">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <h3 class="font-medium text-slate-900 text-sm">Billing</h3>
                <p class="text-xs text-slate-500 mt-1">Manage payments</p>
            </a>

            <a href="{{ route('customer.profile.edit') }}" class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 text-center hover:border-indigo-500 hover:ring-1 hover:ring-indigo-500 transition-all group">
                <div class="p-3 bg-purple-100 rounded-lg mx-auto w-fit mb-3 group-hover:bg-purple-200 transition-colors">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h3 class="font-medium text-slate-900 text-sm">Profile</h3>
                <p class="text-xs text-slate-500 mt-1">Update your info</p>
            </a>
        </div>
    </div>

</div>
@endsection