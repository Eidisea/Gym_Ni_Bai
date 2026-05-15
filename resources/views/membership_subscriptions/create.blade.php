@extends('layouts.management')

@section('title', 'Create Membership Subscription')
@section('subtitle', 'Add a new customer membership subscription')

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
                <span class="text-gray-100 text-sm font-medium">Create Subscription</span>
            </div>
        </li>
    </ol>
</nav>

<div class="mb-4">
    <h1 class="text-xl font-bold text-gray-100">Create Membership Subscription</h1>
</div>

<div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
    <form method="POST" action="{{ route('membership-subscriptions.store') }}">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="customer_id" class="block text-sm font-medium text-gray-300 mb-1.5">
                    Customer <span class="text-red-400">*</span>
                </label>
                <select name="customer_id" id="customer_id"
                        class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        required>
                    <option value="">Select a customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->customer_id }}" {{ old('customer_id') == $customer->customer_id ? 'selected' : '' }}>
                            {{ $customer->full_name }} ({{ $customer->user->email }})
                        </option>
                    @endforeach
                </select>
                @error('customer_id')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="plan_id" class="block text-sm font-medium text-gray-300 mb-1.5">
                    Membership Plan <span class="text-red-400">*</span>
                </label>
                <select name="plan_id" id="plan_id"
                        class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        required>
                    <option value="">Select a membership plan</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->plan_id }}" 
                                data-duration="{{ $plan->duration_days }}"
                                data-price="{{ $plan->base_price }}"
                                {{ old('plan_id') == $plan->plan_id ? 'selected' : '' }}>
                            {{ $plan->plan_name }} - ₱{{ number_format($plan->base_price, 2) }} ({{ $plan->duration_days }} days)
                        </option>
                    @endforeach
                </select>
                @error('plan_id')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-300 mb-1.5">
                    Start Date <span class="text-red-400">*</span>
                </label>
                <input type="date" name="start_date" id="start_date" 
                       value="{{ old('start_date', now()->format('Y-m-d')) }}"
                       min="{{ now()->format('Y-m-d') }}"
                       class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       required>
                @error('start_date')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-300 mb-1.5">
                    End Date <span class="text-red-400">*</span>
                </label>
                <input type="date" name="end_date" id="end_date" 
                       value="{{ old('end_date') }}"
                       class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       required>
                @error('end_date')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="status" class="block text-sm font-medium text-gray-300 mb-1.5">
                    Status
                </label>
                <select name="status" id="status"
                        class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                @error('status')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t border-slate-600">
            <a href="{{ route('membership-subscriptions.index') }}" 
               class="px-3 py-1.5 text-sm text-gray-400 hover:text-gray-100">
                Cancel
            </a>
            <button type="submit" 
                    class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                Create Subscription
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const planSelect = document.getElementById('plan_id');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    function calculateEndDate() {
        const selectedOption = planSelect.options[planSelect.selectedIndex];
        const duration = selectedOption.getAttribute('data-duration');
        const startDate = startDateInput.value;

        if (duration && startDate) {
            const start = new Date(startDate);
            const end = new Date(start);
            end.setDate(start.getDate() + parseInt(duration));
            
            endDateInput.value = end.toISOString().split('T')[0];
        }
    }

    planSelect.addEventListener('change', calculateEndDate);
    startDateInput.addEventListener('change', calculateEndDate);

    calculateEndDate();
});
</script>
@endsection
