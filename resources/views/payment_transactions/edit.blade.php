@extends('layouts.management')

@section('title', 'Edit Payment Transaction')
@section('subtitle', 'Update transaction details')

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
                <a href="{{ route('payment-transactions.index') }}" class="text-gray-400 hover:text-gray-100 text-sm">Payment Transactions</a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-100 text-sm font-medium">Edit</span>
            </div>
        </li>
    </ol>
</nav>

<div class="mb-4">
    <h1 class="text-xl font-bold text-gray-100">Edit Payment Transaction</h1>
</div>

<div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
    <form method="POST" action="{{ route('payment-transactions.update', $paymentTransaction) }}">
        @csrf
        @method('PUT')
        
        <div class="bg-slate-700 border border-slate-600 rounded-lg p-3 mb-4">
            <h3 class="text-base font-semibold text-gray-100 mb-2">Transaction Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                <div>
                    <span class="text-gray-400">Transaction ID:</span>
                    <span class="text-gray-100 ml-2">#{{ $paymentTransaction->transaction_id }}</span>
                </div>
                <div>
                    <span class="text-gray-400">Customer:</span>
                    <span class="text-gray-100 ml-2">{{ $paymentTransaction->customerProfile->full_name }}</span>
                </div>
                <div>
                    <span class="text-gray-400">Payment Method:</span>
                    <span class="text-gray-100 ml-2">{{ ucfirst($paymentTransaction->payment_method) }}</span>
                </div>
                <div>
                    <span class="text-gray-400">Transaction Date:</span>
                    <span class="text-gray-100 ml-2">{{ $paymentTransaction->transaction_date->format('M j, Y g:i A') }}</span>
                </div>
                @if($paymentTransaction->subscription)
                <div>
                    <span class="text-gray-400">Subscription:</span>
                    <span class="text-gray-100 ml-2">{{ $paymentTransaction->subscription->plan->plan_name }}</span>
                </div>
                @endif
                @if($paymentTransaction->booking)
                <div>
                    <span class="text-gray-400">Booking:</span>
                    <span class="text-gray-100 ml-2">{{ $paymentTransaction->booking->schedule->fitnessClass->class_name }}</span>
                </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="amount" class="block text-sm font-medium text-gray-300 mb-1.5">Amount <span class="text-red-400">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-sm text-gray-400">₱</span>
                    <input type="number" name="amount" id="amount" step="0.01" min="0" required
                           value="{{ old('amount', $paymentTransaction->amount) }}"
                           class="w-full pl-8 pr-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                @error('amount')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-300 mb-1.5">Status <span class="text-red-400">*</span></label>
                <select name="status" id="status" required
                        class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="pending" {{ old('status', $paymentTransaction->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ old('status', $paymentTransaction->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="failed" {{ old('status', $paymentTransaction->status) == 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="refunded" {{ old('status', $paymentTransaction->status) == 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
                @error('status')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-4">
            <label for="notes" class="block text-sm font-medium text-gray-300 mb-1.5">Notes</label>
            <textarea name="notes" id="notes" rows="3" maxlength="1000"
                      class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('notes', $paymentTransaction->notes) }}</textarea>
            @error('notes')
                <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
            @enderror
        </div>

        @if($paymentTransaction->payment_method === 'cash' && $paymentTransaction->cashPayment)
        <div class="mt-4 bg-slate-700 border border-slate-600 rounded-lg p-3">
            <h3 class="text-base font-semibold text-gray-100 mb-2">Cash Payment Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                <div>
                    <span class="text-gray-400">Staff Member:</span>
                    <span class="text-gray-100 ml-2">{{ $paymentTransaction->cashPayment->staffProfile->full_name }}</span>
                </div>
                <div>
                    <span class="text-gray-400">Amount Received:</span>
                    <span class="text-gray-100 ml-2">₱{{ number_format($paymentTransaction->cashPayment->amount_received, 2) }}</span>
                </div>
                <div>
                    <span class="text-gray-400">Change Given:</span>
                    <span class="text-gray-100 ml-2">₱{{ number_format($paymentTransaction->cashPayment->change_given, 2) }}</span>
                </div>
            </div>
        </div>
        @elseif($paymentTransaction->payment_method === 'card' && $paymentTransaction->cardPayment)
        <div class="mt-4 bg-slate-700 border border-slate-600 rounded-lg p-3">
            <h3 class="text-base font-semibold text-gray-100 mb-2">Card Payment Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                <div>
                    <span class="text-gray-400">Card Number:</span>
                    <span class="text-gray-100 ml-2">**** **** **** {{ $paymentTransaction->cardPayment->card_last_four }}</span>
                </div>
                <div>
                    <span class="text-gray-400">Card Type:</span>
                    <span class="text-gray-100 ml-2">{{ ucfirst($paymentTransaction->cardPayment->card_type) }}</span>
                </div>
                <div>
                    <span class="text-gray-400">Authorization Code:</span>
                    <span class="text-gray-100 ml-2">{{ $paymentTransaction->cardPayment->authorization_code }}</span>
                </div>
                @if($paymentTransaction->cardPayment->processor_reference)
                <div>
                    <span class="text-gray-400">Processor Reference:</span>
                    <span class="text-gray-100 ml-2">{{ $paymentTransaction->cardPayment->processor_reference }}</span>
                </div>
                @endif
            </div>
        </div>
        @elseif($paymentTransaction->payment_method === 'ewallet' && $paymentTransaction->ewalletPayment)
        <div class="mt-4 bg-slate-700 border border-slate-600 rounded-lg p-3">
            <h3 class="text-base font-semibold text-gray-100 mb-2">E-Wallet Payment Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                <div>
                    <span class="text-gray-400">Provider:</span>
                    <span class="text-gray-100 ml-2">{{ ucfirst($paymentTransaction->ewalletPayment->provider) }}</span>
                </div>
                <div>
                    <span class="text-gray-400">Reference Number:</span>
                    <span class="text-gray-100 ml-2">{{ $paymentTransaction->ewalletPayment->reference_number }}</span>
                </div>
                @if($paymentTransaction->ewalletPayment->account_identifier)
                <div>
                    <span class="text-gray-400">Account Identifier:</span>
                    <span class="text-gray-100 ml-2">{{ $paymentTransaction->ewalletPayment->account_identifier }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif

        <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t border-slate-700">
            <a href="{{ route('payment-transactions.show', $paymentTransaction) }}" 
               class="px-3 py-1.5 text-sm text-gray-400 hover:text-gray-100">
                Cancel
            </a>
            <button type="submit" 
                    class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                Update Transaction
            </button>
        </div>
    </form>
</div>
@endsection
