@extends('layouts.management')

@section('title', 'Management Dashboard')
@section('subtitle', 'Overview of gym operations and quick actions')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-100">Welcome back, {{ auth()->user()->name ?? 'Manager' }}!</h2>
                <p class="text-gray-400 mt-1">Here's what's happening at your gym today.</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-400">{{ now()->format('l, F j, Y') }}</p>
                <p class="text-lg font-semibold text-indigo-400">{{ now()->format('g:i A') }}</p>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Customers -->
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-600 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-400">Total Customers</p>
                    <p class="text-2xl font-bold text-gray-100">{{ \App\Models\CustomerProfile::count() }}</p>
                </div>
            </div>
        </div>

        <!-- Active Memberships -->
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-600 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-400">Active Memberships</p>
                    <p class="text-2xl font-bold text-gray-100">{{ \App\Models\MembershipSubscription::where('status', 'active')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Today's Classes -->
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-600 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-400">Today's Classes</p>
                    <p class="text-2xl font-bold text-gray-100">{{ \App\Models\ClassSchedule::whereDate('start_time', today())->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-600 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-400">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-100">₱{{ number_format(\App\Models\PaymentTransaction::where('status', 'completed')->sum('amount'), 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Quick Actions Card -->
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-100 mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('customer.register') }}" 
                   class="flex items-center p-3 bg-slate-700 hover:bg-slate-600 rounded-lg transition-colors group">
                    <div class="p-2 bg-blue-600 rounded-lg group-hover:bg-blue-500 transition-colors">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-100">Customer Registration</p>
                        <p class="text-xs text-gray-400">Direct link to customer signup</p>
                    </div>
                </a>

                <a href="{{ route('class-schedules.create') }}" 
                   class="flex items-center p-3 bg-slate-700 hover:bg-slate-600 rounded-lg transition-colors group">
                    <div class="p-2 bg-green-600 rounded-lg group-hover:bg-green-500 transition-colors">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-100">Schedule New Class</p>
                        <p class="text-xs text-gray-400">Create a new class schedule</p>
                    </div>
                </a>

                <a href="{{ route('payment-transactions.create') }}" 
                   class="flex items-center p-3 bg-slate-700 hover:bg-slate-600 rounded-lg transition-colors group">
                    <div class="p-2 bg-purple-600 rounded-lg group-hover:bg-purple-500 transition-colors">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-100">Process Payment</p>
                        <p class="text-xs text-gray-400">Record a new payment transaction</p>
                    </div>
                </a>

                @can('admin-only')
                <a href="{{ route('trainer-profiles.create') }}" 
                   class="flex items-center p-3 bg-slate-700 hover:bg-slate-600 rounded-lg transition-colors group">
                    <div class="p-2 bg-yellow-600 rounded-lg group-hover:bg-yellow-500 transition-colors">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-100">Add New Trainer</p>
                        <p class="text-xs text-gray-400">Register a new fitness trainer</p>
                    </div>
                </a>
                @endcan
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-100 mb-4">Recent Activity</h3>
            <div class="space-y-4">
                @php
                    $recentBookings = \App\Models\ClassBooking::with([
                        'customerProfile' => fn($q) => $q->withTrashed(),
                        'schedule' => fn($q) => $q->withTrashed()->with(['fitnessClass' => fn($fq) => $fq->withTrashed()])
                    ])
                        ->orderBy('booked_at', 'desc')
                        ->limit(5)
                        ->get();
                @endphp

                @forelse($recentBookings as $booking)
                <div class="flex items-center space-x-3">
                    <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-100 truncate">
                            <span class="font-medium">{{ $booking->customerProfile?->full_name ?? 'Archived Customer' }}</span>
                            booked {{ $booking->schedule?->fitnessClass?->class_name ?? 'Archived Class' }}
                        </p>
                        <p class="text-xs text-gray-400">{{ $booking->booked_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-4">
                    <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p class="text-sm text-gray-400">No recent bookings</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Upcoming Classes Today -->
    <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-100">Today's Class Schedule</h3>
            <a href="{{ route('class-schedules.index') }}" 
               class="text-sm text-indigo-400 hover:text-indigo-300 transition-colors">
                View All Schedules →
            </a>
        </div>

        @php
            $todaySchedules = \App\Models\ClassSchedule::with(['fitnessClass', 'trainerProfile'])
                ->whereDate('start_time', today())
                ->orderBy('start_time')
                ->get();
        @endphp

        @if($todaySchedules->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-slate-700">
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Time</th>
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Class</th>
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Trainer</th>
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Capacity</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @foreach($todaySchedules as $schedule)
                    <tr class="hover:bg-slate-700/50 transition-colors">
                        <td class="py-3 px-4 text-sm text-gray-300">
                            {{ $schedule->start_time->format('g:i A') }} - {{ $schedule->end_time->format('g:i A') }}
                        </td>
                        <td class="py-3 px-4 text-sm font-medium text-gray-100">
                            {{ $schedule->fitnessClass->class_name }}
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-300">
                            {{ $schedule->trainerProfile->full_name }}
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-300">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                         {{ $schedule->remaining_slots > 5 ? 'bg-green-900 text-green-200' : 
                                            ($schedule->remaining_slots > 0 ? 'bg-yellow-900 text-yellow-200' : 'bg-red-900 text-red-200') }}">
                                {{ $schedule->booked_slots }}/{{ $schedule->available_slots }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-8">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <h4 class="text-lg font-medium text-gray-100 mb-2">No Classes Scheduled Today</h4>
            <p class="text-gray-400 mb-4">There are no fitness classes scheduled for today.</p>
            <a href="{{ route('class-schedules.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Schedule a Class
            </a>
        </div>
        @endif
    </div>
</div>
@endsection