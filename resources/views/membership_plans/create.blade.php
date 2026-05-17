@extends('layouts.management')

@section('title', 'Create Membership Plan')
@section('subtitle', 'Add a new membership plan option')

@section('content')
<nav class="flex mb-4" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1">
        <li class="inline-flex items-center">
            <a href="{{ route('management.dashboard') }}" class="text-gray-400 hover:text-gray-100 text-sm transition-colors">
                Dashboard
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-3 h-3 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <a href="{{ route('management.membership-plans.index') }}" class="text-gray-400 hover:text-gray-100 text-sm transition-colors">
                    Membership Plans
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-3 h-3 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-100 text-sm font-medium">Create</span>
            </div>
        </li>
    </ol>
</nav>

<div class="mb-4">
    <h1 class="text-lg font-bold text-gray-100">Create Membership Plan</h1>
    <p class="text-xs text-gray-400 mt-0.5">Add a new membership plan</p>
</div>

<div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
    <form method="POST" action="{{ route('management.membership-plans.store') }}">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label for="plan_name" class="block text-sm font-medium text-gray-300 mb-1">Plan Name</label>
                <input type="text" 
                       name="plan_name" 
                       id="plan_name" 
                       value="{{ old('plan_name') }}"
                       class="w-full px-3 py-2 text-sm bg-slate-700 border border-slate-600 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       required>
                @error('plan_name')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="base_price" class="block text-sm font-medium text-gray-300 mb-1">Price (₱)</label>
                <input type="number" 
                       name="base_price" 
                       id="base_price" 
                       value="{{ old('base_price') }}"
                       min="0" 
                       step="0.01"
                       class="w-full px-3 py-2 text-sm bg-slate-700 border border-slate-600 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       required>
                @error('base_price')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="duration_days" class="block text-sm font-medium text-gray-300 mb-1">Duration (days)</label>
                <select name="duration_days" 
                        id="duration_days"
                        class="w-full px-3 py-2 text-sm bg-slate-700 border border-slate-600 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        required>
                    <option value="">Select duration</option>
                    <option value="1" {{ old('duration_days') == '1' ? 'selected' : '' }}>1 Day</option>
                    <option value="7" {{ old('duration_days') == '7' ? 'selected' : '' }}>7 Days</option>
                    <option value="30" {{ old('duration_days') == '30' ? 'selected' : '' }}>30 Days</option>
                    <option value="90" {{ old('duration_days') == '90' ? 'selected' : '' }}>90 Days</option>
                    <option value="180" {{ old('duration_days') == '180' ? 'selected' : '' }}>180 Days</option>
                    <option value="365" {{ old('duration_days') == '365' ? 'selected' : '' }}>365 Days</option>
                </select>
                @error('duration_days')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t border-slate-600">
            <a href="{{ route('management.membership-plans.index') }}" 
               class="px-3 py-1.5 text-sm text-gray-400 hover:text-gray-100 transition-colors">
                Cancel
            </a>
            <button type="submit" 
                    class="px-4 py-1.5 text-sm bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                Create Plan
            </button>
        </div>
    </form>
</div>
@endsection
