@extends('layouts.management')

@section('title', 'Add New Trainer')
@section('subtitle', 'Register a new fitness trainer')

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
                <a href="{{ route('trainer-profiles.index') }}" class="text-gray-400 hover:text-gray-100 transition-colors">
                    Trainer Profiles
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-100 font-medium">Add New Trainer</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-100">Add New Trainer</h1>
    <p class="text-gray-400 mt-1">Register a new fitness trainer to your team</p>
</div>

<!-- Form Card -->
<div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
    <form method="POST" action="{{ route('trainer-profiles.store') }}">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- First Name -->
            <div>
                <label for="first_name" class="block text-sm font-medium text-gray-300 mb-2">
                    First Name <span class="text-red-400">*</span>
                </label>
                <input type="text" 
                       name="first_name" 
                       id="first_name" 
                       value="{{ old('first_name') }}"
                       class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="John"
                       required>
                @error('first_name')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Last Name -->
            <div>
                <label for="last_name" class="block text-sm font-medium text-gray-300 mb-2">
                    Last Name <span class="text-red-400">*</span>
                </label>
                <input type="text" 
                       name="last_name" 
                       id="last_name" 
                       value="{{ old('last_name') }}"
                       class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="Doe"
                       required>
                @error('last_name')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Specialization -->
            <div class="md:col-span-2">
                <label for="specialization" class="block text-sm font-medium text-gray-300 mb-2">
                    Specialization <span class="text-red-400">*</span>
                </label>
                <input type="text" 
                       name="specialization" 
                       id="specialization" 
                       value="{{ old('specialization') }}"
                       class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="e.g., Strength Training, Yoga, Cardio, CrossFit"
                       required>
                @error('specialization')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-400">Enter the trainer's primary area of expertise</p>
            </div>
        </div>

        <!-- Info Box -->
        <div class="mt-6 p-4 bg-blue-900/20 border border-blue-700 rounded-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h4 class="text-sm font-medium text-blue-100 mb-1">Trainer Account Information</h4>
                    <p class="text-sm text-blue-200">Trainers are standalone profiles and do not have login accounts. They will be assigned to class schedules by staff members.</p>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end space-x-4 mt-8 pt-6 border-t border-slate-600">
            <a href="{{ route('trainer-profiles.index') }}" 
               class="px-4 py-2 text-gray-400 hover:text-gray-100 transition-colors">
                Cancel
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                Add Trainer
            </button>
        </div>
    </form>
</div>
@endsection