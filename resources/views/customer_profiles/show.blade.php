@extends('layouts.management')

@section('title', $customerProfile->full_name)
@section('subtitle', 'Customer profile details and activity')

@section('content')
<!-- Breadcrumbs -->
<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="{{ route('management.dashboard') }}" class="text-gray-400 hover:text-gray-100 transition-colors">
                Dashboard
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <a href="{{ route('management.customer-profiles.index') }}" class="text-gray-400 hover:text-gray-100 transition-colors">
                    Customer Profiles
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-100 font-medium">{{ $customerProfile->full_name }}</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Header with Actions -->
<div class="flex justify-between items-start mb-6">
    <div class="flex items-center">
        <div class="w-16 h-16 bg-indigo-600 rounded-full flex items-center justify-center">
            <span class="text-xl font-medium text-white">{{ substr($customerProfile->first_name, 0, 1) }}{{ substr($customerProfile->last_name, 0, 1) }}</span>
        </div>
        <div class="ml-4">
            <h1 class="text-2xl font-bold text-gray-100">{{ $customerProfile->full_name }}</h1>
            <div class="flex items-center mt-1 space-x-4">
                <span class="text-sm text-gray-400">{{ $customerProfile->user->email }}</span>
                @if($customerProfile->trashed())
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-900 text-slate-200">
                    Archived
                </span>
                @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                             {{ $customerProfile->user->is_active ? 'bg-green-900 text-green-200' : 'bg-red-900 text-red-200' }}">
                    {{ $customerProfile->user->is_active ? 'Active' : 'Inactive' }}
                </span>
                @endif
            </div>
        </div>
    </div>
    
    <div class="flex items-center space-x-3">
        @can('update', $customerProfile)
        <a href="{{ route('management.customer-profiles.edit', $customerProfile) }}" 
           class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit Profile
        </a>
        @endcan
        
        @can('delete', $customerProfile)
        <form method="POST" action="{{ route('management.customer-profiles.destroy', $customerProfile) }}" 
              onsubmit="return confirm('Are you sure you want to delete this customer profile? This action cannot be undone.')" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Delete Profile
            </button>
        </form>
        @endcan
    </div>
</div>

<!-- Profile Details -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Personal Information -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-100 mb-4">Personal Information</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-400">First Name</label>
                    <p class="text-gray-100 font-medium">{{ $customerProfile->first_name }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-400">Last Name</label>
                    <p class="text-gray-100 font-medium">{{ $customerProfile->last_name }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-400">Phone Number</label>
                    <p class="text-gray-100 font-medium">{{ $customerProfile->phone_number }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-400">Date of Birth</label>
                    <p class="text-gray-100 font-medium">
                        @if($customerProfile->date_of_birth)
                            {{ $customerProfile->date_of_birth->format('M j, Y') }} ({{ $customerProfile->date_of_birth->age }} years old)
                        @else
                            <span class="text-gray-400">Not provided</span>
                        @endif
                    </p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-400">Email Address</label>
                    <p class="text-gray-100 font-medium">{{ $customerProfile->user->email }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-400">Member Since</label>
                    <p class="text-gray-100 font-medium">{{ $customerProfile->created_at->format('M j, Y') }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="space-y-4">
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-100 mb-4">Quick Stats</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Active Memberships</span>
                    <span class="text-gray-100 font-medium">{{ $customerProfile->subscriptions->where('status', 'active')->count() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Total Bookings</span>
                    <span class="text-gray-100 font-medium">{{ $customerProfile->bookings->count() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Account Status</span>
                    <span class="text-gray-100 font-medium">{{ $customerProfile->user->is_active ? 'Active' : 'Inactive' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Memberships & Bookings -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Active Memberships -->
    <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-100">Active Memberships</h3>
            <a href="{{ route('management.membership-subscriptions.create') }}?customer_id={{ $customerProfile->customer_id }}" 
               class="text-sm text-indigo-400 hover:text-indigo-300 transition-colors">
                Add Membership →
            </a>
        </div>

        @php $activeMemberships = $customerProfile->subscriptions->where('status', 'active') @endphp
        @if($activeMemberships->count() > 0)
        <div class="space-y-3">
            @foreach($activeMemberships as $subscription)
            <div class="border border-slate-600 rounded-lg p-4">
                <div class="flex justify-between items-start mb-2">
                    <h4 class="font-medium text-gray-100">{{ $subscription->plan->plan_name }}</h4>
                    <span class="text-sm text-indigo-400">₱{{ number_format($subscription->plan->base_price, 2) }}</span>
                </div>
                <div class="text-sm text-gray-400">
                    <div>{{ $subscription->start_date->format('M j, Y') }} - {{ $subscription->end_date->format('M j, Y') }}</div>
                    <div class="mt-1">
                        @if($subscription->end_date->isFuture())
                            Expires in {{ $subscription->end_date->diffForHumans() }}
                        @else
                            <span class="text-red-400">Expired {{ $subscription->end_date->diffForHumans() }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-6">
            <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
            </svg>
            <p class="text-sm text-gray-400">No active memberships</p>
        </div>
        @endif
    </div>

    <!-- Recent Bookings -->
    <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-100">Recent Bookings</h3>
            <a href="{{ route('management.class-bookings.create') }}?customer_id={{ $customerProfile->customer_id }}" 
               class="text-sm text-indigo-400 hover:text-indigo-300 transition-colors">
                Book Class →
            </a>
        </div>

        @if($customerProfile->bookings->count() > 0)
        <div class="space-y-3">
            @foreach($customerProfile->bookings->sortByDesc('booked_at')->take(5) as $booking)
            <div class="border border-slate-600 rounded-lg p-4">
                <div class="flex justify-between items-start mb-2">
                    <h4 class="font-medium text-gray-100">{{ $booking->schedule->fitnessClass->class_name }}</h4>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                 {{ $booking->status === 'confirmed' ? 'bg-green-900 text-green-200' : 
                                    ($booking->status === 'cancelled' ? 'bg-red-900 text-red-200' : 'bg-blue-900 text-blue-200') }}">
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>
                <div class="text-sm text-gray-400">
                    <div>{{ $booking->schedule->start_time->format('M j, Y g:i A') }}</div>
                    <div class="mt-1">Booked {{ $booking->booked_at->diffForHumans() }}</div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-6">
            <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <p class="text-sm text-gray-400">No class bookings yet</p>
        </div>
        @endif
    </div>
</div>
@endsection