@extends('layouts.management')

@section('title', 'Roles')
@section('subtitle', 'Manage system roles and permissions')

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
                <span class="text-gray-100 font-medium">Roles</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Header with Create Button -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-100">Roles</h1>
        <p class="text-gray-400 mt-1">Manage system roles and user access levels</p>
    </div>
    <a href="{{ route('roles.create') }}" 
       class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Create Role
    </a>
</div>

<!-- Roles Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    @foreach($roles as $role)
    <div class="bg-slate-800 border border-slate-700 rounded-lg p-6 hover:border-indigo-500 transition-colors">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <span class="text-2xl font-bold text-gray-100">{{ $role->users_count }}</span>
        </div>
        
        <h3 class="text-lg font-semibold text-gray-100 mb-2">{{ $role->role_name }}</h3>
        <p class="text-sm text-gray-400 mb-4">{{ $role->users_count }} {{ Str::plural('user', $role->users_count) }} assigned</p>
        
        <div class="flex items-center space-x-2">
            <a href="{{ route('roles.show', $role) }}" 
               class="flex-1 text-center px-3 py-2 bg-slate-700 hover:bg-slate-600 text-gray-100 text-sm rounded-lg transition-colors">
                View
            </a>
            <a href="{{ route('roles.edit', $role) }}" 
               class="text-gray-400 hover:text-yellow-400 transition-colors p-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </a>
            <form method="POST" action="{{ route('roles.destroy', $role) }}" 
                  onsubmit="return confirm('Are you sure you want to delete this role?')" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-gray-400 hover:text-red-400 transition-colors p-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>

<!-- Roles Table (Alternative View) -->
<div class="mt-8 bg-slate-800 border border-slate-700 rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-700">
        <h2 class="text-lg font-semibold text-gray-100">All Roles</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-slate-700">
                <tr>
                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Role ID</th>
                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Role Name</th>
                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Users</th>
                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Created</th>
                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                @foreach($roles as $role)
                <tr class="hover:bg-slate-700/50 transition-colors">
                    <td class="py-4 px-4 text-sm text-gray-300">
                        {{ $role->role_id }}
                    </td>
                    <td class="py-4 px-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                                <span class="text-xs font-medium text-white">{{ substr($role->role_name, 0, 1) }}</span>
                            </div>
                            <span class="ml-3 text-sm font-medium text-gray-100">{{ $role->role_name }}</span>
                        </div>
                    </td>
                    <td class="py-4 px-4 text-sm text-gray-300">
                        {{ $role->users_count }} {{ Str::plural('user', $role->users_count) }}
                    </td>
                    <td class="py-4 px-4 text-sm text-gray-300">
                        {{ $role->created_at->format('M j, Y') }}
                    </td>
                    <td class="py-4 px-4 text-sm text-gray-300">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('roles.show', $role) }}" 
                               class="text-gray-400 hover:text-indigo-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <a href="{{ route('roles.edit', $role) }}" 
                               class="text-gray-400 hover:text-yellow-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('roles.destroy', $role) }}" 
                                  onsubmit="return confirm('Are you sure you want to delete this role?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-400 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection