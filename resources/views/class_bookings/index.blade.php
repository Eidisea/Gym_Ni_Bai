@extends('layouts.management')

@section('title', 'Class Bookings')
@section('subtitle', 'Manage customer class bookings and reservations')

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
                <span class="text-gray-100 text-sm font-medium">Class Bookings</span>
            </div>
        </li>
    </ol>
</nav>

<div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-bold text-gray-100">Class Bookings</h1>
    <a href="{{ route('class-bookings.create') }}" 
       class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        New Booking
    </a>
</div>

<div class="mb-4" x-data="{ 
    filterOpen: false, 
    sortOpen: false,
    search: '{{ $search ?? '' }}',
    filter: '{{ $filter ?? '' }}',
    sort: '{{ $sort ?? 'date_desc' }}',
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
        
        const url = '{{ route('class-bookings.index') }}' + (params.toString() ? '?' + params.toString() : '');
        
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
        this.sort = 'date_desc';
        this.updateTable();
    }
}">
    <div class="flex items-center space-x-2">
        <div class="flex-1 relative">
            <input type="text" 
                   x-model="search"
                   @input="handleSearch()"
                   placeholder="Search by customer name, class name, or status..."
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
                        All Bookings
                    </button>
                    <button type="button" @click="handleFilter('confirmed')" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-slate-700"
                            :class="filter === 'confirmed' ? 'text-indigo-400 bg-slate-700/50' : 'text-gray-100'">
                        Confirmed
                    </button>
                    <button type="button" @click="handleFilter('attended')" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-slate-700"
                            :class="filter === 'attended' ? 'text-indigo-400 bg-slate-700/50' : 'text-gray-100'">
                        Attended
                    </button>
                    <button type="button" @click="handleFilter('cancelled')" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-slate-700"
                            :class="filter === 'cancelled' ? 'text-indigo-400 bg-slate-700/50' : 'text-gray-100'">
                        Cancelled
                    </button>
                    <button type="button" @click="handleFilter('no_show')" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-slate-700"
                            :class="filter === 'no_show' ? 'text-indigo-400 bg-slate-700/50' : 'text-gray-100'">
                        No Show
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
                    <button type="button" @click="handleSort('date_desc')" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-slate-700"
                            :class="sort === 'date_desc' ? 'text-indigo-400 bg-slate-700/50' : 'text-gray-100'">
                        Date: Newest First
                    </button>
                    <button type="button" @click="handleSort('date_asc')" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-slate-700"
                            :class="sort === 'date_asc' ? 'text-indigo-400 bg-slate-700/50' : 'text-gray-100'">
                        Date: Oldest First
                    </button>
                    <button type="button" @click="handleSort('customer_name')" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-slate-700"
                            :class="sort === 'customer_name' ? 'text-indigo-400 bg-slate-700/50' : 'text-gray-100'">
                        Customer Name A-Z
                    </button>
                    <button type="button" @click="handleSort('class_name')" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-slate-700"
                            :class="sort === 'class_name' ? 'text-indigo-400 bg-slate-700/50' : 'text-gray-100'">
                        Class Name A-Z
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
<div class="bg-slate-800 border border-slate-700 rounded-lg overflow-hidden">
    @if($bookings->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-slate-700">
                <tr>
                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-400 uppercase">Customer</th>
                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-400 uppercase">Class</th>
                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-400 uppercase">Schedule</th>
                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-400 uppercase">Booked At</th>
                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-400 uppercase">Status</th>
                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                @foreach($bookings as $booking)
                <tr class="hover:bg-slate-700/50">
                    <td class="py-2 px-3">
                        <div class="text-sm font-medium text-gray-100">{{ $booking->customerProfile?->full_name ?? 'Archived Customer' }}</div>
                        <div class="text-xs text-gray-400">{{ $booking->customerProfile?->user?->email ?? 'N/A' }}</div>
                    </td>
                    <td class="py-2 px-3">
                        <div class="text-sm text-gray-100">{{ $booking->schedule?->fitnessClass?->class_name ?? 'Archived Class' }}</div>
                        <div class="text-xs text-gray-400">{{ $booking->schedule?->trainerProfile?->full_name ?? 'Archived Trainer' }}</div>
                    </td>
                    <td class="py-2 px-3">
                        <div class="text-sm text-gray-100">{{ $booking->schedule?->start_time?->format('M j, Y') ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-400">{{ $booking->schedule?->start_time?->format('g:i A') ?? 'N/A' }} - {{ $booking->schedule?->end_time?->format('g:i A') ?? 'N/A' }}</div>
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
                        @if($booking->status === 'confirmed' && $booking->schedule->start_time->isFuture())
                            <div class="text-xs text-green-400 mt-0.5">{{ $booking->schedule->start_time->diffForHumans() }}</div>
                        @endif
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
    @if($bookings->hasPages())
    <div class="px-4 py-3 border-t border-slate-700">
        {{ $bookings->links() }}
    </div>
    @endif
    @else
    <div class="text-center py-8">
        <svg class="w-10 h-10 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        <h3 class="text-sm font-medium text-gray-100 mb-1">No Class Bookings</h3>
        <p class="text-xs text-gray-400 mb-3">Create your first class booking</p>
        <a href="{{ route('class-bookings.create') }}" 
           class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            Create First Booking
        </a>
    </div>
    @endif
</div>
</div>

<script>
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
