@extends('layouts.management')

@section('title', 'Class Schedules')
@section('subtitle', 'Manage fitness class schedules and sessions')

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
                <span class="text-gray-100 text-sm font-medium">Class Schedules</span>
            </div>
        </li>
    </ol>
</nav>

<div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-bold text-gray-100">Class Schedules</h1>
    <div class="flex items-center space-x-2">
        <a href="{{ route('class-schedules.index', ['show_archived' => $showArchived ? 0 : 1]) }}" 
           class="inline-flex items-center px-3 py-1.5 bg-slate-700 hover:bg-slate-600 text-white text-sm rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
            </svg>
            {{ $showArchived ? 'Show Active' : 'Show Archived' }}
        </a>
        @if(!$showArchived)
        <a href="{{ route('class-schedules.create') }}" 
           class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Schedule Class
        </a>
        @endif
    </div>
</div>

<div class="bg-slate-800 border border-slate-700 rounded-lg overflow-hidden">
    @if($schedules->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-slate-700">
                <tr>
                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-400 uppercase">Class</th>
                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-400 uppercase">Trainer</th>
                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-400 uppercase">Date & Time</th>
                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-400 uppercase">Capacity</th>
                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-400 uppercase">Status</th>
                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                @foreach($schedules as $schedule)
                <tr class="hover:bg-slate-700/50">
                    <td class="py-2 px-3">
                        <div class="text-sm font-medium text-gray-100">{{ $schedule->fitnessClass?->class_name ?? 'Archived Class' }}</div>
                        <div class="text-xs text-gray-400">{{ $schedule->fitnessClass?->max_participants ?? 'N/A' }} max</div>
                    </td>
                    <td class="py-2 px-3">
                        <div class="text-sm text-gray-100">{{ $schedule->trainerProfile?->full_name ?? 'Archived Trainer' }}</div>
                        <div class="text-xs text-gray-400">{{ $schedule->trainerProfile?->specialization ?? 'N/A' }}</div>
                    </td>
                    <td class="py-2 px-3">
                        <div class="text-sm text-gray-100">{{ $schedule->start_time->format('M j, Y') }}</div>
                        <div class="text-xs text-gray-400">{{ $schedule->start_time->format('g:i A') }} - {{ $schedule->end_time->format('g:i A') }}</div>
                    </td>
                    <td class="py-2 px-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                     {{ $schedule->remaining_slots > 5 ? 'bg-green-100 text-green-800' : 
                                        ($schedule->remaining_slots > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ $schedule->booked_slots }}/{{ $schedule->available_slots }}
                        </span>
                    </td>
                    <td class="py-2 px-3">
                        @if($showArchived)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-700 text-gray-300">Archived</span>
                        @else
                            @if($schedule->start_time->isFuture())
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Upcoming</span>
                            @elseif($schedule->end_time->isPast())
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Completed</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">In Progress</span>
                            @endif
                        @endif
                    </td>
                    <td class="py-2 px-3">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('class-schedules.show', $schedule->schedule_id) }}" class="text-gray-400 hover:text-indigo-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            @if($showArchived)
                                <form method="POST" action="{{ route('class-schedules.restore', $schedule->schedule_id) }}" 
                                      onsubmit="return confirm('Restore this schedule?')" class="inline">
                                    @csrf
                                    <button type="submit" class="text-gray-400 hover:text-green-400" title="Restore">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </button>
                                </form>
                            @else
                                @can('update', $schedule)
                                <a href="{{ route('class-schedules.edit', $schedule) }}" class="text-gray-400 hover:text-yellow-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                @endcan
                                @can('delete', $schedule)
                                <form method="POST" action="{{ route('class-schedules.destroy', $schedule) }}" 
                                      onsubmit="return confirm('Are you sure you want to archive this schedule? Historical data will be preserved.')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-slate-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                                @endcan
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($schedules->hasPages())
    <div class="px-4 py-3 border-t border-slate-700">
        {{ $schedules->links() }}
    </div>
    @endif
    @else
    <div class="text-center py-8">
        <svg class="w-10 h-10 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        <h3 class="text-sm font-medium text-gray-100 mb-1">No Class Schedules</h3>
        <p class="text-xs text-gray-400 mb-3">Schedule your first fitness class</p>
        <a href="{{ route('class-schedules.create') }}" 
           class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Schedule First Class
        </a>
    </div>
    @endif
</div>
@endsection