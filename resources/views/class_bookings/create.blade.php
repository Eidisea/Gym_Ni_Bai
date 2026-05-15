@extends('layouts.management')

@section('title', 'Create Class Booking')
@section('subtitle', 'Book a customer for a fitness class')

@section('content')
<div class="flex items-center justify-between mb-4">
    <nav class="flex" aria-label="Breadcrumb">
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
                    <span class="text-gray-100 text-sm font-medium">Create Booking</span>
                </div>
            </li>
        </ol>
    </nav>
    
    <a href="{{ route('class-bookings.index') }}" 
       class="inline-flex items-center text-sm text-gray-400 hover:text-white transition-colors">
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to List
    </a>
</div>

<div class="mb-4">
    <h1 class="text-xl font-bold text-gray-100">Create Class Booking</h1>
</div>

<div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
    <form method="POST" action="{{ route('class-bookings.store') }}">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="customer_search" class="block text-sm font-medium text-gray-300 mb-1.5">
                    Customer <span class="text-red-400">*</span>
                </label>
                <input type="text" 
                       id="customer_search" 
                       list="customers_list"
                       placeholder="Type customer name..."
                       value="{{ old('customer_id') ? $customers->firstWhere('customer_id', old('customer_id'))?->full_name : '' }}"
                       class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <datalist id="customers_list">
                    @foreach($customers as $customer)
                        <option value="{{ $customer->full_name }}" data-id="{{ $customer->customer_id }}"></option>
                    @endforeach
                </datalist>
                <input type="hidden" name="customer_id" id="customer_id" value="{{ old('customer_id') }}">
                @error('customer_id')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="schedule_search" class="block text-sm font-medium text-gray-300 mb-1.5">
                    Class Schedule <span class="text-red-400">*</span>
                </label>
                <input type="text" 
                       id="schedule_search" 
                       list="schedules_list"
                       placeholder="Type class name or date..."
                       value="{{ old('schedule_id') ? $schedules->firstWhere('schedule_id', old('schedule_id'))?->fitnessClass->class_name . ' - ' . $schedules->firstWhere('schedule_id', old('schedule_id'))?->start_time->format('M j, Y g:i A') : '' }}"
                       class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <datalist id="schedules_list">
                    @foreach($schedules as $schedule)
                        <option value="{{ $schedule->fitnessClass->class_name }} - {{ $schedule->start_time->format('M j, Y g:i A') }}" 
                                data-id="{{ $schedule->schedule_id }}"></option>
                    @endforeach
                </datalist>
                <input type="hidden" name="schedule_id" id="schedule_id" value="{{ old('schedule_id') }}">
                @error('schedule_id')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t border-slate-700">
            <a href="{{ route('class-bookings.index') }}" 
               class="px-3 py-1.5 text-sm text-gray-400 hover:text-gray-100">
                Cancel
            </a>
            <button type="submit" 
                    class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                Create Booking
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('customer_search').addEventListener('input', function(e) {
    const value = e.target.value;
    const options = document.querySelectorAll('#customers_list option');
    let found = false;
    
    options.forEach(option => {
        if (option.value === value) {
            document.getElementById('customer_id').value = option.getAttribute('data-id');
            found = true;
        }
    });
    
    if (!found) {
        document.getElementById('customer_id').value = '';
    }
});

document.getElementById('schedule_search').addEventListener('input', function(e) {
    const value = e.target.value;
    const options = document.querySelectorAll('#schedules_list option');
    let found = false;
    
    options.forEach(option => {
        if (option.value === value) {
            document.getElementById('schedule_id').value = option.getAttribute('data-id');
            found = true;
        }
    });
    
    if (!found) {
        document.getElementById('schedule_id').value = '';
    }
});
</script>
@endsection
