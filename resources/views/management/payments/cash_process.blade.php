@extends('layouts.management')

@section('title', 'Process Cash Payment')
@section('subtitle', 'Record cash payment transaction')

@section('content')
<!-- Breadcrumbs -->
<nav class="flex mb-4" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1">
        <li class="inline-flex items-center">
            <a href="{{ route('management.dashboard') }}" class="text-xs text-gray-400 hover:text-gray-100">
                Dashboard
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-3 h-3 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-xs text-gray-100 font-medium">Process Cash Payment</span>
            </div>
        </li>
    </ol>
</nav>

<div class="max-w-2xl">
    <div class="bg-slate-800 border border-slate-700 rounded p-4">
        <h2 class="text-sm font-semibold text-gray-100 mb-4">Cash Payment Processing</h2>

        <form method="POST" action="{{ route('payments.cash-process.store') }}">
            @csrf

            <!-- Customer Selection -->
            <div class="mb-3">
                <label for="customer_id" class="block text-xs font-medium text-gray-300 mb-1">Customer</label>
                <select id="customer_id" name="customer_id" required
                        class="w-full px-2 py-1.5 text-sm bg-slate-700 border border-slate-600 rounded text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500
                               @error('customer_id') border-red-500 @enderror">
                    <option value="">Select Customer</option>
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

            <!-- Subscription Selection -->
            <div class="mb-3">
                <label for="subscription_id" class="block text-xs font-medium text-gray-300 mb-1">Subscription</label>
                <select id="subscription_id" name="subscription_id"
                        class="w-full px-2 py-1.5 text-sm bg-slate-700 border border-slate-600 rounded text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500
                               @error('subscription_id') border-red-500 @enderror">
                    <option value="">Select Subscription (Optional)</option>
                    @foreach($subscriptions as $subscription)
                        <option value="{{ $subscription->subscription_id }}" 
                                data-amount="{{ $subscription->plan->base_price }}"
                                {{ old('subscription_id') == $subscription->subscription_id ? 'selected' : '' }}>
                            {{ $subscription->customerProfile->full_name }} - {{ $subscription->plan->plan_name }} (₱{{ number_format($subscription->plan->base_price, 2) }})
                        </option>
                    @endforeach
                </select>
                @error('subscription_id')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Amount -->
            <div class="mb-3">
                <label for="amount" class="block text-xs font-medium text-gray-300 mb-1">Amount Due</label>
                <div class="relative">
                    <span class="absolute left-2 top-1.5 text-sm text-gray-400">₱</span>
                    <input type="number" id="amount" name="amount" step="0.01" min="0" required
                           value="{{ old('amount') }}"
                           class="w-full pl-6 pr-2 py-1.5 text-sm bg-slate-700 border border-slate-600 rounded text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500
                                  @error('amount') border-red-500 @enderror">
                </div>
                @error('amount')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Amount Tendered -->
            <div class="mb-3">
                <label for="amount_tendered" class="block text-xs font-medium text-gray-300 mb-1">Amount Tendered</label>
                <div class="relative">
                    <span class="absolute left-2 top-1.5 text-sm text-gray-400">₱</span>
                    <input type="number" id="amount_tendered" name="amount_tendered" step="0.01" min="0" required
                           value="{{ old('amount_tendered') }}"
                           class="w-full pl-6 pr-2 py-1.5 text-sm bg-slate-700 border border-slate-600 rounded text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500
                                  @error('amount_tendered') border-red-500 @enderror">
                </div>
                @error('amount_tendered')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Change Display -->
            <div class="mb-3 p-2 bg-slate-700 rounded">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-medium text-gray-300">Change</span>
                    <span id="change_display" class="text-sm font-semibold text-green-400">₱0.00</span>
                </div>
            </div>

            <!-- Receipt Number -->
            <div class="mb-4">
                <label for="receipt_number" class="block text-xs font-medium text-gray-300 mb-1">Receipt Number</label>
                <input type="text" id="receipt_number" name="receipt_number" required
                       value="{{ old('receipt_number', 'RCP-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT)) }}"
                       class="w-full px-2 py-1.5 text-sm bg-slate-700 border border-slate-600 rounded text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500
                              @error('receipt_number') border-red-500 @enderror">
                @error('receipt_number')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-700">
                <a href="{{ route('payment-transactions.index') }}" 
                   class="px-3 py-1.5 text-xs font-medium bg-slate-700 hover:bg-slate-600 text-gray-100 rounded transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-3 py-1.5 text-xs font-medium bg-indigo-600 hover:bg-indigo-700 text-white rounded transition-colors">
                    Process Payment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const subscriptionSelect = document.getElementById('subscription_id');
    const amountInput = document.getElementById('amount');
    const tenderedInput = document.getElementById('amount_tendered');
    const changeDisplay = document.getElementById('change_display');

    // Auto-fill amount when subscription is selected
    subscriptionSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const amount = selectedOption.getAttribute('data-amount');
        if (amount) {
            amountInput.value = amount;
            calculateChange();
        }
    });

    // Calculate change
    function calculateChange() {
        const amount = parseFloat(amountInput.value) || 0;
        const tendered = parseFloat(tenderedInput.value) || 0;
        const change = tendered - amount;
        
        if (change >= 0) {
            changeDisplay.textContent = '₱' + change.toFixed(2);
            changeDisplay.classList.remove('text-red-400');
            changeDisplay.classList.add('text-green-400');
        } else {
            changeDisplay.textContent = '₱' + Math.abs(change).toFixed(2) + ' short';
            changeDisplay.classList.remove('text-green-400');
            changeDisplay.classList.add('text-red-400');
        }
    }

    amountInput.addEventListener('input', calculateChange);
    tenderedInput.addEventListener('input', calculateChange);
});
</script>
@endsection
