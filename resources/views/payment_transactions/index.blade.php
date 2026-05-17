@extends('layouts.management')

@section('title', 'Payment Transactions')
@section('subtitle', 'Manage customer payments and transaction records')

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
                <span class="text-gray-100 text-sm font-medium">Payment Transactions</span>
            </div>
        </li>
    </ol>
</nav>

<div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-bold text-gray-100">Payment Transactions</h1>
    <a href="{{ route('management.payment-transactions.create') }}" 
       class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        New Transaction
    </a>
</div>

<div class="mb-4" x-data="{ 
    filterOpen: false, 
    sortOpen: false,
    search: '{{ $search }}',
    filter: '{{ $filter }}',
    sort: '{{ $sort ?? 'newest' }}',
    searchDelay: null,
    async updateTable() {
        const params = new URLSearchParams();
        if (this.search) params.set('search', this.search);
        if (this.filter) params.set('filter', this.filter);
        if (this.sort) params.set('sort', this.sort);
        
        const url = '{{ route('management.payment-transactions.index') }}' + (params.toString() ? '?' + params.toString() : '');
        
        const response = await fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const html = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newContainer = doc.getElementById('table-container');
        
        if (newContainer) {
            document.getElementById('table-container').innerHTML = newContainer.innerHTML;
            window.history.pushState({}, '', url);
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
        this.sort = 'newest';
        this.updateTable();
    }
}">
    <div class="flex items-center space-x-2">
        <div class="flex-1 relative">
            <input type="text" 
                   x-model="search"
                   @input="handleSearch()"
                   placeholder="Search by transaction ID, customer name, or reference..."
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
                 class="absolute right-0 mt-2 w-56 bg-slate-800 border border-slate-700 rounded-lg shadow-lg z-10">
                <div class="py-1">
                    <div class="px-3 py-2 text-xs font-medium text-gray-400 uppercase border-b border-slate-700">Status</div>
                    <button type="button" @click="handleFilter('')" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-slate-700"
                            :class="!filter ? 'text-indigo-400 bg-slate-700/50' : 'text-gray-100'">
                        All Statuses
                    </button>
                    <button type="button" @click="handleFilter('completed')" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-slate-700"
                            :class="filter === 'completed' ? 'text-indigo-400 bg-slate-700/50' : 'text-gray-100'">
                        Completed
                    </button>
                    <button type="button" @click="handleFilter('pending')" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-slate-700"
                            :class="filter === 'pending' ? 'text-indigo-400 bg-slate-700/50' : 'text-gray-100'">
                        Pending
                    </button>
                    <button type="button" @click="handleFilter('failed')" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-slate-700"
                            :class="filter === 'failed' ? 'text-indigo-400 bg-slate-700/50' : 'text-gray-100'">
                        Failed
                    </button>
                    <button type="button" @click="handleFilter('refunded')" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-slate-700"
                            :class="filter === 'refunded' ? 'text-indigo-400 bg-slate-700/50' : 'text-gray-100'">
                        Refunded
                    </button>
                    <button type="button" @click="handleFilter('voided')" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-slate-700"
                            :class="filter === 'voided' ? 'text-indigo-400 bg-slate-700/50' : 'text-gray-100'">
                        Voided
                    </button>
                    <div class="px-3 py-2 text-xs font-medium text-gray-400 uppercase border-t border-b border-slate-700 mt-1">Payment Method</div>
                    <button type="button" @click="handleFilter('cash')" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-slate-700"
                            :class="filter === 'cash' ? 'text-indigo-400 bg-slate-700/50' : 'text-gray-100'">
                        Cash
                    </button>
                    <button type="button" @click="handleFilter('card')" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-slate-700"
                            :class="filter === 'card' ? 'text-indigo-400 bg-slate-700/50' : 'text-gray-100'">
                        Card
                    </button>
                    <button type="button" @click="handleFilter('ewallet')" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-slate-700"
                            :class="filter === 'ewallet' ? 'text-indigo-400 bg-slate-700/50' : 'text-gray-100'">
                        E-Wallet
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
                    <button type="button" @click="handleSort('newest')" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-slate-700"
                            :class="sort === 'newest' ? 'text-indigo-400 bg-slate-700/50' : 'text-gray-100'">
                        Newest First
                    </button>
                    <button type="button" @click="handleSort('oldest')" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-slate-700"
                            :class="sort === 'oldest' ? 'text-indigo-400 bg-slate-700/50' : 'text-gray-100'">
                        Oldest First
                    </button>
                    <button type="button" @click="handleSort('amount_high')" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-slate-700"
                            :class="sort === 'amount_high' ? 'text-indigo-400 bg-slate-700/50' : 'text-gray-100'">
                        Amount: High to Low
                    </button>
                    <button type="button" @click="handleSort('amount_low')" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-slate-700"
                            :class="sort === 'amount_low' ? 'text-indigo-400 bg-slate-700/50' : 'text-gray-100'">
                        Amount: Low to High
                    </button>
                    <button type="button" @click="handleSort('customer_name')" 
                            class="w-full text-left px-3 py-2 text-sm hover:bg-slate-700"
                            :class="sort === 'customer_name' ? 'text-indigo-400 bg-slate-700/50' : 'text-gray-100'">
                        Customer Name A-Z
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

<div id="table-container">
<div class="bg-slate-800 border border-slate-700 rounded-lg overflow-hidden">
    @if($transactions->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-slate-700">
                <tr>
                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-400 uppercase">Transaction</th>
                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-400 uppercase">Customer</th>
                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-400 uppercase">Type</th>
                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-400 uppercase">Amount</th>
                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-400 uppercase">Payment Method</th>
                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-400 uppercase">Status</th>
                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-400 uppercase">Date</th>
                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                @foreach($transactions as $transaction)
                <tr class="hover:bg-slate-700/50">
                    <td class="py-2 px-3">
                        <div class="text-sm font-medium text-gray-100">#{{ $transaction->transaction_id }}</div>
                        @if($transaction->notes)
                            <div class="text-xs text-gray-400 truncate max-w-32">{{ $transaction->notes }}</div>
                        @endif
                    </td>
                    <td class="py-2 px-3">
                        <div class="text-sm font-medium text-gray-100">{{ $transaction->customerProfile?->full_name ?? 'Archived Customer' }}</div>
                        <div class="text-xs text-gray-400">{{ $transaction->customerProfile?->user?->email ?? 'N/A' }}</div>
                    </td>
                    <td class="py-2 px-3">
                        @if($transaction->subscription)
                            <div class="text-sm text-gray-100">Membership</div>
                            <div class="text-xs text-gray-400">{{ $transaction->subscription?->plan?->plan_name ?? 'Archived Plan' }}</div>
                        @elseif($transaction->booking)
                            <div class="text-sm text-gray-100">Class Booking</div>
                            <div class="text-xs text-gray-400">{{ $transaction->booking?->schedule?->fitnessClass?->class_name ?? 'Archived Class' }}</div>
                        @else
                            <div class="text-sm text-gray-400">General Payment</div>
                        @endif
                    </td>
                    <td class="py-2 px-3 text-sm font-medium text-gray-100">
                        ₱{{ number_format($transaction->amount, 2) }}
                    </td>
                    <td class="py-2 px-3">
                        <div class="flex items-center text-sm">
                            @if($transaction->payment_method === 'cash')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Cash</span>
                            @elseif($transaction->payment_method === 'card')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Card</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">E-Wallet</span>
                            @endif
                        </div>
                        @if($transaction->payment_method === 'card' && $transaction->cardPayment)
                            <div class="text-xs text-gray-400">**** {{ $transaction->cardPayment->card_last_four }}</div>
                        @elseif($transaction->payment_method === 'ewallet' && $transaction->ewalletPayment)
                            <div class="text-xs text-gray-400">{{ ucfirst($transaction->ewalletPayment->provider) }}</div>
                        @endif
                    </td>
                    <td class="py-2 px-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                     {{ $transaction->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                        ($transaction->status === 'failed' ? 'bg-red-100 text-red-800' : 
                                         ($transaction->status === 'refunded' ? 'bg-orange-100 text-orange-800' : 'bg-yellow-100 text-yellow-800')) }}">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </td>
                    <td class="py-2 px-3">
                        <div class="text-sm text-gray-100">{{ $transaction->transaction_date->format('M j, Y') }}</div>
                        <div class="text-xs text-gray-400">{{ $transaction->transaction_date->format('g:i A') }}</div>
                    </td>
                    <td class="py-2 px-3">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('management.payment-transactions.show', $transaction) }}" class="text-gray-400 hover:text-indigo-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            @can('update', $transaction)
                            <a href="{{ route('management.payment-transactions.edit', $transaction) }}" class="text-gray-400 hover:text-yellow-400">
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
    @if($transactions->hasPages())
    <div class="px-4 py-3 border-t border-slate-700">
        {{ $transactions->links() }}
    </div>
    @endif
    @else
    <div class="text-center py-8">
        <svg class="w-10 h-10 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        <h3 class="text-sm font-medium text-gray-100 mb-1">No Payment Transactions</h3>
        <p class="text-xs text-gray-400 mb-3">Create your first payment transaction</p>
        <a href="{{ route('management.payment-transactions.create') }}" 
           class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Create First Transaction
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
        });
    }
});
</script>
@endsection
