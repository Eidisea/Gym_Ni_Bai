@extends('layouts.management')

@section('title', 'Edit Customer Profile')
@section('subtitle', 'Update customer information')

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
                <a href="{{ route('management.customer-profiles.index') }}" class="text-gray-400 hover:text-gray-100 text-sm transition-colors">
                    Customer Profiles
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-3 h-3 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <a href="{{ route('customer-profiles.show', $customerProfile) }}" class="text-gray-400 hover:text-gray-100 text-sm transition-colors">
                    {{ $customerProfile->full_name }}
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-3 h-3 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-100 text-sm font-medium">Edit</span>
            </div>
        </li>
    </ol>
</nav>

<div class="mb-4">
    <h1 class="text-lg font-bold text-gray-100">Edit Customer Profile</h1>
    <p class="text-xs text-gray-400 mt-0.5">Update information for {{ $customerProfile->full_name }}</p>
</div>

<div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
    <form method="POST" action="{{ route('customer-profiles.update', $customerProfile) }}">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="first_name" class="block text-sm font-medium text-gray-300 mb-1">First Name</label>
                <input type="text" 
                       name="first_name" 
                       id="first_name" 
                       value="{{ old('first_name', $customerProfile->first_name) }}"
                       class="w-full px-3 py-2 text-sm bg-slate-700 border border-slate-600 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       required>
                @error('first_name')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="last_name" class="block text-sm font-medium text-gray-300 mb-1">Last Name</label>
                <input type="text" 
                       name="last_name" 
                       id="last_name" 
                       value="{{ old('last_name', $customerProfile->last_name) }}"
                       class="w-full px-3 py-2 text-sm bg-slate-700 border border-slate-600 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       required>
                @error('last_name')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone_number" class="block text-sm font-medium text-gray-300 mb-1">Phone Number</label>
                <input type="tel" 
                       name="phone_number" 
                       id="phone_number" 
                       value="{{ old('phone_number', $customerProfile->phone_number) }}"
                       class="w-full px-3 py-2 text-sm bg-slate-700 border border-slate-600 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       required>
                @error('phone_number')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="date_of_birth" class="block text-sm font-medium text-gray-300 mb-1">Date of Birth</label>
                <input type="date" 
                       name="date_of_birth" 
                       id="date_of_birth" 
                       value="{{ old('date_of_birth', $customerProfile->date_of_birth?->format('Y-m-d')) }}"
                       max="{{ now()->subYears(13)->format('Y-m-d') }}"
                       class="w-full px-3 py-2 text-sm bg-slate-700 border border-slate-600 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       required>
                @error('date_of_birth')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email Address</label>
                <input type="email" 
                       id="email" 
                       value="{{ $customerProfile->user->email }}"
                       class="w-full px-3 py-2 text-sm bg-slate-600 border border-slate-500 rounded-lg text-gray-300 cursor-not-allowed"
                       readonly>
                <p class="mt-1 text-xs text-gray-400">Email cannot be changed</p>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t border-slate-600">
            <a href="{{ route('customer-profiles.show', $customerProfile) }}" 
               class="px-3 py-1.5 text-sm text-gray-400 hover:text-gray-100 transition-colors">
                Cancel
            </a>
            <button type="submit" 
                    class="px-4 py-1.5 text-sm bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                Update Profile
            </button>
        </div>
    </form>
</div>
@endsection
