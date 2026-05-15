@extends('layouts.management')

@section('title', ($membershipSubscription->customerProfile?->full_name ?? 'Archived Customer') . ' Subscription')
@section('subtitle', 'Membership subscription details and history')

@section('content')
<nav class="flex mb-4" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1">
        <li class="inline-flex items-center">
            <a href="{{ route('management.dashboard') }}" class="text-gray-400 hover:text-gray-100 text-sm">Dashboard</a>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <a href="{{ route('membership-subscriptions.index') }}" class="text-gray-400 hover:text-gray-100 text-sm">Membership Subscriptions</a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-100 text-sm font-medium">{{ $membershipSubscription->customerProfile?->full_name ?? 'Archived Customer' }}</span>
            </div>
        </li>
    </ol>
</nav>

<div class="flex justify-between items-start mb-4">
    <div class="flex items-center">
        <div class="w-12 h-12 bg-indigo-600 rounded-full flex items-center justify-center">
            <span class="text-base font-medium text-white">{{ $membershipSubscription->customerProfile ? substr($membershipSubscription->customerProfile->first_name, 0, 1) . substr($membershipSubscription->customerProfile->last_name, 0, 1) : 'AC' }}</span>
        </div>
        <div class="ml-3">
            <h1 class="text-xl font-bold text-gray-100">{{ $membershipSubscription->customerProfile?->full_name ?? 'Archived Customer' }}</h1>
            <div class="flex items-center mt-0.5 space-x-3">
                <span class="text-sm text-gray-400">{{ $membershipSubscription->plan->plan_name }}</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                             {{ $membershipSubscription->status === 'active' ? 'bg-green-100 text-green-800' : 
                                ($membershipSubscription->status === 'expired' ? 'bg-red-100 text-red-800' : 
                                 ($membershipSubscription->status === 'cancelled' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800')) }}">
                    {{ ucfirst($membershipSubscription->status) }}
                </span>
            </div>
        </div>
    </div>
    
    <div class="flex items-center space-x-2">
        @can('update', $membershipSubscription)
        <a href="{{ route('membership-subscriptions.edit', $membershipSubscription) }}" 
           class="inline-flex items-center px-3 py-1.5 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit
        </a>
        @if($membershipSubscription->status !== 'cancelled')
        <form method="POST" action="{{ route('membership-subscriptions.cancel', $membershipSubscription) }}" 
              onsubmit="return confirm('Are you sure you want to archive/cancel this record? Financial and historical linkages will be preserved.')" class="inline">
            @csrf
            <button type="submit" 
                    class="inline-flex items-center px-3 py-1.5 bg-slate-600 hover:bg-slate-700 text-white text-sm font-medium rounded-lg">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Cancel
            </button>
        </form>
        @endif
        @endcan
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
    <div class="lg:col-span-2">
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
            <h3 class="text-base font-semibold text-gray-100 mb-3">Subscription Details</h3>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <label class="text-xs font-medium text-gray-400">Customer</label>
                    <p class="text-gray-100 font-medium">{{ $membershipSubscription->customerProfile?->full_name ?? 'Archived Customer' }}</p>
                    <p class="text-xs text-gray-400">{{ $membershipSubscription->customerProfile?->user->email ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-400">Membership Plan</label>
                    <p class="text-gray-100 font-medium">{{ $membershipSubscription->plan->plan_name }}</p>
                    <p class="text-xs text-gray-400">₱{{ number_format($membershipSubscription->plan->base_price, 2) }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-400">Start Date</label>
                    <p class="text-gray-100 font-medium">{{ $membershipSubscription->start_date->format('M j, Y') }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-400">End Date</label>
                    <p class="text-gray-100 font-medium">{{ $membershipSubscription->end_date->format('M j, Y') }}</p>
                    @if($membershipSubscription->status === 'active')
                        @if($membershipSubscription->end_date->isFuture())
                            <p class="text-xs text-green-400">{{ $membershipSubscription->end_date->diffForHumans() }}</p>
                        @else
                            <p class="text-xs text-red-400">Expired {{ $membershipSubscription->end_date->diffForHumans() }}</p>
                        @endif
                    @endif
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-400">Duration</label>
                    <p class="text-gray-100 font-medium">{{ $membershipSubscription->plan->duration_days }} days</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-400">Created</label>
                    <p class="text-gray-100 font-medium">{{ $membershipSubscription->created_at->format('M j, Y') }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="space-y-3">
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
            <h3 class="text-base font-semibold text-gray-100 mb-3">Quick Stats</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-400">Status</span>
                    <span class="text-gray-100 font-medium">{{ ucfirst($membershipSubscription->status) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Days Remaining</span>
                    <span class="text-gray-100 font-medium">
                        @if($membershipSubscription->end_date->isFuture())
                            {{ $membershipSubscription->end_date->diffInDays() }}
                        @else
                            0
                        @endif
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Total Value</span>
                    <span class="text-gray-100 font-medium">₱{{ number_format($membershipSubscription->plan->base_price, 2) }}</span>
                </div>
            </div>
        </div>

        @if($membershipSubscription->status === 'active' && $membershipSubscription->end_date->isFuture())
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
            <h3 class="text-base font-semibold text-gray-100 mb-3">Progress</h3>
            @php
                $totalDays = $membershipSubscription->start_date->diffInDays($membershipSubscription->end_date);
                $usedDays = $membershipSubscription->start_date->diffInDays(now());
                $progress = $totalDays > 0 ? min(100, ($usedDays / $totalDays) * 100) : 0;
            @endphp
            <div class="space-y-1.5">
                <div class="flex justify-between text-xs">
                    <span class="text-gray-400">{{ $usedDays }} of {{ $totalDays }} days used</span>
                    <span class="text-gray-100">{{ number_format($progress, 1) }}%</span>
                </div>
                <div class="w-full bg-slate-700 rounded-full h-1.5">
                    <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ $progress }}%"></div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-base font-semibold text-gray-100">Customer Information</h3>
        @if($membershipSubscription->customerProfile)
        <a href="{{ route('customer-profiles.show', $membershipSubscription->customerProfile) }}" 
           class="text-sm text-indigo-400 hover:text-indigo-300">
            View Full Profile →
        </a>
        @endif
    </div>

    @if($membershipSubscription->customerProfile)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
        <div>
            <label class="text-xs font-medium text-gray-400">Contact Information</label>
            <div class="mt-1.5 space-y-0.5">
                <p class="text-gray-100">{{ $membershipSubscription->customerProfile->user->email }}</p>
                <p class="text-gray-300">{{ $membershipSubscription->customerProfile->phone_number }}</p>
            </div>
        </div>
        <div>
            <label class="text-xs font-medium text-gray-400">Personal Details</label>
            <div class="mt-1.5 space-y-0.5">
                @if($membershipSubscription->customerProfile->date_of_birth)
                    <p class="text-gray-100">{{ $membershipSubscription->customerProfile->date_of_birth->age }} years old</p>
                    <p class="text-gray-300">Born {{ $membershipSubscription->customerProfile->date_of_birth->format('M j, Y') }}</p>
                @else
                    <p class="text-gray-400">Age not provided</p>
                @endif
            </div>
        </div>
        <div>
            <label class="text-xs font-medium text-gray-400">Account Status</label>
            <div class="mt-1.5 space-y-0.5">
                <p class="text-gray-100">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                 {{ $membershipSubscription->customerProfile->user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $membershipSubscription->customerProfile->user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </p>
                <p class="text-gray-300">Member since {{ $membershipSubscription->customerProfile->created_at->format('M Y') }}</p>
            </div>
        </div>
    </div>
    @else
    <div class="text-center py-4">
        <p class="text-sm text-gray-400">Customer profile has been archived</p>
    </div>
    @endif
</div>
@endsection
