@extends('layouts.management')

@section('title', 'Role Details')
@section('subtitle', 'View role information and assigned users')

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
                <span class="text-gray-100 font-medium">{{ $role->role_name }}</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Header with Actions -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-100">{{ $role->role_name }}</h1>
        <p class="text-gray-400 mt-1">Role ID: {{ $role->role_id }} • {{ $role->users->count() }} {{ Str::plural('user', $role->users->count()) }} assigned</p>
    </div>
    <div class="flex items-center space-x-3">
        <a href="{{ route('roles.edit', $role) }}" 
           class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit Role
        </a>
        <form method="POST" action="{{ route('roles.destroy', $role) }}" 
              onsubmit="return confirm('Are you sure you want to delete this role?')" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Delete
            </button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Role Details -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Role Information -->
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-100 mb-4">Role Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Role ID</label>
                    <p class="text-gray-100">{{ $role->role_id }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Role Name</label>
                    <p class="text-gray-100 text-lg font-semibold">{{ $role->role_name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Created</label>
                    <p class="text-gray-100">{{ $role->created_at->format('F j, Y') }}</p>
                    <p class="text-gray-400 text-sm">{{ $role->created_at->format('g:i A') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Last Updated</label>
                    <p class="text-gray-100">{{ $role->updated_at->format('F j, Y') }}</p>
                    <p class="text-gray-400 text-sm">{{ $role->updated_at->format('g:i A') }}</p>
                </div>
            </div>
        </div>

        <!-- Assigned Users -->
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-100 mb-4">Assigned Users ({{ $role->users->count() }})</h2>
            @if($role->users->count() > 0)
            <div class="space-y-3">
                @foreach($role->users as $user)
                <div class="border border-slate-600 rounded-lg p-4 hover:bg-slate-700/50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center">
                                <span class="text-sm font-medium text-white">{{ substr($user->email, 0, 1) }}</span>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-100">{{ $user->name }}</div>
                                <div class="text-sm text-gray-400">{{ $user->email }}</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                         {{ $user->is_active ? 'bg-green-900 text-green-200' : 'bg-red-900 text-red-200' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            @if($user->customerProfile)
                            <a href="{{ route('customer-profiles.show', $user->customerProfile) }}" 
                               class="text-gray-400 hover:text-indigo-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            @elseif($user->staffProfile)
                            <a href="{{ route('staff-profiles.show', $user->staffProfile) }}" 
                               class="text-gray-400 hover:text-indigo-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-100 mb-2">No Users Assigned</h3>
                <p class="text-gray-400">This role has no users assigned to it yet.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Statistics & Actions -->
    <div class="space-y-6">
        <!-- Statistics Card -->
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-100 mb-4">Statistics</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Total Users</label>
                    <p class="text-3xl font-bold text-gray-100">{{ $role->users->count() }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Active Users</label>
                    <p class="text-2xl font-bold text-green-400">{{ $role->users->where('is_active', true)->count() }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Inactive Users</label>
                    <p class="text-2xl font-bold text-red-400">{{ $role->users->where('is_active', false)->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Role Status -->
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-100 mb-4">Role Status</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Status</label>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-900 text-green-200">
                        Active
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Created</label>
                    <p class="text-gray-100">{{ $role->created_at->diffForHumans() }}</p>
                </div>
                @if($role->updated_at != $role->created_at)
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Last Modified</label>
                    <p class="text-gray-100">{{ $role->updated_at->diffForHumans() }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-100 mb-4">Quick Actions</h2>
            <div class="space-y-3">
                <a href="{{ route('roles.edit', $role) }}" 
                   class="block w-full text-center px-4 py-2 bg-slate-700 hover:bg-slate-600 text-gray-100 rounded-lg transition-colors">
                    Edit Role
                </a>
                <a href="{{ route('roles.index') }}" 
                   class="block w-full text-center px-4 py-2 bg-slate-700 hover:bg-slate-600 text-gray-100 rounded-lg transition-colors">
                    Back to Roles
                </a>
            </div>
        </div>
    </div>
</div>
@endsection