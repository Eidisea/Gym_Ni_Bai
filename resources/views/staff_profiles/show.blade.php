@extends('layouts.management')

@section('title', 'Staff Member Details')
@section('subtitle', 'View staff member information and activity')

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
                <a href="{{ route('management.staff-profiles.index') }}" class="text-gray-400 hover:text-gray-100 transition-colors">
                    Staff Profiles
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-100 font-medium">{{ $staffProfile->full_name }}</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Header with Actions -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-100">{{ $staffProfile->full_name }}</h1>
        <p class="text-gray-400 mt-1">{{ $staffProfile->department }} • {{ $staffProfile->user->email }}</p>
    </div>
    <div class="flex items-center space-x-3">
        @can('update', $staffProfile)
        <a href="{{ route('management.staff-profiles.edit', $staffProfile) }}" 
           class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit Staff Member
        </a>
        @endcan
        
        @can('admin-only')
        <button type="button" onclick="openDeactivateModal({{ $staffProfile->staff_id }}, {!! json_encode($staffProfile->full_name) !!})" 
                class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
            </svg>
            Deactivate
        </button>
        @endcan
    </div>
</div>

<div id="deactivateModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-slate-800 border border-slate-700 rounded-lg p-4 max-w-md w-full mx-4">
        <h3 class="text-base font-semibold text-gray-100 mb-3">Deactivate Staff Member</h3>
        <p class="text-sm text-gray-400 mb-3">Deactivate <span id="staffName" class="font-semibold text-gray-100"></span>?</p>
        
        <form id="deactivateForm" method="POST" action="">
            @csrf
            <div class="mb-3">
                <label for="archive_reason" class="block text-sm font-medium text-gray-300 mb-1">Reason</label>
                <select id="archive_reason" name="archive_reason" required
                        class="w-full px-3 py-2 text-sm bg-slate-700 border border-slate-600 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select reason</option>
                    <option value="Resigned">Resigned</option>
                    <option value="Terminated">Terminated</option>
                    <option value="Transferred">Transferred</option>
                    <option value="Leave of Absence">Leave of Absence</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeDeactivateModal()" 
                        class="px-3 py-1.5 text-sm bg-slate-700 hover:bg-slate-600 text-gray-100 rounded-lg transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-3 py-1.5 text-sm bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition-colors">
                    Deactivate
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openDeactivateModal(staffId, staffName) {
    document.getElementById('staffName').textContent = staffName;
    document.getElementById('deactivateForm').action = '/management/staff-profiles/' + staffId + '/archive';
    document.getElementById('deactivateModal').classList.remove('hidden');
}

function closeDeactivateModal() {
    document.getElementById('deactivateModal').classList.add('hidden');
    document.getElementById('archive_reason').value = '';
}
</script>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Staff Details -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Personal Information -->
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-100 mb-4">Personal Information</h2>
            <div class="flex items-center space-x-4 mb-6">
                <div class="w-20 h-20 bg-indigo-600 rounded-full flex items-center justify-center">
                    <span class="text-2xl font-medium text-white">{{ substr($staffProfile->first_name, 0, 1) }}{{ substr($staffProfile->last_name, 0, 1) }}</span>
                </div>
                <div>
                    <h3 class="text-xl font-medium text-gray-100">{{ $staffProfile->full_name }}</h3>
                    <p class="text-gray-400">Staff ID: {{ $staffProfile->staff_id }}</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">First Name</label>
                    <p class="text-gray-100">{{ $staffProfile->first_name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Last Name</label>
                    <p class="text-gray-100">{{ $staffProfile->last_name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Department</label>
                    <p class="text-gray-100">{{ $staffProfile->department }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">User ID</label>
                    <p class="text-gray-100">{{ $staffProfile->user_id }}</p>
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-100 mb-4">Account Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Email Address</label>
                    <p class="text-gray-100">{{ $staffProfile->user->email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Role</label>
                    <p class="text-gray-100">{{ $staffProfile->user->role->role_name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Account Status</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                 {{ $staffProfile->user->is_active ? 'bg-green-900 text-green-200' : 'bg-red-900 text-red-200' }}">
                        {{ $staffProfile->user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Member Since</label>
                    <p class="text-gray-100">{{ $staffProfile->created_at->format('M j, Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Cash Payment Activity -->
        @if($staffProfile->cashPayments->count() > 0)
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-100 mb-4">Cash Payment Activity</h2>
            <div class="space-y-3">
                @foreach($staffProfile->cashPayments->take(10) as $cashPayment)
                <div class="border border-slate-600 rounded-lg p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-gray-100">Transaction #{{ $cashPayment->transaction_id }}</p>
                            <p class="text-sm text-gray-400">{{ $cashPayment->transaction->transaction_date->format('M j, Y g:i A') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-100">${{ number_format($cashPayment->transaction->amount, 2) }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                         {{ $cashPayment->transaction->status === 'completed' ? 'bg-green-900 text-green-200' : 'bg-yellow-900 text-yellow-200' }}">
                                {{ ucfirst($cashPayment->transaction->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-3 text-sm">
                        <div>
                            <span class="text-gray-400">Received:</span>
                            <span class="text-gray-100 ml-2">${{ number_format($cashPayment->amount_received, 2) }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400">Change:</span>
                            <span class="text-gray-100 ml-2">${{ number_format($cashPayment->change_given, 2) }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
                @if($staffProfile->cashPayments->count() > 10)
                <p class="text-sm text-gray-400 text-center">Showing 10 of {{ $staffProfile->cashPayments->count() }} transactions</p>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Status & Statistics -->
    <div class="space-y-6">
        <!-- Status Card -->
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-100 mb-4">Status</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Account Status</label>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                 {{ $staffProfile->user->is_active ? 'bg-green-900 text-green-200' : 'bg-red-900 text-red-200' }}">
                        {{ $staffProfile->user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Created</label>
                    <p class="text-gray-100">{{ $staffProfile->created_at->format('M j, Y') }}</p>
                </div>
                @if($staffProfile->updated_at != $staffProfile->created_at)
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Last Updated</label>
                    <p class="text-gray-100">{{ $staffProfile->updated_at->format('M j, Y') }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Activity Statistics -->
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-100 mb-4">Activity Statistics</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Cash Payments Processed</label>
                    <p class="text-2xl font-bold text-gray-100">{{ $staffProfile->cashPayments->count() }}</p>
                </div>
                @if($staffProfile->cashPayments->count() > 0)
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Total Amount Handled</label>
                    <p class="text-2xl font-bold text-gray-100">${{ number_format($staffProfile->cashPayments->sum(fn($cp) => $cp->transaction->amount), 2) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Last Transaction</label>
                    <p class="text-gray-100">{{ $staffProfile->cashPayments->first()->transaction->transaction_date->format('M j, Y') }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-100 mb-4">Quick Actions</h2>
            <div class="space-y-3">
                <a href="{{ route('management.staff-profiles.edit', $staffProfile) }}" 
                   class="block w-full text-center px-4 py-2 bg-slate-700 hover:bg-slate-600 text-gray-100 rounded-lg transition-colors">
                    Edit Profile
                </a>
                @if($staffProfile->cashPayments->count() > 0)
                <a href="{{ route('management.payment-transactions.index') }}" 
                   class="block w-full text-center px-4 py-2 bg-slate-700 hover:bg-slate-600 text-gray-100 rounded-lg transition-colors">
                    View All Transactions
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection