@extends('layouts.management')

@section('title', $classSchedule->fitnessClass?->class_name ?? 'Class Schedule')
@section('subtitle', 'Class schedule details and bookings')

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
                <a href="{{ route('class-schedules.index') }}" class="text-gray-400 hover:text-gray-100 text-sm">Class Schedules</a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-100 text-sm font-medium">{{ $classSchedule->fitnessClass?->class_name ?? 'Archived Class' }}</span>
            </div>
        </li>
    </ol>
</nav>

<div class="flex justify-between items-start mb-4">
    <div>
        <div class="flex items-center space-x-2">
            <h1 class="text-xl font-bold text-gray-100">{{ $classSchedule->fitnessClass?->class_name ?? 'Archived Class' }}</h1>
            @if($classSchedule->trashed())
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">Archived</span>
            @endif
        </div>
        <div class="flex items-center mt-1 space-x-3">
            <span class="text-sm text-gray-400">{{ $classSchedule->start_time->format('M j, Y g:i A') }} - {{ $classSchedule->end_time->format('g:i A') }}</span>
            @if(!$classSchedule->trashed())
                @if($classSchedule->start_time->isFuture())
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Upcoming</span>
                @elseif($classSchedule->end_time->isPast())
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Completed</span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">In Progress</span>
                @endif
            @endif
        </div>
    </div>
    
    <div class="flex items-center space-x-2">
        @if($classSchedule->trashed())
            @can('update', $classSchedule)
            <form method="POST" action="{{ route('class-schedules.restore', $classSchedule->schedule_id) }}">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Restore Schedule
                </button>
            </form>
            @endcan
        @else
            @can('update', $classSchedule)
            @if($classSchedule->start_time->isFuture() && !str_starts_with($classSchedule->location ?? '', 'CANCELLED:'))
            <a href="{{ route('class-schedules.edit', $classSchedule) }}" 
               class="inline-flex items-center px-3 py-1.5 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </a>
            @endif
            @endcan
            
            @can('delete', $classSchedule)
            <button onclick='openArchiveModal({{ $classSchedule->schedule_id }}, {!! json_encode($classSchedule->fitnessClass?->class_name ?? 'Class Schedule') !!})' 
                    class="inline-flex items-center px-3 py-1.5 bg-slate-600 hover:bg-slate-700 text-white text-sm font-medium rounded-lg">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                </svg>
                Archive
            </button>
            @endcan
        @endif
        
        <a href="{{ route('class-schedules.index') }}" class="text-gray-400 hover:text-white text-sm">
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to List
        </a>
    </div>
</div>

<div id="archiveModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-slate-800 border border-slate-700 rounded-lg max-w-md w-full p-4">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-base font-semibold text-gray-100">Archive Class Schedule</h3>
            <button type="button" onclick="closeArchiveModal()" 
                    class="text-gray-400 hover:text-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <p class="text-sm text-gray-400 mb-3">Archive <span id="scheduleName" class="font-semibold text-gray-100"></span>?</p>
        
        <form id="archiveForm" method="POST" action="">
            @csrf
            
            <div class="mb-3">
                <label for="archive_reason" class="block text-sm font-medium text-gray-300 mb-1">Reason</label>
                <select id="archive_reason" name="archive_reason" required
                        class="w-full px-3 py-2 text-sm bg-slate-700 border border-slate-600 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-slate-500">
                    <option value="">Select reason</option>
                    <option value="Cancelled">Cancelled</option>
                    <option value="Trainer Unavailable">Trainer Unavailable</option>
                    <option value="Low Enrollment">Low Enrollment</option>
                    <option value="Facility Issue">Facility Issue</option>
                    <option value="Other">Other</option>
                </select>
                
                <div class="bg-yellow-900/20 border border-yellow-700 rounded-lg p-2.5 mt-3">
                    <div class="flex">
                        <svg class="w-4 h-4 text-yellow-400 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div class="text-xs text-yellow-200">
                            <p class="font-medium">Note: Active bookings must be cancelled before archiving this schedule.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeArchiveModal()"
                        class="px-3 py-1.5 text-sm text-gray-400 hover:text-gray-100">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-3 py-1.5 bg-slate-600 hover:bg-slate-700 text-white text-sm font-medium rounded-lg">
                    Archive Schedule
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openArchiveModal(scheduleId, scheduleName) {
    document.getElementById('scheduleName').textContent = scheduleName;
    document.getElementById('archiveForm').action = `/class-schedules/${scheduleId}/archive`;
    document.getElementById('archiveModal').classList.remove('hidden');
}

function closeArchiveModal() {
    document.getElementById('archiveModal').classList.add('hidden');
    document.getElementById('archive_reason').value = '';
}
</script>

@if(str_starts_with($classSchedule->location ?? '', 'CANCELLED:'))
<div class="bg-red-900/20 border border-red-700 rounded-lg p-4 mb-4">
    <div class="flex">
        <svg class="w-5 h-5 text-red-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        <div>
            <h3 class="text-base font-semibold text-red-300 mb-1">Class Cancelled</h3>
            <p class="text-sm text-red-200">{{ str_replace('CANCELLED: ', '', $classSchedule->location) }}</p>
        </div>
    </div>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
    <div class="lg:col-span-2">
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
            <h3 class="text-base font-semibold text-gray-100 mb-3">Schedule Details</h3>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <label class="text-xs font-medium text-gray-400">Fitness Class</label>
                    <p class="text-gray-100 font-medium">{{ $classSchedule->fitnessClass->class_name }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-400">Trainer</label>
                    <p class="text-gray-100 font-medium">{{ $classSchedule->trainerProfile->full_name }}</p>
                    <p class="text-xs text-gray-400">{{ $classSchedule->trainerProfile->specialization }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-400">Start Time</label>
                    <p class="text-gray-100 font-medium">{{ $classSchedule->start_time->format('M j, Y g:i A') }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-400">End Time</label>
                    <p class="text-gray-100 font-medium">{{ $classSchedule->end_time->format('M j, Y g:i A') }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-400">Duration</label>
                    <p class="text-gray-100 font-medium">{{ $classSchedule->start_time->diffInMinutes($classSchedule->end_time) }} minutes</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-400">Location</label>
                    <p class="text-gray-100 font-medium">
                        @if(str_starts_with($classSchedule->location, 'CANCELLED:'))
                            <span class="text-red-400">Cancelled</span>
                        @else
                            {{ $classSchedule->location }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <div>
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
            <h3 class="text-base font-semibold text-gray-100 mb-3">Capacity</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-400">Available Slots</span>
                    <span class="text-gray-100 font-medium">{{ $classSchedule->available_slots }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Booked Slots</span>
                    <span class="text-gray-100 font-medium">{{ $classSchedule->booked_slots }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Remaining Slots</span>
                    <span class="text-gray-100 font-medium">{{ $classSchedule->remaining_slots }}</span>
                </div>
                <div class="pt-2 border-t border-slate-600">
                    <div class="flex justify-between mb-1">
                        <span class="text-gray-400">Capacity</span>
                        <span class="text-gray-100 font-medium">{{ number_format(($classSchedule->booked_slots / $classSchedule->available_slots) * 100, 1) }}%</span>
                    </div>
                    <div class="w-full bg-slate-700 rounded-full h-1.5">
                        <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ ($classSchedule->booked_slots / $classSchedule->available_slots) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-base font-semibold text-gray-100">Class Bookings</h3>
        <a href="{{ route('class-bookings.create') }}?schedule_id={{ $classSchedule->schedule_id }}" 
           class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Booking
        </a>
    </div>

    @if($classSchedule->bookings->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr class="border-b border-slate-700">
                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-400 uppercase">Customer</th>
                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-400 uppercase">Booked At</th>
                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-400 uppercase">Status</th>
                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                @foreach($classSchedule->bookings->sortByDesc('booked_at') as $booking)
                <tr class="hover:bg-slate-700/50">
                    <td class="py-2 px-3">
                        <div class="text-sm font-medium text-gray-100">{{ $booking->customerProfile->full_name }}</div>
                        <div class="text-xs text-gray-400">{{ $booking->customerProfile->user->email }}</div>
                    </td>
                    <td class="py-2 px-3 text-sm text-gray-300">
                        {{ $booking->booked_at->format('M j, Y g:i A') }}
                    </td>
                    <td class="py-2 px-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                     {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 
                                        ($booking->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                    <td class="py-2 px-3">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('class-bookings.show', $booking) }}" class="text-gray-400 hover:text-indigo-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            @can('update', $booking)
                            <a href="{{ route('class-bookings.edit', $booking) }}" class="text-gray-400 hover:text-yellow-400">
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
    <div class="text-center py-6">
        <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        <h4 class="text-sm font-medium text-gray-100 mb-1">No Bookings Yet</h4>
        <p class="text-xs text-gray-400 mb-3">No customers have booked this class</p>
        <a href="{{ route('class-bookings.create') }}?schedule_id={{ $classSchedule->schedule_id }}" 
           class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add First Booking
        </a>
    </div>
    @endif
</div>
@endsection
