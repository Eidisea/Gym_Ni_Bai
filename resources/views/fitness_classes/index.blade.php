@extends('layouts.management')

@section('title', 'Fitness Classes')
@section('subtitle', 'Manage gym fitness class offerings')

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
                <span class="text-gray-100 text-sm font-medium">Fitness Classes</span>
            </div>
        </li>
    </ol>
</nav>

<div class="flex justify-between items-center mb-4">
    <div>
        <h1 class="text-lg font-bold text-gray-100">Fitness Classes</h1>
        <p class="text-xs text-gray-400 mt-0.5">Manage fitness class offerings</p>
    </div>
    <div class="flex items-center space-x-2">
        @can('admin-only')
        <a href="{{ route('fitness-classes.index', ['archived' => $showArchived ? 0 : 1]) }}" 
           class="inline-flex items-center px-3 py-1.5 bg-slate-700 hover:bg-slate-600 text-white text-sm rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
            </svg>
            {{ $showArchived ? 'Show Active' : 'Show Archived' }}
        </a>
        @endcan
        @if(!$showArchived)
        @can('create', App\Models\FitnessClass::class)
        <a href="{{ route('fitness-classes.create') }}" 
           class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Class
        </a>
        @endcan
        @endif
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($fitnessClasses as $class)
    <div class="bg-slate-800 border border-slate-700 rounded-lg p-4 hover:bg-slate-700/50 transition-colors">
        <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
                <h3 class="text-base font-semibold text-gray-100 mb-1.5">{{ $class->class_name }}</h3>
                <p class="text-gray-400 text-sm mb-2">{{ Str::limit($class->description, 80) }}</p>
            </div>
        </div>

        <div class="space-y-1.5 mb-3">
            <div class="flex items-center text-sm text-gray-300">
                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Max {{ $class->max_participants }} participants
            </div>
            <div class="flex items-center text-sm text-gray-300">
                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                {{ $class->schedules_count }} sessions
            </div>
        </div>

        <div class="flex items-center justify-between pt-3 border-t border-slate-600">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $showArchived ? 'bg-gray-700 text-gray-300' : 'bg-blue-900 text-blue-200' }}">
                {{ $showArchived ? 'Archived' : 'Active' }}
            </span>
            
            <div class="flex items-center space-x-2">
                <a href="{{ route('fitness-classes.show', $class->class_id) }}" 
                   class="text-gray-400 hover:text-indigo-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </a>
                @if($showArchived)
                    @can('admin-only')
                    <form method="POST" action="{{ route('fitness-classes.restore', $class->class_id) }}" 
                          onsubmit="return confirm('Restore this class?')" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-green-400 transition-colors" title="Restore">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                    </form>
                    @endcan
                @else
                    @can('update', $class)
                    <a href="{{ route('fitness-classes.edit', $class) }}" 
                       class="text-gray-400 hover:text-yellow-400 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </a>
                    @endcan
                    @can('admin-only')
                    <button onclick="openArchiveModal({{ $class->class_id }}, '{{ $class->class_name }}')" 
                            class="text-gray-400 hover:text-slate-500 transition-colors" title="Archive">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                        </svg>
                    </button>
                    @endcan
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full">
        <div class="text-center py-8 bg-slate-800 border border-slate-700 rounded-lg">
            <svg class="w-10 h-10 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <h3 class="text-base font-medium text-gray-100 mb-1">{{ $showArchived ? 'No Archived Classes' : 'No Fitness Classes' }}</h3>
            <p class="text-sm text-gray-400 mb-3">{{ $showArchived ? 'No archived classes.' : 'Create your first fitness class.' }}</p>
            @if(!$showArchived)
            @can('create', App\Models\FitnessClass::class)
            <a href="{{ route('fitness-classes.create') }}" 
               class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add First Class
            </a>
            @endcan
            @endif
        </div>
    </div>
    @endforelse
</div>

<div id="archiveModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-slate-800 border border-slate-700 rounded-lg p-4 max-w-md w-full mx-4">
        <h3 class="text-base font-semibold text-gray-100 mb-3">Archive Fitness Class</h3>
        <p class="text-sm text-gray-400 mb-3">Archive this item? It will be hidden from future selections, but historical data will be preserved. <span id="className" class="font-semibold text-gray-100"></span></p>
        
        <form id="archiveForm" method="POST" action="">
            @csrf
            <div class="mb-3">
                <label for="archive_reason" class="block text-sm font-medium text-gray-300 mb-1">Reason</label>
                <select id="archive_reason" name="archive_reason" required
                        class="w-full px-3 py-2 text-sm bg-slate-700 border border-slate-600 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select reason</option>
                    <option value="Low Attendance">Low Attendance</option>
                    <option value="Replaced by New Class">Replaced by New Class</option>
                    <option value="Seasonal">Seasonal</option>
                    <option value="Equipment Unavailable">Equipment Unavailable</option>
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
function openArchiveModal(classId, className) {
    document.getElementById('className').textContent = className;
    document.getElementById('archiveForm').action = `/fitness-classes/${classId}/archive`;
    document.getElementById('archiveModal').classList.remove('hidden');
}

function closeArchiveModal() {
    document.getElementById('archiveModal').classList.add('hidden');
    document.getElementById('archive_reason').value = '';
}
</script>
@endsection
