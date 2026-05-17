@extends('layouts.management')

@section('title', 'Create Role')
@section('subtitle', 'Add a new system role')

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
                <a href="{{ route('management.roles.index') }}" class="text-gray-400 hover:text-gray-100 transition-colors">
                    Roles
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-100 font-medium">Create Role</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-100">Create Role</h1>
    <p class="text-gray-400 mt-1">Add a new role to the system</p>
</div>

<!-- Form -->
<div class="max-w-2xl">
    <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
        <form method="POST" action="{{ route('management.roles.store') }}">
            @csrf
            
            <div class="mb-6">
                <label for="role_name" class="block text-sm font-medium text-gray-300 mb-2">Role Name *</label>
                <input type="text" name="role_name" id="role_name" required maxlength="50"
                       value="{{ old('role_name') }}"
                       placeholder="e.g., Manager, Receptionist, Trainer"
                       class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                @error('role_name')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-400 text-sm mt-2">Enter a unique role name. This will be used throughout the system.</p>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-900/20 border border-blue-700 rounded-lg p-4 mb-6">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-blue-300 mb-1">Role Information</h4>
                        <p class="text-sm text-blue-200">Roles define user access levels in the system. The default roles are:</p>
                        <ul class="text-sm text-blue-200 mt-2 space-y-1 list-disc list-inside">
                            <li><strong>Admin</strong> - Full system access</li>
                            <li><strong>Staff</strong> - Can create and view records</li>
                            <li><strong>Customer</strong> - Customer portal access</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-slate-700">
                <a href="{{ route('management.roles.index') }}" 
                   class="px-4 py-2 text-gray-400 hover:text-gray-100 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                    Create Role
                </button>
            </div>
        </form>
    </div>
</div>
@endsection