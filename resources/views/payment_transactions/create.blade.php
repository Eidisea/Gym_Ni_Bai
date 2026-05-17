@extends('layouts.management')

@section('title', 'Create Payment Transaction')
@section('subtitle', 'Process a new customer payment')

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
                <a href="{{ route('management.payment-transactions.index') }}" class="text-gray-400 hover:text-gray-100 text-sm">Payment Transactions</a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-100 text-sm font-medium">Create Transaction</span>
            </div>
        </li>
    </ol>
</nav>

<div class="mb-4">
    <h1 class="text-xl font-bold text-gray-100">Create Payment Transaction</h1>
</div>

<div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
    <form method="POST" action="{{ route('management.payment-transactions.store') }}" id="paymentForm">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label for="customer_id" class="block text-sm font-medium text-gray-300 mb-1.5">Customer <span class="text-red-400">*</span></label>
                <select name="customer_id" id="customer_id" required
                        class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select a customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->customer_id }}" {{ old('customer_id') == $customer->customer_id ? 'selected' : '' }}>
                            {{ $customer->full_name }} ({{ $customer->user->email }})
                        </option>
                    @endforeach
                </select>
                @error('customer_id')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="amount" class="block text-sm font-medium text-gray-300 mb-1.5">Amount <span class="text-red-400">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-sm text-gray-400">₱</span>
                    <input type="number" name="amount" id="amount" step="0.01" min="0" required
                           value="{{ old('amount') }}"
                           class="w-full pl-8 pr-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                @error('amount')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="subscription_id" class="block text-sm font-medium text-gray-300 mb-1.5">Membership Subscription</label>
                <select name="subscription_id" id="subscription_id"
                        class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select a subscription (optional)</option>
                    @foreach($subscriptions as $subscription)
                        <option value="{{ $subscription->subscription_id }}" {{ old('subscription_id') == $subscription->subscription_id ? 'selected' : '' }}>
                            {{ $subscription->customerProfile->full_name }} - {{ $subscription->plan->plan_name }}
                        </option>
                    @endforeach
                </select>
                @error('subscription_id')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="booking_id" class="block text-sm font-medium text-gray-300 mb-1.5">Class Booking</label>
                <select name="booking_id" id="booking_id"
                        class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select a booking (optional)</option>
                    @foreach($bookings as $booking)
                        <option value="{{ $booking->booking_id }}" {{ old('booking_id') == $booking->booking_id ? 'selected' : '' }}>
                            {{ $booking->customerProfile->full_name }} - {{ $booking->schedule->fitnessClass->class_name }}
                        </option>
                    @endforeach
                </select>
                @error('booking_id')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="payment_method" class="block text-sm font-medium text-gray-300 mb-1.5">Payment Method <span class="text-red-400">*</span></label>
                <select name="payment_method" id="payment_method" required
                        class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select payment method</option>
                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Credit/Debit Card</option>
                    <option value="ewallet" {{ old('payment_method') == 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                </select>
                @error('payment_method')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-300 mb-1.5">Status <span class="text-red-400">*</span></label>
                <select name="status" id="status" required
                        class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="failed" {{ old('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
                @error('status')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div id="cashFields" class="hidden border-t border-slate-700 pt-4 mb-4">
            <h3 class="text-base font-semibold text-gray-100 mb-3">Cash Payment Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="staff_id" class="block text-sm font-medium text-gray-300 mb-1.5">Staff Member <span class="text-red-400">*</span></label>
                    <select name="staff_id" id="staff_id"
                            class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select staff member</option>
                        @foreach($staff as $staffMember)
                            <option value="{{ $staffMember->staff_id }}" {{ old('staff_id') == $staffMember->staff_id ? 'selected' : '' }}>
                                {{ $staffMember->full_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('staff_id')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="amount_received" class="block text-sm font-medium text-gray-300 mb-1.5">Amount Received <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-sm text-gray-400">₱</span>
                        <input type="number" name="amount_received" id="amount_received" step="0.01" min="0"
                               value="{{ old('amount_received') }}"
                               class="w-full pl-8 pr-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    @error('amount_received')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="change_given" class="block text-sm font-medium text-gray-300 mb-1.5">Change Given</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-sm text-gray-400">₱</span>
                        <input type="number" name="change_given" id="change_given" step="0.01" min="0"
                               value="{{ old('change_given') }}"
                               class="w-full pl-8 pr-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    @error('change_given')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div id="cardFields" class="hidden border-t border-slate-700 pt-4 mb-4">
            <h3 class="text-base font-semibold text-gray-100 mb-3">Card Payment Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="card_last_four" class="block text-sm font-medium text-gray-300 mb-1.5">Last 4 Digits <span class="text-red-400">*</span></label>
                    <input type="text" name="card_last_four" id="card_last_four" maxlength="4" pattern="[0-9]{4}"
                           value="{{ old('card_last_four') }}"
                           class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('card_last_four')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="card_type" class="block text-sm font-medium text-gray-300 mb-1.5">Card Type <span class="text-red-400">*</span></label>
                    <select name="card_type" id="card_type"
                            class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select card type</option>
                        <option value="visa" {{ old('card_type') == 'visa' ? 'selected' : '' }}>Visa</option>
                        <option value="mastercard" {{ old('card_type') == 'mastercard' ? 'selected' : '' }}>Mastercard</option>
                        <option value="amex" {{ old('card_type') == 'amex' ? 'selected' : '' }}>American Express</option>
                        <option value="discover" {{ old('card_type') == 'discover' ? 'selected' : '' }}>Discover</option>
                    </select>
                    @error('card_type')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="authorization_code" class="block text-sm font-medium text-gray-300 mb-1.5">Authorization Code <span class="text-red-400">*</span></label>
                    <input type="text" name="authorization_code" id="authorization_code" maxlength="50"
                           value="{{ old('authorization_code') }}"
                           class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('authorization_code')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="processor_reference" class="block text-sm font-medium text-gray-300 mb-1.5">Processor Reference</label>
                    <input type="text" name="processor_reference" id="processor_reference" maxlength="100"
                           value="{{ old('processor_reference') }}"
                           class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('processor_reference')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div id="ewalletFields" class="hidden border-t border-slate-700 pt-4 mb-4">
            <h3 class="text-base font-semibold text-gray-100 mb-3">E-Wallet Payment Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="provider" class="block text-sm font-medium text-gray-300 mb-1.5">Provider <span class="text-red-400">*</span></label>
                    <select name="provider" id="provider"
                            class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select provider</option>
                        <option value="gcash" {{ old('provider') == 'gcash' ? 'selected' : '' }}>GCash</option>
                        <option value="paymaya" {{ old('provider') == 'paymaya' ? 'selected' : '' }}>PayMaya</option>
                        <option value="grabpay" {{ old('provider') == 'grabpay' ? 'selected' : '' }}>GrabPay</option>
                        <option value="paypal" {{ old('provider') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                    </select>
                    @error('provider')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="reference_number" class="block text-sm font-medium text-gray-300 mb-1.5">Reference Number <span class="text-red-400">*</span></label>
                    <input type="text" name="reference_number" id="reference_number" maxlength="100"
                           value="{{ old('reference_number') }}"
                           class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('reference_number')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="account_identifier" class="block text-sm font-medium text-gray-300 mb-1.5">Account Identifier</label>
                    <input type="text" name="account_identifier" id="account_identifier" maxlength="100"
                           value="{{ old('account_identifier') }}"
                           class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('account_identifier')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="mb-4">
            <label for="notes" class="block text-sm font-medium text-gray-300 mb-1.5">Notes</label>
            <textarea name="notes" id="notes" rows="3" maxlength="1000"
                      class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('notes') }}</textarea>
            @error('notes')
                <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-700">
            <a href="{{ route('management.payment-transactions.index') }}" 
               class="px-3 py-1.5 text-sm text-gray-400 hover:text-gray-100">
                Cancel
            </a>
            <button type="submit" 
                    class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                Create Transaction
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethodSelect = document.getElementById('payment_method');
    const cashFields = document.getElementById('cashFields');
    const cardFields = document.getElementById('cardFields');
    const ewalletFields = document.getElementById('ewalletFields');

    function togglePaymentFields() {
        const method = paymentMethodSelect.value;
        
        cashFields.classList.add('hidden');
        cardFields.classList.add('hidden');
        ewalletFields.classList.add('hidden');
        
        if (method === 'cash') {
            cashFields.classList.remove('hidden');
        } else if (method === 'card') {
            cardFields.classList.remove('hidden');
        } else if (method === 'ewallet') {
            ewalletFields.classList.remove('hidden');
        }
    }

    paymentMethodSelect.addEventListener('change', togglePaymentFields);
    togglePaymentFields();
});
</script>
@endsection
