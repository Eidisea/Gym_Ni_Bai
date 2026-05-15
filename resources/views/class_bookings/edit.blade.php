@extends('layouts.management')

@section('title', 'Edit Class Booking')
@section('subtitle', 'Update booking details')

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
                <a href="{{ route('class-bookings.index') }}" class="text-gray-400 hover:text-gray-100 text-sm">Class Bookings</a>
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
    <h1 class="text-xl font-bold text-gray-100">Edit Class Booking</h1>
</div>

<div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
    <form method="POST" action="{{ route('class-bookings.update', $classBooking) }}">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="customer_id" class="block text-sm font-medium text-gray-300 mb-1.5">Customer <span class="text-red-400">*</span></label>
                <select name="customer_id" id="customer_id" 
                        class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select a customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->customer_id }}" 
                                {{ old('customer_id', $classBooking->customer_id) == $customer->customer_id ? 'selected' : '' }}>
                            {{ $customer->full_name }} ({{ $customer->user->email }})
                        </option>
                    @endforeach
                </select>
                @error('customer_id')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="schedule_id" class="block text-sm font-medium text-gray-300 mb-1.5">Class Schedule <span class="text-red-400">*</span></label>
                <select name="schedule_id" id="schedule_id" 
                        class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select a class schedule</option>
                    @foreach($schedules as $schedule)
                        <option value="{{ $schedule->schedule_id }}" 
                                {{ old('schedule_id', $classBooking->schedule_id) == $schedule->schedule_id ? 'selected' : '' }}>
                            {{ $schedule->fitnessClass->class_name }} - {{ $schedule->start_time->format('M j, Y g:i A') }} 
                            ({{ $schedule->trainerProfile->full_name }})
                        </option>
                    @endforeach
                </select>
                @error('schedule_id')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-300 mb-1.5">Status</label>
                <select name="status" id="status" 
                        class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="confirmed" {{ old('status', $classBooking->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="cancelled" {{ old('status', $classBooking->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="completed" {{ old('status', $classBooking->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
                @error('status')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Booked At</label>
                <div class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-400">
                    {{ $classBooking->booked_at->format('M j, Y g:i A') }}
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t border-slate-700">
            <a href="{{ route('class-bookings.show', $classBooking) }}" 
               class="px-3 py-1.5 text-sm text-gray-400 hover:text-gray-100">
                Cancel
            </a>
            <button type="submit" 
                    class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                Update Booking
            </button>
        </div>
    </form>
</div>
@endsection
