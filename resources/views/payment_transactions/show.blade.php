@extends('layouts.management')

@section('title', 'Payment Transaction Details')
@section('subtitle', 'View complete transaction information')

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
                <span class="text-gray-100 text-sm font-medium">Transaction #{{ $paymentTransaction->transaction_id }}</span>
            </div>
        </li>
    </ol>
</nav>

<div class="flex justify-between items-center mb-4">
    <div>
        <h1 class="text-xl font-bold text-gray-100">Transaction #{{ $paymentTransaction->transaction_id }}</h1>
        <p class="text-sm text-gray-400 mt-0.5">{{ $paymentTransaction->customerProfile?->full_name ?? 'Archived Customer' }} - ₱{{ number_format($paymentTransaction->amount, 2) }}</p>
    </div>
    <div class="flex items-center space-x-2">
        @can('update', $paymentTransaction)
        <a href="{{ route('management.payment-transactions.edit', $paymentTransaction) }}" 
           class="inline-flex items-center px-3 py-1.5 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit
        </a>
        @endcan
        @can('delete', $paymentTransaction)
        <form method="POST" action="{{ route('management.payment-transactions.destroy', $paymentTransaction) }}" 
              onsubmit="return confirm('Are you sure you want to VOID this transaction? This action will permanently alter financial ledgers.')" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="inline-flex items-center px-3 py-1.5 bg-red-800 hover:bg-red-900 text-red-100 text-sm font-medium rounded-lg">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                Void Transaction
            </button>
        </form>
        @endcan
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
            <h2 class="text-base font-semibold text-gray-100 mb-3">Customer Information</h2>
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-indigo-600 rounded-full flex items-center justify-center">
                    <span class="text-base font-medium text-white">{{ substr($paymentTransaction->customerProfile->first_name, 0, 1) }}{{ substr($paymentTransaction->customerProfile->last_name, 0, 1) }}</span>
                </div>
                <div>
                    <h3 class="text-base font-medium text-gray-100">{{ $paymentTransaction->customerProfile->full_name }}</h3>
                    <p class="text-sm text-gray-400">{{ $paymentTransaction->customerProfile->user->email }}</p>
                    <p class="text-sm text-gray-400">{{ $paymentTransaction->customerProfile->phone_number }}</p>
                </div>
            </div>
        </div>

        <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
            <h2 class="text-base font-semibold text-gray-100 mb-3">Transaction Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                <div>
                    <label class="text-xs font-medium text-gray-400">Transaction ID</label>
                    <p class="text-gray-100">#{{ $paymentTransaction->transaction_id }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-400">Amount</label>
                    <p class="text-xl font-bold text-gray-100">₱{{ number_format($paymentTransaction->amount, 2) }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-400">Payment Method</label>
                    <div class="flex items-center">
                        @if($paymentTransaction->payment_method === 'cash')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Cash</span>
                        @elseif($paymentTransaction->payment_method === 'card')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Card</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">E-Wallet</span>
                        @endif
                    </div>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-400">Transaction Date</label>
                    <p class="text-gray-100">{{ $paymentTransaction->transaction_date->format('l, F j, Y') }}</p>
                    <p class="text-xs text-gray-400">{{ $paymentTransaction->transaction_date->format('g:i A') }}</p>
                </div>
            </div>
        </div>

        @if($paymentTransaction->payment_method === 'cash' && $paymentTransaction->cashPayment)
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
            <h2 class="text-base font-semibold text-gray-100 mb-3">Cash Payment Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                <div>
                    <label class="text-xs font-medium text-gray-400">Staff Member</label>
                    <p class="text-gray-100">{{ $paymentTransaction->cashPayment->staffProfile->full_name }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-400">Amount Received</label>
                    <p class="text-gray-100">₱{{ number_format($paymentTransaction->cashPayment->amount_received, 2) }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-400">Change Given</label>
                    <p class="text-gray-100">₱{{ number_format($paymentTransaction->cashPayment->change_given, 2) }}</p>
                </div>
            </div>
        </div>
        @elseif($paymentTransaction->payment_method === 'card' && $paymentTransaction->cardPayment)
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
            <h2 class="text-base font-semibold text-gray-100 mb-3">Card Payment Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                <div>
                    <label class="text-xs font-medium text-gray-400">Card Number</label>
                    <p class="text-gray-100">**** **** **** {{ $paymentTransaction->cardPayment->card_last_four }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-400">Card Type</label>
                    <p class="text-gray-100">{{ ucfirst($paymentTransaction->cardPayment->card_type) }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-400">Authorization Code</label>
                    <p class="text-gray-100">{{ $paymentTransaction->cardPayment->authorization_code }}</p>
                </div>
                @if($paymentTransaction->cardPayment->processor_reference)
                <div>
                    <label class="text-xs font-medium text-gray-400">Processor Reference</label>
                    <p class="text-gray-100">{{ $paymentTransaction->cardPayment->processor_reference }}</p>
                </div>
                @endif
            </div>
        </div>
        @elseif($paymentTransaction->payment_method === 'ewallet' && $paymentTransaction->ewalletPayment)
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
            <h2 class="text-base font-semibold text-gray-100 mb-3">E-Wallet Payment Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                <div>
                    <label class="text-xs font-medium text-gray-400">Provider</label>
                    <p class="text-gray-100">{{ ucfirst($paymentTransaction->ewalletPayment->provider) }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-400">Reference Number</label>
                    <p class="text-gray-100">{{ $paymentTransaction->ewalletPayment->reference_number }}</p>
                </div>
                @if($paymentTransaction->ewalletPayment->account_identifier)
                <div>
                    <label class="text-xs font-medium text-gray-400">Account Identifier</label>
                    <p class="text-gray-100">{{ $paymentTransaction->ewalletPayment->account_identifier }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        @if($paymentTransaction->subscription || $paymentTransaction->booking)
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
            <h2 class="text-base font-semibold text-gray-100 mb-3">Related Information</h2>
            @if($paymentTransaction->subscription)
            <div class="border border-slate-600 rounded-lg p-3 mb-3">
                <h3 class="text-sm font-medium text-gray-100 mb-2">Membership Subscription</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs">
                    <div>
                        <span class="text-gray-400">Plan:</span>
                        <span class="text-gray-100 ml-1">{{ $paymentTransaction->subscription->plan->plan_name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Status:</span>
                        <span class="text-gray-100 ml-1">{{ ucfirst($paymentTransaction->subscription->status) }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Start Date:</span>
                        <span class="text-gray-100 ml-1">{{ $paymentTransaction->subscription->start_date->format('M j, Y') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">End Date:</span>
                        <span class="text-gray-100 ml-1">{{ $paymentTransaction->subscription->end_date->format('M j, Y') }}</span>
                    </div>
                </div>
            </div>
            @endif
            @if($paymentTransaction->booking)
            <div class="border border-slate-600 rounded-lg p-3">
                <h3 class="text-sm font-medium text-gray-100 mb-2">Class Booking</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs">
                    <div>
                        <span class="text-gray-400">Class:</span>
                        <span class="text-gray-100 ml-1">{{ $paymentTransaction->booking->schedule->fitnessClass->class_name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Trainer:</span>
                        <span class="text-gray-100 ml-1">{{ $paymentTransaction->booking->schedule->trainerProfile->full_name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Date & Time:</span>
                        <span class="text-gray-100 ml-1">{{ $paymentTransaction->booking->schedule->start_time->format('M j, Y g:i A') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Status:</span>
                        <span class="text-gray-100 ml-1">{{ ucfirst($paymentTransaction->booking->status) }}</span>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        @if($paymentTransaction->notes)
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
            <h2 class="text-base font-semibold text-gray-100 mb-3">Notes</h2>
            <p class="text-sm text-gray-300 whitespace-pre-wrap">{{ $paymentTransaction->notes }}</p>
        </div>
        @endif
    </div>

    <div class="space-y-4">
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
            <h2 class="text-base font-semibold text-gray-100 mb-3">Transaction Status</h2>
            <div class="space-y-3 text-sm">
                <div>
                    <label class="text-xs font-medium text-gray-400">Current Status</label>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                 {{ $paymentTransaction->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                    ($paymentTransaction->status === 'failed' ? 'bg-red-100 text-red-800' : 
                                     ($paymentTransaction->status === 'refunded' ? 'bg-orange-100 text-orange-800' : 'bg-yellow-100 text-yellow-800')) }}">
                        {{ ucfirst($paymentTransaction->status) }}
                    </span>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-400">Created</label>
                    <p class="text-gray-100">{{ $paymentTransaction->created_at->format('M j, Y g:i A') }}</p>
                </div>
                @if($paymentTransaction->updated_at != $paymentTransaction->created_at)
                <div>
                    <label class="text-xs font-medium text-gray-400">Last Updated</label>
                    <p class="text-gray-100">{{ $paymentTransaction->updated_at->format('M j, Y g:i A') }}</p>
                </div>
                @endif
            </div>
        </div>

        <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
            <h2 class="text-base font-semibold text-gray-100 mb-3">Quick Actions</h2>
            <div class="space-y-2">
                <a href="{{ route('management.customer-profiles.show', $paymentTransaction->customerProfile) }}" 
                   class="block w-full text-center px-3 py-1.5 bg-slate-700 hover:bg-slate-600 text-gray-100 text-sm rounded-lg">
                    View Customer Profile
                </a>
                @if($paymentTransaction->subscription)
                <a href="{{ route('management.membership-subscriptions.show', $paymentTransaction->subscription) }}" 
                   class="block w-full text-center px-3 py-1.5 bg-slate-700 hover:bg-slate-600 text-gray-100 text-sm rounded-lg">
                    View Subscription
                </a>
                @endif
                @if($paymentTransaction->booking)
                <a href="{{ route('management.class-bookings.show', $paymentTransaction->booking) }}" 
                   class="block w-full text-center px-3 py-1.5 bg-slate-700 hover:bg-slate-600 text-gray-100 text-sm rounded-lg">
                    View Booking
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
