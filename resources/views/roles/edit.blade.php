@extends('layouts.management')

@section('title', 'Edit Role')
@section('subtitle', 'Update role information')

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
                <a href="{{ route('roles.index') }}" class="text-gray-400 hover:text-gray-100 transition-colors">
                    Roles
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <a href="{{ route('roles.show', $role) }}" class="text-gray-400 hover:text-gray-100 transition-colors">
                    {{ $role->role_name }}
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-100 font-medium">Edit</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-100">Edit Role</h1>
    <p class="text-gray-400 mt-1">Update {{ $role->role_name }} role information</p>
</div>

<!-- Form -->
<div class="max-w-2xl">
    <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
        <form method="POST" action="{{ route('roles.update', $role) }}">
            @csrf
            @method('PUT')
            
            <div class="mb-6">
                <label for="role_name" class="block text-sm font-medium text-gray-300 mb-2">Role Name *</label>
                <input type="text" name="role_name" id="role_name" required maxlength="50"
                       value="{{ old('role_name', $role->role_name) }}"
                       class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                @error('role_name')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-400 text-sm mt-2">Role name must be unique across the system.</p>
            </div>

            <!-- Role Info -->
            <div class="bg-slate-700 border border-slate-600 rounded-lg p-4 mb-6">
                <h4 class="text-sm font-medium text-gray-300 mb-2">Role Information</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-400">Role ID:</span>
                        <span class="text-gray-100 ml-2">{{ $role->role_id }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Users Assigned:</span>
                        <span class="text-gray-100 ml-2">{{ $role->users()->count() }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Created:</span>
                        <span class="text-gray-100 ml-2">{{ $role->created_at->format('M j, Y') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Last Updated:</span>
                        <span class="text-gray-100 ml-2">{{ $role->updated_at->format('M j, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Warning Box -->
            @if($role->users()->count() > 0)
            <div class="bg-yellow-900/20 border border-yellow-700 rounded-lg p-4 mb-6">
                <div class="flex">
                    <svg class="w-5 h-5 text-yellow-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-yellow-300 mb-1">Warning</h4>
                        <p class="text-sm text-yellow-200">This role is currently assigned to {{ $role->users()->count() }} {{ Str::plural('user', $role->users()->count()) }}. Changing the role name will affect all assigned users.</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-slate-700">
                <a href="{{ route('roles.show', $role) }}" 
                   class="px-4 py-2 text-gray-400 hover:text-gray-100 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                    Update Role
                </button>
            </div>
        </form>
    </div>
</div>
@endsection