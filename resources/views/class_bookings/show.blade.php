@extends('layouts.management')

@section('title', 'Class Booking Details')
@section('subtitle', 'View booking information and related transactions')

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
                <a href="{{ route('management.class-bookings.index') }}" class="text-gray-400 hover:text-gray-100 text-sm">Class Bookings</a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-100 text-sm font-medium">Booking #{{ $classBooking->booking_id }}</span>
            </div>
        </li>
    </ol>
</nav>

<div class="flex justify-between items-center mb-4">
    <div>
        <h1 class="text-xl font-bold text-gray-100">Booking #{{ $classBooking->booking_id }}</h1>
        <p class="text-sm text-gray-400 mt-0.5">{{ $classBooking->customerProfile->full_name }} - {{ $classBooking->schedule->fitnessClass->class_name }}</p>
    </div>
    <div class="flex items-center space-x-2">
        @can('update', $classBooking)
        <a href="{{ route('management.class-bookings.edit', $classBooking) }}" 
           class="inline-flex items-center px-3 py-1.5 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit
        </a>
        @if($classBooking->status === 'confirmed')
        <form method="POST" action="{{ route('management.class-bookings.cancel', $classBooking) }}" 
              onsubmit="return confirm('Cancel this booking? The seat will be released back to the class capacity.')" class="inline">
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

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
            <h2 class="text-base font-semibold text-gray-100 mb-3">Customer Information</h2>
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-indigo-600 rounded-full flex items-center justify-center">
                    <span class="text-base font-medium text-white">{{ substr($classBooking->customerProfile->first_name, 0, 1) }}{{ substr($classBooking->customerProfile->last_name, 0, 1) }}</span>
                </div>
                <div>
                    <h3 class="text-base font-medium text-gray-100">{{ $classBooking->customerProfile->full_name }}</h3>
                    <p class="text-sm text-gray-400">{{ $classBooking->customerProfile->user->email }}</p>
                    <p class="text-sm text-gray-400">{{ $classBooking->customerProfile->phone_number }}</p>
                </div>
            </div>
        </div>

        <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
            <h2 class="text-base font-semibold text-gray-100 mb-3">Class Schedule</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                <div>
                    <label class="text-xs font-medium text-gray-400">Class Name</label>
                    <p class="text-gray-100">{{ $classBooking->schedule->fitnessClass->class_name }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-400">Trainer</label>
                    <p class="text-gray-100">{{ $classBooking->schedule->trainerProfile->full_name }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-400">Date & Time</label>
                    <p class="text-gray-100">{{ $classBooking->schedule->start_time->format('l, F j, Y') }}</p>
                    <p class="text-xs text-gray-400">{{ $classBooking->schedule->start_time->format('g:i A') }} - {{ $classBooking->schedule->end_time->format('g:i A') }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-400">Location</label>
                    <p class="text-gray-100">{{ $classBooking->schedule->location }}</p>
                </div>
            </div>
        </div>

        @if($classBooking->transactions->count() > 0)
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
            <h2 class="text-base font-semibold text-gray-100 mb-3">Payment Transactions</h2>
            <div class="space-y-3">
                @foreach($classBooking->transactions as $transaction)
                <div class="border border-slate-600 rounded-lg p-3">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="text-sm font-medium text-gray-100">Transaction #{{ $transaction->transaction_id }}</p>
                            <p class="text-xs text-gray-400">{{ $transaction->transaction_date->format('M j, Y g:i A') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-100">${{ number_format($transaction->amount, 2) }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                         {{ $transaction->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                            ($transaction->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <span class="text-gray-400">Payment Method:</span>
                            <span class="text-gray-100 ml-1">{{ ucfirst($transaction->payment_method) }}</span>
                        </div>
                        @if($transaction->payment_method === 'cash' && $transaction->cashPayment)
                        <div>
                            <span class="text-gray-400">Staff:</span>
                            <span class="text-gray-100 ml-1">{{ $transaction->cashPayment->staffProfile->full_name }}</span>
                        </div>
                        @elseif($transaction->payment_method === 'card' && $transaction->cardPayment)
                        <div>
                            <span class="text-gray-400">Card:</span>
                            <span class="text-gray-100 ml-1">**** {{ $transaction->cardPayment->card_last_four }}</span>
                        </div>
                        @elseif($transaction->payment_method === 'ewallet' && $transaction->ewalletPayment)
                        <div>
                            <span class="text-gray-400">Provider:</span>
                            <span class="text-gray-100 ml-1">{{ ucfirst($transaction->ewalletPayment->provider) }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <div class="space-y-4">
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
            <h2 class="text-base font-semibold text-gray-100 mb-3">Booking Status</h2>
            <div class="space-y-3 text-sm">
                <div>
                    <label class="text-xs font-medium text-gray-400">Current Status</label>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                 {{ $classBooking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 
                                    ($classBooking->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                        {{ ucfirst($classBooking->status) }}
                    </span>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-400">Booked At</label>
                    <p class="text-gray-100">{{ $classBooking->booked_at->format('M j, Y g:i A') }}</p>
                </div>
                @if($classBooking->status === 'confirmed')
                <div>
                    <label class="text-xs font-medium text-gray-400">Class Status</label>
                    @if($classBooking->schedule->start_time->isPast())
                        <p class="text-blue-400">Class completed</p>
                    @else
                        <p class="text-green-400">{{ $classBooking->schedule->start_time->diffForHumans() }}</p>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
            <h2 class="text-base font-semibold text-gray-100 mb-3">Quick Actions</h2>
            <div class="space-y-2">
                <a href="{{ route('management.customer-profiles.show', $classBooking->customerProfile) }}" 
                   class="block w-full text-center px-3 py-1.5 bg-slate-700 hover:bg-slate-600 text-gray-100 text-sm rounded-lg">
                    View Customer Profile
                </a>
                <a href="{{ route('management.class-schedules.show', $classBooking->schedule) }}" 
                   class="block w-full text-center px-3 py-1.5 bg-slate-700 hover:bg-slate-600 text-gray-100 text-sm rounded-lg">
                    View Class Schedule
                </a>
                @if($classBooking->transactions->isEmpty())
                <a href="{{ route('management.payment-transactions.create') }}?booking_id={{ $classBooking->booking_id }}" 
                   class="block w-full text-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg">
                    Add Payment
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
