@extends('layouts.management')

@section('title', 'Trainer Profiles')
@section('subtitle', 'Manage fitness trainers and instructors')

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
                <span class="text-gray-100 text-sm font-medium">Trainer Profiles</span>
            </div>
        </li>
    </ol>
</nav>

<div class="flex justify-between items-center mb-4">
    <div>
        <h1 class="text-lg font-bold text-gray-100">Trainer Profiles</h1>
        <p class="text-xs text-gray-400 mt-0.5">Manage fitness trainers and specializations</p>
    </div>
    <div class="flex items-center space-x-2">
        @can('admin-only')
        <a href="{{ route('trainer-profiles.index', ['archived' => $showArchived ? 0 : 1]) }}" 
           class="inline-flex items-center px-3 py-1.5 bg-slate-700 hover:bg-slate-600 text-white text-sm rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
            </svg>
            {{ $showArchived ? 'Show Active' : 'Show Archived' }}
        </a>
        @endcan
        @if(!$showArchived)
        @can('create', App\Models\TrainerProfile::class)
        <a href="{{ route('trainer-profiles.create') }}" 
           class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            Add Trainer
        </a>
        @endcan
        @endif
    </div>
</div>

<div class="mb-4" x-data="{ 
    filterOpen: false, 
    sortOpen: false,
    search: '{{ $search ?? '' }}',
    filter: '{{ $filter ?? '' }}',
    sort: '{{ $sort ?? 'name_asc' }}',
    searchDelay: null,
    loading: false,
    abortController: null,
    async updateTable() {
        if (this.abortController) {
            this.abortController.abort();
        }
        
        this.loading = true;
        this.abortController = new AbortController();
        
        const params = new URLSearchParams();
        if (this.search) params.set('search', this.search);
        if (this.filter) params.set('filter', this.filter);
        if (this.sort) params.set('sort', this.sort);
        @if($showArchived)
        params.set('archived', '1');
        @endif
        
        const url = '{{ route('trainer-profiles.index') }}' + (params.toString() ? '?' + params.toString() : '');
        
        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                signal: this.abortController.signal
            });
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContainer = doc.getElementById('table-container');
            
            if (newContainer) {
                document.getElementById('table-container').innerHTML = newContainer.innerHTML;
                window.history.pushState({}, '', url);
            }
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Fetch error:', error);
            }
        } finally {
            this.loading = false;
        }
    },
    handleSearch() {
        clearTimeout(this.searchDelay);
        this.searchDelay = setTimeout(() => this.updateTable(), 10);
    },
    handleFilter(value) {
        this.filter = value;
        this.filterOpen = false;
        this.updateTable();
    },
    handleSort(value) {
        this.sort = value;
        this.sortOpen = false;
        this.updateTable();
    },
    clearFilters() {
        this.search = '';
        this.filter = '';
        this.sort = 'name_asc';
        this.updateTable();
    }
}">
    <div class="flex items-center space-x-2">
        <div class="flex-1 relative">
            <input type="text" 
                   x-model="search"
                   @input="handleSearch()"
                   placeholder="Search by name or specialization..."
                   class="w-full px-3 py-2 pl-10 bg-slate-800 border border-slate-700 rounded-lg text-sm text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        <div class="relative">
            <button type="button" 
                    @click="filterOpen = !filterOpen"
                    class="inline-flex items-center px-3 py-2 bg-slate-800 border border-slate-700 hover:bg-slate-700 text-gray-100 text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                Filter By
                <span x-show="filter" class="ml-1.5 px-1.5 py-0.5 bg-indigo-600 text-white text-xs rounded">1</span>
            </button>
            <div x-show="filterOpen" 
                 @click.away="filterOpen = false"
                 x-transition
                 class="absolute right-0 mt-2 w-48 bg-slate-800 border border-slate-700 rounded-lg shadow-lg z-10">
                <div class="py-1">
                    <button type="button" @click="handleFilter('')" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-slate-700"
                            :class="!filter ? 'text-indigo-400 bg-slate-700/50' : 'text-gray-100'">
                        All Trainers
                    </button>
                    <button type="button" @click="handleFilter('active')" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-slate-700"
                            :class="filter === 'active' ? 'text-indigo-400 bg-slate-700/50' : 'text-gray-100'">
                        Active
                    </button>
                    <button type="button" @click="handleFilter('archived')" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-slate-700"
                            :class="filter === 'archived' ? 'text-indigo-400 bg-slate-700/50' : 'text-gray-100'">
                        Archived
                    </button>
                </div>
            </div>
        </div>

        <div class="relative">
            <button type="button" 
                    @click="sortOpen = !sortOpen"
                    class="inline-flex items-center px-3 py-2 bg-slate-800 border border-slate-700 hover:bg-slate-700 text-gray-100 text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                </svg>
                Sort By
            </button>
            <div x-show="sortOpen" 
                 @click.away="sortOpen = false"
                 x-transition
                 class="absolute right-0 mt-2 w-48 bg-slate-800 border border-slate-700 rounded-lg shadow-lg z-10">
                <div class="py-1">
                    <button type="button" @click="handleSort('name_asc')" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-slate-700"
                            :class="sort === 'name_asc' ? 'text-indigo-400 bg-slate-700/50' : 'text-gray-100'">
                        Name A-Z
                    </button>
                    <button type="button" @click="handleSort('name_desc')" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-slate-700"
                            :class="sort === 'name_desc' ? 'text-indigo-400 bg-slate-700/50' : 'text-gray-100'">
                        Name Z-A
                    </button>
                    <button type="button" @click="handleSort('specialization')" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-slate-700"
                            :class="sort === 'specialization' ? 'text-indigo-400 bg-slate-700/50' : 'text-gray-100'">
                        Specialization
                    </button>
                    <button type="button" @click="handleSort('newest')" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-slate-700"
                            :class="sort === 'newest' ? 'text-indigo-400 bg-slate-700/50' : 'text-gray-100'">
                        Newest First
                    </button>
                </div>
            </div>
        </div>

        <button type="button" 
                x-show="search || filter"
                @click="clearFilters()"
                class="px-3 py-2 bg-slate-700 hover:bg-slate-600 text-white text-sm font-medium rounded-lg transition-colors">
            Clear
        </button>
    </div>
</div>

<div id="table-container" :class="{ 'opacity-50 pointer-events-none': loading }">
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($profiles as $trainer)
    <div class="bg-slate-800 border border-slate-700 rounded-lg p-4 hover:bg-slate-700/50 transition-colors">
        <div class="flex items-center mb-3">
            <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center">
                <span class="text-sm font-medium text-white">{{ substr($trainer->first_name, 0, 1) }}{{ substr($trainer->last_name, 0, 1) }}</span>
            </div>
            <div class="ml-3">
                <h3 class="text-base font-semibold text-gray-100">{{ $trainer->full_name }}</h3>
                <p class="text-xs text-gray-400">{{ $trainer->specialization }}</p>
            </div>
        </div>

        <div class="space-y-1.5 mb-3">
            <div class="flex items-center text-sm text-gray-300">
                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                {{ $trainer->schedules->count() }} classes
            </div>
            <div class="flex items-center text-sm text-gray-300">
                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Since {{ $trainer->created_at->format('M Y') }}
            </div>
        </div>

        <div class="flex items-center justify-between pt-3 border-t border-slate-600">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $showArchived ? 'bg-gray-700 text-gray-300' : 'bg-green-900 text-green-200' }}">
                {{ $showArchived ? 'Archived' : 'Available' }}
            </span>
            
            <div class="flex items-center space-x-2">
                <a href="{{ route('trainer-profiles.show', $trainer->trainer_id) }}" 
                   class="text-gray-400 hover:text-indigo-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </a>
                @if($showArchived)
                    @can('admin-only')
                    <form method="POST" action="{{ route('trainer-profiles.restore', $trainer->trainer_id) }}" 
                          onsubmit="return confirm('Restore this trainer?')" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-green-400 transition-colors" title="Restore">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                    </form>
                    @endcan
                @else
                    @can('update', $trainer)
                    <a href="{{ route('trainer-profiles.edit', $trainer) }}" 
                       class="text-gray-400 hover:text-yellow-400 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </a>
                    @endcan
                    @can('admin-only')
                    <button onclick="openArchiveModal({{ $trainer->trainer_id }}, '{{ $trainer->full_name }}')" 
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <h3 class="text-base font-medium text-gray-100 mb-1">{{ $showArchived ? 'No Archived Trainers' : 'No Trainer Profiles' }}</h3>
            <p class="text-sm text-gray-400 mb-3">{{ $showArchived ? 'No archived trainers.' : 'Add your first fitness trainer.' }}</p>
            @if(!$showArchived)
            @can('create', App\Models\TrainerProfile::class)
            <a href="{{ route('trainer-profiles.create') }}" 
               class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Add First Trainer
            </a>
            @endcan
            @endif
        </div>
    </div>
    @endforelse
</div>

@if($profiles->hasPages())
<div class="mt-4">
    {{ $profiles->links() }}
</div>
@endif
</div>

<div id="archiveModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-slate-800 border border-slate-700 rounded-lg p-4 max-w-md w-full mx-4">
        <h3 class="text-base font-semibold text-gray-100 mb-3">Archive Trainer</h3>
        <p class="text-sm text-gray-400 mb-3">Archive this trainer? Past schedules will remain intact. <span id="trainerName" class="font-semibold text-gray-100"></span></p>
        
        <form id="archiveForm" method="POST" action="">
            @csrf
            <div class="mb-3">
                <label for="archive_reason" class="block text-sm font-medium text-gray-300 mb-1">Reason</label>
                <select id="archive_reason" name="archive_reason" required
                        class="w-full px-3 py-2 text-sm bg-slate-700 border border-slate-600 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select reason</option>
                    <option value="Resigned">Resigned</option>
                    <option value="Contract Ended">Contract Ended</option>
                    <option value="Moved Away">Moved Away</option>
                    <option value="Retired">Retired</option>
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
    document.getElementById('archiveForm').action = `/trainer-profiles/${trainerId}/archive`;
    document.getElementById('archiveModal').classList.remove('hidden');
}

function closeArchiveModal() {
    document.getElementById('archiveModal').classList.add('hidden');
    document.getElementById('archive_reason').value = '';
}

document.addEventListener('click', function(e) {
    if (e.target.closest('.pagination a')) {
        e.preventDefault();
        const url = e.target.closest('a').href;
        
        document.getElementById('table-container').classList.add('opacity-50', 'pointer-events-none');
        
        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContainer = doc.getElementById('table-container');
            
            if (newContainer) {
                document.getElementById('table-container').innerHTML = newContainer.innerHTML;
                window.history.pushState({}, '', url);
            }
            document.getElementById('table-container').classList.remove('opacity-50', 'pointer-events-none');
        });
    }
});
</script>
@endsection
