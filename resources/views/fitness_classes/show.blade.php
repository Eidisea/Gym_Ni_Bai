@extends('layouts.management')

@section('title', $fitnessClass->class_name)
@section('subtitle', 'Fitness class details and schedules')

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
                <a href="{{ route('management.fitness-classes.index') }}" class="text-gray-400 hover:text-gray-100 transition-colors">
                    Fitness Classes
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-100 font-medium">{{ $fitnessClass->class_name }}</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Header with Actions -->
<div class="flex justify-between items-start mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-100">{{ $fitnessClass->class_name }}</h1>
        <div class="flex items-center mt-2 space-x-4">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-900 text-blue-200">
                Active
            </span>
            <span class="text-sm text-gray-400">Max {{ $fitnessClass->max_participants }} participants</span>
        </div>
    </div>
    
    <div class="flex items-center space-x-3">
        @can('update', $fitnessClass)
        <a href="{{ route('management.fitness-classes.edit', $fitnessClass) }}" 
           class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit Class
        </a>
        @endcan
        
        @can('delete', $fitnessClass)
        <form method="POST" action="{{ route('management.fitness-classes.destroy', $fitnessClass) }}" 
              onsubmit="return confirm('Are you sure you want to delete this fitness class? This action cannot be undone.')" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Delete Class
            </button>
        </form>
        @endcan
    </div>
</div>

<!-- Class Details -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Main Details -->
    <div class="lg:col-span-2">
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-100 mb-4">Class Description</h3>
            @if($fitnessClass->description)
                <p class="text-gray-300 leading-relaxed">{{ $fitnessClass->description }}</p>
            @else
                <p class="text-gray-400 italic">No description provided for this class.</p>
            @endif
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="space-y-4">
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-100 mb-4">Quick Stats</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Max Capacity</span>
                    <span class="text-gray-100 font-medium">{{ $fitnessClass->max_participants }} people</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Total Schedules</span>
                    <span class="text-gray-100 font-medium">{{ $fitnessClass->schedules->count() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Status</span>
                    <span class="text-gray-100 font-medium">Active</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Created</span>
                    <span class="text-gray-100 font-medium">{{ $fitnessClass->created_at->format('M j, Y') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scheduled Sessions -->
<div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-100">Scheduled Sessions</h3>
        <a href="{{ route('management.class-schedules.create') }}?class_id={{ $fitnessClass->class_id }}" 
           class="inline-flex items-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Schedule Session
        </a>
    </div>

    @if($fitnessClass->schedules->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr class="border-b border-slate-700">
                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Date & Time</th>
                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Trainer</th>
                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Capacity</th>
                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                @foreach($fitnessClass->schedules->sortBy('start_time') as $schedule)
                <tr class="hover:bg-slate-700/50 transition-colors">
                    <td class="py-3 px-4 text-sm text-gray-300">
                        <div>
                            <div class="font-medium">{{ $schedule->start_time->format('M j, Y') }}</div>
                            <div class="text-gray-400">{{ $schedule->start_time->format('g:i A') }} - {{ $schedule->end_time->format('g:i A') }}</div>
                        </div>
                    </td>
                    <td class="py-3 px-4 text-sm text-gray-300">
                        {{ $schedule->trainerProfile->full_name }}
                    </td>
                    <td class="py-3 px-4 text-sm text-gray-300">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                     {{ $schedule->remaining_slots > 5 ? 'bg-green-900 text-green-200' : 
                                        ($schedule->remaining_slots > 0 ? 'bg-yellow-900 text-yellow-200' : 'bg-red-900 text-red-200') }}">
                            {{ $schedule->booked_slots }}/{{ $schedule->available_slots }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-sm text-gray-300">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('management.class-schedules.show', $schedule) }}" 
                               class="text-gray-400 hover:text-indigo-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            @can('update', $schedule)
                            <a href="{{ route('management.class-schedules.edit', $schedule) }}" 
                               class="text-gray-400 hover:text-yellow-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="text-center py-8">
        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        <h4 class="text-lg font-medium text-gray-100 mb-2">No Scheduled Sessions</h4>
        <p class="text-gray-400 mb-4">This class hasn't been scheduled yet.</p>
        <a href="{{ route('management.class-schedules.create') }}?class_id={{ $fitnessClass->class_id }}" 
           class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Schedule First Session
        </a>
    </div>
    @endif
</div>
@endsection