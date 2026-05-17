@extends('layouts.management')

@section('title', $membershipPlan->plan_name)
@section('subtitle', 'Membership plan details and subscriptions')

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
                <a href="{{ route('management.membership-plans.index') }}" class="text-gray-400 hover:text-gray-100 transition-colors">
                    Membership Plans
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-100 font-medium">{{ $membershipPlan->plan_name }}</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Header with Actions -->
<div class="flex justify-between items-start mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-100">{{ $membershipPlan->plan_name }}</h1>
        <div class="flex items-center mt-2 space-x-4">
            <span class="text-2xl font-bold text-indigo-400">₱{{ number_format($membershipPlan->base_price, 2) }}</span>
            <span class="text-sm text-gray-400">{{ $membershipPlan->duration_days }} days access</span>
        </div>
    </div>
    
    <div class="flex items-center space-x-3">
        @can('update', $membershipPlan)
        <a href="{{ route('management.membership-plans.edit', $membershipPlan) }}" 
           class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit Plan
        </a>
        @endcan
        
        @can('admin-only')
        <button onclick="openArchiveModal({{ $membershipPlan->plan_id }}, '{{ $membershipPlan->plan_name }}')" 
                class="inline-flex items-center px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
            </svg>
            Archive
        </button>
        @endcan
    </div>
</div>

<div id="archiveModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-slate-800 border border-slate-700 rounded-lg p-4 max-w-md w-full mx-4">
        <h3 class="text-base font-semibold text-gray-100 mb-3">Archive Membership Plan</h3>
        <p class="text-sm text-gray-400 mb-3">Archive <span id="planName" class="font-semibold text-gray-100"></span>?</p>
        
        <form id="archiveForm" method="POST" action="">
            @csrf
            <div class="mb-3">
                <label for="archive_reason" class="block text-sm font-medium text-gray-300 mb-1">Reason</label>
                <select id="archive_reason" name="archive_reason" required
                        class="w-full px-3 py-2 text-sm bg-slate-700 border border-slate-600 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select reason</option>
                    <option value="Discontinued">Discontinued</option>
                    <option value="Replaced by New Plan">Replaced by New Plan</option>
                    <option value="Low Demand">Low Demand</option>
                    <option value="Pricing Update">Pricing Update</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeArchiveModal()" 
                        class="px-3 py-1.5 text-sm bg-slate-700 hover:bg-slate-600 text-gray-100 rounded-lg transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-3 py-1.5 text-sm bg-slate-600 hover:bg-slate-700 text-white rounded-lg transition-colors">
                    Archive
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openArchiveModal(planId, planName) {
    document.getElementById('planName').textContent = planName;
    document.getElementById('archiveForm').action = '/management/membership-plans/' + planId + '/archive';
    document.getElementById('archiveModal').classList.remove('hidden');
}

function closeArchiveModal() {
    document.getElementById('archiveModal').classList.add('hidden');
    document.getElementById('archive_reason').value = '';
}
</script>

<!-- Plan Details -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Main Details -->
    <div class="lg:col-span-2">
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-100 mb-4">Plan Overview</h3>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-400">Plan Name</label>
                        <p class="text-gray-100 font-medium">{{ $membershipPlan->plan_name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-400">Price</label>
                        <p class="text-gray-100 font-medium">₱{{ number_format($membershipPlan->base_price, 2) }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-400">Duration</label>
                        <p class="text-gray-100 font-medium">{{ $membershipPlan->duration_days }} days</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-400">Status</label>
                        <p class="text-gray-100 font-medium">Available</p>
                    </div>
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
                    <span class="text-gray-400">Active Subscriptions</span>
                    <span class="text-gray-100 font-medium">{{ $membershipPlan->subscriptions->count() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Total Revenue</span>
                    <span class="text-gray-100 font-medium">₱{{ number_format($membershipPlan->subscriptions->count() * $membershipPlan->base_price, 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Created</span>
                    <span class="text-gray-100 font-medium">{{ $membershipPlan->created_at->format('M j, Y') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Active Subscriptions -->
<div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-100">Active Subscriptions</h3>
        <a href="{{ route('management.membership-subscriptions.create') }}?plan_id={{ $membershipPlan->plan_id }}" 
           class="inline-flex items-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            New Subscription
        </a>
    </div>

    @if($membershipPlan->subscriptions->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr class="border-b border-slate-700">
                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Customer</th>
                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Start Date</th>
                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-400 uppercase tracking-wider">End Date</th>
                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                @foreach($membershipPlan->subscriptions->sortByDesc('start_date') as $subscription)
                <tr class="hover:bg-slate-700/50 transition-colors">
                    <td class="py-3 px-4 text-sm text-gray-300">
                        <div>
                            <div class="font-medium">{{ $subscription->customerProfile->full_name }}</div>
                            <div class="text-gray-400">{{ $subscription->customerProfile->user->email }}</div>
                        </div>
                    </td>
                    <td class="py-3 px-4 text-sm text-gray-300">
                        {{ $subscription->start_date->format('M j, Y') }}
                    </td>
                    <td class="py-3 px-4 text-sm text-gray-300">
                        {{ $subscription->end_date->format('M j, Y') }}
                    </td>
                    <td class="py-3 px-4 text-sm text-gray-300">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                     {{ $subscription->status === 'active' ? 'bg-green-900 text-green-200' : 
                                        ($subscription->status === 'expired' ? 'bg-red-900 text-red-200' : 'bg-yellow-900 text-yellow-200') }}">
                            {{ ucfirst($subscription->status) }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-sm text-gray-300">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('management.membership-subscriptions.show', $subscription) }}" 
                               class="text-gray-400 hover:text-indigo-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            @can('update', $subscription)
                            <a href="{{ route('management.membership-subscriptions.edit', $subscription) }}" 
                               class="text-gray-400 hover:text-yellow-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="text-center py-8">
        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        <h4 class="text-lg font-medium text-gray-100 mb-2">No Active Subscriptions</h4>
        <p class="text-gray-400 mb-4">No customers have subscribed to this plan yet.</p>
        <a href="{{ route('management.membership-subscriptions.create') }}?plan_id={{ $membershipPlan->plan_id }}" 
           class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Create First Subscription
        </a>
    </div>
    @endif
</div>
@endsection