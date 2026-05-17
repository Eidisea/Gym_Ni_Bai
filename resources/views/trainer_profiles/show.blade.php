@extends('layouts.management')

@section('title', $trainerProfile->full_name)
@section('subtitle', 'Trainer profile and class schedules')

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
                <a href="{{ route('management.trainer-profiles.index') }}" class="text-gray-400 hover:text-gray-100 transition-colors">
                    Trainer Profiles
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-100 font-medium">{{ $trainerProfile->full_name }}</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Header with Actions -->
<div class="flex justify-between items-start mb-6">
    <div class="flex items-center">
        <div class="w-16 h-16 bg-indigo-600 rounded-full flex items-center justify-center">
            <span class="text-xl font-medium text-white">{{ substr($trainerProfile->first_name, 0, 1) }}{{ substr($trainerProfile->last_name, 0, 1) }}</span>
        </div>
        <div class="ml-4">
            <h1 class="text-2xl font-bold text-gray-100">{{ $trainerProfile->full_name }}</h1>
            <div class="flex items-center mt-1 space-x-4">
                <span class="text-sm text-gray-400">{{ $trainerProfile->specialization }}</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-900 text-green-200">
                    Available
                </span>
            </div>
        </div>
    </div>
    
    <div class="flex items-center space-x-3">
        @can('update', $trainerProfile)
        <a href="{{ route('management.trainer-profiles.edit', $trainerProfile) }}" 
           class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit Profile
        </a>
        @endcan
        
        @can('admin-only')
        <button onclick="openArchiveModal({{ $trainerProfile->trainer_id }}, '{{ $trainerProfile->full_name }}')" 
                class="inline-flex items-center px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
            </svg>
            Archive
        </button>
        @endcan
    </div>
</div>

<div id="archiveModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-slate-800 border border-slate-700 rounded-lg p-4 max-w-md w-full mx-4">
        <h3 class="text-base font-semibold text-gray-100 mb-3">Archive Trainer</h3>
        <p class="text-sm text-gray-400 mb-3">Archive <span id="trainerName" class="font-semibold text-gray-100"></span>?</p>
        
        <form id="archiveForm" method="POST" action="">
            @csrf
            <div class="mb-3">
                <label for="archive_reason" class="block text-sm font-medium text-gray-300 mb-1">Reason</label>
                <select id="archive_reason" name="archive_reason" required
                        class="w-full px-3 py-2 text-sm bg-slate-700 border border-slate-600 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select reason</option>
                    <option value="Contract Ended">Contract Ended</option>
                    <option value="Resigned">Resigned</option>
                    <option value="Inactive">Inactive</option>
                    <option value="Relocated">Relocated</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeArchiveModal()" 
                        class="px-3 py-1.5 text-sm bg-slate-700 hover:bg-slate-600 text-gray-100 rounded-lg transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-3 py-1.5 text-sm bg-slate-600 hover:bg-slate-700 text-white rounded-lg transition-colors">
                    Archive
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openArchiveModal(trainerId, trainerName) {
    document.getElementById('trainerName').textContent = trainerName;
    document.getElementById('archiveForm').action = '/management/trainer-profiles/' + trainerId + '/archive';
    document.getElementById('archiveModal').classList.remove('hidden');
}

function closeArchiveModal() {
    document.getElementById('archiveModal').classList.add('hidden');
    document.getElementById('archive_reason').value = '';
}
</script>

<!-- Profile Details -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Personal Information -->
    <div class="lg:col-span-2">
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-100 mb-4">Trainer Information</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-400">First Name</label>
                    <p class="text-gray-100 font-medium">{{ $trainerProfile->first_name }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-400">Last Name</label>
                    <p class="text-gray-100 font-medium">{{ $trainerProfile->last_name }}</p>
                </div>
                <div class="col-span-2">
                    <label class="text-sm font-medium text-gray-400">Specialization</label>
                    <p class="text-gray-100 font-medium">{{ $trainerProfile->specialization }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-400">Joined</label>
                    <p class="text-gray-100 font-medium">{{ $trainerProfile->created_at->format('M j, Y') }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-400">Status</label>
                    <p class="text-gray-100 font-medium">Available</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="space-y-4">
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-100 mb-4">Quick Stats</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Total Classes</span>
                    <span class="text-gray-100 font-medium">{{ $trainerProfile->schedules->count() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Upcoming Classes</span>
                    <span class="text-gray-100 font-medium">{{ $trainerProfile->schedules->where('start_time', '>', now())->count() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Experience</span>
                    <span class="text-gray-100 font-medium">{{ $trainerProfile->created_at->diffForHumans(null, true) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Class Schedules -->
<div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-100">Class Schedules</h3>
        <a href="{{ route('management.class-schedules.create') }}?trainer_id={{ $trainerProfile->trainer_id }}" 
           class="inline-flex items-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Schedule Class
        </a>
    </div>

    @if($trainerProfile->schedules->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr class="border-b border-slate-700">
                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Class</th>
                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Date & Time</th>
                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Capacity</th>
                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                @foreach($trainerProfile->schedules->sortBy('start_time') as $schedule)
                <tr class="hover:bg-slate-700/50 transition-colors">
                    <td class="py-3 px-4 text-sm text-gray-300">
                        <div class="font-medium">{{ $schedule->fitnessClass->class_name }}</div>
                    </td>
                    <td class="py-3 px-4 text-sm text-gray-300">
                        <div>
                            <div class="font-medium">{{ $schedule->start_time->format('M j, Y') }}</div>
                            <div class="text-gray-400">{{ $schedule->start_time->format('g:i A') }} - {{ $schedule->end_time->format('g:i A') }}</div>
                        </div>
                    </td>
                    <td class="py-3 px-4 text-sm text-gray-300">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                     {{ $schedule->remaining_slots > 5 ? 'bg-green-900 text-green-200' : 
                                        ($schedule->remaining_slots > 0 ? 'bg-yellow-900 text-yellow-200' : 'bg-red-900 text-red-200') }}">
                            {{ $schedule->booked_slots }}/{{ $schedule->available_slots }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-sm text-gray-300">
                        @if($schedule->start_time->isFuture())
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-900 text-blue-200">
                                Upcoming
                            </span>
                        @elseif($schedule->end_time->isPast())
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-900 text-gray-200">
                                Completed
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-900 text-green-200">
                                In Progress
                            </span>
                        @endif
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
        <h4 class="text-lg font-medium text-gray-100 mb-2">No Scheduled Classes</h4>
        <p class="text-gray-400 mb-4">This trainer hasn't been assigned to any classes yet.</p>
        <a href="{{ route('management.class-schedules.create') }}?trainer_id={{ $trainerProfile->trainer_id }}" 
           class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Schedule First Class
        </a>
    </div>
    @endif
</div>
@endsection